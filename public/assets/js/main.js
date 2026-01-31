/**
 * CesiStages - JavaScript principal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des composants
    initMobileMenu();
    initUserMenu();
    initFlashMessages();
});

/**
 * Menu mobile (burger)
 */
function initMobileMenu() {
    const burgerMenu = document.querySelector('.burger-menu');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (burgerMenu && mobileMenu) {
        // Fermer le menu en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (!burgerMenu.contains(e.target) && !mobileMenu.contains(e.target) && mobileMenu.classList.contains('active')) {
                toggleMobileMenu();
            }
        });
    }
}

function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const burgerMenu = document.querySelector('.burger-menu');
    
    if (mobileMenu && burgerMenu) {
        mobileMenu.classList.toggle('active');
        burgerMenu.classList.toggle('active');
        
        // Animation simple pour le burger icon si on voulait ajouter des classes spécifiques aux spans
    }
}

/**
 * Menu utilisateur (dropdown)
 */
function initUserMenu() {
    // Si plusieurs menus utilisateurs, on pourrait utiliser querySelectorAll
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenuToggle && userDropdown) {
        
        // Toggle au clic
        window.toggleUserMenu = function() {
            userDropdown.classList.toggle('active');
        };

        // Fermer en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (!userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }
}

/**
 * Gestion des messages flash
 */
function initFlashMessages() {
    const flashMessages = document.querySelectorAll('.flash-message');
    
    flashMessages.forEach(message => {
        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            if (message && message.parentNode) {
                message.style.opacity = '0';
                setTimeout(() => message.remove(), 300);
            }
        }, 5000);
    });
}
