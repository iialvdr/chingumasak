### **ChinguMasak**

ChinguMasak adalah platform web yang didedikasikan untuk berbagi resep masakan Korea otentik dan inovatif. Misi utama proyek ini adalah menyediakan resep yang mudah diakses dan menyenangkan untuk dimasak oleh semua orang, baik pemula maupun koki berpengalaman.

---

### **Fitur Utama**

Situs web ini memiliki fungsionalitas untuk pengguna umum dan panel admin untuk pengelolaan konten.

#### **Fitur Pengguna**
* **Halaman Utama**: Menampilkan resep unggulan dan kategori populer.
* **Semua Resep**: Menjelajahi daftar lengkap resep.
* **Kategori Resep**: Menyaring resep berdasarkan kategori.
* **Pencarian Resep**: Mencari resep berdasarkan judul, bahan, atau langkah-langkah.
* **Detail Resep**: Melihat detail, bahan, langkah-langkah, dan video tutorial untuk setiap resep.
* **Komentar**: Pengguna dapat menambahkan komentar pada setiap halaman resep.

#### **Fitur Panel Admin**
* **Login Aman**: Panel admin dilindungi dengan otentikasi login.
* **Kelola Resep**: Menambah, mengubah, dan menghapus resep masakan.
* **Kelola Komentar**: Menghapus komentar yang tidak pantas.

---

### **Teknologi yang Digunakan**
* **Backend**: PHP.
* **Database**: MySQL.
* **Frontend**: HTML, CSS, JavaScript.

---

### **Instalasi**

Untuk menjalankan proyek ini secara lokal, ikuti langkah-langkah berikut:
1.  **Web Server**: Pastikan kamu memiliki lingkungan server web (seperti XAMPP, WAMP, atau LAMP) dengan PHP dan MySQL terinstal.
2.  **Kloning Repositori**: Unduh atau klon file proyek ini ke direktori `htdocs` (atau folder root server) kamu.
3.  **Konfigurasi Database**:
    * Buat database MySQL baru dengan nama `if0_39253347_chingumasak`.
    * Ganti detail koneksi database di file `includes/db.php` dengan kredensial database kamu.
4.  **Impor Skema Database**: Skema database dapat dibuat secara manual berdasarkan file-file PHP yang disediakan, seperti `admin/show_admin_table.php` yang menunjukkan struktur tabel `admin`, dan `admin/list_recipe.php` yang menunjukkan tabel `recipes`.

---
### **Kredensial Admin**

Untuk mengakses panel admin, gunakan kredensial berikut:
* **Username**: `admin`
* **Password**: `admin`

**Catatan**: Sesuai dengan kode, kredensial ini hanya untuk tujuan demo/debug dan tidak aman untuk lingkungan produksi.
