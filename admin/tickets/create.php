<?php

require_once __DIR__ . '/../../model/DBconnection.php';
require_once __DIR__ . '/../../model/Respon.php';
require_once __DIR__ . '/ticket.php';

class TicketCreator
{
    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    public function simpanTiket(Ticket $tiket)
    {
        $sql = "INSERT INTO tickets (event_id, name, price, stock) VALUES (:event_id, :name, :price, :stock)";
        $params = [
            ':event_id' => $tiket->getEventId(),
            ':name'     => $tiket->getName(),
            ':price'    => $tiket->getPrice(),
            ':stock'    => $tiket->getStock()
        ];

        return $this->db->send_query($sql, $params);
    }
}

// ==========================================
// TESTING CREATE
// ==========================================
echo "=== TESTING CREATE TIKET KE POSTGRESQL ===\n\n";

$db = new DBconnection();
$creator = new TicketCreator($db);

try {
    $tiketBaru = new Ticket(null, 1, "PRESALE FESTIVAL UNAIR", 85000, 75);

    $respon = $creator->simpanTiket($tiketBaru);
    $dumpRespon = serialize($respon);

    if (str_contains(strtolower($dumpRespon), 'berhasil')) {
        echo "[SUKSES] Data tiket berhasil masuk ke PostgreSQL!\n";
        echo "Event ID : " . $tiketBaru->getEventId() . "\n";
        echo "Nama     : " . $tiketBaru->getName() . "\n";
        echo "Harga    : Rp " . number_format($tiketBaru->getPrice(), 0, ',', '.') . "\n";
        echo "Stok     : " . $tiketBaru->getStock() . " pcs\n";
    } else {
        echo "[GAGAL] Database menolak eksekusi.\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}