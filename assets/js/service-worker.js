// service-worker.js

// Nama cache, penting: UBAH INI SETIAP KALI ADA PERUBAHAN BESAR PADA ASET (misal: CSS, JS, Gambar)
const CACHE_NAME = 'resep-korea-v11'; 

// Daftar aset (file) yang akan di-precache saat Service Worker diinstal
const ASSETS_TO_PRECACHE = [
    '/', // Halaman utama website
    '/index.php', // Jika index.php adalah entry point utama
    '/?page=recipes', // Halaman daftar resep
    '/?page=category', // Halaman daftar kategori
    '/assets/css/style.css', // File CSS utama kamu
    '/assets/js/main.js',   // Contoh: jika kamu punya file JavaScript utama
    '/assets/images/hero.webp', // Gambar hero
    '/assets/images/default-placeholder.png', // Gambar placeholder default
    // Tambahkan URL relatif dari aset-aset lain yang SANGAT PENTING
    // dan ingin kamu simpan di cache agar cepat diakses, contoh:
    // '/assets/images/logo.png',
    // '/includes/db.php', // Jika ini adalah bagian dari front-end yang diakses langsung
    // dll.
];

// --- Event: install ---
// Terjadi saat Service Worker pertama kali diinstal di browser pengguna.
// Di sini kita akan membuka cache dan menyimpan semua aset yang terdaftar.
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Menginstal Service Worker...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Memulai pre-caching aset...');
                return cache.addAll(ASSETS_TO_PRECACHE);
            })
            .then(() => {
                console.log('[Service Worker] Pre-caching selesai. Melewati proses waiting.');
                return self.skipWaiting(); // Memaksa Service Worker baru untuk aktif segera
            })
            .catch((error) => console.error('[Service Worker] Gagal pre-caching:', error))
    );
});

// --- Event: activate ---
// Terjadi saat Service Worker aktif dan mengambil alih kontrol halaman.
// Di sini kita akan menghapus cache lama untuk memastikan pengguna selalu mendapatkan versi terbaru.
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Mengaktifkan Service Worker...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    // Hapus cache yang bukan versi saat ini
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Menghapus cache lama:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('[Service Worker] Cache lama dihapus. Mengambil kendali klien.');
            return clients.claim(); // Memaksa Service Worker untuk mengambil kendali klien segera
        })
    );
});

// --- Event: fetch ---
// Terjadi setiap kali browser mencoba mengambil sumber daya (HTML, CSS, JS, gambar, dll.).
// Di sini kita akan mencegat permintaan dan melayani dari cache jika ada.
self.addEventListener('fetch', (event) => {
    // Abaikan permintaan untuk ekstensi Chrome atau yang bukan HTTP/HTTPS
    if (event.request.url.startsWith('chrome-extension://') || !event.request.url.startsWith('http')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then((cachedResponse) => {
                // Jika aset ditemukan di cache, kembalikan dari cache
                if (cachedResponse) {
                    console.log(`[Service Worker] Melayani dari cache: ${event.request.url}`);
                    return cachedResponse;
                }

                // Jika tidak ada di cache, coba ambil dari jaringan
                console.log(`[Service Worker] Mengambil dari jaringan: ${event.request.url}`);
                return fetch(event.request).then((networkResponse) => {
                    // Periksa apakah response dari jaringan valid
                    if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                        return networkResponse;
                    }

                    // Kloning response karena stream hanya bisa dibaca sekali
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache); // Simpan di cache untuk penggunaan selanjutnya
                    });

                    return networkResponse;
                });
            })
            .catch((error) => {
                // Ini akan dijalankan jika fetch gagal (misalnya, offline dan tidak ada di cache)
                console.error('[Service Worker] Jaringan atau cache gagal untuk:', event.request.url, error);
                // Kamu bisa mengembalikan halaman offline khusus di sini
                // Contoh: return caches.match('/offline.html');
                // Untuk saat ini, kita bisa mengembalikan respons kosong atau error
                return new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
            })
    );
});