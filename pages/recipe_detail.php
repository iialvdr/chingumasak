<?php
session_start();

// db.php sudah di-include di index.php
// if (!isset($conn) || !$conn) {
//     include __DIR__ . '/../includes/db.php';
// }

// Menggunakan konstanta global yang sudah didefinisikan di db.php
// $base_asset_url = defined('BASE_ASSET_PATH') ? BASE_ASSET_PATH : '../assets';
// $app_root_url = defined('APP_ROOT_PATH_FOR_LINKS') ? APP_ROOT_PATH_FOR_LINKS : (str_replace('/assets', '', $base_asset_url));
// if ($app_root_url === '../assets') $app_root_url = '..';
// if ($app_root_url === './assets') $app_root_url = '.';

$recipe_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$recipe = null;
$category_name = null;
$message = '';
$recipe_id_for_comments = 0;

// Variabel untuk meta tags dinamis (tidak digunakan lagi)
// $meta_title = "Resep Tidak Ditemukan - Resep Kuliner Korea";
// $meta_description = "Maaf, resep yang Anda cari tidak dapat ditemukan di ChinguMasak.";
// $meta_image = BASE_ASSET_PATH . "/images/default-share-image.jpg"; // Gambar default jika resep tidak ditemukan
// $current_page_url = htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) . '/?page=recipe_detail&slug=' . htmlspecialchars($recipe_slug);


if (isset($_SESSION['flash_message_comment_success'])) {
    $message = "<p id='comment-status-message' class='message success' style='background-color: #d4edda !important; color: #155724 !important; border: 1px solid #c3e6cb !important; padding: 1.2rem 1.8rem !important; margin-bottom: 1.8rem !important; border-radius: 8px !important; text-align: center !important;'>Komentar Anda berhasil dikirim!</p>";
    unset($_SESSION['flash_message_comment_success']);
} elseif (isset($_SESSION['flash_message_comment_error'])) {
    $message = "<p id='comment-status-message' class='message error' style='background-color: #f8d7da !important; color: #721c24 !important; border: 1px solid #f5c6cb !important; padding: 1.2rem 1.8rem !important; margin-bottom: 1.8rem !important; border-radius: 8px !important; text-align: center !important;'>Gagal mengirim komentar: " . htmlspecialchars($_SESSION['flash_message_comment_error']) . "</p>";
    unset($_SESSION['flash_message_comment_error']);
}


if (empty($recipe_slug)) {
    $message = "<p class='message error'>Resep tidak ditemukan. Slug resep tidak valid atau tidak diberikan.</p>";
} else {
    $stmt_recipe = $conn->prepare(
        "SELECT r.*, c.name as category_name, c.slug as category_slug
         FROM recipes r
         LEFT JOIN categories c ON r.category_id = c.id
         WHERE r.slug = ?"
    );
    if ($stmt_recipe) {
        $stmt_recipe->bind_param("s", $recipe_slug);
        $stmt_recipe->execute();
        $result_recipe = $stmt_recipe->get_result();
        if ($result_recipe && $result_recipe->num_rows > 0) {
            $recipe = $result_recipe->fetch_assoc();
            $category_name = $recipe['category_name'];
            $category_slug_for_link = $recipe['category_slug'] ?? '';
            $recipe_id_for_comments = $recipe['id'];

            // Update meta tags based on recipe data (tidak digunakan lagi)
            // $meta_title = htmlspecialchars($recipe['title']) . " Resep Korea | ChinguMasak";
            // $meta_description = substr(strip_tags($recipe['ingredients']), 0, 150) . "...";
            // if (empty(trim($meta_description))) {
            //     $meta_description = substr(strip_tags($recipe['steps']), 0, 150) . "...";
            // }
            // if (empty(trim($meta_description))) {
            //      $meta_description = "Pelajari cara membuat resep " . htmlspecialchars($recipe['title']) . " yang lezat dan otentik di ChinguMasak.";
            // }
            // $meta_image = getRecipeImageUrl($recipe['image']);
            // $current_page_url = htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) . '/?page=recipe_detail&slug=' . htmlspecialchars($recipe['slug']);

        }
        $stmt_recipe->close();
    } else {
        $_SESSION['flash_message_comment_error'] = "Gagal mengambil data resep.";
        header('Location: ' . htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) . '/?page=recipes');
        exit;
    }

    if ($recipe && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
        $name = isset($_POST['nama']) ? trim($_POST['nama']) : '';
        $comment_text = isset($_POST['komentar']) ? trim($_POST['komentar']) : '';

        if (!empty($name) && !empty($comment_text)) {
            $stmt_insert_comment = $conn->prepare("INSERT INTO comments (recipe_id, name, comment) VALUES (?, ?, ?)");
            if ($stmt_insert_comment) {
                $stmt_insert_comment->bind_param("iss", $recipe_id_for_comments, $name, $comment_text);
                if ($stmt_insert_comment->execute()) {
                    $_SESSION['flash_message_comment_success'] = "Komentar Anda berhasil dikirim!";
                } else {
                    $_SESSION['flash_message_comment_error'] = "Gagal mengirim komentar: " . htmlspecialchars($stmt_insert_comment->error);
                }
                $stmt_insert_comment->close();
            } else {
                 $_SESSION['flash_message_comment_error'] = "Gagal mempersiapkan statement komentar: " . htmlspecialchars($conn->error);
            }
        } else {
            $_SESSION['flash_message_comment_error'] = "Nama dan isi komentar tidak boleh kosong.";
        }

        header('Location: ' . htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) . '/?page=recipe_detail&slug=' . $recipe['slug'] . '#comment-form');
        exit;
    }

    $comments = [];
    if ($recipe) {
        $stmt_comments = $conn->prepare("SELECT name, comment, created_at FROM comments WHERE recipe_id = ? ORDER BY created_at DESC");
        if ($stmt_comments) {
            $stmt_comments->bind_param("i", $recipe_id_for_comments);
            $stmt_comments->execute();
            $comment_result = $stmt_comments->get_result();
            if ($comment_result && $comment_result->num_rows > 0) {
                while ($row = $comment_result->fetch_assoc()) {
                    $comments[] = $row;
                }
            }
            $stmt_comments->close();
        } else {
             if (empty($message)) {
                 $message = "<p class='message error'>Gagal mengambil data komentar.</p>";
             }
        }
    }
}
?>

<div class="recipe-detail-container">
    <?php if (empty($recipe) && !empty($recipe_slug) || empty($recipe) && empty($recipe_slug)): ?>
        <?= $message ?>
        <p style="text-align: center; margin-top:1em;"><a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipes" class="cta-button">Kembali ke Daftar Resep</a></p>
    <?php elseif ($recipe): ?>
    <article class="recipe-detail">
        <h2><?= htmlspecialchars($recipe['title']) ?></h2>

        <?php if ($category_name && isset($category_slug_for_link)): ?>
            <div class="recipe-meta">
                <span><a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=category&slug=<?= htmlspecialchars($category_slug_for_link) ?>" class="category-link-badge"><?= htmlspecialchars($category_name) ?></a></span>
                </div>
        <?php endif; ?>

        <img src="<?= htmlspecialchars(getRecipeImageUrl($recipe['image'])) ?>" alt="Gambar <?= htmlspecialchars($recipe['title']) ?>" class="recipe-image-detail">

        <h3>Bahan-Bahan:</h3>
        <div class="content-text">
          <?= nl2br(htmlspecialchars($recipe['ingredients'])) ?>
        </div>

        <h3>Langkah-Langkah Memasak:</h3>
        <div class="content-text">
          <?= nl2br(htmlspecialchars($recipe['steps'])) ?>
        </div>

        <?php if (!empty($recipe['video_url'])): ?>
          <h3>Video Tutorial:</h3>
          <div class="video-container">
            <iframe src="<?= htmlspecialchars($recipe['video_url']) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        <?php endif; ?>
    </article>

    <section class="comment-section" id="comment-form">
      <h3>Berikan Komentar Anda</h3>
      <?php if (!empty($message)): ?>
          <?= $message ?>
      <?php endif; ?>

      <form method="post" action="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipe_detail&slug=<?= $recipe['slug'] ?>#comment-form">
        <div>
            <label for="nama">Nama Anda:</label>
            <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
        </div>
        <div>
            <label for="komentar">Komentar Anda:</label>
            <textarea id="komentar" name="komentar" placeholder="Tulis komentar Anda di sini..." rows="5" required><?= isset($_POST['komentar']) ? htmlspecialchars($_POST['komentar']) : '' ?></textarea>
        </div>
        <button type="submit" name="submit_comment">Kirim Komentar</button>
      </form>

      <h3 id="comment-list-title">Komentar (<?= count($comments) ?>)</h3>
      <?php if (count($comments) > 0): ?>
        <ul class="comment-list">
          <?php foreach ($comments as $c): ?>
            <li>
              <strong><?= htmlspecialchars($c['name']) ?></strong>
              <p><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
              <em><?= htmlspecialchars(date('d M Y, H:i', strtotime($c['created_at']))) ?></em>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p style="text-align:center;">Belum ada komentar terbaru untuk resep ini. Jadilah yang pertama berkomentar!</p>
      <?php endif; ?>
    </section>

    <?php else: ?>
        <p class='message error'>Resep yang Anda cari tidak dapat ditemukan.</p>
        <p style="text-align: center; margin-top:1em;"><a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipes" class="cta-button">Kembali ke Daftar Resep</a></p>
    <?php endif; ?>
</div>