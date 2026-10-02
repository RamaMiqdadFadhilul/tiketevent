<?php

require_once "../../bootstrap.php";

$db = new DBconnection();
$ticket = new Ticket($db);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {

    $respon = $ticket->delete($id);

    if (!$respon->success) {

        header(
            'Location: index.php?error=' .
            urlencode(
                'Ticket tidak bisa dihapus karena masih digunakan oleh transaksi.'
            )
        );

        exit;
    }
}

header('Location: index.php');
exit;