<?php
include '../includes/db.php';
session_start(); // Pastikan session_start() ada

// Optional: Cek login admin jika halaman ini hanya untuk admin
// if (!isset($_SESSION['admin'])) {
//     header('Location: login.php');
//     exit;
// }

include 'header.php'; // Include header untuk styling admin

// Tampilkan flash messages jika ada
if (isset($_SESSION['flash_message_success'])) {
    echo '<p class="message success">' . htmlspecialchars($_SESSION['flash_message_success']) . '</p>';
    unset($_SESSION['flash_message_success']);
}
if (isset($_SESSION['flash_message_error'])) {
    echo '<p class="message error">' . htmlspecialchars($_SESSION['flash_message_error']) . '</p>';
    unset($_SESSION['flash_message_error']);
}

?>
<main class="admin-main"> <h2>Admin Table Structure</h2>
    <div class="table-responsive"> <table border='1' cellpadding='5'>
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Key</th>
                    <th>Default</th>
                    <th>Extra</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("DESCRIBE admin");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td data-label='Field'>" . htmlspecialchars($row['Field']) . "</td>";
                        echo "<td data-label='Type'>" . htmlspecialchars($row['Type']) . "</td>";
                        echo "<td data-label='Null'>" . htmlspecialchars($row['Null']) . "</td>";
                        echo "<td data-label='Key'>" . htmlspecialchars($row['Key']) . "</td>";
                        echo "<td data-label='Default'>" . htmlspecialchars($row['Default']) . "</td>";
                        echo "<td data-label='Extra'>" . htmlspecialchars($row['Extra']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Failed to get table structure.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <h2>Sample Admin Data</h2>
    <div class="table-responsive"> <table border='1' cellpadding='5'>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result2 = $conn->query("SELECT username, password FROM admin LIMIT 5");
                if ($result2) {
                    while ($row = $result2->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td data-label='Username'>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td data-label='Password'>" . htmlspecialchars($row['password']) . "</td>"; // Hati-hati menampilkan password! Ini hanya untuk debug.
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>Failed to get admin data.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</main>
<?php include 'footer.php'; ?>