<?php include 'includes/header.php'; ?>
    <div class="container">
      <div class="generic-page-container">
        <h2 style="color: #c00000; text-align: center; font-size: 2.5em; margin-bottom: 0.5em;">400 - Permintaan Buruk</h2>
        <p style="text-align: center; font-size: 1.1em; margin-bottom: 1.5em;">
            Maaf, permintaan yang kamu kirimkan tidak dapat diproses oleh server karena formatnya tidak benar atau tidak valid.
            Silakan periksa kembali permintaan kamu.
        </p>
        <p style="text-align: center; margin-bottom: 2em;">
            Coba kembali ke <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=home" style="color: #c00000; text-decoration: none; font-weight: 600;">halaman utama</a>
            atau <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipes" style="color: #c00000; text-decoration: none; font-weight: 600;">lihat semua resep</a> kami.
        </p>
      </div>
    </div>
<?php include 'includes/footer.php'; ?>