<?php

class Ticket extends BaseModel
{
    protected string $tabel = 'tickets';

    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO tickets
                (
                    event_id,
                    name,
                    price,
                    stock
                )
             VALUES
                (
                    $1,
                    $2,
                    $3,
                    $4
                )
             RETURNING *',
            [
                $data['event_id'],
                $data['name'],
                $data['price'],
                $data['stock']
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE tickets
             SET
                event_id = $1,
                name = $2,
                price = $3,
                stock = $4
             WHERE id = $5',
            [
                $data['event_id'],
                $data['name'],
                $data['price'],
                $data['stock'],
                $id
            ]
        );
    }

    public function delete(int $id): Respon
    {
        $check = $this->db->send_query(
            'SELECT id
            FROM order_items
            WHERE ticket_id = $1
            LIMIT 1',
            [$id]
        );

        if (!$check->success) {
            return new Respon(
                false,
                $check->message
            );
        }

        if (!empty($check->data)) {
            return new Respon(
                false,
                'Ticket tidak dapat dihapus karena sudah digunakan dalam transaksi.'
            );
        }

        return $this->db->send_query(
            'DELETE FROM tickets
            WHERE id = $1',
            [$id]
        );
    }
}
