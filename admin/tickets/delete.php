<?php

// 1. Hubungkan file koneksi DB dan Respon
require_once __DIR__ . '/../../model/DBconnection.php';
require_once __DIR__ . '/../../model/Respon.php';

class TicketDeleter
{
    private DBconnection $db;

    // Constructor menerima objek koneksi database
    public function __construct(DBconnection $db)
    {
        $this->db = $db;
    }

    // Method Delete utama berdasarkan ID tiket
    public function delete(int $id)
    {
        $sql = "DELETE FROM tickets WHERE id = :id";
        $params = [
            ':id' => $id
        ];

        return $this->db->send_query($sql, $params);
    }
}

// ==========================================
// TESTING DELETE DI TERMINAL
// ==========================================
echo "=== TESTING DELETE TIKET DARI POSTGRESQL ===\n\n";

$db = new DBconnection();
$deleter = new TicketDeleter($db);

// Kita ambil tiket paling akhir/terbaru buat kelinci percobaan hapus
$sqlCek = "SELECT id, name FROM tickets ORDER BY id DESC LIMIT 1";
$responCek = $db->send_query($sqlCek);

if (empty($responCek->data)) {
    die("[INFO] Tidak ada data tiket di database untuk dihapus.\n");
}

$target = $responCek->data[0];
$targetId = (int)$target['id'];
$targetNama = $target['name'];

echo "Mencoba menghapus: '{$targetNama}' (ID: {$targetId})...\n";

// Eksekusi Hapus
$respon = $deleter->delete($targetId);
$dumpRespon = serialize($respon);

if (str_contains(strtolower($dumpRespon), 'berhasil')) {
    echo "[SUKSES] Tiket ID {$targetId} berhasil dihapus permanen dari PostgreSQL!\n";
} else {
    echo "[GAGAL] Error saat menghapus data dari database.\n";
}