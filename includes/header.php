<?php
// Pastikan BASE_ASSET_PATH dan APP_ROOT_PATH_FOR_LINKS terdefinisi
// Ini seharusnya sudah didefinisikan oleh db.php yang di-include lebih dulu di index.php
// Jika file ini diakses langsung (bukan via index.php), definisi ini akan dijalankan.
if (!defined('BASE_ASSET_PATH')) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $app_sub_directory = '';

    if (strpos($script_name, 'index.php') !== false) {
        $app_sub_directory = rtrim(dirname($script_name), '/');
    } else {
        // Asumsi untuk includes/header.php yang diakses langsung
        // Misalnya dari pages/x.php atau admin/x.php
        $current_dir = dirname($script_name);
        // Menentukan berapa banyak '../' yang dibutuhkan untuk kembali ke root web
        // Ini adalah tebakan terbaik jika tidak ada routing atau BASE_ASSET_PATH
        // tidak didefinisikan sebelumnya (misal melalui index.php)
        $relative_path_to_root = '';
        if (strpos($current_dir, '/admin') !== false) {
            $relative_path_to_root = '../../'; // Dari admin/ -> kembali 2 level
        } else if (strpos($current_dir, '/pages') !== false || strpos($current_dir, '/includes') !== false) {
            $relative_path_to_root = '../'; // Dari pages/ atau includes/ -> kembali 1 level
        }
        $app_sub_directory = rtrim($relative_path_to_root, '/');
    }
    define('BASE_ASSET_PATH', rtrim($app_sub_directory . '/assets', '/'));
}

if (!defined('APP_ROOT_PATH_FOR_LINKS')) {
    $app_root_path_for_links = str_replace('/assets', '', BASE_ASSET_PATH);
    if ($app_root_path_for_links === '') {
        $app_root_path_for_links = '.';
    }
    define('APP_ROOT_PATH_FOR_LINKS', $app_root_path_for_links);
}

$current_page = $_GET['page'] ?? 'home';
// Untuk admin, kita perlu logika berbeda karena tidak ada parameter 'page' di URL admin
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ChinguMasak - Resep Masakan Korea Autentik</title>
  <link rel="icon" href="<?= htmlspecialchars(BASE_ASSET_PATH) ?>/images/icon.png" type="image/png">
  <link rel="stylesheet" href="<?= htmlspecialchars(BASE_ASSET_PATH) ?>/css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/b8a52d1b77.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="site-header">
  <div class="container header-container">
  <a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/" class="logo-link">
    <img src="<?= htmlspecialchars(BASE_ASSET_PATH) ?>/images/logo.png" alt="ChinguMMasak" class="logo-img">
</a>

    <button class="menu-toggle" aria-controls="primary-navigation" aria-expanded="false" aria-label="Toggle menu">
      <span class="hamburger"></span>
    </button>

    <nav id="primary-navigation" class="main-nav" role="navigation" aria-label="Main Navigation">
      <ul>
        <li class="<?= ($current_page === 'home') ? 'active' : '' ?>"><a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=home">Beranda</a></li>
        <li class="<?= ($current_page === 'recipes') ? 'active' : '' ?>"><a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=recipes">Semua Resep</a></li>
        <li class="<?= ($current_page === 'category') ? 'active' : '' ?>"><a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=category">Kategori</a></li>
        <li class="<?= ($current_page === 'about') ? 'active' : '' ?>"><a href="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/?page=about">Tentang Kami</a></li>
      </ul>
    </nav>

    <form action="<?= htmlspecialchars(APP_ROOT_PATH_FOR_LINKS) ?>/" method="GET" class="search-bar" role="search" aria-label="Search Recipes">
      <input type="hidden" name="page" value="search_results"> <input type="text" name="q" placeholder="Cari resep masakan..." aria-label="Search recipes" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" />
      <button type="submit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg></button>
    </form>
  </div>
</header>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.querySelector('.menu-toggle');
      const nav = document.getElementById('primary-navigation');
          if (menuToggle && nav) {
                  menuToggle.addEventListener('click', () => {
                            const expanded = menuToggle.getAttribute('aria-expanded') === 'true' || false;
                                      menuToggle.setAttribute('aria-expanded', !expanded);
                                                nav.classList.toggle('active');
                                                        });
                                                            }
                                                              });
  // Kode baru untuk efek navbar saat di-scroll
  window.addEventListener('scroll', () => {
      const header = document.querySelector('.site-header');
      if (window.scrollY > 50) { // Menambahkan kelas 'scrolled' setelah menggulir 50px
          header.classList.add('scrolled');
      } else {
          header.classList.remove('scrolled');
      }
  });
</script>
