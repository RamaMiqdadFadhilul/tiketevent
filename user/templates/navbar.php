<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a
            class="navbar-brand fw-bold text-primary"
            href="index.php"
        >
            <i class="bi bi-ticket-perforated-fill me-1"></i>
            <?= Konfigurasi::APP_NAME ?>
        </a>
        <div class="d-flex align-items-center gap-3">
            <a
                href="index.php"
                class="nav-link <?= ($activePage ?? '') === 'home' ? 'text-primary fw-semibold' : '' ?>"
            >
                Beranda
            </a>
            <a
                href="index.php#kategori"
                class="nav-link <?= ($activePage ?? '') === 'category' ? 'text-primary fw-semibold' : '' ?>"
            >
                Kategori
            </a>
            <a
                href="index.php#event"
                class="nav-link <?= ($activePage ?? '') === 'event' ? 'text-primary fw-semibold' : '' ?>"
            >
                Event
            </a>
        </div>
    </div>
</nav>