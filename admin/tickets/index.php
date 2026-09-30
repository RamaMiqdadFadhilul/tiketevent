<?php

require_once __DIR__ . '/../../model/DBconnection.php';
require_once __DIR__ . '/../../model/Respon.php';
require_once __DIR__ . '/ticket.php';

class TicketReader
{
    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    public function ambilSemua(): array
    {
        $sql = "SELECT id, event_id, name, price, stock FROM tickets ORDER BY id ASC";
        $respon = $this->db->send_query($sql);

        $rows = $respon->data ?? [];

        $daftarTiket = [];
        foreach ($rows as $row) {
            $daftarTiket[] = new Ticket(
                (int)$row['id'],
                (int)$row['event_id'],
                $row['name'],
                (float)$row['price'],
                (int)$row['stock']
            );
        }

        return $daftarTiket;
    }
}

// ==========================================
// TESTING READ
// ==========================================
echo "=== TESTING READ TIKET DARI DATABASE ===\n\n";

$db = new DBconnection();
$reader = new TicketReader($db);
$semuaTiket = $reader->ambilSemua();

if (empty($semuaTiket)) {
    echo "Belum ada tiket di database.\n";
} else {
    echo "Total Tiket di Database: " . count($semuaTiket) . "\n";
    echo "--------------------------------------------------\n";
    foreach ($semuaTiket as $t) {
        echo "ID       : " . $t->getId() . "\n";
        echo "Event ID : " . $t->getEventId() . "\n";
        echo "Nama     : " . $t->getName() . "\n";
        echo "Harga    : Rp " . number_format($t->getPrice(), 0, ',', '.') . "\n";
        echo "Stok     : " . $t->getStock() . " pcs\n";
        echo "--------------------------------------------------\n";
    }
}