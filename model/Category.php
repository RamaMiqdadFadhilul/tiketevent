<?php

class Category extends BaseModel
{
    protected string $tabel = 'categories';

    protected string $primary_key = 'id';

    public function insert(array $data): Respon
    {
        return $this->db->send_query(
            'INSERT INTO categories (name, description)
             VALUES (:name, :description)',
            [
                'name' => $data['name'],
                'description' => $data['description']
            ]
        );
    }

    public function update(int $id, array $data): Respon
    {
        return $this->db->send_query(
            'UPDATE categories
             SET name = :name,
                 description = :description
             WHERE id = :id',
            [
                'name' => $data['name'],
                'description' => $data['description'],
                'id' => $id
            ]
        );
    }

    public function delete(int $id): Respon
    {
        return $this->db->send_query(
            'DELETE FROM categories
             WHERE id = :id',
            [
                'id' => $id
            ]
        );
    }
}