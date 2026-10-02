<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

        <!-- Sidebar Toggle -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>

        <!-- Right Navbar -->
        <ul class="navbar-nav ms-auto">

            <!-- Admin -->
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" data-bs-toggle="dropdown">
                    <span class="d-none d-md-inline">
                        <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <span class="dropdown-item-text">
                            <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>
                        </span>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item" href="../logout.php">
                            Logout
                        </a>
                    </li>

                </ul>
            </li>

        </ul>

    </div>
</nav>