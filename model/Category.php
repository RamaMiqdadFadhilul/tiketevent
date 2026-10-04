<?php

class Category extends BaseModel
{
    protected string $tabel = 'categories';

    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO categories
                (name, description)
             VALUES
                ($1, $2)
             RETURNING *',
            [
                $data['name'],
                $data['description'] ?? null
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE categories
             SET
                name = $1,
                description = $2
             WHERE id = $3',
            [
                $data['name'],
                $data['description'] ?? null,
                $id
            ]
        );
    }

    public function delete(int $id): Respon
    {
        $check = $this->db->send_query(
            'SELECT id
            FROM events
            WHERE category_id = $1
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
                'Category tidak dapat dihapus karena masih digunakan oleh event.'
            );
        }

        return $this->db->send_query(
            'DELETE FROM categories
            WHERE id = $1',
            [$id]
        );
    }
}
