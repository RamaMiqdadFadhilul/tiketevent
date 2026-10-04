<?php

session_start();

require_once __DIR__ . "/../bootstrap.php";

$order = $_SESSION['order_success'] ?? null;

if (!$order) {
    header("Location: index.php");
    exit;
}

$payment_method = match ($order['payment_method']) {

    'transfer_bank' =>
        'Transfer Bank',

    'e_wallet' =>
        'E-Wallet',

    'qris' =>
        'QRIS',

    default =>
        $order['payment_method']
};

unset($_SESSION['order_success']);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Pemesanan Berhasil - <?= Konfigurasi::APP_NAME ?></title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
</head>
<body class="bg-light">

<?php

$activePage = 'home';

require_once __DIR__ . "/templates/navbar.php";

?>

<div class="container py-5">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row text-center g-0">
                <div class="col-3">
                    <div class="text-success">
                        <div
                            class="mx-auto mb-1 rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            <i class="bi bi-check"></i>
                        </div>
                        <small>Pilih Tiket</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="text-success">
                        <div
                            class="mx-auto mb-1 rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            <i class="bi bi-check"></i>
                        </div>
                        <small>Detail Pesanan</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="text-success">
                        <div
                            class="mx-auto mb-1 rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            <i class="bi bi-check"></i>
                        </div>
                        <small>Pembayaran</small>
                    </div>
                </div>
                <div class="col-3">
                    <div class="fw-bold text-primary">
                        <div
                            class="mx-auto mb-1 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;"
                        >
                            <i class="bi bi-check"></i>
                        </div>
                        <small>Selesai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5 text-center">
                    <div
                        class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-4"
                        style="width: 80px; height: 80px;"
                    >
                        <i class="bi bi-check-lg fs-1"></i>
                    </div>
                    <h2 class="fw-bold mb-2">Pemesanan Berhasil!</h2>
                    <p class="text-muted mb-4">Terima kasih telah melakukan pemesanan tiket.</p>
                    <div class="bg-light rounded p-3 mb-4">
                        <small class="text-muted">Kode Pesanan</small>
                        <h4 class="fw-bold text-primary mb-0">
                            <?= htmlspecialchars(
                                $order['order_code']
                            ) ?>
                        </h4>
                    </div>

                    <div class="text-start border rounded p-4 mb-4">
                        <h5 class="fw-bold mb-3">Detail Pesanan</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Nama</span>
                            <span class="fw-semibold">
                                <?= htmlspecialchars(
                                    $order['customer_name']
                                ) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Email</span>
                            <span>
                                <?= htmlspecialchars(
                                    $order['customer_email']
                                ) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Metode Pembayaran</span>
                            <span class="fw-semibold">
                                <?= htmlspecialchars(
                                    $payment_method
                                ) ?>
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total Pembayaran</span>
                            <span class="fw-bold text-primary fs-5">
                                Rp <?= number_format(
                                    $order['total_amount'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </span>
                        </div>
                    </div>
                    <div class="alert alert-success text-start">
                        <i class="bi bi-check-circle me-2"></i>
                        Pembayaran berhasil dan tiket Anda
                        telah tercatat dalam sistem.
                    </div>
                    <!-- BACK TO HOME -->
                    <a
                        href="index.php"
                        class="btn btn-primary btn-lg"
                    >
                        <i class="bi bi-house me-2"></i>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>