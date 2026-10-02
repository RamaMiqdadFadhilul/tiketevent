<?php

class Ticket extends BaseModel
{
    protected string $tabel = 'tickets';
    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO tickets
                (event_id, name, price, stock)
             VALUES
                (:event_id, :name, :price, :stock)
             RETURNING *',
            [
                'event_id' => $data['event_id'],
                'name' => $data['name'],
                'price' => $data['price'],
                'stock' => $data['stock']
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE tickets
             SET
                event_id = :event_id,
                name = :name,
                price = :price,
                stock = :stock
             WHERE id = :id',
            [
                'id' => $id,
                'event_id' => $data['event_id'],
                'name' => $data['name'],
                'price' => $data['price'],
                'stock' => $data['stock']
            ]
        );
    }

    public function delete(int $id): Respon
    {
        return $this->db->send_query(
            'DELETE FROM tickets
             WHERE id = :id',
            [
                'id' => $id
            ]
        );
    }
}