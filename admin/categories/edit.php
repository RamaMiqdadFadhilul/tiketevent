<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$data = $category->find_by_id($id);

if (!$data) {

    echo "Category tidak ditemukan.";
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if ($name === '') {

        $error = 'Nama category wajib diisi.';

    } else {

        $respon = $category->update($id, [
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
</head>

<body>

<h1>Edit Category</h1>

<?php if ($error): ?>

    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>

<?php endif; ?>

<form method="POST">

    <p>

        <label>Nama Category</label>

        <br>

        <input
            type="text"
            name="name"
            maxlength="100"
            value="<?= htmlspecialchars($data['name']) ?>"
            required
        >

    </p>

    <p>

        <label>Deskripsi</label>

        <br>

        <textarea
            name="description"
            rows="5"
            cols="40"
        ><?= htmlspecialchars($data['description'] ?? '') ?></textarea>

    </p>

    <button type="submit">
        Update
    </button>

    <a href="index.php">
        Kembali
    </a>

</form>

</body>

</html>