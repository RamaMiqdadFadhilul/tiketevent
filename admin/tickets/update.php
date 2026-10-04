<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$ticket = new Ticket($db);
$event = new Event($db);

$id = (int) ($_GET['id'] ?? 0);

$data = $ticket->find_by_id($id);

if (!$data) {
    header('Location: index.php');
    exit;
}

$events = $event->find_all();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_id = (int) ($_POST['event_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    if (
        $event_id <= 0 ||
        $name === '' ||
        $price === '' ||
        $stock === ''
    ) {

        $error = 'Semua data ticket wajib diisi.';

    } elseif ((float) $price < 0) {

        $error = 'Harga ticket tidak boleh negatif.';

    } elseif ((int) $stock < 0) {

        $error = 'Stock ticket tidak boleh negatif.';

    } else {

        $result = $ticket->update($id, [
            'event_id' => $event_id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock
        ]);

        if ($result->success) {

            header('Location: index.php');
            exit;

        }

        $error = $result->message;
    }
}

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Edit Ticket</h3>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">

            <?php if ($error): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Event</label>
                            <select
                                name="event_id"
                                class="form-select"
                                required
                            >

                                <?php foreach ($events as $item): ?>

                                    <option
                                        value="<?= $item['id'] ?>"
                                        <?= $data['event_id'] == $item['id']
                                            ? 'selected'
                                            : '' ?>
                                    >
                                        <?= htmlspecialchars($item['name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Ticket</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($data['name']) ?>"
                            >
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga</label>
                                <input
                                    type="number"
                                    name="price"
                                    class="form-control"
                                    min="0"
                                    required
                                    value="<?= htmlspecialchars($data['price']) ?>"
                                >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock</label>
                                <input
                                    type="number"
                                    name="stock"
                                    class="form-control"
                                    min="0"
                                    required
                                    value="<?= htmlspecialchars($data['stock']) ?>"
                                >
                            </div>
                        </div>
                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Kembali
                        </a>
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
