<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php'; // Pastikan db.php di-include untuk fungsi generateSlug dan getRecipeImageUrl

function normalize_newlines_for_storage($input_string) {
    return str_replace(["\r\n", "\r"], "\n", $input_string);
}

if (!isset($_GET['id'])) {
    $_SESSION['flash_message_error'] = "ID Resep tidak ditemukan.";
    header('Location: list_recipe.php');
    exit;
}

$id = (int) $_GET['id'];
$stmt_get_recipe = $conn->prepare("SELECT *, is_featured FROM recipes WHERE id = ?");
if ($stmt_get_recipe) {
    $stmt_get_recipe->bind_param("i", $id);
    $stmt_get_recipe->execute();
    $result = $stmt_get_recipe->get_result();
    $data = $result->fetch_assoc();
    $stmt_get_recipe->close();
} else {
    $_SESSION['flash_message_error'] = "Gagal mengambil data resep: " . $conn->error;
    header('Location: list_recipe.php');
    exit;
}

if (!$data) {
    $_SESSION['flash_message_error'] = "Resep tidak ditemukan.";
    header('Location: list_recipe.php');
    exit;
}

$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name");
$categories_list = [];
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories_list[] = $row;
    }
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category_id = (int)$_POST['kategori'];
    $bahan_to_save = normalize_newlines_for_storage($_POST['bahan']);
    $langkah_to_save = normalize_newlines_for_storage($_POST['langkah']);
    $video_url = $_POST['video_url'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $image = $data['image'];

    // --- Generate Slug ---
    $slug_val = generateSlug($conn, $title, 'recipes', 'slug', $id);

    if (!empty($_FILES['image']['name'])) {
        if (!empty($image) && file_exists("../assets/images/" . $image)) {
            unlink("../assets/images/" . $image);
        }
        $target_dir = "../assets/images/";
        $filename = basename($_FILES["image"]["name"]);
        $new_image_filename = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $filename);
        $target_file = $target_dir . $new_image_filename;
        $filetype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($filetype, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $new_image_filename;
            } else {
                $error = "Gagal upload gambar baru.";
            }
        } else {
            $error = "Format gambar harus JPG, JPEG, PNG, atau GIF.";
        }
    }

    if (!$error) {
        // --- Masukkan slug ke query UPDATE ---
        $stmt_update = $conn->prepare("UPDATE recipes SET title=?, slug=?, category_id=?, ingredients=?, steps=?, image=?, video_url=?, is_featured=? WHERE id=?");
        $stmt_update->bind_param("ssissssii", $title, $slug_val, $category_id, $bahan_to_save, $langkah_to_save, $image, $video_url, $is_featured, $id);

        if ($stmt_update->execute()) {
            $success = "Resep berhasil diperbarui!";
            $stmt_refresh = $conn->prepare("SELECT *, is_featured FROM recipes WHERE id = ?");
            if ($stmt_refresh) {
                $stmt_refresh->bind_param("i", $id);
                $stmt_refresh->execute();
                $result_refresh = $stmt_refresh->get_result();
                $data = $result_refresh->fetch_assoc(); // Update $data with fresh values
                $stmt_refresh->close();
            }
        } else {
            $error = "Gagal memperbarui resep: " . $stmt_update->error;
            if ($stmt_update->errno == 1062) { // MySQL error code for duplicate entry for unique key
                $error .= " (Slug duplikat, coba judul lain?)";
            }
        }
        $stmt_update->close();
    }
}
?>

<?php include 'header.php'; ?>

<a class="back-link" href="list_recipe.php">← Kembali ke daftar resep</a>

<form method="post" enctype="multipart/form-data">
    <h2>Edit Resep: <?= htmlspecialchars($data['title']) ?></h2>

    <?php if ($success): ?><p class="message success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="message error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <div>
        <label for="title">Judul Resep</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($data['title']) ?>" required>
    </div>

    <div>
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($categories_list as $category_item): ?>
                <option value="<?= $category_item['id'] ?>" <?= ($data['category_id'] == $category_item['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category_item['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="bahan">Bahan-Bahan (setiap bahan di baris baru)</label>
        <textarea id="bahan" name="bahan" rows="4" required><?= htmlspecialchars($data['ingredients']) ?></textarea>
    </div>

    <div>
        <label for="langkah">Langkah-langkah (setiap langkah di baris baru)</label>
        <textarea id="langkah" name="langkah" rows="6" required><?= htmlspecialchars($data['steps']) ?></textarea>
    </div>

    <div>
        <label>Gambar Saat Ini</label><br>
        <img src="<?= htmlspecialchars(getRecipeImageUrl($data['image'])) ?>" width="150" alt="Gambar Resep Saat Ini" style="margin-bottom: 10px; border-radius: 6px; border: 1px solid #ddd;"><br>
    </div>

    <div>
        <label for="image">Upload Gambar Baru (Kosongkan jika tidak ingin mengganti)</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
    </div>

    <div>
        <label for="video_url">Link Video YouTube</label>
        <input type="url" id="video_url" name="video_url" value="<?= htmlspecialchars($data['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=xxxxxxxxxxx">
    </div>

    <div>
        <label for="is_featured">Jadikan Resep Unggulan?</label>
        <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($data['is_featured']) && $data['is_featured'] ? 'checked' : '' ?>>
        <small>(Centang untuk menjadikan resep ini sebagai unggulan)</small>
    </div>

    <button type="submit" class="btn">Simpan Perubahan</button>
</form>

<?php include 'footer.php'; ?>