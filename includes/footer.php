    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Enhanced animations
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Parallax effect
            $(window).scroll(function() {
                const scrolled = $(window).scrollTop();
                $('.floating').css('transform', `translateY(${scrolled * 0.05}px)`);
            });
            
            // Tooltip initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
        
        // Add confetti effect for registration button
        function celebrate() {
            const button = document.querySelector('.btn-arten-secondary');
            if(button) {
                button.addEventListener('click', function() {
                    // Add animation class
                    this.classList.add('animate__animated', 'animate__pulse');
                    setTimeout(() => {
                        this.classList.remove('animate__animated', 'animate__pulse');
                    }, 1000);
                });
            }
        }
        
        // Call when DOM is loaded
        document.addEventListener('DOMContentLoaded', celebrate);
    </script>
    
    <!-- Smooth scroll polyfill for older browsers -->
    <script>
        if (!('scrollBehavior' in document.documentElement.style)) {
            import('https://unpkg.com/smoothscroll-polyfill@0.4.4/dist/smoothscroll.min.js');
        }
    </script>
</body>
</html>