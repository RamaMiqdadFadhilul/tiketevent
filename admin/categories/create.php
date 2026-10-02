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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tambah Category</title>
</head>

<body>

<h1>Tambah Category</h1>

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
        ></textarea>
    </p>

    <button type="submit">
        Simpan
    </button>

    <a href="index.php">
        Kembali
    </a>

</form>

</body>

</html>