<?php

session_start();

$order = $_SESSION['order_success'] ?? null;

if (!$order) {
    header("Location: index.php");
    exit;
}

$payment_method = match ($order['payment_method']) {
    'transfer_bank' => 'Transfer Bank',
    'e_wallet' => 'E-Wallet',
    'qris' => 'QRIS',
    default => $order['payment_method']
};

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pemesanan Berhasil</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="index.php"
        >
            Event Ticketing System
        </a>

    </div>

</nav>


<div class="container py-5">

    <!-- STEPPER -->

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row text-center">

                <div class="col-3">

                    <a
                        href="index.php"
                        class="text-decoration-none text-muted"
                    >
                        <div class="fw-semibold">
                            1. Pilih Kategori
                        </div>
                    </a>

                </div>


                <div class="col-3">

                    <a
                        href="#"
                        class="text-decoration-none text-muted"
                    >
                        <div>
                            2. Detail Pesanan
                        </div>
                    </a>

                </div>


                <div class="col-3">

                    <a
                        href="#"
                        class="text-decoration-none text-muted"
                    >
                        <div>
                            3. Metode Pembayaran
                        </div>
                    </a>

                </div>


                <div class="col-3">

                    <div class="fw-bold text-primary">
                        4. Selesai
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm">

                <div class="card-body p-5 text-center">

                    <!-- ICON -->

                    <div
                        class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-4"
                        style="width: 80px; height: 80px;"
                    >

                        <span class="fs-1">
                            ✓
                        </span>

                    </div>


                    <h2 class="fw-bold mb-2">
                        Pemesanan Berhasil!
                    </h2>

                    <p class="text-muted mb-4">
                        Terima kasih telah melakukan pemesanan tiket.
                    </p>


                    <!-- ORDER CODE -->

                    <div class="bg-light rounded p-3 mb-4">

                        <small class="text-muted">
                            Kode Pesanan
                        </small>

                        <h4 class="fw-bold text-primary mb-0">
                            <?= htmlspecialchars(
                                $order['order_code']
                            ) ?>
                        </h4>

                    </div>


                    <!-- DETAIL -->

                    <div class="text-start border rounded p-4 mb-4">

                        <h5 class="fw-bold mb-3">
                            Detail Pesanan
                        </h5>


                        <div class="d-flex justify-content-between mb-2">

                            <span class="text-muted">
                                Nama
                            </span>

                            <span class="fw-semibold">
                                <?= htmlspecialchars(
                                    $order['customer_name']
                                ) ?>
                            </span>

                        </div>


                        <div class="d-flex justify-content-between mb-2">

                            <span class="text-muted">
                                Email
                            </span>

                            <span>
                                <?= htmlspecialchars(
                                    $order['customer_email']
                                ) ?>
                            </span>

                        </div>


                        <div class="d-flex justify-content-between mb-2">

                            <span class="text-muted">
                                Metode Pembayaran
                            </span>

                            <span class="fw-semibold">
                                <?= htmlspecialchars(
                                    $payment_method
                                ) ?>
                            </span>

                        </div>


                        <hr>


                        <div class="d-flex justify-content-between">

                            <span class="fw-bold">
                                Total Pembayaran
                            </span>

                            <span class="fw-bold text-primary fs-5">

                                Rp<?= number_format(
                                    $order['total_amount'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </span>

                        </div>

                    </div>


                    <div class="alert alert-success">

                        Pembayaran berhasil dan tiket Anda
                        telah tercatat dalam sistem.

                    </div>


                    <a
                        href="index.php"
                        class="btn btn-primary btn-lg"
                    >
                        Kembali ke Beranda
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>