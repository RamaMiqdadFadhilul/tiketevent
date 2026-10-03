<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$result = $db->send_query(
    "SELECT *
     FROM orders
     ORDER BY created_at DESC"
);

$orders = $result->data;

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
                        Orders
                    </h3>

                </div>

            </div>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="app-content">

        <div class="container-fluid">

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Daftar Pesanan
                    </h3>

                </div>


                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover">

                            <thead>

                                <tr>

                                    <th width="50">
                                        #
                                    </th>

                                    <th>
                                        Kode Order
                                    </th>

                                    <th>
                                        Customer
                                    </th>

                                    <th>
                                        Email
                                    </th>

                                    <th>
                                        No. HP
                                    </th>

                                    <th>
                                        Total
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Tanggal
                                    </th>

                                    <th width="100">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if (empty($orders)): ?>

                                    <tr>

                                        <td
                                            colspan="9"
                                            class="text-center text-muted"
                                        >
                                            Belum ada pesanan.
                                        </td>

                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($orders as $index => $order): ?>

                                        <tr>

                                            <td>
                                                <?= $index + 1 ?>
                                            </td>


                                            <td>

                                                <span class="fw-semibold">

                                                    <?= htmlspecialchars(
                                                        $order['order_code']
                                                    ) ?>

                                                </span>

                                            </td>


                                            <td>
                                                <?= htmlspecialchars(
                                                    $order['customer_name']
                                                ) ?>
                                            </td>


                                            <td>
                                                <?= htmlspecialchars(
                                                    $order['customer_email']
                                                ) ?>
                                            </td>


                                            <td>
                                                <?= htmlspecialchars(
                                                    $order['customer_phone']
                                                ) ?>
                                            </td>


                                            <td>

                                                Rp<?= number_format(
                                                    $order['total_amount'],
                                                    0,
                                                    ',',
                                                    '.'
                                                ) ?>

                                            </td>


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


                                            <td>

                                                <?= date(
                                                    'd-m-Y H:i',
                                                    strtotime(
                                                        $order['created_at']
                                                    )
                                                ) ?>

                                            </td>


                                            <td>

                                                <a
                                                    href="detail.php?id=<?= $order['id'] ?>"
                                                    class="btn btn-sm btn-primary"
                                                >

                                                    <i class="bi bi-eye"></i>

                                                    Detail

                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<?php require_once "../templates/footer.php"; ?>