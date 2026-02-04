document.addEventListener("DOMContentLoaded", function () {
    // Sticky header functionality
    const mainHeader = document.querySelector(".main-header");
    const topBarHeight = document.querySelector(".top-bar")?.offsetHeight || 0;

    window.addEventListener("scroll", function () {
        if (window.scrollY > topBarHeight) {
            mainHeader.classList.add("sticky");
            document.body.style.paddingTop = mainHeader.offsetHeight + "px";
        } else {
            mainHeader.classList.remove("sticky");
            document.body.style.paddingTop = 0;
        }
    });

    // Mobile menu toggle
    const menuToggle = document.querySelector(".menu-toggle");
    const mainNavigation = document.querySelector(".main-navigation");

    if (menuToggle && mainNavigation) {
        menuToggle.addEventListener("click", function () {
            this.classList.toggle("menu-active");
            mainNavigation.classList.toggle("menu-active");
            document.body.classList.toggle("menu-open");
        });
    }

    // Mobile dropdown toggle
    const dropdownItems = document.querySelectorAll(".nav-item.has-dropdown");

    if (window.innerWidth <= 991) {
        dropdownItems.forEach((item) => {
            const link = item.querySelector(".nav-link");

            link.addEventListener("click", function (e) {
                if (window.innerWidth <= 991) {
                    e.preventDefault();
                    item.classList.toggle("active");
                }
            });
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener("click", function (e) {
        if (
            mainNavigation.classList.contains("menu-active") &&
            !e.target.closest(".main-navigation") &&
            !e.target.closest(".menu-toggle")
        ) {
            menuToggle.classList.remove("menu-active");
            mainNavigation.classList.remove("menu-active");
            document.body.classList.remove("menu-open");
        }
    });

    // Close mobile menu when window is resized
    window.addEventListener("resize", function () {
        if (
            window.innerWidth > 991 &&
            mainNavigation.classList.contains("menu-active")
        ) {
            menuToggle.classList.remove("menu-active");
            mainNavigation.classList.remove("menu-active");
            document.body.classList.remove("menu-open");
        }
    });
});
