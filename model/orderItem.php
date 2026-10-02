<?php
class orderItem extends BaseModel{
    protected string $tabel = 'order_items';
    protected string $primary_key = 'id';

    public function insert(array $data): Response{
        $query = '
            INSERT INTO order_items (
                order_id,
                ticket_id,
                quantity,
                price,
                subtotal
            )
            VALUES (
                :order_id,
                :ticket_id,
                :quantity,
                :price,
                :subtotal
            )
        ';

        return $this->db->send_query($query, $data);
    }
}