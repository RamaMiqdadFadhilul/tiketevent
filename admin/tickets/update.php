<?php

require_once __DIR__ . '/../../model/DBconnection.php';
require_once __DIR__ . '/../../model/Respon.php';
require_once __DIR__ . '/ticket.php';

class TicketUpdater
{
    private DBconnection $db;

    // Constructor untuk menerima instance koneksi
    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    // Method Update utama
    public function update(Ticket $tiket)
    {
        $sql = "UPDATE tickets SET name = :name, price = :price, stock = :stock WHERE id = :id";
        $params = [
            ':id'    => $tiket->getId(),
            ':name'  => $tiket->getName(),
            ':price' => $tiket->getPrice(),
            ':stock' => $tiket->getStock()
        ];

        return $this->db->send_query($sql, $params);
    }
}