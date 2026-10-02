<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if ($name === '') {

        $error = 'Nama category wajib diisi.';

    } else {

        $respon = $category->insert([
            'name' => $name,
            'description' => $description
        ]);

        if ($respon->success) {

            header('Location: index.php');
            exit;

        } else {

            $error = $respon->message;
        }
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

            <h3 class="mb-0">Tambah Category</h3>

        </div>
    </div>

    <!-- Content -->
    <div class="app-content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Form Tambah Category</h3>
                </div>

                <div class="card-body">

                    <?php if ($error): ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <!-- Nama Category -->
                        <div class="mb-3">

                            <label for="name" class="form-label">
                                Nama Category
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                maxlength="100"
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
                                rows="5"
                            ></textarea>

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