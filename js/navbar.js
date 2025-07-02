document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const navList = document.querySelector('.nav-list');
    const mobileOverlay = document.querySelector('.mobile-overlay');
    const navLinks = document.querySelectorAll('.nav-link');
    
    function toggleMobileMenu() {
        const isOpen = navList.classList.contains('mobile-open');
        
        if (!isOpen) {
            // Abrir menú
            mobileToggle.classList.add('active');
            navList.classList.add('mobile-open');
            mobileOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            // Cerrar menú
            closeMobileMenu();
        }
    }
    
    function closeMobileMenu() {
        mobileToggle.classList.remove('active');
        navList.classList.remove('mobile-open');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Event listeners
    mobileToggle.addEventListener('click', toggleMobileMenu);
    mobileOverlay.addEventListener('click', closeMobileMenu);
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            closeMobileMenu();
            setTimeout(() => {
            }, 300);
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navList.classList.contains('mobile-open')) {
            closeMobileMenu();
        }
    });
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });
});