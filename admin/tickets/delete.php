<?php

require_once "../../bootstrap.php";

$db = new DBconnection();
$ticket = new Ticket($db);

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$result = $ticket->delete($id);

if ($result->success) {
    header("Location: index.php?message=deleted");
    exit;
}

// Kalau gagal
header(
    "Location: index.php?error=" .
    urlencode($result->message)
);
exit;