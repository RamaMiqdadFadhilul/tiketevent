<?php

require_once "../bootstrap.php";

$db = new DBconnection();

$ticketModel = new Ticket($db);

$tickets = $ticketModel->find_all();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Event Ticketing System</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 40px auto;
        }

        .ticket {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        input,
        select,
        button {
            padding: 8px;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        button {
            cursor: pointer;
        }
    </style>
</head>

<body>

    <h1>Event Ticketing System</h1>

    <h2>Daftar Tiket</h2>

    <?php foreach ($tickets as $ticket): ?>

        <div class="ticket">

            <h3>
                <?= htmlspecialchars($ticket['name']) ?>
            </h3>

            <p>
                Harga:
                Rp<?= number_format($ticket['price'], 0, ',', '.') ?>
            </p>

            <p>
                Stok:
                <?= $ticket['stock'] ?>
            </p>

        </div>

    <?php endforeach; ?>


    <h2>Pesan Tiket</h2>

    <form action="../order.php" method="POST">

        <div class="form-group">

            <label for="customer_name">
                Nama
            </label>

            <br>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
                required
            >

        </div>


        <div class="form-group">

            <label for="customer_email">
                Email
            </label>

            <br>

            <input
                type="email"
                id="customer_email"
                name="customer_email"
                required
            >

        </div>


        <div class="form-group">

            <label for="customer_phone">
                Nomor HP
            </label>

            <br>

            <input
                type="text"
                id="customer_phone"
                name="customer_phone"
                required
            >

        </div>


        <div class="form-group">

            <label for="ticket_id">
                Pilih Tiket
            </label>

            <br>

            <select
                id="ticket_id"
                name="ticket_id"
                required
            >

                <option value="">
                    -- Pilih Tiket --
                </option>

                <?php foreach ($tickets as $ticket): ?>

                    <option
                        value="<?= $ticket['id'] ?>"
                    >
                        <?= htmlspecialchars($ticket['name']) ?>
                        -
                        Rp<?= number_format($ticket['price'], 0, ',', '.') ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <div class="form-group">

            <label for="quantity">
                Jumlah Tiket
            </label>

            <br>

            <input
                type="number"
                id="quantity"
                name="quantity"
                min="1"
                value="1"
                required
            >

        </div>


        <button type="submit">
            Pesan Tiket
        </button>

    </form>

</body>

</html>