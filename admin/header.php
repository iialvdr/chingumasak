<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // Mencegah caching yang kuat
header("Cache-Control: post-check=0, pre-check=0", false); // Mencegah caching tambahan
header("Pragma: no-cache"); // Header untuk kompatibilitas lebih lama
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Tanggal di masa lalu untuk kadaluwarsa
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin ChinguMasak</title>
  <link rel="stylesheet" href="../assets/css/admin-style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/b8a52d1b77.js" crossorigin="anonymous"></script>

  <script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
  </script>

</head>
<body>
  <div class="admin-wrapper">
    <aside class="admin-sidebar">
      <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-logo">
          <img src="../assets/images/logo.png" alt="ChinguMasak Admin" class="logo-img">
        </a>
      </div>
      <nav class="admin-nav">
        <ul>
          <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="list_recipe.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'list_recipe.php' || basename($_SERVER['PHP_SELF']) == 'add_recipe.php' || basename($_SERVER['PHP_SELF']) == 'edit_recipe.php') ? 'active' : '' ?>"><i class="fas fa-book"></i> Kelola Resep</a></li>
          <li><a href="manage_comments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_comments.php' ? 'active' : '' ?>"><i class="fas fa-comments"></i> Kelola Komentar</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a class="admin-logout" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </aside>

    <div class="admin-content-area">
        <header class="admin-topbar">
            <button class="menu-toggle-mobile" aria-label="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="admin-title-mobile">Panel Admin ChinguMasak</h1>
            <div class="user-info-topbar">
                <span>Halo, <?= htmlspecialchars($_SESSION['admin']) ?></span>
            </div>
        </header>
        <main class="admin-main">