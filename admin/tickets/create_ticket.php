<?php

require_once __DIR__ . '/../../model/DBconnection.php';
require_once __DIR__ . '/../../model/Respon.php';
require_once __DIR__ . '/ticket.php';

class TicketManager
{
    private DBconnection $db;

    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    // ==========================================
    // 1. CREATE: Tambah Tiket Baru
    // ==========================================
    public function create(Ticket $tiket)
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

    // ==========================================
    // 2. READ: Ambil Semua & Berdasarkan ID
    // ==========================================
    public function readAll(): array
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

    public function readById(int $id): ?Ticket
    {
        $sql = "SELECT id, event_id, name, price, stock FROM tickets WHERE id = :id LIMIT 1";
        $respon = $this->db->send_query($sql, [':id' => $id]);

        if (empty($respon->data)) {
            return null;
        }

        $row = $respon->data[0];
        return new Ticket(
            (int)$row['id'],
            (int)$row['event_id'],
            $row['name'],
            (float)$row['price'],
            (int)$row['stock']
        );
    }

    // ==========================================
    // 3. UPDATE: Perbarui Data Tiket
    // ==========================================
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

    // ==========================================
    // 4. DELETE: Hapus Tiket Berdasarkan ID
    // ==========================================
    public function delete(int $id)
    {
        $sql = "DELETE FROM tickets WHERE id = :id";
        return $this->db->send_query($sql, [':id' => $id]);
    }
}

// =========================================================================
// TESTING FULL CRUD (CREATE -> READ -> UPDATE -> DELETE)
// =========================================================================

echo "====================================================\n";
echo "   TESTING FULL CRUD TIKET KE POSTGRESQL (OOP)\n";
echo "====================================================\n\n";

$db = new DBconnection();
$manager = new TicketManager($db);

try {
    // 1. CREATE
    echo "[1] CREATE: Menambahkan Tiket Baru...\n";
    $tiketBaru = new Ticket(null, 1, "TIKET DEMO SIKLUS CRUD", 125000, 40);
    $manager->create($tiketBaru);
    echo " -> Sukses membuat tiket: {$tiketBaru->getName()}\n\n";

    // 2. READ ALL
    echo "[2] READ ALL: Menampilkan Semua Tiket di Database...\n";
    $semuaTiket = $manager->readAll();
    echo "Total Tiket: " . count($semuaTiket) . "\n";
    foreach ($semuaTiket as $t) {
        echo " - [ID {$t->getId()}] {$t->getName()} | Rp " . number_format($t->getPrice(), 0, ',', '.') . " | Stok: {$t->getStock()}\n";
    }
    echo "\n";

    // Ambil ID tiket yang baru saja dibuat (posisi paling akhir)
    $tiketTarget = end($semuaTiket);
    $targetId = $tiketTarget->getId();

    // 3. UPDATE
    echo "[3] UPDATE: Mengubah Tiket ID {$targetId}...\n";
    echo " -> Sebelum: {$tiketTarget->getName()} (Rp " . number_format($tiketTarget->getPrice(), 0, ',', '.') . ")\n";
    
    $tiketTarget->setName("TIKET DEMO SIKLUS CRUD [VIP UPDATED]");
    $tiketTarget->setPrice(200000);
    $tiketTarget->setStock(20);
    $manager->update($tiketTarget);

    $tiketUpdated = $manager->readById($targetId);
    echo " -> Sesudah: {$tiketUpdated->getName()} (Rp " . number_format($tiketUpdated->getPrice(), 0, ',', '.') . " | Stok: {$tiketUpdated->getStock()})\n\n";

    // 4. DELETE
    echo "[4] DELETE: Menghapus Tiket Demo ID {$targetId}...\n";
    $manager->delete($targetId);
    echo " -> Tiket ID {$targetId} berhasil dihapus dari PostgreSQL!\n";

    // Verifikasi pembersihan
    $cekHapus = $manager->readById($targetId);
    if ($cekHapus === null) {
        echo " -> Verifikasi: Data bersih dan terhapus dari database.\n\n";
    }

} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

echo "====================================================\n";
echo "           SEMUA PROSES CRUD BERHASIL!\n";
echo "====================================================\n";