<?php

class Transaction
{
    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    public function checkout(
        int $event_id,
        array $tickets,
        string $customer_name,
        string $customer_email,
        string $customer_phone,
        string $payment_method
    ): Respon {

        $conn = $this->db->getConnection();

        try {

            if (!pg_query($conn, "BEGIN")) {
                throw new Exception(
                    "Transaksi database gagal dimulai."
                );
            }

            $ticket_ids = array_keys($tickets);

            $placeholders = [];
            $params = [$event_id];

            foreach ($ticket_ids as $index => $ticket_id) {

                $placeholders[] = '$' . ($index + 2);
                $params[] = $ticket_id;
            }

            $query =
                "SELECT *
                 FROM tickets
                 WHERE event_id = $1
                 AND id IN (" .
                    implode(',', $placeholders) .
                 ")
                 ORDER BY price";

            $result = pg_query_params(
                $conn,
                $query,
                $params
            );

            if ($result === false) {

                throw new Exception(
                    pg_last_error($conn)
                );
            }

            $ticket_data = pg_fetch_all($result) ?: [];

            if (
                count($ticket_data) !==
                count($tickets)
            ) {

                throw new Exception(
                    "Data tiket tidak valid."
                );
            }

            $total_amount = 0;


            foreach ($ticket_data as $ticket) {

                $ticket_id =
                    (int) $ticket['id'];

                $quantity =
                    (int) $tickets[$ticket_id];


                if ($quantity <= 0) {

                    throw new Exception(
                        "Jumlah tiket tidak valid."
                    );
                }

                if (
                    $quantity >
                    (int) $ticket['stock']
                ) {

                    throw new Exception(
                        "Stok tiket " .
                        $ticket['name'] .
                        " tidak mencukupi."
                    );
                }

                $total_amount +=
                    $ticket['price'] *
                    $quantity;
            }

            $order_code =
                'ORD-' .
                date('YmdHis') .
                '-' .
                random_int(100, 999);

            $result = pg_query_params(
                $conn,
                "INSERT INTO orders
                    (
                        order_code,
                        customer_name,
                        customer_email,
                        customer_phone,
                        total_amount,
                        status
                    )
                 VALUES
                    (
                        $1,
                        $2,
                        $3,
                        $4,
                        $5,
                        'paid'
                    )
                 RETURNING id",
                [
                    $order_code,
                    $customer_name,
                    $customer_email,
                    $customer_phone,
                    $total_amount
                ]
            );

            if ($result === false) {

                throw new Exception(
                    pg_last_error($conn)
                );
            }

            $order = pg_fetch_assoc($result);

            if (!$order) {

                throw new Exception(
                    "Order gagal dibuat."
                );
            }

            $order_id =
                (int) $order['id'];

            foreach ($ticket_data as $ticket) {

                $ticket_id =
                    (int) $ticket['id'];

                $quantity =
                    (int) $tickets[$ticket_id];

                $price =
                    $ticket['price'];

                $subtotal =
                    $price * $quantity;

                $result = pg_query_params(
                    $conn,
                    "INSERT INTO order_items
                        (
                            order_id,
                            ticket_id,
                            quantity,
                            price,
                            subtotal
                        )
                     VALUES
                        (
                            $1,
                            $2,
                            $3,
                            $4,
                            $5
                        )",
                    [
                        $order_id,
                        $ticket_id,
                        $quantity,
                        $price,
                        $subtotal
                    ]
                );


                if ($result === false) {

                    throw new Exception(
                        pg_last_error($conn)
                    );
                }

                $result = pg_query_params(
                    $conn,
                    "UPDATE tickets
                     SET stock = stock - $1
                     WHERE id = $2
                     AND stock >= $1",
                    [
                        $quantity,
                        $ticket_id
                    ]
                );

                if ($result === false) {

                    throw new Exception(
                        pg_last_error($conn)
                    );
                }

                if (
                    pg_affected_rows($result) !== 1
                ) {

                    throw new Exception(
                        "Stok tiket tidak mencukupi."
                    );
                }
            }

            $result = pg_query_params(
                $conn,
                "INSERT INTO payments
                    (
                        order_id,
                        payment_method,
                        amount,
                        status,
                        paid_at
                    )
                 VALUES
                    (
                        $1,
                        $2,
                        $3,
                        'paid',
                        CURRENT_TIMESTAMP
                    )",
                [
                    $order_id,
                    $payment_method,
                    $total_amount
                ]
            );

            if ($result === false) {

                throw new Exception(
                    pg_last_error($conn)
                );
            }

            if (!pg_query($conn, "COMMIT")) {

                throw new Exception(
                    "Transaksi gagal disimpan."
                );
            }

            return new Respon(
                true,
                'Transaksi berhasil.',
                [[
                    'order_id' =>
                        $order_id,

                    'order_code' =>
                        $order_code,

                    'total_amount' =>
                        $total_amount
                ]]
            );

        } catch (Exception $e) {
            
            @pg_query($conn, "ROLLBACK");


            return new Respon(
                false,
                $e->getMessage()
            );
        }
    }
}
