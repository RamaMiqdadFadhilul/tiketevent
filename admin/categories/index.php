<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$categories = $category->find_all();

require_once "../templates/header.php";
require_once "../templates/navbar.php";
require_once "../templates/sidebar.php";

?>

<main class="app-main">

    <!-- Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Categories</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Data Category
                    </h3>
                    <div class="card-tools">
                        <a href="create.php" class="btn btn-primary btn-sm">
                            + Tambah Category
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>

                        <div id="errorAlert" class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($_GET['error']) ?>

                            <button
                                type="button"
                                onclick="document.getElementById('errorAlert').remove()"
                                style="
                                    float: right;
                                    border: none;
                                    background: none;
                                    font-size: 24px;
                                    line-height: 1;
                                    cursor: pointer;
                                "
                            >
                                &times;
                            </button>
                        </div>

                        <script>
                            window.history.replaceState({}, document.title, 'index.php');
                        </script>

                    <?php endif; ?>
                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th style="width: 60px;">No.</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($categories)): ?>

                                <tr>
                                    <td colspan="5" class="text-center">
                                        Belum ada data category.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php $no = 1; ?>

                                <?php foreach ($categories as $item): ?>

                                    <tr>

                                        <td>
                                            <?= $no++ ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($item['name']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($item['description'] ?? '') ?>
                                        </td>


                                        <td>

                                            <a
                                                href="edit.php?id=<?= $item['id'] ?>"
                                                class="btn btn-warning btn-sm"
                                            >
                                                Edit
                                            </a>

                                            <a
                                                href="delete.php?id=<?= $item['id'] ?>"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                                            >
                                                Hapus
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
</main>

<?php

require_once "../templates/footer.php";

?>