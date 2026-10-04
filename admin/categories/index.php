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
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-0">Category</h3>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <a
                        href="create.php"
                        class="btn btn-primary"
                    >
                        Tambah Category
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php if (empty($categories)): ?>

                            <tr>
                                <td
                                    colspan="4"
                                    class="text-center"
                                >
                                    Belum ada category.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($categories as $index => $item): ?>

                                <tr>
                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($item['name']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $item['description'] ?? '-'
                                        ) ?>
                                    </td>
                                    <td>
                                        <a
                                            href="update.php?id=<?= $item['id'] ?>"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Edit
                                        </a>
                                        <a
                                            href="delete.php?id=<?= $item['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus category ini?')"
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