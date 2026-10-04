<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {

        $error = 'Nama category wajib diisi.';

    } else {

        $result = $category->insert([
            'name' => $name,
            'description' => $description
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
            <h3 class="mb-0">Tambah Category</h3>
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
                            <label class="form-label">Nama Category</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                required
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea
                                name="description"
                                class="form-control"
                                rows="4"
                            ></textarea>
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