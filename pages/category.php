<?php
// db.php sudah di-include di index.php
// if (!isset($conn) || !$conn) {
//     include __DIR__ . '/../includes/db.php';
// }

// Menggunakan konstanta global yang sudah didefinisikan di db.php
// $base_asset_url = BASE_ASSET_PATH;
// $app_root_url = APP_ROOT_PATH_FOR_LINKS;

// Mengubah dari 'id' menjadi 'slug' untuk kategori
$category_slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$category_name = "Kategori Resep";
$current_category_id = 0; // Untuk menyimpan ID kategori yang ditemukan

// Variabel untuk meta tags dinamis (tidak digunakan lagi)
// $meta_title = "Kategori Resep Korea - Temukan Resep Favoritmu!";
// $meta_description = "Jelajahi berbagai kategori resep masakan Korea otentik dan mudah diikuti. Temukan hidangan favoritmu!";
// $meta_image = BASE_ASSET_PATH . "/images/default-share-image.jpg"; // Gambar default

if (!empty($category_slug)) {
    // Mengambil nama dan ID kategori berdasarkan slug
    $stmt_cat_name = $conn->prepare("SELECT id, name FROM categories WHERE slug = ?");
    if ($stmt_cat_name) {
        $stmt_cat_name->bind_param("s", $category_slug); // 's' untuk string (slug)
        $stmt_cat_name->execute();
        $result_cat_name = $stmt_cat_name->get_result();
        if ($cat_data = $result_cat_name->fetch_assoc()) {
            $category_name = "Resep Kategori: " . htmlspecialchars($cat_data['name']);
            $current_category_id = $cat_data['id']; // Simpan ID untuk query resep

            // Update meta tags jika kategori ditemukan (tidak digunakan lagi)
            // $meta_title = htmlspecialchars($cat_data['name']) . " Resep Korea | Resep Kuliner Korea";
            // $meta_description = "Temukan semua resep masakan Korea dalam kategori " . htmlspecialchars($cat_data['name']) . ". Mudah dibuat dan autentik!";
            // Kamu bisa menambahkan logika untuk mengambil gambar kategori jika ada
            // $meta_image = getCategoryImageUrl($cat_data['image']);
        } else {
            $category_name = "Kategori Tidak Ditemukan";
            // $meta_title = "Kategori Tidak Ditemukan - Resep Kuliner Korea"; // tidak digunakan lagi
            // $meta_description = "Kategori resep yang Anda cari tidak ditemukan."; // tidak digunakan lagi
        }
        $stmt_cat_name->close();
    }

    // Hanya ambil resep jika kategori ditemukan
    if ($current_category_id > 0) {
        // Mengambil resep berdasarkan category_id
        $stmt_recipes = $conn->prepare("SELECT id, title, image, slug FROM recipes WHERE category_id = ? ORDER BY created_at DESC");
        if ($stmt_recipes) {
            $stmt_recipes->bind_param("i", $current_category_id); // 'i' untuk integer (id kategori)
            $stmt_recipes->execute();
            $result_recipes = $stmt_recipes->get_result();
        } else {
            $result_recipes = null;
            echo "<p class='message error'>Gagal mengambil resep untuk kategori ini.</p>";
        }
    } else {
        $result_recipes = null; // Tidak ada resep jika kategori tidak ditemukan
    }

} else {
    // Mengambil semua kategori, tambahkan slug ke query
    // PERUBAHAN DI SINI: Urutkan berdasarkan ID ASC (terkecil ke terbesar)
    $result_categories = $conn->query("SELECT id, name, slug FROM categories ORDER BY id ASC");
}
?>

<section class="container categories-page">
  <h2><?= $category_name ?></h2>

  <?php if (!empty($category_slug) && $current_category_id > 0): // Cek jika kategori ditemukan berdasarkan slug ?>
    <div class="grid">
      <?php if (isset($result_recipes) && $result_recipes && $result_recipes->num_rows > 0): ?>
        <?php while($row = $result_recipes->fetch_assoc()): ?>
          <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipe_detail&slug=<?= $row['slug'] ?>" class="recipe-card">
            <img src="<?= htmlspecialchars(getRecipeImageUrl($row['image'])) ?>" alt="<?= htmlspecialchars($row['title']) ?>" />
            <div class="recipe-card-content">
              <h4><?= htmlspecialchars($row['title']) ?></h4>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align: center; grid-column: 1 / -1;">Tidak ada resep yang ditemukan dalam kategori ini.</p>
      <?php endif; ?>
      <?php if (isset($stmt_recipes) && $stmt_recipes) $stmt_recipes->close(); ?>
    </div>
    <p style="text-align: center; margin-top: 2em;">
        <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=category" class="cta-button" style="font-size: 0.9em;">Lihat Semua Kategori</a>
    </p>

  <?php else: // Menampilkan daftar kategori jika tidak ada slug atau slug tidak valid ?>
    <p style="text-align: center; margin-bottom: 2em;">Pilih kategori untuk menemukan resep sesuai selera Anda.</p>
    <div class="grid">
      <?php if (isset($result_categories) && $result_categories && $result_categories->num_rows > 0): ?>
        <?php while($cat = $result_categories->fetch_assoc()): ?>
          <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=category&slug=<?= $cat['slug'] ?>" class="category-card"> <div class="category-card-content">
                <h3><?= htmlspecialchars($cat['name']) ?></h3>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align: center; grid-column: 1 / -1;">Tidak ada kategori yang tersedia saat ini.</p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

</section>