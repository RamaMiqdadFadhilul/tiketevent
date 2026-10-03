<?php

session_start();

require_once "../bootstrap.php";


/*
|--------------------------------------------------------------------------
| Jika sudah login sebagai admin
|--------------------------------------------------------------------------
*/

if (
    isset($_SESSION['user_id']) &&
    isset($_SESSION['user_role']) &&
    $_SESSION['user_role'] === 'admin'
) {
    header("Location: index.php");
    exit;
}


$error = "";


/*
|--------------------------------------------------------------------------
| Proses Login
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $error = "Email dan password wajib diisi.";

    } else {

        $db = new DBconnection();

        $userModel = new User($db);

        $result = $userModel->login(
            $email,
            $password
        );

        if ($result->success) {

            $user = $result->data[0];

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            header("Location: index.php");
            exit;

        } else {

            $error = $result->message;

        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login Admin - Ticket Event</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">

    <div
        class="card shadow border-0"
        style="width: 400px;"
    >

        <div class="card-body p-4">

            <div class="text-center mb-4">

                <h3 class="fw-bold">
                    Login Admin
                </h3>

                <p class="text-muted mb-0">
                    Ticket Event
                </p>

            </div>


            <?php if (!empty($error)): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form method="POST">

                <div class="mb-3">

                    <label
                        for="email"
                        class="form-label"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    >

                </div>


                <div class="mb-4">

                    <label
                        for="password"
                        class="form-label"
                    >
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    Login
                </button>

            </form>

        </div>

    </div>

</div>

</body>

</html>