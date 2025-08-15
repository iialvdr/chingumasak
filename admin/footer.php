</main>
    </div></div><script>
      document.addEventListener('DOMContentLoaded', function() {
          const menuToggle = document.querySelector('.menu-toggle-mobile');
          const sidebar = document.querySelector('.admin-sidebar');
          const adminWrapper = document.querySelector('.admin-wrapper');

          if (menuToggle && sidebar && adminWrapper) {
              menuToggle.addEventListener('click', function() {
                  sidebar.classList.toggle('active');
                  adminWrapper.classList.toggle('sidebar-active');
              });

              // Close sidebar when clicking outside on mobile
              adminWrapper.addEventListener('click', function(event) {
                  if (sidebar.classList.contains('active') && !sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                      sidebar.classList.remove('active');
                      adminWrapper.classList.remove('sidebar-active');
                  }
              });
          }

          // Kode baru untuk menyembunyikan notifikasi setelah 3 detik
          const successMessage = document.querySelector('.message.success');
          const errorMessage = document.querySelector('.message.error');
          const warningMessage = document.querySelector('.message.warning');

          if (successMessage) {
              setTimeout(() => {
                  successMessage.style.display = 'none';
              }, 3000); // Sembunyikan setelah 3000 milidetik (3 detik)
          }
          if (errorMessage) {
              setTimeout(() => {
                  errorMessage.style.display = 'none';
              }, 3000); // Sembunyikan setelah 3000 milidetik (3 detik)
          }
          if (warningMessage) {
              setTimeout(() => {
                  warningMessage.style.display = 'none';
              }, 3000); // Sembunyikan setelah 3000 milidetik (3 detik)
          }
      });
  </script>
</body>
</html>