<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);


// Ambil ID dari URL
$id = (int) $_GET['id'];


// Hapus data
$respon = $event->delete($id);


// Cek hasil
if ($respon->success) {

    header("Location: index.php");
    exit;

} else {

    echo "Gagal menghapus event: ";
    echo $respon->message;
}