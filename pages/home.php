<?php

// db.php sudah di-include di index.php, jadi tidak perlu lagi di sini
// if (!isset($conn) || !$conn) {
//     include __DIR__ . '/../includes/db.php';
// }

// Menggunakan konstanta global yang sudah didefinisikan di db.php
// $base_asset_url = BASE_ASSET_PATH;
// $app_root_url = APP_ROOT_PATH_FOR_LINKS;

$featured_sql = "SELECT id, title, image, slug FROM recipes ORDER BY created_at DESC LIMIT 3";

if ($conn->query("SHOW COLUMNS FROM `recipes` LIKE 'is_featured'")->num_rows > 0)
{
    $featured_sql = "SELECT id, title, image, slug FROM recipes WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 3";
}

$featured_result = $conn->query($featured_sql);

$category_sql = "SELECT id, name, slug FROM categories ORDER BY id ASC";
$category_result = $conn->query($category_sql);

$comments_sql = "SELECT c.name, c.comment, r.title as recipe_title, r.slug as recipe_slug FROM comments c JOIN recipes r ON c.recipe_id = r.id ORDER BY c.created_at DESC LIMIT 5";
$comments_result = $conn->query($comments_sql);

?>

<section class="hero">
    <div class="container">
        <h2>Temukan dan Masak Resep Masakan Korea Favoritmu!</h2>
        <p>Mulai perjalanan kulinermu dengan resep-resep Korea yang autentik dan mudah diikuti.</p>
        <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipes" class="cta-button">Lihat Semua Resep</a>
    </div>
</section>

<section class="container">
    <h3>Resep Unggulan</h3>
    <div class="featured-recipes">
        <div class="grid">
            <?php if ($featured_result && $featured_result->num_rows > 0): ?>
                <?php while ($row = $featured_result->fetch_assoc()): ?>
                    <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipe_detail&slug=<?= $row['slug'] ?>" class="recipe-card">
                        <img src="<?= htmlspecialchars(getRecipeImageUrl($row['image'])) ?>" alt="<?= htmlspecialchars($row['title']) ?>" />
                        <div class="recipe-card-content">
                            <h4><?= htmlspecialchars($row['title']) ?></h4>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada resep unggulan untuk ditampilkan saat ini.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="container">
    <h3>Kategori Populer</h3>
    <div class="categories">
        <div class="grid">
            <?php if ($category_result && $category_result->num_rows > 0): ?>
                <?php while ($cat = $category_result->fetch_assoc()): ?>
                    <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=category&slug=<?= htmlspecialchars($cat['slug']) ?>" class="category-card">
                        <div class="category-card-content">
                            <h3><?= htmlspecialchars($cat['name']) ?></h3>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada kategori yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="recent-comments container">
    <h3>Komentar Terbaru</h3>
    <?php if ($comments_result && $comments_result->num_rows > 0): ?>
        <ul>
            <?php while ($comment = $comments_result->fetch_assoc()): ?>
                <li>
                    <strong><?= htmlspecialchars($comment['name']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    <em>pada resep: <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipe_detail&slug=<?= htmlspecialchars($comment['recipe_slug']) ?>" class="comment-recipe-link"><?= htmlspecialchars($comment['recipe_title']) ?></a></em>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Belum ada komentar terbaru.</p>
    <?php endif; ?>
</section>