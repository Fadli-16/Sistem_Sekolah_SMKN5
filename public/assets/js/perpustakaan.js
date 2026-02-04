/**
 * Perpustakaan JS - Handles all JavaScript functionality for the library system
 */
const Perpustakaan = {
    /**
     * Initialize all necessary functionality
     */
    init: function () {
        this.initDataTables();
        this.initFormValidation();
        this.initStatusChanges();
        this.initMobileNavigation();
        this.initViewToggle();
    },

    /**
     * Initialize DataTables with common settings
     */
    initDataTables: function () {
        if ($.fn.DataTable && $("#data-table").length) {
            // Check if this table is already a DataTable
            if (!$.fn.DataTable.isDataTable("#data-table")) {
                // Initialize DataTable
                const dataTable = $("#data-table").DataTable({
                    responsive: true,
                    language: {
                        // Just use an empty string for search label
                        search: "",
                        searchPlaceholder: "Cari buku...",
                        lengthMenu: "<i class='bi bi-list-ul'></i> _MENU_ entri",
                        info: "Menampilkan <b>_START_</b> sampai <b>_END_</b> dari <b>_TOTAL_</b> entri",
                        infoEmpty: "Tidak ada data untuk ditampilkan",
                        infoFiltered: "(disaring dari _MAX_ total entri)",
                        zeroRecords: "Tidak ada data yang cocok dengan pencarian",
                        emptyTable: "Tidak ada data tersedia",
                        paginate: {
                            first: "<i class='bi bi-chevron-double-left'></i>",
                            last: "<i class='bi bi-chevron-double-right'></i>",
                            next: "<i class='bi bi-chevron-right'></i>",
                            previous: "<i class='bi bi-chevron-left'></i>",
                        },
                    },
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, -1],
                        [5, 10, 25, 50, "Semua"],
                    ],
                    columnDefs: [
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false,
                        },
                    ],
                    dom: '<"dt-controls"<"dt-controls-row"<"dt-length"l><"dt-search"f>>>rt<"dt-bottom"<"dt-info"i><"dt-pagination"p>>',
                    responsive: {
                        details: {
                            type: "column",
                            target: "tr",
                        },
                    },
                    initComplete: function () {
                        // Add custom classes for enhanced styling
                        $('.dataTables_filter input').addClass('dt-search-input');
                        $('.dataTables_length select').addClass('dt-length-select');
                        
                        // Configure search input
                        const searchInput = $(".dataTables_filter input");
                        searchInput.attr("autocomplete", "off");
                        
                        // Add animation classes
                        setTimeout(function() {
                            $('.dt-controls').addClass('dt-fade-in');
                            $('.dt-bottom').addClass('dt-fade-in');
                        }, 100);
                        
                        // Enhance select elements with custom styling
                        if ($.fn.select2) {
                            $('.dt-length-select').select2({
                                minimumResultsForSearch: Infinity,
                                dropdownCssClass: 'dt-select-dropdown'
                            });
                        }
                    }
                });
            }
        }
    },

    /**
     * Toggle between grid and table view for books
     */
    initViewToggle: function () {
        const gridView = document.getElementById("gridView");
        const tableView = document.getElementById("tableView");
        const gridViewBtn = document.getElementById("gridViewBtn");
        const tableViewBtn = document.getElementById("tableViewBtn");

        if (!gridView || !tableView || !gridViewBtn || !tableViewBtn) return;

        // Use a more efficient approach to toggle views
        const toggleView = (showGrid) => {
            // Use classList toggle with second parameter for more efficient toggling
            gridView.classList.toggle("d-none", !showGrid);
            tableView.classList.toggle("d-none", showGrid);
            gridViewBtn.classList.toggle("active", showGrid);
            tableViewBtn.classList.toggle("active", !showGrid);
            
            // Save preference
            localStorage.setItem("bookViewPreference", showGrid ? "grid" : "table");
            
            // Reinitialize DataTables if showing table view
            if (!showGrid && $.fn.DataTable && $.fn.DataTable.isDataTable("#data-table")) {
                // Adjust columns for better display
                $("#data-table").DataTable().columns.adjust();
            }
        };

        // Apply saved preference
        const viewPreference = localStorage.getItem("bookViewPreference") || "table";
        toggleView(viewPreference === "grid");

        // Event listeners with optimized handler
        gridViewBtn.addEventListener("click", () => toggleView(true));
        tableViewBtn.addEventListener("click", () => toggleView(false));
    },

    /**
     * Show grid view and hide table view
     */
    showGridView: function (gridView, tableView, gridViewBtn, tableViewBtn) {
        gridView.classList.remove("d-none");
        tableView.classList.add("d-none");
        gridViewBtn.classList.add("active");
        tableViewBtn.classList.remove("active");
    },

    /**
     * Show table view and hide grid view
     */
    showTableView: function (gridView, tableView, gridViewBtn, tableViewBtn) {
        gridView.classList.add("d-none");
        tableView.classList.remove("d-none");
        gridViewBtn.classList.remove("active");
        tableViewBtn.classList.add("active");
    },

    /**
     * Initialize form validation
     */
    initFormValidation: function () {
        // Book selection validation
        const bookSelector = document.getElementById("buku_id");
        if (bookSelector) {
            bookSelector.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.disabled && selectedOption.value !== "") {
                    Swal.fire({
                        title: "Peringatan",
                        text: "Buku ini tidak tersedia untuk dipinjam karena stok habis",
                        icon: "warning",
                        confirmButtonColor: "#4ecdc4",
                    });
                    this.value = "";
                }
            });
        }

        // Year validation for book form
        const yearInput = document.getElementById("tahun_terbit");
        if (yearInput) {
            yearInput.addEventListener("input", function () {
                const year = parseInt(this.value);
                const currentYear = new Date().getFullYear();

                if (year > currentYear) {
                    this.value = currentYear;
                    Swal.fire({
                        title: "Peringatan",
                        text: "Tahun terbit tidak boleh melebihi tahun saat ini",
                        icon: "warning",
                        confirmButtonColor: "#4ecdc4",
                    });
                }
            });
        }
    },

    /**
     * Initialize status change confirmations for borrowing
     */
    initStatusChanges: function () {
        const statusSelector = document.getElementById("status");
        if (statusSelector) {
            statusSelector.addEventListener("change", function () {
                const newStatus = this.value;
                const currentStatus = document.querySelector(
                    "[data-current-status]"
                )?.dataset.currentStatus;
                const bookStock =
                    document.querySelector("[data-book-stock]")?.dataset
                        .bookStock;

                if (newStatus !== currentStatus) {
                    if (newStatus === "Disetujui" && parseInt(bookStock) < 1) {
                        Swal.fire({
                            title: "Peringatan",
                            text: "Stok buku ini habis. Apakah Anda yakin ingin menyetujui peminjaman?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#4ecdc4",
                            cancelButtonColor: "#dc3545",
                            confirmButtonText: "Ya, Setujui",
                            cancelButtonText: "Batal",
                        }).then((result) => {
                            if (!result.isConfirmed) {
                                this.value = currentStatus;
                            }
                        });
                    }
                }
            });
        }
    },

    /**
     * Enhance mobile navigation
     */
    initMobileNavigation: function () {
        const navbar = document.querySelector(".navbar");
        if (navbar) {
            const toggleButton = navbar.querySelector(".navbar-toggler");
            const navbarCollapse = navbar.querySelector(".navbar-collapse");

            if (toggleButton && navbarCollapse) {
                toggleButton.addEventListener("click", function () {
                    document.body.classList.toggle("nav-open");
                });

                // Close navbar when clicking outside
                document.addEventListener("click", function (event) {
                    if (
                        document.body.classList.contains("nav-open") &&
                        !navbar.contains(event.target)
                    ) {
                        document.body.classList.remove("nav-open");
                        navbarCollapse.classList.remove("show");
                    }
                });
            }
        }
    },

    /**
     * Handle book deletion confirmations
     */
    confirmDelete: function (id) {
        // Cache the form element to avoid repeated DOM queries
        const form = document.getElementById("deleteForm" + id);
        if (!form) return;
        
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4ecdc4",
            cancelButtonColor: "#dc3545",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    },

    /**
     * Handle logout confirmation
     */
    logout: function () {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Anda akan keluar dari akun",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#4ecdc4",
            cancelButtonColor: "#dc3545",
            confirmButtonText: "Ya, Logout!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("logoutForm").submit();
            }
        });
    },
};

// Initialize when the document is ready
document.addEventListener("DOMContentLoaded", function () {
    Perpustakaan.init();
});
