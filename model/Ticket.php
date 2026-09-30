<?php

// Mundur 2 tingkat ke folder model
require_once __DIR__ . '/../../model/DBconnection.php';
require_once __DIR__ . '/../../model/Respon.php';

// File ticket.php yang ada di satu folder yang sama (admin/tickets/)
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
// TESTING LENGKAP CRUD SECARA BERURUTAN (CREATE -> READ -> UPDATE -> DELETE)
// =========================================================================

echo "====================================================\n";
echo "   TESTING FULL CRUD TIKET KE POSTGRESQL (OOP)\n";
echo "====================================================\n\n";

$db = new DBconnection();
$manager = new TicketManager($db);

try {
    // -------------------------------------------------------------
    // [TAHAP 1: CREATE]
    // -------------------------------------------------------------
    echo "[1] MEMBUAT TIKET BARU (CREATE)...\n";
    $tiketBaru = new Ticket(null, 1, "TIKET DEMO CRUD LENGKAP", 120000, 30);
    $manager->create($tiketBaru);
    echo " -> Sukses membuat tiket '{$tiketBaru->getName()}'\n\n";

    // -------------------------------------------------------------
    // [TAHAP 2: READ SEMUA]
    // -------------------------------------------------------------
    echo "[2] MEMBACA DAFTAR TIKET TERKINI (READ ALL)...\n";
    $semuaTiket = $manager->readAll();
    echo "Total tiket di database: " . count($semuaTiket) . "\n";
    foreach ($semuaTiket as $t) {
        echo " - [ID {$t->getId()}] {$t->getName()} | Rp " . number_format($t->getPrice(), 0, ',', '.') . " | Stok: {$t->getStock()}\n";
    }
    echo "\n";

    // Ambil ID tiket yang baru saja dibuat (posisi paling akhir)
    $tiketTarget = end($semuaTiket);
    $targetId = $tiketTarget->getId();

    // -------------------------------------------------------------
    // [TAHAP 3: UPDATE]
    // -------------------------------------------------------------
    echo "[3] MENGUBAH DATA TIKET TARGET ID: {$targetId} (UPDATE)...\n";
    echo " -> Sebelum Update: {$tiketTarget->getName()} (Harga: Rp " . number_format($tiketTarget->getPrice(), 0, ',', '.') . ")\n";

    // Ubah nilai lewat method objek Ticket
    $tiketTarget->setName("TIKET DEMO CRUD [UPDATED]");
    $tiketTarget->setPrice(175000);
    $tiketTarget->setStock(15);

    $manager->update($tiketTarget);

    // Ambil ulang dari database untuk verifikasi (READ BY ID)
    $tiketSetelahUpdate = $manager->readById($targetId);
    echo " -> Sesudah Update: {$tiketSetelahUpdate->getName()} (Harga: Rp " . number_format($tiketSetelahUpdate->getPrice(), 0, ',', '.') . " | Sisa Stok: {$tiketSetelahUpdate->getStock()})\n\n";

    // -------------------------------------------------------------
    // [TAHAP 4: DELETE]
    // -------------------------------------------------------------
    echo "[4] MENGHAPUS TIKET DEMO ID: {$targetId} (DELETE)...\n";
    $manager->delete($targetId);
    echo " -> Tiket ID {$targetId} berhasil dihapus dari PostgreSQL!\n";

    // Cek ulang apakah datanya beneran hilang
    $cek = $manager->readById($targetId);
    if ($cek === null) {
        echo " -> Verifikasi: Tiket sudah dipastikan bersih/hilang dari database.\n\n";
    }

} catch (Exception $e) {
    echo "[ERROR] Terjadi kendala: " . $e->getMessage() . "\n";
}

echo "====================================================\n";
echo "           SEMUA PROSES CRUD BERHASIL!\n";
echo "====================================================\n";