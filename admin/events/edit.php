<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);

// Ambil ID dari URL
$id = (int) $_GET['id'];

// Ambil data event berdasarkan ID
$data = $event->find_by_id($id);

if ($data === null) {
    die("Event tidak ditemukan.");
}


// Ambil semua category
$categories = $db->send_query(
    "SELECT * FROM categories ORDER BY name"
)->data;


// Kalau tombol Update ditekan
if (isset($_POST['update'])) {

    $dataUpdate = [
        'category_id' => $_POST['category_id'],
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'location' => $_POST['location'],
        'event_date' => $_POST['event_date'],
        'event_time' => $_POST['event_time'],
        'image' => $_POST['image'],
        'status' => $_POST['status']
    ];

    $respon = $event->update($id, $dataUpdate);

    if ($respon->success) {

        header("Location: index.php");
        exit;

    } else {

        $error = $respon->message;
    }
}


require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";

?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">
        <div class="container-fluid">

            <h3 class="mb-0">Edit Event</h3>

        </div>
    </div>


    <!-- Content -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Form Edit Event</h3>
                </div>

                <div class="card-body">

                    <?php if (isset($error)): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>

                    <?php endif; ?>


                    <form method="POST">

                        <!-- Category -->
                        <div class="mb-3">

                            <label
                                for="category_id"
                                class="form-label"
                            >
                                Kategori
                            </label>

                            <select
                                name="category_id"
                                id="category_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- Pilih Kategori --
                                </option>

                                <?php foreach ($categories as $category): ?>

                                    <option
                                        value="<?= $category['id'] ?>"
                                        <?= $data['category_id'] == $category['id'] ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Nama Event -->
                        <div class="mb-3">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Nama Event
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


                        <!-- Deskripsi -->
                        <div class="mb-3">

                            <label
                                for="description"
                                class="form-label"
                            >
                                Deskripsi
                            </label>

                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="4"
                            ><?= htmlspecialchars($data['description'] ?? '') ?></textarea>

                        </div>


                        <!-- Lokasi -->
                        <div class="mb-3">

                            <label
                                for="location"
                                class="form-label"
                            >
                                Lokasi
                            </label>

                            <input
                                type="text"
                                name="location"
                                id="location"
                                class="form-control"
                                value="<?= htmlspecialchars($data['location']) ?>"
                                required
                            >

                        </div>


                        <!-- Tanggal -->
                        <div class="mb-3">

                            <label
                                for="event_date"
                                class="form-label"
                            >
                                Tanggal Event
                            </label>

                            <input
                                type="date"
                                name="event_date"
                                id="event_date"
                                class="form-control"
                                value="<?= htmlspecialchars($data['event_date']) ?>"
                                required
                            >

                        </div>


                        <!-- Waktu -->
                        <div class="mb-3">

                            <label
                                for="event_time"
                                class="form-label"
                            >
                                Waktu Event
                            </label>

                            <input
                                type="time"
                                name="event_time"
                                id="event_time"
                                class="form-control"
                                value="<?= htmlspecialchars($data['event_time']) ?>"
                                required
                            >

                        </div>


                        <!-- Image -->
                        <div class="mb-3">

                            <label
                                for="image"
                                class="form-label"
                            >
                                Image
                            </label>

                            <input
                                type="text"
                                name="image"
                                id="image"
                                class="form-control"
                                value="<?= htmlspecialchars($data['image'] ?? '') ?>"
                            >

                        </div>


                        <!-- Status -->
                        <div class="mb-3">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                                required
                            >

                                <option
                                    value="upcoming"
                                    <?= $data['status'] === 'upcoming' ? 'selected' : '' ?>
                                >
                                    Upcoming
                                </option>

                                <option
                                    value="ongoing"
                                    <?= $data['status'] === 'ongoing' ? 'selected' : '' ?>
                                >
                                    Ongoing
                                </option>

                                <option
                                    value="completed"
                                    <?= $data['status'] === 'completed' ? 'selected' : '' ?>
                                >
                                    Completed
                                </option>

                                <option
                                    value="cancelled"
                                    <?= $data['status'] === 'cancelled' ? 'selected' : '' ?>
                                >
                                    Cancelled
                                </option>

                            </select>

                        </div>


                        <!-- Button -->
                        <button
                            type="submit"
                            name="update"
                            class="btn btn-primary"
                        >
                            Update
                        </button>

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Kembali
                        </a>

                    </form>

                </div>

            </div>

        </div>
    </div>

</main>

<?php

require_once "../templates/footer.php";

?>