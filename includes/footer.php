<footer class="site-footer">
  <div class="container footer-container">
    <p>&copy; <?= date("Y") ?> ChinguMasak. All Rights Reserved.</p>
    <div class="social-icons">
      <a href="https://www.instagram.com/choda._.n" aria-label="Instagram" title="Instagram">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
      </a>
      <a href="https://www.instagram.com/magenta_6262" aria-label="YouTube" title="YouTube">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z"/></svg>
      </a>
      <a href="https://www.instagram.com/i_am_young22" aria-label="Facebook" title="Facebook">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"/></svg>
      </a>
      <a href="https://www.instagram.com/siyo.co.kr" aria-label="X (Twitter)" title="X (Twitter)"> 
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M389.2 48h72.8L280.3 226.2 480 464H342L217.5 288.7 101.9 464H28L210.7 207.2 0 48h147.2L251.6 195.3 389.2 48zm-22.9 368h50.7L109 97.4H58.2L366.3 416z"/></svg>
      </a>
    </div>
  </div>
</footer>
<script src="https://kit.fontawesome.com/b8a52d1b77.js" crossorigin="anonymous"></script>
<script src="//instant.page/5.2.0" type="module" integrity="sha384-fEfW3bDQLSPieHABtZ7PNDFbLgGBre6xJ8XwkmHGzNAbFngbTCMgXrLStKoxJFXy"></script>
<script>
    // Kode pendaftaran Service Worker (jika ada)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            // Pastikan path service worker benar
            navigator.serviceWorker.register('/assets/js/service-worker.js')
                .then((registration) => {
                    console.log('Service Worker berhasil didaftarkan dengan scope:', registration.scope);
                })
                .catch((error) => {
                    console.error('Pendaftaran Service Worker gagal:', error);
                });
        });
    }

    // Kode untuk membuat navbar muncul/hilang saat scroll
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.querySelector('.site-header');
        let lastScrollY = window.pageYOffset;

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > lastScrollY && window.pageYOffset > 100) {
                // Saat scroll ke bawah, tambahkan kelas untuk menyembunyikan header.
                header.classList.add('hide');
            } else if (window.pageYOffset < lastScrollY) {
                // Saat scroll ke atas, hapus kelas untuk menampilkan header.
                header.classList.remove('hide');
            }
            lastScrollY = window.pageYOffset;
        });

        // Kode Preload on Hover (yang kamu tempel di sini)
        document.body.addEventListener('mouseover', async (event) => {
            const link = event.target.closest('a');
            if (link && link.href && !link.dataset.prefetched) {
                const url = new URL(link.href);
                if (url.origin === window.location.origin) {
                    const prefetchDocLink = document.createElement('link');
                    prefetchDocLink.rel = 'prefetch';
                    prefetchDocLink.href = url.href;
                    document.head.appendChild(prefetchDocLink);
                    link.dataset.prefetched = 'true';
                    console.log(`[Prefetch on Hover] Memuat dokumen: ${url.href}`);

                    try {
                        const response = await fetch(url.href);
                        if (response.ok) {
                            const htmlText = await response.text();
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(htmlText, 'text/html');
                            const images = doc.querySelectorAll('img');
                            images.forEach(img => {
                                const imgSrc = img.src;
                                if (imgSrc && imgSrc.startsWith(window.location.origin)) {
                                    const preloadImageLink = document.createElement('link');
                                    preloadImageLink.rel = 'preload';
                                    preloadImageLink.as = 'image';
                                    preloadImageLink.href = imgSrc;
                                    document.head.appendChild(preloadImageLink);
                                    console.log(`[Prefetch on Hover] Memuat gambar: ${imgSrc}`);
                                }
                            });
                        }
                    } catch (error) {
                        console.error(`[Prefetch on Hover] Gagal mengambil HTML atau gambar untuk ${url.href}:`, error);
                    }
                }
            }
        });

        // KODE BARU UNTUK MENYEMBUNYIKAN PESAN SETELAH 2 DETIK
        const commentStatusMessage = document.getElementById('comment-status-message');
        if (commentStatusMessage) {
            setTimeout(() => {
                commentStatusMessage.style.display = 'none';
            }, 2000); // Sembunyikan setelah 2000 milidetik (2 detik)
        }
    });
</script>
</body>
</html>
