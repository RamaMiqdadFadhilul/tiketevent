<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

$event = new Event($db);

// Ambil ID dari URL
$id = (int) $_GET['id'];

// Ambil data event berdasarkan ID
$data = $event->find_by_id($id);

if ($data === null) {
    die("Event tidak ditemukan.");
}


// Kalau tombol Update ditekan
if (isset($_POST['update'])) {

    $dataUpdate = [
        'category_id' => $_POST['category_id'],
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'location' => $_POST['location'],
        'event_date' => $_POST['event_date'],
        'event_time' => $_POST['event_time'],
        'image' => $_POST['image'],
        'status' => $_POST['status']
    ];

    $respon = $event->update($id, $dataUpdate);

    if ($respon->success) {

        header("Location: index.php");
        exit;

    } else {

        echo "Gagal mengubah event: ";
        echo $respon->message;
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Event</title>
</head>

<body>

<h1>Edit Event</h1>

<form method="POST">

    <label>Category ID</label>
    <br>

    <input
        type="number"
        name="category_id"
        value="<?= $data['category_id']; ?>"
        required
    >

    <br><br>


    <label>Nama Event</label>
    <br>

    <input
        type="text"
        name="name"
        value="<?= htmlspecialchars($data['name']); ?>"
        required
    >

    <br><br>


    <label>Deskripsi</label>
    <br>

    <textarea name="description"><?= htmlspecialchars($data['description'] ?? ''); ?></textarea>

    <br><br>


    <label>Lokasi</label>
    <br>

    <input
        type="text"
        name="location"
        value="<?= htmlspecialchars($data['location']); ?>"
        required
    >

    <br><br>


    <label>Tanggal Event</label>
    <br>

    <input
        type="date"
        name="event_date"
        value="<?= $data['event_date']; ?>"
        required
    >

    <br><br>


    <label>Waktu Event</label>
    <br>

    <input
        type="time"
        name="event_time"
        value="<?= $data['event_time']; ?>"
        required
    >

    <br><br>


    <label>Image</label>
    <br>

    <input
        type="text"
        name="image"
        value="<?= htmlspecialchars($data['image'] ?? ''); ?>"
    >

    <br><br>


    <label>Status</label>
    <br>

    <select name="status" required>

        <option value="upcoming"
            <?= $data['status'] === 'upcoming' ? 'selected' : ''; ?>>
            Upcoming
        </option>

        <option value="ongoing"
            <?= $data['status'] === 'ongoing' ? 'selected' : ''; ?>>
            Ongoing
        </option>

        <option value="completed"
            <?= $data['status'] === 'completed' ? 'selected' : ''; ?>>
            Completed
        </option>

        <option value="cancelled"
            <?= $data['status'] === 'cancelled' ? 'selected' : ''; ?>>
            Cancelled
        </option>

    </select>

    <br><br>


    <button type="submit" name="update">
        Update
    </button>

</form>

<br>

<a href="index.php">
    Kembali
</a>

</body>

</html>