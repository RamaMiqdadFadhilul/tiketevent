<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {

    $respon = $category->delete($id);

    if (!$respon->success) {
        header(
            'Location: index.php?error=' .
            urlencode('Category tidak bisa dihapus karena masih digunakan oleh Event.')
        );
        exit;
    }
}

header('Location: index.php');
exit;