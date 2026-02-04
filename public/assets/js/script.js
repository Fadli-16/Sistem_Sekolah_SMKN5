/**
 * Main JavaScript functionality for the SMK Padang School System
 */

// Initialize when document is ready
document.addEventListener("DOMContentLoaded", function () {
    // Initialize any necessary components
    initializeComponents();

    // Handle animations for elements
    handleAnimations();

    // Initialize event listeners
    setupEventListeners();
});

/**
 * Initialize all necessary components
 */
function initializeComponents() {
    // Initialize DataTables if it exists
    if (typeof $.fn.DataTable !== "undefined" && $("#data-table").length > 0) {
        $("#data-table").DataTable({
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya",
                },
            },
        });
    }

    // Initialize FullCalendar if it exists
    if (typeof $.fn.fullCalendar !== "undefined" && $("#calendar").length > 0) {
        $("#calendar").fullCalendar({
            header: {
                left: "prev,next today",
                center: "title",
                right: "month,agendaWeek,agendaDay",
            },
            themeSystem: "bootstrap4",
            events: "jadwal-events.php",
            eventLimit: true,
            eventColor: "#004080",
            timeFormat: "H:mm",
            locale: "id",
        });
    }
}

/**
 * Handle animations for elements
 */
function handleAnimations() {
    // Animate elements when they come into view
    if ("IntersectionObserver" in window) {
        const animatedElements =
            document.querySelectorAll(".animate-on-scroll");

        const animationObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("animated");
                        animationObserver.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1 }
        );

        animatedElements.forEach((element) => {
            animationObserver.observe(element);
        });
    } else {
        // Fallback for browsers that don't support IntersectionObserver
        document.querySelectorAll(".animate-on-scroll").forEach((element) => {
            element.classList.add("animated");
        });
    }
}

/**
 * Set up event listeners
 */
function setupEventListeners() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            const targetId = this.getAttribute("href");

            if (targetId === "#") return;

            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                e.preventDefault();
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: "smooth",
                });
            }
        });
    });

    // Add other event listeners as needed
}

/**
 * Show confirmation dialog using SweetAlert if available
 * @param {string} title - Title of confirmation dialog
 * @param {string} text - Message text
 * @param {string} icon - Icon to display (warning, error, success, info)
 * @param {function} confirmCallback - Function to call when confirmed
 * @param {function} cancelCallback - Function to call when canceled
 */
function showConfirmation(title, text, icon, confirmCallback, cancelCallback) {
    if (typeof swal !== "undefined") {
        swal({
            title: title,
            text: text,
            icon: icon || "warning",
            buttons: true,
            dangerMode: true,
        }).then((willConfirm) => {
            if (willConfirm && typeof confirmCallback === "function") {
                confirmCallback();
            } else if (!willConfirm && typeof cancelCallback === "function") {
                cancelCallback();
            }
        });
    } else {
        // Fallback to standard confirm dialog if SweetAlert is not available
        if (confirm(title + "\n" + text)) {
            if (typeof confirmCallback === "function") {
                confirmCallback();
            }
        } else {
            if (typeof cancelCallback === "function") {
                cancelCallback();
            }
        }
    }
}

// Export functions for global use
window.showConfirmation = showConfirmation;
