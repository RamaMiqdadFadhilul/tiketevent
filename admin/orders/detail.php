<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$order_id = (int) ($_GET['id'] ?? 0);

if ($order_id <= 0) {
    header("Location: index.php");
    exit;
}


// ===============================
// AMBIL DATA ORDER
// ===============================

$order_result = $db->send_query(
    "SELECT *
     FROM orders
     WHERE id = :id",
    [
        'id' => $order_id
    ]
);

if (!$order_result->success || empty($order_result->data)) {
    header("Location: index.php");
    exit;
}

$order = $order_result->data[0];


// ===============================
// AMBIL DETAIL TIKET
// ===============================

$item_result = $db->send_query(
    "SELECT
        order_items.*,
        tickets.name AS ticket_name,
        events.name AS event_name
     FROM order_items
     JOIN tickets
        ON tickets.id = order_items.ticket_id
     JOIN events
        ON events.id = tickets.event_id
     WHERE order_items.order_id = :order_id
     ORDER BY order_items.id",
    [
        'order_id' => $order_id
    ]
);

$order_items = $item_result->data;


// ===============================
// AMBIL DATA PAYMENT
// ===============================

$payment_result = $db->send_query(
    "SELECT *
     FROM payments
     WHERE order_id = :order_id",
    [
        'order_id' => $order_id
    ]
);

$payment = $payment_result->data[0] ?? null;


// ===============================
// TEMPLATE
// ===============================

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";

?>

<main class="app-main">

    <!-- HEADER -->

    <div class="app-content-header">

        <div class="container-fluid">

            <div class="row">

                <div class="col-sm-6">

                    <h3 class="mb-0">
                        Detail Order
                    </h3>

                </div>

                <div class="col-sm-6 text-end">

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>

                </div>

            </div>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="app-content">

        <div class="container-fluid">

            <div class="row g-4">

                <!-- ========================= -->
                <!-- INFORMASI ORDER -->
                <!-- ========================= -->

                <div class="col-lg-6">

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">
                                Informasi Pesanan
                            </h3>

                        </div>

                        <div class="card-body">

                            <table class="table table-borderless">

                                <tr>

                                    <th width="180">
                                        Kode Order
                                    </th>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $order['order_code']
                                            ) ?>
                                        </strong>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Tanggal
                                    </th>

                                    <td>
                                        <?= date(
                                            'd-m-Y H:i',
                                            strtotime(
                                                $order['created_at']
                                            )
                                        ) ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Status
                                    </th>

                                    <td>

                                        <?php

                                        $badge = match ($order['status']) {

                                            'paid' =>
                                                'bg-success',

                                            'pending' =>
                                                'bg-warning text-dark',

                                            'cancelled' =>
                                                'bg-danger',

                                            default =>
                                                'bg-secondary'

                                        };

?>

                                        <span
                                            class="badge <?= $badge ?>"
                                        >

                                            <?= htmlspecialchars(
                                                ucfirst(
                                                    $order['status']
                                                )
                                            ) ?>

                                        </span>

                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Total
                                    </th>

                                    <td>

                                        <strong class="text-primary">

                                            Rp<?= number_format(
                                                $order['total_amount'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                        </strong>

                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

                </div>


                <!-- ========================= -->
                <!-- DATA CUSTOMER -->
                <!-- ========================= -->

                <div class="col-lg-6">

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">
                                Data Customer
                            </h3>

                        </div>

                        <div class="card-body">

                            <table class="table table-borderless">

                                <tr>

                                    <th width="180">
                                        Nama
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $order['customer_name']
                                        ) ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        Email
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $order['customer_email']
                                        ) ?>
                                    </td>

                                </tr>


                                <tr>

                                    <th>
                                        No. HP
                                    </th>

                                    <td>
                                        <?= htmlspecialchars(
                                            $order['customer_phone']
                                        ) ?>
                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

                </div>


                <!-- ========================= -->
                <!-- DETAIL TIKET -->
                <!-- ========================= -->

                <div class="col-12">

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">
                                Tiket yang Dibeli
                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table
                                    class="table table-bordered table-hover"
                                >

                                    <thead>

                                        <tr>

                                            <th width="50">
                                                #
                                            </th>

                                            <th>
                                                Event
                                            </th>

                                            <th>
                                                Tiket
                                            </th>

                                            <th>
                                                Harga
                                            </th>

                                            <th>
                                                Jumlah
                                            </th>

                                            <th>
                                                Subtotal
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <?php if (empty($order_items)): ?>

                                            <tr>

                                                <td
                                                    colspan="6"
                                                    class="text-center text-muted"
                                                >
                                                    Tidak ada detail tiket.
                                                </td>

                                            </tr>

                                        <?php else: ?>

                                            <?php foreach (
                                                $order_items as $index => $item
                                            ): ?>

                                                <tr>

                                                    <td>
                                                        <?= $index + 1 ?>
                                                    </td>


                                                    <td>

                                                        <?= htmlspecialchars(
                                                            $item['event_name']
                                                        ) ?>

                                                    </td>


                                                    <td>

                                                        <?= htmlspecialchars(
                                                            $item['ticket_name']
                                                        ) ?>

                                                    </td>


                                                    <td>

                                                        Rp<?= number_format(
                                                            $item['price'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) ?>

                                                    </td>


                                                    <td>

                                                        <?= $item['quantity'] ?>

                                                    </td>


                                                    <td>

                                                        Rp<?= number_format(
                                                            $item['subtotal'],
                                                            0,
                                                            ',',
                                                            '.'
                                                        ) ?>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        <?php endif; ?>

                                    </tbody>


                                    <tfoot>

                                        <tr>

                                            <th
                                                colspan="5"
                                                class="text-end"
                                            >
                                                Total
                                            </th>

                                            <th>

                                                Rp<?= number_format(
                                                    $order['total_amount'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>

                                            </th>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ========================= -->
                <!-- PEMBAYARAN -->
                <!-- ========================= -->

                <div class="col-12">

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">
                                Informasi Pembayaran
                            </h3>

                        </div>

                        <div class="card-body">

                            <?php if ($payment): ?>

                                <table class="table table-borderless">

                                    <tr>

                                        <th width="200">
                                            Metode Pembayaran
                                        </th>

                                        <td>

                                            <?php

                                            $payment_method =
                                                match (
                                                    $payment['payment_method']
                                                ) {

                                                    'transfer_bank' =>
                                                        'Transfer Bank',

                                                    'e_wallet' =>
                                                        'E-Wallet',

                                                    'qris' =>
                                                        'QRIS',

                                                    default =>
                                                        $payment[
                                                            'payment_method'
                                                        ]

                                                };

                                ?>

                                            <?= htmlspecialchars(
                                                $payment_method
                                            ) ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Jumlah
                                        </th>

                                        <td>

                                            Rp<?= number_format(
                                                $payment['amount'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Status Pembayaran
                                        </th>

                                        <td>

                                            <?php

                                            $payment_badge =
                                                match (
                                                    $payment['status']
                                                ) {

                                                    'paid' =>
                                                        'bg-success',

                                                    'pending' =>
                                                        'bg-warning text-dark',

                                                    'failed' =>
                                                        'bg-danger',

                                                    default =>
                                                        'bg-secondary'

                                                };

                                ?>

                                            <span
                                                class="badge <?= $payment_badge ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    ucfirst(
                                                        $payment['status']
                                                    )
                                                ) ?>

                                            </span>

                                        </td>

                                    </tr>


                                    <tr>

                                        <th>
                                            Waktu Pembayaran
                                        </th>

                                        <td>

                                            <?= $payment['paid_at']
                                                ? date(
                                                    'd-m-Y H:i',
                                                    strtotime(
                                                        $payment['paid_at']
                                                    )
                                                )
                                                : '-'
                                ?>

                                        </td>

                                    </tr>

                                </table>

                            <?php else: ?>

                                <div class="alert alert-warning mb-0">

                                    Belum ada data pembayaran.

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<?php require_once "../templates/footer.php"; ?>