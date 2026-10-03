<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="app-header navbar navbar-expand bg-body">

    <div class="container-fluid">

        <!-- Sidebar Toggle -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a
                    class="nav-link"
                    data-lte-toggle="sidebar"
                    href="#"
                    role="button"
                >
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>


        <!-- Right Navbar -->
        <ul class="navbar-nav ms-auto">

            <li class="nav-item">

                <a
                    class="nav-link text-danger"
                    href="/admin/logout.php"
                >
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="ms-1">Logout</span>
                </a>

            </li>

        </ul>

    </div>

</nav>