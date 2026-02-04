document.addEventListener("DOMContentLoaded", function () {
    // Initialize Swiper slider
    const heroSwiper = new Swiper(".hero-slider", {
        loop: true,
        grabCursor: true,
        effect: "fade",
        fadeEffect: {
            crossFade: true,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    // Counter animation
    const counters = document.querySelectorAll(".counter");
    const speed = 200; // Lower = faster

    // Function to animate counters when they are in viewport
    function animateCounters() {
        counters.forEach((counter) => {
            const updateCount = () => {
                const target =
                    +counter.getAttribute("data-target") || +counter.innerText;
                const count = +counter.innerText;

                // Lower increment for slower animation
                const increment = target / speed;

                // If counter is not at target, increment
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 10);
                } else {
                    counter.innerText = target;
                }
            };

            updateCount();
        });
    }

    // Run counter animation when page loads
    animateCounters();

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();

            const targetId = this.getAttribute("href");
            if (targetId === "#") return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });

    // Intersection Observer for revealing elements on scroll
    const revealElements = document.querySelectorAll(
        ".feature-card, .lab-card, .stat-card"
    );

    if ("IntersectionObserver" in window) {
        const revealObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("revealed");
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.15,
            }
        );

        revealElements.forEach((element) => {
            revealObserver.observe(element);
            element.classList.add("reveal-item"); // Add initial class for CSS transitions
        });
    } else {
        // Fallback for browsers without IntersectionObserver
        revealElements.forEach((element) => {
            element.classList.add("revealed");
        });
    }

    // Add CSS for reveal animations
    const style = document.createElement("style");
    style.textContent = `
        .reveal-item {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .revealed {
            opacity: 1;
            transform: translateY(0);
        }
    `;
    document.head.appendChild(style);
});
