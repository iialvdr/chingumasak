<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php';

if (isset($_GET['hapus'])) {
    $id_komentar_hapus = (int) $_GET['hapus'];
    $delete_stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    if ($delete_stmt) {
        $delete_stmt->bind_param("i", $id_komentar_hapus);
        if ($delete_stmt->execute()) {
            $_SESSION['flash_message_success'] = "Komentar berhasil dihapus.";
        } else {
            $_SESSION['flash_message_error'] = "Gagal menghapus komentar: " . $delete_stmt->error;
        }
        $delete_stmt->close();
    } else {
        $_SESSION['flash_message_error'] = "Gagal mempersiapkan statement hapus: " . $conn->error;
    }
    header('Location: manage_comments.php');
    exit;
}

// Ambil semua komentar tanpa LIMIT dan OFFSET
$sql = "SELECT c.id, c.name, c.comment, c.created_at, r.title as recipe_title, r.id as recipe_id, r.slug as recipe_slug
        FROM comments c
        JOIN recipes r ON c.recipe_id = r.id
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    $_SESSION['flash_message_error'] = "Error mengambil data komentar: " . $conn->error;
    $result = new stdClass();
    $result->num_rows = 0;
}
?>

<?php include 'header.php'; ?>

    <h2>Daftar Komentar</h2>

    <?php
    if (isset($_SESSION['flash_message_success'])) {
        echo '<p class="message success">' . htmlspecialchars($_SESSION['flash_message_success']) . '</p>';
        unset($_SESSION['flash_message_success']);
    }
    if (isset($_SESSION['flash_message_error'])) {
        echo '<p class="message error">' . htmlspecialchars($_SESSION['flash_message_error']) . '</p>';
        unset($_SESSION['flash_message_error']);
    }
    ?>

    <?php if ($result && $result->num_rows > 0): ?>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Nama Pengirim</th>
              <th>Komentar</th>
              <th>Resep</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td data-label="Nama Pengirim"><?= htmlspecialchars($row['name']) ?></td>
                <td data-label="Komentar"><?= nl2br(htmlspecialchars($row['comment'])) ?></td>
                <td data-label="Resep">
                    <a href="../?page=recipe_detail&slug=<?= $row['recipe_slug'] ?>" target="_blank" title="Lihat Resep">
                        <?= htmlspecialchars($row['recipe_title']) ?>
                    </a>
                </td>
                <td data-label="Tanggal"><?= htmlspecialchars(date('d M Y, H:i', strtotime($row['created_at']))) ?></td>
                <td data-label="Aksi">
                  <div class="action-buttons">
                    <a class="btn btn-danger" href="manage_comments.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?')">Hapus</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>Tidak ada komentar untuk ditampilkan.</p>
    <?php endif; ?>

<?php include 'footer.php'; ?>