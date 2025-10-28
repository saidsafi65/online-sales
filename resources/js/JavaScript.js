// Modern Sales System JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Form validation enhancement
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                // Focus on first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
            form.classList.add('was-validated');
        });
    });

    // Loading states for buttons
    document.querySelectorAll('.btn-loading').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التحميل...';
            this.disabled = true;

            // Re-enable after 3 seconds (adjust as needed)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 3000);
        });
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const targetTable = document.querySelector(this.dataset.target);

            if (targetTable) {
                const rows = targetTable.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });
    });

    // Number formatting for currency inputs
    const currencyInputs = document.querySelectorAll('.currency-input');
    currencyInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            let value = this.value.replace(/[^\d.]/g, '');
            if (value) {
                this.value = parseFloat(value).toFixed(2);
            }
        });
    });

    // Dark mode toggle (if needed)
    const darkModeToggle = document.querySelector('#darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        });

        // Load saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
    }

    // Sidebar toggle for mobile
    const sidebarToggle = document.querySelector('#sidebarToggle');
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }

    // Animate numbers (counter effect)
    const animateNumbers = function() {
        const numbers = document.querySelectorAll('.animate-number');
        numbers.forEach(function(number) {
            const target = parseInt(number.dataset.target || number.textContent);
            const duration = parseInt(number.dataset.duration || 2000);
            const increment = target / (duration / 16);
            let current = 0;

            const timer = setInterval(function() {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                number.textContent = Math.floor(current).toLocaleString('ar-SA');
            }, 16);
        });
    };

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');

                // Trigger number animation if element has animate-number class
                if (entry.target.classList.contains('animate-number')) {
                    animateNumbers();
                }
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.fade-in-up, .animate-number').forEach(function(el) {
        observer.observe(el);
    });

    // Print functionality
    window.printPage = function() {
        window.print();
    };

    // Export functionality
    window.exportData = function(format, tableId) {
        const table = document.querySelector('#' + tableId);
        if (!table) return;

        if (format === 'csv') {
            exportToCSV(table);
        } else if (format === 'excel') {
            exportToExcel(table);
        }
    };

    function exportToCSV(table) {
        const rows = table.querySelectorAll('tr');
        const csv = [];

        rows.forEach(function(row) {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            cols.forEach(function(col) {
                rowData.push('"' + col.textContent.trim() + '"');
            });
            csv.push(rowData.join(','));
        });

        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'data.csv';
        link.click();
    }

    // Confirmation dialogs
    window.confirmAction = function(message, callback) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'تأكيد',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed && callback) {
                    callback();
                }
            });
        } else {
            if (confirm(message) && callback) {
                callback();
            }
        }
    };

    // Success message
    window.showSuccess = function(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'نجح!',
                text: message,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    };

    // Error message
    window.showError = function(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'خطأ!',
                text: message,
                icon: 'error'
            });
        } else {
            alert(message);
        }
    };
});

// Utility functions
const Utils = {
    // Format currency
    formatCurrency: function(amount, currency = 'شيكل') {
        return parseFloat(amount).toLocaleString('ar-SA', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ' + currency;
    },

      // Format date
    formatDate: function(date, format = 'YYYY-MM-DD') {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');

        switch(format) {
            case 'DD/MM/YYYY':
                return `${day}/${month}/${year}`;
            case 'MM/DD/YYYY':
                return `${month}/${day}/${year}`;
            case 'YYYY-MM-DD':
            default:
                return `${year}-${month}-${day}`;
        }
    },

    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Generate random ID
    generateId: function() {
        return Math.random().toString(36).substr(2, 9);
    },

    // Validate email
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    // Copy to clipboard
    copyToClipboard: function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                this.showToast('تم النسخ بنجاح', 'success');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            this.showToast('تم النسخ بنجاح', 'success');
        }
    },

    // Show toast notification
    showToast: function(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${this.getToastIcon(type)} me-2"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    },

    getToastIcon: function(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
};

// Make Utils globally available
window.Utils = Utils;
