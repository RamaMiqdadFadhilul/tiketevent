<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $image = $_POST['image'];
    $status = $_POST['status'];

    $query = "
        INSERT INTO events (
            category_id,
            name,
            description,
            location,
            event_date,
            event_time,
            image,
            status
        )
        VALUES (
            :category_id,
            :name,
            :description,
            :location,
            :event_date,
            :event_time,
            :image,
            :status
        )
    ";

    $result = $db->send_query($query, [
        'category_id' => $category_id,
        'name' => $name,
        'description' => $description,
        'location' => $location,
        'event_date' => $event_date,
        'event_time' => $event_time,
        'image' => $image,
        'status' => $status
    ]);

    if ($result->success) {
        header("Location: index.php");
        exit;
    }

    $error = $result->message;
}

$categories = $db->send_query(
    "SELECT * FROM categories ORDER BY name"
)->data;

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";

?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">
        <div class="container-fluid">

            <h3 class="mb-0">Tambah Event</h3>

        </div>
    </div>


    <!-- Content -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Form Tambah Event</h3>
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

                            <label for="category_id" class="form-label">
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

                                    <option value="<?= $category['id'] ?>">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Nama Event -->
                        <div class="mb-3">

                            <label for="name" class="form-label">
                                Nama Event
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                required
                            >

                        </div>


                        <!-- Deskripsi -->
                        <div class="mb-3">

                            <label for="description" class="form-label">
                                Deskripsi
                            </label>

                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="4"
                            ></textarea>

                        </div>


                        <!-- Lokasi -->
                        <div class="mb-3">

                            <label for="location" class="form-label">
                                Lokasi
                            </label>

                            <input
                                type="text"
                                name="location"
                                id="location"
                                class="form-control"
                                required
                            >

                        </div>


                        <!-- Tanggal -->
                        <div class="mb-3">

                            <label for="event_date" class="form-label">
                                Tanggal Event
                            </label>

                            <input
                                type="date"
                                name="event_date"
                                id="event_date"
                                class="form-control"
                                required
                            >

                        </div>


                        <!-- Waktu -->
                        <div class="mb-3">

                            <label for="event_time" class="form-label">
                                Waktu Event
                            </label>

                            <input
                                type="time"
                                name="event_time"
                                id="event_time"
                                class="form-control"
                                required
                            >

                        </div>


                        <!-- Image -->
                        <div class="mb-3">

                            <label for="image" class="form-label">
                                Image
                            </label>

                            <input
                                type="text"
                                name="image"
                                id="image"
                                class="form-control"
                            >

                        </div>


                        <!-- Status -->
                        <div class="mb-3">

                            <label for="status" class="form-label">
                                Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                                required
                            >

                                <option value="upcoming">
                                    Upcoming
                                </option>

                                <option value="ongoing">
                                    Ongoing
                                </option>

                                <option value="completed">
                                    Completed
                                </option>

                                <option value="cancelled">
                                    Cancelled
                                </option>

                            </select>

                        </div>


                        <!-- Button -->
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Simpan
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