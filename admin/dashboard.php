<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php';

// Hitung jumlah resep
$total_recipes = 0;
$result_recipes = $conn->query("SELECT COUNT(*) as total FROM recipes");
if ($result_recipes) {
    $row_recipes = $result_recipes->fetch_assoc();
    $total_recipes = $row_recipes['total'];
}

// Hitung jumlah komentar
$total_comments = 0;
$result_comments = $conn->query("SELECT COUNT(*) as total FROM comments");
if ($result_comments) {
    $row_comments = $result_comments->fetch_assoc();
    $total_comments = $row_comments['total'];
}

?>

<?php include 'header.php'; ?>

  <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['admin']) ?>!</h2>
  <p>Gunakan panel navigasi di samping kiri untuk mengelola resep masakan dan komentar pengguna.</p>
  <p>Anda dapat menambah, mengubah, atau menghapus resep, serta melihat dan menghapus komentar yang masuk.</p>

  <div class="dashboard-stats">
      <div class="stat-card">
          <h3>Total Resep</h3>
          <p class="stat-number"><?= $total_recipes ?></p>
          <a href="list_recipe.php" class="stat-link">Lihat Semua Resep <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="stat-card">
          <h3>Total Komentar</h3>
          <p class="stat-number"><?= $total_comments ?></p>
          <a href="manage_comments.php" class="stat-link">Lihat Semua Komentar <i class="fas fa-arrow-right"></i></a>
      </div>
  </div>

<?php include 'footer.php'; ?>