# ChinguMasak: Jelajahi Kelezatan Resep Korea Autentik 🍲✨

## Selamat Datang di ChinguMasak!
ChinguMasak adalah platform resep masakan Korea yang didedikasikan untuk para pecinta kuliner dan mereka yang ingin mencoba pengalaman memasak hidangan Korea autentik di rumah. Dari hidangan klasik seperti Kimchi Jjigae hingga kreasi modern yang menggoda selera, kami hadir untuk memandu Anda melalui setiap langkah. Siapkan diri Anda untuk petualangan rasa yang tak terlupakan!

## ✨ Fitur Unggulan Kami ✨

### 🧑‍🍳 Jelajahi Ribuan Resep Korea
Temukan resep favorit Anda dengan mudah. Dari hidangan pembuka yang menyegarkan, hidangan utama yang mengenyangkan, hingga makanan penutup yang manis. Setiap resep dilengkapi dengan bahan-bahan lengkap, langkah-langkah detail, dan tips berguna.
* **Kategori Resep Intuitif:** Filter resep berdasarkan kategori populer seperti "Sup & Stew", "Nasi & Mie", "Daging & Unggas", dan banyak lagi.
* **Pencarian Cerdas:** Temukan resep yang Anda inginkan dalam sekejap dengan fitur pencarian yang akurat berdasarkan judul, bahan, atau bahkan metode masakan.

### 🎥 Panduan Memasak Interaktif
Beberapa resep kami dilengkapi dengan tautan video tutorial dari YouTube untuk membantu Anda memvisualisasikan setiap langkah, menjadikan proses memasak lebih mudah dan menyenangkan.

### 💬 Bagikan Pengalaman Anda
Setelah mencoba resep kami, jangan ragu untuk meninggalkan komentar dan membagikan pengalaman memasak Anda dengan komunitas ChinguMasak lainnya! Berikan *rating*, tips, atau bahkan modifikasi yang Anda lakukan.

### 📱 Akses Kapan Saja, Di Mana Saja (PWA Ready!)
ChinguMasak dirancang sebagai Progressive Web App (PWA). Ini berarti Anda bisa menyimpan aplikasi kami ke layar utama ponsel Anda dan menikmati akses offline untuk resep yang sudah Anda kunjungi. Masak tanpa khawatir koneksi internet!

## ⚙️ Di Balik Layar: Panel Admin yang Efisien
Untuk para administrator, kami menyediakan panel khusus untuk mengelola seluruh konten resep dan interaksi pengguna:
* **Dashboard Insightful:** Pantau statistik resep dan komentar secara sekilas.
* **Manajemen Resep Lengkap:** Tambah, edit, atau hapus resep dengan mudah. Upload gambar, atur kategori, dan tandai resep sebagai "Unggulan" untuk ditampilkan di beranda.
* **Moderasi Komentar:** Tinjau dan kelola komentar yang masuk untuk menjaga kualitas komunitas.

---

## 🛠️ Teknologi yang Menggerakkan ChinguMasak
Proyek ini dibangun dengan fondasi teknologi yang solid:
* **Backend:** PHP (Native)
* **Database:** MySQLi
* **Frontend:** HTML5, CSS3, JavaScript
* **Arsitektur:** Aplikasi berbasis halaman (Page-based)

## 🚀 Cara Memulai (Untuk Pengembang)
1.  **Siapkan Database:**
    * Buat database MySQL baru di *environment* lokal Anda.
    * Perbarui informasi koneksi database di `includes/db.php` sesuai dengan konfigurasi Anda.
    * *(Opsional: Import schema dan data contoh jika tersedia)*

2.  **Siapkan Web Server:**
    * Tempatkan seluruh folder proyek ChinguMasak ke dalam direktori web server Anda (misalnya: `htdocs` untuk XAMPP/AppServ, `www` untuk WAMP).
    * Pastikan PHP dan MySQL server sudah berjalan.

3.  **Jelajahi Aplikasi:**
    * **Situs Publik:** Buka browser Anda dan kunjungi URL root proyek Anda (contoh: `http://localhost/chingumasak`).
    * **Panel Admin:** Akses area admin melalui `http://localhost/chingumasak/admin`. *(Catatan: Kredensial login admin awal dapat dilihat pada skema database atau di file `admin/show_admin_table.php` jika ada.)*

## 💡 Catatan Penting
* **Keamanan:** Fitur login admin saat ini menyimpan kata sandi dalam *plain-text* di database. Untuk implementasi produksi, sangat disarankan untuk menggunakan metode *hashing* kata sandi yang aman (misalnya `password_hash()` di PHP).
* **Pengelolaan Gambar:** Gambar resep disimpan di `assets/images/`. Pastikan direktori ini memiliki izin tulis yang benar agar proses unggah berjalan lancar.

---

Selamat memasak dan menikmati kelezatan masakan Korea bersama ChinguMasak!
