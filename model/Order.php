<?php

class Order extends BaseModel
{
    protected string $tabel = 'orders';

    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO orders
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
                    $6
                )
             RETURNING *',
            [
                $data['order_code'],
                $data['customer_name'],
                $data['customer_email'],
                $data['customer_phone'],
                $data['total_amount'],
                $data['status'] ?? 'pending'
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE orders
             SET
                order_code = $1,
                customer_name = $2,
                customer_email = $3,
                customer_phone = $4,
                total_amount = $5,
                status = $6
             WHERE id = $7',
            [
                $data['order_code'],
                $data['customer_name'],
                $data['customer_email'],
                $data['customer_phone'],
                $data['total_amount'],
                $data['status'] ?? 'pending',
                $id
            ]
        );
    }

    public function delete(int $id): Respon
    {
        return $this->db->send_query(
            'DELETE FROM orders
             WHERE id = $1',
            [
                $id
            ]
        );
    }
}
