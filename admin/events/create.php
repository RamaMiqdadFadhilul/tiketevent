<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);
$category = new Category($db);

$categories = $category->find_all();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category_id = (int) ($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';

    $image = '';

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {

            $error = 'Gagal mengupload gambar.';

        } else {

            $file_name = $_FILES['image']['name'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_size = $_FILES['image']['size'];

            $extension = strtolower(
                pathinfo($file_name, PATHINFO_EXTENSION)
            );

            $allowed_extensions = [
                'jpg',
                'jpeg',
                'png',
                'webp'
            ];

            if (!in_array($extension, $allowed_extensions)) {

                $error = 'Format gambar harus JPG, JPEG, PNG, atau WEBP.';

            } elseif ($file_size > 5 * 1024 * 1024) {

                $error = 'Ukuran gambar maksimal 5 MB.';

            } else {

                $image = uniqid('event_', true) . '.' . $extension;

                $upload_path = "../../img/" . $image;

                if (!move_uploaded_file($file_tmp, $upload_path)) {

                    $error = 'Gambar gagal disimpan.';

                }
            }
        }
    }

    if ($error === '') {

        if (
            $category_id <= 0 ||
            $name === '' ||
            $location === '' ||
            $event_date === '' ||
            $event_time === ''
        ) {

            $error = 'Data event wajib diisi dengan lengkap.';

        } else {

            $result = $event->insert([
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

                header('Location: index.php');
                exit;

            }

            if ($image !== '' && file_exists("../../img/" . $image)) {
                unlink("../../img/" . $image);
            }

            $error = $result->message;
        }
    }
}

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Tambah Event</h3>
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
                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select
                                name="category_id"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    -- Pilih Category --
                                </option>
                                <?php foreach ($categories as $item): ?>
                                    <option
                                        value="<?= $item['id'] ?>"
                                        <?= (
                                            ($_POST['category_id'] ?? '') == $item['id']
                                        ) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($item['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Event</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea
                                name="description"
                                class="form-control"
                                rows="4"
                            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input
                                type="text"
                                name="location"
                                class="form-control"
                                required
                                value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
                            >
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Event</label>
                                <input
                                    type="date"
                                    name="event_date"
                                    class="form-control"
                                    required
                                    value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>"
                                >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Waktu Event</label>
                                <input
                                    type="time"
                                    name="event_time"
                                    class="form-control"
                                    required
                                    value="<?= htmlspecialchars($_POST['event_time'] ?? '') ?>"
                                >
                            </div>

                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Event</label>
                            <input
                                type="file"
                                name="image"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp"
                            >
                            <div class="form-text">
                                Format: JPG, JPEG, PNG, WEBP. Maksimal 5 MB.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select
                                name="status"
                                class="form-select"
                            >
                                <option
                                    value="upcoming"
                                    <?= ($_POST['status'] ?? 'upcoming') === 'upcoming' ? 'selected' : '' ?>
                                >
                                    Upcoming
                                </option>
                                <option
                                    value="ongoing"
                                    <?= ($_POST['status'] ?? '') === 'ongoing' ? 'selected' : '' ?>
                                >
                                    Ongoing
                                </option>
                                <option
                                    value="completed"
                                    <?= ($_POST['status'] ?? '') === 'completed' ? 'selected' : '' ?>
                                >
                                    Completed
                                </option>
                                <option
                                    value="cancelled"
                                    <?= ($_POST['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>
                                >
                                    Cancelled
                                </option>
                            </select>
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
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>