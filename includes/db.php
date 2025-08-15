<?php
// Setel zona waktu default PHP ke Asia/Jakarta (WIB)
date_default_timezone_set('Asia/Jakarta');

$host = "";
$user = "";
$pass = "";
$db = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

$conn->query("SET time_zone = '+07:00'");

// Definisi path aplikasi dasar
if (!defined('BASE_ASSET_PATH')) {
    $script_name = $_SERVER['SCRIPT_NAME'];
    $app_sub_directory = '';

    if (strpos($script_name, 'index.php') !== false) {
        $app_sub_directory = rtrim(dirname($script_name), '/');
    } else if (strpos($script_name, '/admin/') !== false) {
        $app_sub_directory = rtrim(dirname(dirname($script_name)), '/'); // Go up two levels for admin
    } else {
        $app_sub_directory = rtrim(dirname(dirname($script_name)), '/'); // Default for pages if not root
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

function generateSlug($conn, $text, $table, $column, $exclude_id = null) {
    // 1. Bersihkan teks dan ubah ke huruf kecil
    $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text); // Hapus karakter non-alfanumerik kecuali spasi dan strip
    $text = trim($text); // Hapus spasi di awal/akhir
    $text = str_replace(' ', '-', $text); // Ganti spasi dengan strip
    $text = preg_replace('/-+/', '-', $text); // Hapus strip ganda
    $slug = strtolower($text); // Ubah ke huruf kecil

    if (empty($slug)) {
        $slug = 'resep'; // Default slug jika teks kosong
    }

    // 2. Periksa keunikan
    $original_slug = $slug;
    $counter = 1;
    $is_unique = false;

    while (!$is_unique) {
        $query = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
        $params = [$slug];
        $types = 's';

        if ($exclude_id !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
            $types .= 'i';
        }

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            // Handle error in prepare, e.g., log it or return a fallback slug
            error_log("Failed to prepare slug uniqueness check: " . $conn->error);
            return uniqid('slug_'); // Return a unique fallback
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            $is_unique = true;
        } else {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }
    }

    return $slug;
}

function getRecipeImageUrl($imageFilename) {
    $baseAssetPath = BASE_ASSET_PATH;
    $fullImagePath = __DIR__ . '/../assets/images/' . basename($imageFilename);

    if (!empty($imageFilename) && file_exists($fullImagePath)) {
        return $baseAssetPath . '/images/' . basename($imageFilename);
    } else {
        return $baseAssetPath . '/images/default-placeholder.png';
    }
}

?>
