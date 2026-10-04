<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
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
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item">
                <span class="nav-link">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars(
                        $_SESSION['user_name'] ?? 'Admin'
                    ) ?>
                </span>
            </li>
            <li class="nav-item">
                <a
                    class="nav-link text-danger"
                    href="/admin/logout.php"
                    onclick="return confirm('Yakin ingin logout?')"
                >
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>