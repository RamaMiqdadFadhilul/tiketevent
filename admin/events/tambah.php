<?php

require_once "../../bootstrap.php";

$db = new DBconnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $image = $_POST['image'];
    $status = $_POST['status'];

    $query = "
        INSERT INTO events (
            category_id,
            name,
            description,
            location,
            event_date,
            event_time,
            image,
            status
        )
        VALUES (
            :category_id,
            :name,
            :description,
            :location,
            :event_date,
            :event_time,
            :image,
            :status
        )
    ";

    $result = $db->send_query($query, [
        'category_id' => $category_id,
        'name' => $name,
        'description' => $description,
        'location' => $location,
        'event_date' => $event_date,
        'event_time' => $event_time,
        'image' => $image,
        'status' => $status
    ]);

    if ($result->success) {
        header("Location: index.php");
        exit;
    }

    $error = $result->message;
}

$categories = $db->send_query(
    "SELECT * FROM categories ORDER BY name"
)->data;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Event</title>
</head>
<body>

<h1>Tambah Event</h1>

<?php if (isset($error)): ?>
    <p style="color: red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST">

    <label>Kategori</label>
    <br>
    <select name="category_id" required>
        <option value="">-- Pilih Kategori --</option>

        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>">
                <?= htmlspecialchars($category['name']) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <br><br>

    <label>Nama Event</label>
    <br>
    <input type="text" name="name" required>

    <br><br>

    <label>Deskripsi</label>
    <br>
    <textarea name="description"></textarea>

    <br><br>

    <label>Lokasi</label>
    <br>
    <input type="text" name="location" required>

    <br><br>

    <label>Tanggal Event</label>
    <br>
    <input type="date" name="event_date" required>

    <br><br>

    <label>Waktu Event</label>
    <br>
    <input type="time" name="event_time" required>

    <br><br>

    <label>Image</label>
    <br>
    <input type="text" name="image">

    <br><br>

    <label>Status</label>
    <br>
    <select name="status" required>
        <option value="upcoming">Upcoming</option>
        <option value="ongoing">Ongoing</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <br><br>

    <button type="submit">Simpan</button>
    <a href="index.php">Kembali</a>

</form>

</body>
</html>