<?php
// Setel zona waktu default PHP ke Asia/Jakarta (WIB)
date_default_timezone_set('Asia/Jakarta');

$host = "sql102.infinityfree.com";
$user = "if0_39253347";
$pass = "4kMP4hzMBv5";
$db = "if0_39253347_chingumasak";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Setel zona waktu untuk koneksi MySQL ke WIB (+07:00)
// Ini penting agar fungsi NOW() atau CURRENT_TIMESTAMP di MySQL sesuai dengan WIB
$conn->query("SET time_zone = '+07:00'");


/**
 * Fungsi untuk membuat slug dari string (misalnya judul resep)
 * Memastikan slug unik di tabel yang diberikan.
 *
 * @param mysqli $conn Koneksi database
 * @param string $text Teks sumber (misalnya judul resep)
 * @param string $table Nama tabel untuk memeriksa keunikan
 * @param string $column Nama kolom slug di tabel
 * @param int|null $exclude_id ID yang dikecualikan (untuk edit, agar slug resep itu sendiri tidak dianggap duplikat)
 * @return string Slug yang unik
 */
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
?>