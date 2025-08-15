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
    $title_val = $_POST['judul'];
    $category_id_val = (int)$_POST['kategori'];
    $ingredients_val = normalize_newlines_for_storage($_POST['bahan']);
    $steps_val = normalize_newlines_for_storage($_POST['langkah']);
    $video_url_val = $_POST['video_url'];
    $image_filename = '';

    // --- Generate Slug ---
    $slug_val = generateSlug($conn, $title_val, 'recipes', 'slug'); // Panggil fungsi generateSlug

    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "../assets/images/";
        $original_filename = basename($_FILES["gambar"]["name"]);
        $new_image_filename = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $original_filename);
        $target_file_path = $target_dir . $new_image_filename;
        $filetype = strtolower(pathinfo($target_file_path, PATHINFO_EXTENSION));

        if (in_array($filetype, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file_path)) {
                $image_filename = $new_image_filename;
            } else {
                $error = "Gagal upload gambar.";
            }
        } else {
            $error = "Format gambar harus JPG, JPEG, PNG, atau GIF.";
        }
    }

    if (!$error) {
        // --- Masukkan slug ke query INSERT ---
        $stmt = $conn->prepare("INSERT INTO recipes (title, slug, category_id, ingredients, steps, image, video_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissss", $title_val, $slug_val, $category_id_val, $ingredients_val, $steps_val, $image_filename, $video_url_val);

        if ($stmt->execute()) {
            $success = "Resep berhasil ditambahkan!";
            $_POST = array(); // Bersihkan POST data setelah sukses
        } else {
            $error = "Gagal menyimpan ke database: " . $stmt->error;
            // Jika error karena slug duplikat (jarang jika generateSlug benar), tambahkan penanganan
            if ($stmt->errno == 1062) { // MySQL error code for duplicate entry for unique key
                $error .= " (Slug duplikat, coba judul lain?)";
            }
        }
        $stmt->close();
    }
}
?>

<?php include 'header.php'; ?>

<a class="back-link" href="list_recipe.php">← Kembali ke daftar resep</a>

<form method="post" enctype="multipart/form-data">
    <h2>Tambah Resep Baru</h2>

    <?php if ($success): ?><p class="message success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="message error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <div>
        <label for="judul">Judul Resep</label>
        <input type="text" id="judul" name="judul" value="<?= isset($_POST['judul']) && $error ? htmlspecialchars($_POST['judul']) : '' ?>" required>
    </div>

    <div>
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($categories_list as $category_item): ?>
                <option value="<?= $category_item['id'] ?>" <?= (isset($_POST['kategori']) && $_POST['kategori'] == $category_item['id'] && $error) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category_item['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="bahan">Bahan-Bahan (setiap bahan di baris baru)</label>
        <textarea id="bahan" name="bahan" rows="4" required><?= isset($_POST['bahan']) && $error ? htmlspecialchars($_POST['bahan']) : '' ?></textarea>
    </div>

    <div>
        <label for="langkah">Langkah-langkah (setiap langkah di baris baru)</label>
        <textarea id="langkah" name="langkah" rows="6" required><?= isset($_POST['langkah']) && $error ? htmlspecialchars($_POST['langkah']) : '' ?></textarea>
    </div>

    <div>
        <label for="gambar">Upload Gambar</label>
        <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/gif">
    </div>

    <div>
        <label for="video_url">Link Video YouTube (Opsional)</label>
        <input type="url" id="video_url" name="video_url" value="<?= isset($_POST['video_url']) && $error ? htmlspecialchars($_POST['video_url']) : '' ?>" placeholder="https://www.youtube.com/watch?v=xxxxxxxxxxx">
    </div>

    <button type="submit" class="btn">Simpan Resep</button>
</form>

<?php include 'footer.php'; ?>