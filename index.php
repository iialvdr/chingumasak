<?php
include 'includes/db.php'; // db.php sekarang mendefinisikan path konstan

// === UBAH BAGIAN INI UNTUK MENDUKUNG CLEAN URLS ===
$request_uri_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$current_path_segment = '';

if (isset($request_uri_parts[count($request_uri_parts) - 1])) {
    $current_path_segment = $request_uri_parts[count($request_uri_parts) - 1];
    if (strpos($current_path_segment, 'recipe_detail') !== false && isset($request_uri_parts[count($request_uri_parts) - 2])) {
        $current_path_segment = $request_uri_parts[count($request_uri_parts) - 2];
    } else if (strpos($current_path_segment, '?') !== false) {
        $current_path_segment = substr($current_path_segment, 0, strpos($current_path_segment, '?'));
    }
}

$page = $_GET['page'] ?? $current_path_segment;
if (empty($page) || $page === 'korea' || $page === basename(APP_ROOT_PATH_FOR_LINKS)) { // Menambahkan basename(APP_ROOT_PATH_FOR_LINKS) untuk menangani kasus root
    $page = 'home';
}
// === AKHIR BAGIAN UBAH ===

// Daftar halaman yang valid
$valid_pages = ['home', 'recipes', 'category', 'about', 'recipe_detail', 'search_results'];

// Periksa apakah halaman yang diminta valid
// Jika tidak valid, atur $page menjadi '404'
if (!in_array($page, $valid_pages)) {
    header("HTTP/1.0 404 Not Found"); // Mengirim header 404
    $page = '404'; // Set halaman menjadi 404
}

// Sertakan header utama website
include 'includes/header.php';

// Tampilkan konten halaman berdasarkan $page
if ($page === '404') {
    // Konten khusus untuk halaman 404
    ?>
    <div class="container">
      <div class="generic-page-container">
        <h2 style="color: #c00000; text-align: center; font-size: 2.5em; margin-bottom: 0.5em;">404 - Halaman Tidak Ditemukan</h2>
        <p style="text-align: center; font-size: 1.1em; margin-bottom: 1.5em;">
            Maaf, halaman yang kamu cari tidak dapat ditemukan.
            Mungkin resepnya sedang dimasak atau baru saja disajikan!
        </p>
        <p style="text-align: center; margin-bottom: 2em;">
            Coba kembali ke <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/" style="color: #c00000; text-decoration: none; font-weight: 600;">halaman utama</a>
            atau <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/recipes" style="color: #c00000; text-decoration: none; font-weight: 600;">lihat semua resep</a> kami.
        </p>
        <div style="text-align: center;">
            <img src="<?= htmlspecialchars(getRecipeImageUrl('')) ?>" alt="Halaman Tidak Ditemukan" style="max-width: 300px; height: auto; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        </div>
      </div>
    </div>
    <?php
} elseif ($page === 'search_results') {
    // Logika untuk menampilkan hasil pencarian
    $search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
    $search_results = [];
    $num_results = 0;

    if (!empty($search_query)) {
        $search_term = "%" . $search_query . "%";
        $stmt = $conn->prepare(
            "SELECT id, title, image, ingredients, slug
             FROM recipes
             WHERE title LIKE ? OR ingredients LIKE ?
             ORDER BY created_at DESC"
        );
        if ($stmt) {
            $stmt->bind_param("ss", $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $num_results = $result->num_rows;
                while ($row = $result->fetch_assoc()) {
                    $search_results[] = $row;
                }
            }
            $stmt->close();
        } else {
            echo "<p class='message error container'>Gagal mempersiapkan pencarian: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
    ?>
    <section class="container search-results-page">
      <h2>Hasil Pencarian untuk: "<?= htmlspecialchars($search_query) ?>"</h2>

      <?php if (empty($search_query)): ?>
        <p style="text-align: center;">Silakan masukkan kata kunci pada kolom pencarian di atas untuk menemukan resep.</p>
      <?php elseif ($num_results > 0): ?>
        <p style="text-align: center; margin-bottom: 1.5em;">Ditemukan <?= $num_results ?> resep yang cocok dengan pencarian Anda.</p>
        <div class="grid">
          <?php foreach($search_results as $row): ?>
            <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipe_detail&slug=<?= $row['slug'] ?>" class="recipe-card">
              <img src="<?= htmlspecialchars(getRecipeImageUrl($row['image'])) ?>" alt="<?= htmlspecialchars($row['title']) ?>" />
              <div class="recipe-card-content">
                <h4><?= htmlspecialchars($row['title']) ?></h4>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p style="text-align: center; grid-column: 1 / -1;">Tidak ada resep yang ditemukan untuk kata kunci "<?= htmlspecialchars($search_query) ?>".</p>
        <p style="text-align: center;">Coba gunakan kata kunci lain atau <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/recipes">lihat semua resep</a>.</p>
      <?php endif; ?>
    </section>
    <?php
}
else {
    // Sertakan halaman yang valid lainnya
    include "pages/{$page}.php";
}

// Sertakan footer utama website
include 'includes/footer.php';
?>