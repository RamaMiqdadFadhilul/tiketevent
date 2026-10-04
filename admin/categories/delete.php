<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {

    $result = $category->delete($id);

    if (!$result->success) {
        die('Gagal menghapus category: ' . htmlspecialchars($result->message));
    }
}

header('Location: index.php');
exit;
