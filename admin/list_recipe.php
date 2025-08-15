<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php'; // Pastikan db.php di-include untuk getRecipeImageUrl

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_recipe') {
    if (isset($_POST['recipe_id_to_delete']) && filter_var($_POST['recipe_id_to_delete'], FILTER_VALIDATE_INT)) {
        $id_to_delete = (int)$_POST['recipe_id_to_delete'];

        $stmt_select_image = $conn->prepare("SELECT image FROM recipes WHERE id = ?");
        if ($stmt_select_image) {
            $stmt_select_image->bind_param("i", $id_to_delete);
            $stmt_select_image->execute();
            $result_image = $stmt_select_image->get_result();
            $recipe_data_to_delete = $result_image->fetch_assoc();
            $stmt_select_image->close();

            if ($recipe_data_to_delete) {
                if (!empty($recipe_data_to_delete['image'])) {
                    $file_path_to_delete = '../assets/images/' . $recipe_data_to_delete['image'];
                    if (file_exists($file_path_to_delete)) {
                        if (!unlink($file_path_to_delete)) {
                            $_SESSION['flash_message_warning'] = "Resep dihapus dari database, tetapi file gambar gagal dihapus dari server.";
                        }
                    }
                }

                $stmt_delete_recipe = $conn->prepare("DELETE FROM comments WHERE recipe_id = ?"); // Hapus komentar terkait dulu
                if ($stmt_delete_recipe) {
                    $stmt_delete_recipe->bind_param("i", $id_to_delete);
                    $stmt_delete_recipe->execute();
                    $stmt_delete_recipe->close();
                }

                $stmt_delete_recipe = $conn->prepare("DELETE FROM recipes WHERE id = ?");
                if ($stmt_delete_recipe) {
                    $stmt_delete_recipe->bind_param("i", $id_to_delete);
                    if ($stmt_delete_recipe->execute()) {
                        if ($stmt_delete_recipe->affected_rows > 0) {
                            if (!isset($_SESSION['flash_message_warning'])) {
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
                $_SESSION['flash_message_error'] = "Resep dengan ID $id_to_delete tidak ditemukan untuk dihapus.";
            }
        } else {
            $_SESSION['flash_message_error'] = "Terjadi kesalahan saat persiapan mengambil data gambar: " . $conn->error;
        }
    } else {
        $_SESSION['flash_message_error'] = "ID resep untuk dihapus tidak valid.";
    }

    $query_string_params = [];
    if (isset($_GET['search'])) $query_string_params['search'] = $_GET['search'];
    if (isset($_GET['category_id'])) $query_string_params['category_id'] = $_GET['category_id'];
    if (isset($_GET['is_featured'])) $query_string_params['is_featured'] = $_GET['is_featured'];

    $redirect_url = 'list_recipe.php';
    if (!empty($query_string_params)) {
        $redirect_url .= '?' . http_build_query($query_string_params);
    }

    header('Location: ' . $redirect_url);
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$is_featured_filter = isset($_GET['is_featured']) ? (int)$_GET['is_featured'] : -1;

$categories_list = [];
$cat_res = $conn->query("SELECT id, name FROM categories ORDER BY name");
if ($cat_res) {
    while ($cat_row = $cat_res->fetch_assoc()) {
        $categories_list[] = $cat_row;
    }
}

$where_clauses_list = [];
$params_list = [];
$types_list = '';

if ($search !== '') {
    $where_clauses_list[] = "(r.title LIKE ? OR r.ingredients LIKE ? OR r.steps LIKE ?)";
    $search_term = '%' . $search . '%';
    $params_list[] = $search_term;
    $params_list[] = $search_term;
    $params_list[] = $search_term;
    $types_list .= 'sss';
}
if ($category_filter > 0) {
    $where_clauses_list[] = "r.category_id = ?";
    $params_list[] = $category_filter;
    $types_list .= 'i';
}
if ($is_featured_filter !== -1) {
    $where_clauses_list[] = "r.is_featured = ?";
    $params_list[] = $is_featured_filter;
    $types_list .= 'i';
}

$where_sql_list = '';
if (count($where_clauses_list) > 0) {
    $where_sql_list = 'WHERE ' . implode(' AND ', $where_clauses_list);
}

$sql_list = "SELECT r.*, c.name as category_name, r.slug
             FROM recipes r
             JOIN categories c ON r.category_id = c.id
             $where_sql_list
             ORDER BY r.created_at DESC";
$stmt_recipes = $conn->prepare($sql_list);
$res_list = null;
$total_recipes_displayed = 0;

if (!$stmt_recipes) {
    $_SESSION['flash_message_error'] = "Terjadi kesalahan database saat mengambil daftar resep.";
} else {
    if (!empty($params_list)) {
        $stmt_recipes->bind_param($types_list, ...$params_list);
    }
    $stmt_recipes->execute();
    $res_list = $stmt_recipes->get_result();
    $total_recipes_displayed = $res_list ? $res_list->num_rows : 0;
}
?>

<?php include 'header.php'; ?>

    <h2>Daftar Resep</h2>

    <?php
    if (isset($_SESSION['flash_message_success'])) {
        echo '<p class="message success">' . htmlspecialchars($_SESSION['flash_message_success']) . '</p>';
        unset($_SESSION['flash_message_success']);
    }
    if (isset($_SESSION['flash_message_error'])) {
        echo '<p class="message error">' . htmlspecialchars($_SESSION['flash_message_error']) . '</p>';
        unset($_SESSION['flash_message_error']);
    }
    if (isset($_SESSION['flash_message_warning'])) {
        echo '<p class="message warning">' . htmlspecialchars($_SESSION['flash_message_warning']) . '</p>';
        unset($_SESSION['flash_message_warning']);
    }
    ?>

    <form method="get" action="list_recipe.php" class="search-filter">
        <input type="text" name="search" placeholder="Cari judul, bahan, atau langkah..." value="<?= htmlspecialchars($search) ?>">
        <select name="category_id">
            <option value="0" <?= $category_filter === 0 ? 'selected' : '' ?>>Semua Kategori</option>
            <?php foreach ($categories_list as $category_item): ?>
                <option value="<?= $category_item['id'] ?>" <?= ($category_filter == $category_item['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category_item['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="is_featured">
            <option value="-1" <?= $is_featured_filter === -1 ? 'selected' : '' ?>>Semua Status Unggulan</option>
            <option value="1" <?= $is_featured_filter === 1 ? 'selected' : '' ?>>Unggulan</option>
            <option value="0" <?= $is_featured_filter === 0 ? 'selected' : '' ?>>Bukan Unggulan</option>
        </select>
        <button type="submit" class="btn">Cari</button>
        <?php if (!empty($search) || $category_filter > 0 || $is_featured_filter !== -1): ?>
            <a href="list_recipe.php" class="btn btn-secondary">Reset Filter</a>
        <?php endif; ?>
    </form>

    <div class="top-action">
        <a href="add_recipe.php" class="btn">+ Tambah Resep Baru</a>
    </div>

    <p class="recipe-count">Menampilkan <b><?= $total_recipes_displayed ?></b> resep.</p>

    <div class="table-responsive">
      <table>
          <thead>
              <tr>
                  <th>Gambar</th>
                  <th>Judul</th>
                  <th>Kategori</th>
                  <th>Unggulan</th>
                  <th>Tanggal</th>
                  <th>Aksi</th>
              </tr>
          </thead>
          <tbody>
              <?php if ($res_list && $res_list->num_rows > 0): ?>
                  <?php while ($row = $res_list->fetch_assoc()): ?>
                      <tr>
                          <td data-label="Gambar">
                              <img src="<?= htmlspecialchars(getRecipeImageUrl($row['image'])) ?>" alt="<?= htmlspecialchars($row['title']) ?>" class="recipe-thumb">
                          </td>
                          <td data-label="Judul"><?= htmlspecialchars($row['title']) ?></td>
                          <td data-label="Kategori"><?= htmlspecialchars($row['category_name']) ?></td>
                          <td data-label="Unggulan">
                            <?= $row['is_featured'] ? '<span class="status-featured">Ya</span>' : '<span class="status-non-featured">Tidak</span>' ?>
                          </td>
                          <td data-label="Tanggal"><?= htmlspecialchars(date('d M Y, H:i', strtotime($row['created_at']))) ?></td>
                          <td data-label="Aksi">
                              <div class="action-buttons">
                                <a class="btn btn-secondary" href="edit_recipe.php?id=<?= $row['id'] ?>">Edit</a>
                                <a class="btn btn-info" href="../?page=recipe_detail&slug=<?= $row['slug'] ?>" target="_blank" title="Lihat Resep">Lihat</a>
                                <form action="list_recipe.php<?= !empty($_SERVER['QUERY_STRING']) ? '?' . htmlspecialchars($_SERVER['QUERY_STRING']) : '' ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus resep \'<?= htmlspecialchars(addslashes($row['title'])) ?>\' ini?');">
                                    <input type="hidden" name="action" value="delete_recipe">
                                    <input type="hidden" name="recipe_id_to_delete" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                              </div>
                          </td>
                      </tr>
                  <?php endwhile; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="6">Tidak ada resep ditemukan.</td>
                  </tr>
              <?php endif; ?>
          </tbody>
      </table>
    </div>

<?php
if (isset($stmt_recipes) && $stmt_recipes instanceof mysqli_stmt) $stmt_recipes->close();
?>
<?php include 'footer.php'; ?>