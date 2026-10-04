<?php

abstract class BaseModel implements Crudable
{
    protected DBconnection $db;

    protected string $tabel = '';

    protected string $primary_key = '';


    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }


    public function find_all(): array
    {
        $respon = $this->db->send_query(
            'SELECT * FROM ' . $this->tabel .
            ' ORDER BY ' . $this->primary_key
        );

        return $respon->data;
    }


    public function find_by_id(int $id): ?array
    {
        $respon = $this->db->send_query(
            'SELECT * FROM ' . $this->tabel .
            ' WHERE ' . $this->primary_key . ' = $1',
            [
                $id
            ]
        );

        return $respon->data[0] ?? null;
    }


    public function nama_tabel(): string
    {
        return $this->tabel;
    }


    // Wajib diimplementasikan oleh class turunan
    abstract public function insert(array $data): Respon;


    // Wajib diimplementasikan oleh class turunan
    abstract public function update(
        int $id,
        array $data
    ): Respon;


    // Wajib diimplementasikan oleh class turunan
    abstract public function delete(
        int $id
    ): Respon;
}
