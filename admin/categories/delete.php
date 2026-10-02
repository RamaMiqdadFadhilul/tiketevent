<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$category = new Category($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {

    $respon = $category->delete($id);
}

header('Location: index.php');
exit;