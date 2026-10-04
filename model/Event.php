<?php

class Event extends BaseModel
{
    protected string $tabel = 'events';

    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO events
                (
                    category_id,
                    name,
                    description,
                    location,
                    event_date,
                    event_time,
                    image,
                    status
                )
             VALUES
                (
                    $1,
                    $2,
                    $3,
                    $4,
                    $5,
                    $6,
                    $7,
                    $8
                )
             RETURNING *',
            [
                $data['category_id'],
                $data['name'],
                $data['description'] ?? null,
                $data['location'],
                $data['event_date'],
                $data['event_time'],
                $data['image'] ?? null,
                $data['status'] ?? 'upcoming'
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE events
             SET
                category_id = $1,
                name = $2,
                description = $3,
                location = $4,
                event_date = $5,
                event_time = $6,
                image = $7,
                status = $8
             WHERE id = $9',
            [
                $data['category_id'],
                $data['name'],
                $data['description'] ?? null,
                $data['location'],
                $data['event_date'],
                $data['event_time'],
                $data['image'] ?? null,
                $data['status'] ?? 'upcoming',
                $id
            ]
        );
    }

    public function delete(int $id): Respon
    {
        $check = $this->db->send_query(
            'SELECT id
            FROM tickets
            WHERE event_id = $1
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
                'Event tidak dapat dihapus karena masih memiliki ticket.'
            );
        }

        return $this->db->send_query(
            'DELETE FROM events
            WHERE id = $1',
            [$id]
        );
    }
}
