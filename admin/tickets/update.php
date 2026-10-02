<?php

require_once "../../bootstrap.php";

$db = new DBconnection();
$ticket = new Ticket($db);

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data tiket
$data = $ticket->find_by_id($id);

if (!$data) {
    header("Location: index.php");
    exit;
}

// Ambil data event
$eventsResult = $db->send_query(
    "SELECT id, name
     FROM events
     ORDER BY event_date, event_time"
);

$events = $eventsResult->data;

$message = '';


// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_id = (int) ($_POST['event_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    if (
        $event_id <= 0 ||
        $name === '' ||
        $price < 0 ||
        $stock < 0
    ) {

        $message = 'Data tiket tidak valid.';

    } else {

        $result = $ticket->update($id, [
            'event_id' => $event_id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock
        ]);

        if ($result->success) {

            header("Location: index.php");
            exit;

        } else {

            $message = $result->message;

        }
    }
}

?>

<?php require_once "../templates/header.php"; ?>
<?php require_once "../templates/navbar.php"; ?>
<?php require_once "../templates/sidebar.php"; ?>


<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Edit Tiket</h1>
                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">
                            <a href="../dashboard.php">
                                Dashboard
                            </a>
                        </li>

                        <li class="breadcrumb-item">
                            <a href="index.php">
                                Tickets
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Edit
                        </li>

                    </ol>

                </div>

            </div>

        </div>
    </section>


    <!-- Content -->
    <section class="content">

        <div class="container-fluid">

            <?php if ($message): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($message) ?>
                </div>

            <?php endif; ?>


            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Form Edit Tiket
                    </h3>
                </div>


                <form method="POST">

                    <div class="card-body">

                        <!-- Event -->
                        <div class="form-group">

                            <label for="event_id">
                                Event
                            </label>

                            <select
                                name="event_id"
                                id="event_id"
                                class="form-control"
                                required
                            >

                                <option value="">
                                    -- Pilih Event --
                                </option>

                                <?php foreach ($events as $event): ?>

                                    <option
                                        value="<?= $event['id'] ?>"
                                        <?= $data['event_id'] == $event['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars($event['name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Nama Tiket -->
                        <div class="form-group">

                            <label for="name">
                                Nama Tiket
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="<?= htmlspecialchars($data['name']) ?>"
                                required
                            >

                        </div>


                        <!-- Harga -->
                        <div class="form-group">

                            <label for="price">
                                Harga
                            </label>

                            <div class="input-group">

                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        Rp
                                    </span>
                                </div>

                                <input
                                    type="number"
                                    name="price"
                                    id="price"
                                    class="form-control"
                                    min="0"
                                    step="0.01"
                                    value="<?= htmlspecialchars($data['price']) ?>"
                                    required
                                >

                            </div>

                        </div>


                        <!-- Stok -->
                        <div class="form-group">

                            <label for="stock">
                                Stok
                            </label>

                            <input
                                type="number"
                                name="stock"
                                id="stock"
                                class="form-control"
                                min="0"
                                value="<?= htmlspecialchars($data['stock']) ?>"
                                required
                            >

                        </div>

                    </div>


                    <div class="card-footer">

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>

</div>


<?php require_once "../templates/footer.php"; ?>