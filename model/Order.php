<?php

class Order extends BaseModel{
    protected string $tabel = 'orders';
    protected string $primary_key = 'id';

    public function insert(array $data): Respon{
        $query = '
            INSERT INTO orders (
                order_code,
                customer_name,
                customer_email,
                customer_phone,
                total_amount,
                status
            )
            VALUES (
                :order_code,
                :customer_name,
                :customer_email,
                :customer_phone,
                :total_amount,
                :status
            )
        ';

        return $this->db->send_query($query, $data);
    }
}