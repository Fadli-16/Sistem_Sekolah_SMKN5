/**
 * Homepage JavaScript functionality
 */
document.addEventListener("DOMContentLoaded", function () {
    // Initialize components
    initializeAOS();
    setupStickyNavbar();
    setupSmoothScrolling();
    setupBackToTop();
});

/**
 * Initialize AOS (Animate On Scroll) library with consistent settings
 */
function initializeAOS() {
    // Check if AOS is defined
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,             // Consistent animation duration
            easing: 'ease-in-out',     // Smooth easing
            once: true,                // Only animate once
            mirror: false,             // No mirroring on scroll up
            offset: 100,               // Offset (in px) from the original trigger point
            delay: 0,                  // No delay by default
            anchorPlacement: 'top-bottom', // Anchor placement
            disable: false             // Enable on all devices
        });
    }
}

/**
 * Setup sticky navbar with enhanced effects
 */
function setupStickyNavbar() {
    const navbar = document.querySelector(".navbar");
    
    if (navbar) {
        window.addEventListener("scroll", function() {
            if (window.scrollY > 50) {
                navbar.classList.add("navbar-scrolled");
                navbar.style.padding = '0.4rem 1rem';
                navbar.style.boxShadow = '0 4px 10px rgba(0,0,0,0.1)';
            } else {
                navbar.classList.remove("navbar-scrolled");
                navbar.style.padding = '0.6rem 1rem';
                navbar.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            }
        });
    }
}

/**
 * Setup smooth scrolling for all anchor links
 */
function setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            if (this.getAttribute('href') !== '#') {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 0;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL without page reload
                    history.pushState(null, null, targetId);
                }
            }
        });
    });
}

/**
 * Add back-to-top button functionality
 */
function setupBackToTop() {
    // Create the button if it doesn't exist
    if (!document.querySelector('.back-to-top')) {
        const backToTopBtn = document.createElement('button');
        backToTopBtn.classList.add('back-to-top');
        backToTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
        document.body.appendChild(backToTopBtn);
        
        // Add styles
        backToTopBtn.style.position = 'fixed';
        backToTopBtn.style.bottom = '20px';
        backToTopBtn.style.right = '20px';
        backToTopBtn.style.width = '45px';
        backToTopBtn.style.height = '45px';
        backToTopBtn.style.borderRadius = '50%';
        backToTopBtn.style.backgroundColor = '#4ecdc4';
        backToTopBtn.style.color = '#fff';
        backToTopBtn.style.border = 'none';
        backToTopBtn.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        backToTopBtn.style.cursor = 'pointer';
        backToTopBtn.style.display = 'flex';
        backToTopBtn.style.alignItems = 'center';
        backToTopBtn.style.justifyContent = 'center';
        backToTopBtn.style.fontSize = '1.25rem';
        backToTopBtn.style.opacity = '0';
        backToTopBtn.style.visibility = 'hidden';
        backToTopBtn.style.transition = 'all 0.3s ease';
        backToTopBtn.style.zIndex = '99';
        
        // Show/hide based on scroll position
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.style.opacity = '1';
                backToTopBtn.style.visibility = 'visible';
            } else {
                backToTopBtn.style.opacity = '0';
                backToTopBtn.style.visibility = 'hidden';
            }
        });
        
        // Scroll to top on click
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Hover effect
        backToTopBtn.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#3bafa6';
            this.style.transform = 'translateY(-3px)';
        });
        
        backToTopBtn.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '#4ecdc4';
            this.style.transform = 'translateY(0)';
        });
    }
}
