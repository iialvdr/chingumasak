<?php
session_start(); // Memulai sesi PHP

// Mengarahkan pengguna ke halaman login jika belum login sebagai admin
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php'; // Memasukkan file koneksi database dan fungsi helper

// Memastikan permintaan datang dari metode POST dan memiliki aksi 'delete_recipe'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_recipe') {
    // Memvalidasi ID resep yang akan dihapus
    if (isset($_POST['recipe_id_to_delete']) && filter_var($_POST['recipe_id_to_delete'], FILTER_VALIDATE_INT)) {
        $id_to_delete = (int)$_POST['recipe_id_to_delete'];

        // Mengambil nama file gambar resep dari database
        $stmt_select_image = $conn->prepare("SELECT image FROM recipes WHERE id = ?");
        if ($stmt_select_image) {
            $stmt_select_image->bind_param("i", $id_to_delete);
            $stmt_select_image->execute();
            $result_image = $stmt_select_image->get_result();
            $recipe_data_to_delete = $result_image->fetch_assoc();
            $stmt_select_image->close();

            if ($recipe_data_to_delete) {
                // Menghapus file gambar jika ada
                if (!empty($recipe_data_to_delete['image'])) {
                    $file_path_to_delete = '../assets/images/' . $recipe_data_to_delete['image'];
                    if (file_exists($file_path_to_delete)) {
                        if (!unlink($file_path_to_delete)) {
                            $_SESSION['flash_message_warning'] = "Resep berhasil dihapus dari database, tetapi file gambar gagal dihapus dari server.";
                        }
                    }
                }

                // Menghapus komentar terkait resep dari database
                $stmt_delete_comments = $conn->prepare("DELETE FROM comments WHERE recipe_id = ?");
                if ($stmt_delete_comments) {
                    $stmt_delete_comments->bind_param("i", $id_to_delete);
                    $stmt_delete_comments->execute();
                    $stmt_delete_comments->close();
                }

                // Menghapus resep dari database
                $stmt_delete_recipe = $conn->prepare("DELETE FROM recipes WHERE id = ?");
                if ($stmt_delete_recipe) {
                    $stmt_delete_recipe->bind_param("i", $id_to_delete);
                    if ($stmt_delete_recipe->execute()) {
                        if ($stmt_delete_recipe->affected_rows > 0) {
                            if (!isset($_SESSION['flash_message_warning'])) { // Hanya set sukses jika tidak ada warning sebelumnya
                                $_SESSION['flash_message_success'] = "Resep berhasil dihapus.";
                            }
                        } else {
                             $_SESSION['flash_message_error'] = "Resep tidak ditemukan atau sudah dihapus sebelumnya.";
                        }
                    } else {
                        $_SESSION['flash_message_error'] = "Gagal menghapus resep dari database: " . $stmt_delete_recipe->error;
                    }
                    $stmt_delete_recipe->close();
                } else {
                    $_SESSION['flash_message_error'] = "Terjadi kesalahan saat persiapan menghapus resep: " . $conn->error;
                }
            } else {
                $_SESSION['flash_message_error'] = "Resep dengan ID yang ditentukan tidak ditemukan.";
            }
        } else {
            $_SESSION['flash_message_error'] = "Terjadi kesalahan saat persiapan mengambil data gambar resep.";
        }
    } else {
        $_SESSION['flash_message_error'] = "ID resep untuk dihapus tidak valid.";
    }

    // Membangun kembali parameter query string untuk pengalihan agar filter tetap ada
    $query_string_params = [];
    if (isset($_GET['search'])) $query_string_params['search'] = $_GET['search'];
    if (isset($_GET['category_id'])) $query_string_params['category_id'] = $_GET['category_id'];
    if (isset($_GET['is_featured'])) $query_string_params['is_featured'] = $_GET['is_featured']; // Asumsi is_featured juga relevan

    $redirect_url = 'list_recipe.php';
    if (!empty($query_string_params)) {
        $redirect_url .= '?' . http_build_query($query_string_params);
    }

    // Mengarahkan kembali ke halaman daftar resep
    header('Location: ' . $redirect_url);
    exit;
} else {
    // Jika diakses langsung tanpa POST, arahkan kembali ke daftar resep
    $_SESSION['flash_message_error'] = "Aksi penghapusan tidak valid.";
    header('Location: list_recipe.php');
    exit;
}
?>