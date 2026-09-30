<<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$categories = $category->find_all();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Categories</title>
</head>

<body>

<h1>Categories</h1>

<a href="create.php">+ Tambah Category</a>

<br><br>

<table border="1" cellpadding="10">

    <thead>

        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Created At</th>
            <th>Aksi</th>
        </tr>

    </thead>

    <tbody>

        <?php foreach ($categories as $item): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($item['id']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['description'] ?? '') ?>
                </td>

                <td>
                    <?= htmlspecialchars($item['created_at']) ?>
                </td>

                <td>

                    <a href="edit.php?id=<?= $item['id'] ?>">
                        Edit
                    </a>

                    |

                    <a
                        href="delete.php?id=<?= $item['id'] ?>"
                        onclick="return confirm('Yakin ingin menghapus data ini?')"
                    >
                        Hapus
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>

</body>

</html>