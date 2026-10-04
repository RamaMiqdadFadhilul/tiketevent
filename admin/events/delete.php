<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {

    $result = $event->delete($id);

    if (!$result->success) {

        die(
            'Gagal menghapus event: ' .
            htmlspecialchars($result->message)
        );
    }
}

header('Location: index.php');
exit;
