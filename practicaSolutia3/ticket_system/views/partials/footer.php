</div> <!-- Cierre del div .content -->
        <footer class="bg-dark text-white text-center py-3 mt-5">
            <div class="container">
                <p class="mb-0">Sistema de Tickets &copy; <?php echo date('Y'); ?></p>
            </div>
        </footer>
    </div> <!-- Cierre del div .wrapper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para cambiar entre modo oscuro y modo claro
        const themeButton = document.getElementById('theme-button');
        const body = document.body;

        function applyDarkMode(isDark) {
            if (isDark) {
                body.classList.add('dark-mode');
                themeButton.innerHTML = '<i class="fas fa-sun me-2"></i>Modo Claro';
                themeButton.classList.remove('btn-primary-custom');
                themeButton.classList.add('btn-warning');
            } else {
                body.classList.remove('dark-mode');
                themeButton.innerHTML = '<i class="fas fa-moon me-2"></i>Modo Oscuro';
                themeButton.classList.remove('btn-warning');
                themeButton.classList.add('btn-primary-custom');
            }
            // Asegurar que el color del texto del botón sea visible
            themeButton.style.color = 'white'; // Fuerza el color del texto a blanco
        }

        // Cargar preferencia al inicio
        if (localStorage.getItem('darkMode') === 'true') {
            applyDarkMode(true);
        }

        themeButton.addEventListener('click', () => {
            const isDark = !body.classList.contains('dark-mode');
            applyDarkMode(isDark);
            localStorage.setItem('darkMode', isDark);
        });
    </script>
</body>
</html>