<?php
// db.php sudah di-include di index.php
// if (!isset($conn) || !$conn) {
//     include __DIR__ . '/../includes/db.php';
// }

// Menggunakan konstanta global yang sudah didefinisikan di db.php
// $base_asset_url = BASE_ASSET_PATH;
// $app_root_url = APP_ROOT_PATH_FOR_LINKS;

// Tambahkan slug ke query
$sql = "SELECT id, title, image, slug FROM recipes ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<section class="container recipes-page">
  <h2>Semua Resep Masakan Korea</h2>

  <div class="grid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipe_detail&slug=<?= $row['slug'] ?>" class="recipe-card">
          <img src="<?= htmlspecialchars(getRecipeImageUrl($row['image'])) ?>" alt="<?= htmlspecialchars($row['title']) ?>" />
          <div class="recipe-card-content">
            <h4><?= htmlspecialchars($row['title']) ?></h4>
          </div>
        </a>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align: center; grid-column: 1 / -1;">Tidak ada resep yang ditemukan saat ini. Silakan kembali lagi nanti!</p>
    <?php endif; ?>
  </div>
</section>