<?php

class Event extends BaseModel
{
    protected string $tabel = 'events';

    protected string $primary_key = 'id';

    // CREATE
    public function insert(array $data): Respon
    {
        $query = "INSERT INTO events (
                    category_id,
                    name,
                    description,
                    location,
                    event_date,
                    event_time,
                    image,
                    status
                  )
                  VALUES (
                    :category_id,
                    :name,
                    :description,
                    :location,
                    :event_date,
                    :event_time,
                    :image,
                    :status
                  )";

        return $this->db->send_query($query, [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'location' => $data['location'],
            'event_date' => $data['event_date'],
            'event_time' => $data['event_time'],
            'image' => $data['image'],
            'status' => $data['status']
        ]);
    }

    // UPDATE
    public function update(int $id, array $data): Respon
    {
        $query = "UPDATE events SET
                    category_id = :category_id,
                    name = :name,
                    description = :description,
                    location = :location,
                    event_date = :event_date,
                    event_time = :event_time,
                    image = :image,
                    status = :status
                  WHERE id = :id";

        return $this->db->send_query($query, [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'location' => $data['location'],
            'event_date' => $data['event_date'],
            'event_time' => $data['event_time'],
            'image' => $data['image'],
            'status' => $data['status'],
            'id' => $id
        ]);
    }

    // DELETE
    public function delete(int $id): Respon
    {
        $query = "DELETE FROM events
                  WHERE id = :id";

        return $this->db->send_query($query, [
            'id' => $id
        ]);
    }
}