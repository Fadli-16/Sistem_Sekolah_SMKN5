/**
 * Super Admin JS - Handles all JavaScript functionality for the super admin panel
 */
const SuperAdmin = {
    /**
     * Initialize all necessary functionality
     */
    init: function () {
        this.initSidebar();
        this.initUserDropdown();
        this.initTooltips();
        this.initDataTables();
        this.initCharts();
    },

    /**
     * Handle sidebar toggle functionality
     */
    initSidebar: function () {
        const menuToggle = document.querySelector(".sa-menu-toggle");
        const wrapper = document.querySelector(".sa-wrapper");
        const sidebar = document.querySelector(".sa-sidebar");
        const overlay = document.createElement("div");
        overlay.className = "sa-sidebar-overlay";
        document.body.appendChild(overlay);

        // Check if the sidebar state is stored in localStorage
        const sidebarCollapsed =
            localStorage.getItem("sa-sidebar-collapsed") === "true";

        // Apply the stored state on page load
        if (sidebarCollapsed) {
            wrapper.classList.add("sa-sidebar-collapsed");
        }

        if (menuToggle) {
            menuToggle.addEventListener("click", function () {
                // Toggle the collapsed state
                wrapper.classList.toggle("sa-sidebar-collapsed");

                // Store the current state in localStorage
                localStorage.setItem(
                    "sa-sidebar-collapsed",
                    wrapper.classList.contains("sa-sidebar-collapsed")
                );

                // For mobile view (additional handling)
                if (window.innerWidth <= 768) {
                    wrapper.classList.toggle("sa-sidebar-show");

                    if (wrapper.classList.contains("sa-sidebar-show")) {
                        document.body.style.overflow = "hidden";
                        overlay.style.display = "block";
                    } else {
                        document.body.style.overflow = "";
                        overlay.style.display = "none";
                    }
                }
            });
        }

        // Close sidebar when clicking on overlay
        overlay.addEventListener("click", function () {
            wrapper.classList.remove("sa-sidebar-show");
            document.body.style.overflow = "";
            overlay.style.display = "none";
        });

        // Handle window resize
        window.addEventListener("resize", function () {
            if (window.innerWidth > 768) {
                wrapper.classList.remove("sa-sidebar-show");
                document.body.style.overflow = "";
                overlay.style.display = "none";
            }
        });

        // Initially hide overlay
        overlay.style.display = "none";
    },

    /**
     * Initialize user dropdown functionality
     */
    initUserDropdown: function () {
        const userDropdown = document.querySelector(".sa-user-dropdown");
        const dropdownMenu = document.querySelector(".sa-dropdown-menu");

        if (userDropdown && dropdownMenu) {
            userDropdown.addEventListener("click", function (e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle("sa-show");
            });

            // Close dropdown when clicking outside
            document.addEventListener("click", function () {
                dropdownMenu.classList.remove("sa-show");
            });
        }
    },

    /**
     * Initialize tooltips
     */
    initTooltips: function () {
        const tooltips = document.querySelectorAll('[data-toggle="tooltip"]');

        if (tooltips.length && typeof bootstrap !== "undefined") {
            tooltips.forEach((tooltip) => {
                new bootstrap.Tooltip(tooltip);
            });
        }
    },

    /**
     * Initialize DataTables if available
     */
    initDataTables: function () {
        if ($.fn.DataTable && $(".sa-datatable").length) {
            $(".sa-datatable").each(function () {
                const table = $(this);

                if (!$.fn.DataTable.isDataTable(table)) {
                    table.DataTable({
                        responsive: true,
                        language: {
                            search: "",
                            searchPlaceholder: "Search...",
                            lengthMenu: "_MENU_ per page",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            infoEmpty: "Showing 0 to 0 of 0 entries",
                            infoFiltered: "(filtered from _MAX_ total entries)",
                            paginate: {
                                first: '<i class="bi bi-chevron-double-left"></i>',
                                last: '<i class="bi bi-chevron-double-right"></i>',
                                next: '<i class="bi bi-chevron-right"></i>',
                                previous: '<i class="bi bi-chevron-left"></i>',
                            },
                        },
                    });

                    // Add the search icon to the search input
                    const searchInput = table
                        .closest(".dataTables_wrapper")
                        .find(".dataTables_filter input");
                    searchInput.addClass("sa-search-input");
                    table
                        .closest(".dataTables_wrapper")
                        .find(".dataTables_filter label")
                        .append('<i class="bi bi-search sa-search-icon"></i>');
                }
            });
        }
    },

    /**
     * Initialize Charts if available
     */
    initCharts: function () {
        if (typeof Chart !== "undefined") {
            // Users chart
            const usersChart = document.getElementById("usersChart");
            if (usersChart) {
                new Chart(usersChart, {
                    type: "line",
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                        datasets: [
                            {
                                label: "Users",
                                data: [12, 19, 25, 31, 42, 47],
                                borderColor: "#3498db",
                                backgroundColor: "rgba(52, 152, 219, 0.1)",
                                tension: 0.4,
                                fill: true,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "top",
                            },
                            title: {
                                display: true,
                                text: "User Growth",
                            },
                        },
                    },
                });
            }

            // Modules usage chart
            const modulesChart = document.getElementById("modulesChart");
            if (modulesChart) {
                new Chart(modulesChart, {
                    type: "doughnut",
                    data: {
                        labels: [
                            "PPDB",
                            "Academic",
                            "Library",
                            "Laboratory",
                            "Internship",
                        ],
                        datasets: [
                            {
                                data: [25, 30, 20, 15, 10],
                                backgroundColor: [
                                    "#3498db",
                                    "#2ecc71",
                                    "#e74c3c",
                                    "#f39c12",
                                    "#9b59b6",
                                ],
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "bottom",
                            },
                            title: {
                                display: true,
                                text: "Module Usage",
                            },
                        },
                    },
                });
            }
        }
    },

    /**
     * Confirm delete with SweetAlert if available
     */
    confirmDelete: function (id, formId = "deleteForm") {
        // pastikan Swal.fire tersedia
        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
            Swal.fire({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`${formId}${id}`).submit();
                }
            });
        } else {
            // fallback ke confirm bawaan browser
            if (
                confirm(
                    "Are you sure you want to delete this item? This action cannot be undone."
                )
            ) {
                document.getElementById(`${formId}${id}`).submit();
            }
        }
    },

    /**
     * Show a toast notification
     */
    showToast: function (message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `sa-toast sa-toast-${type}`;
        toast.innerHTML = `
            <div class="sa-toast-icon">
                <i class="bi bi-${
                    type === "success"
                        ? "check-circle"
                        : type === "danger"
                        ? "exclamation-circle"
                        : "info-circle"
                }"></i>
            </div>
            <div class="sa-toast-content">${message}</div>
            <button class="sa-toast-close"><i class="bi bi-x"></i></button>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add("sa-show");
        }, 100);

        const closeBtn = toast.querySelector(".sa-toast-close");
        closeBtn.addEventListener("click", () => {
            toast.classList.remove("sa-show");
            setTimeout(() => {
                toast.remove();
            }, 300);
        });

        setTimeout(() => {
            toast.classList.remove("sa-show");
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    },

    /**
     * Log out the user
     */
    logout: function () {
        if (typeof Swal !== "undefined" && typeof Swal.fire === "function") {
            Swal.fire({
                title: "Log Out?",
                text: "Are you sure you want to log out?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, log out",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("logoutForm").submit();
                }
            });
        } else {
            // fallback
            if (confirm("Are you sure you want to log out?")) {
                document.getElementById("logoutForm").submit();
            }
        }
    },
};

// Initialize when the document is ready
document.addEventListener("DOMContentLoaded", function () {
    SuperAdmin.init();
});
