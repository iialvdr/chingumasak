<?php include 'includes/header.php'; ?>
    <div class="container">
      <div class="generic-page-container">
        <h2 style="color: #c00000; text-align: center; font-size: 2.5em; margin-bottom: 0.5em;">404 - Halaman Tidak Ditemukan</h2>
        <p style="text-align: center; font-size: 1.1em; margin-bottom: 1.5em;">
            Maaf, halaman yang kamu cari tidak dapat ditemukan.
            Mungkin resepnya sedang dimasak atau baru saja disajikan!
        </p>
        <p style="text-align: center; margin-bottom: 2em;">
            Coba kembali ke <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=home" style="color: #c00000; text-decoration: none; font-weight: 600;">halaman utama</a>
            atau <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipes" style="color: #c00000; text-decoration: none; font-weight: 600;">lihat semua resep</a> kami.
        </p>
      </div>
    </div>
<?php include 'includes/footer.php'; ?>