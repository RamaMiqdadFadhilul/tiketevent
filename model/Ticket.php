<?php

class Ticket extends BaseModel{
    protected string $tabel = 'tickets';
    protected string $primary_key = 'id';

    public function insert(array $data): Respon{
        $query = '
            INSERT INTO tickets (
                event_id,
                name,
                price,
                stock
            )
            VALUES (
                :event_id,
                :name,
                :price,
                :stock
            )
        ';

        return $this->db->send_query($query, $data);
    }
}