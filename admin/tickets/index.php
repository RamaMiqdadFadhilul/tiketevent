<?php

// 1. Panggil bootstrap layout global
require_once __DIR__ . '/../../bootstrap.php';

// 2. Panggil koneksi DB, Respon, dan Model Ticket
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

// Inisialisasi DB & Ambil Data
$db = new DBconnection();
$reader = new TicketReader($db);
$semuaTiket = $reader->ambilSemua();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Daftar Tiket</h2>
        <a href="create_ticket.php" class="btn btn-primary btn-sm">Tambah Tiket</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Event ID</th>
                <th>Nama Tiket</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($semuaTiket)): ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada data tiket.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($semuaTiket as $t): ?>
                    <tr>
                        <td><?= $t->getId(); ?></td>
                        <td><?= $t->getEventId(); ?></td>
                        <td><?= htmlspecialchars($t->getName()); ?></td>
                        <td>Rp <?= number_format($t->getPrice(), 0, ',', '.'); ?></td>
                        <td><?= $t->getStock(); ?></td>
                        <td>
                            <a href="update.php?id=<?= $t->getId(); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?= $t->getId(); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tiket ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>