(function (window, document, $) {
    'use strict';

    document.documentElement.className += ' ph-modern';

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    ready(function () {
        document.body.classList.add('ph-ready');

        // Give legacy forms immediate, visible feedback without changing payloads.
        var forms = document.querySelectorAll('form:not(#logout-form)');
        Array.prototype.forEach.call(forms, function (form) {
            form.addEventListener('submit', function () {
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) return;
                var submit = form.querySelector('button[type="submit"], input[type="submit"]');
                if (!submit) return;
                submit.classList.add('is-loading');
                submit.setAttribute('aria-busy', 'true');
                if (submit.tagName === 'BUTTON' && !submit.getAttribute('data-original-label')) {
                    submit.setAttribute('data-original-label', submit.innerHTML);
                    submit.innerHTML = '<span class="glyphicon glyphicon-refresh spinning" aria-hidden="true"></span> Memproses…';
                }
            });
        });

        // Better keyboard behavior for the responsive sidebar.
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            var sidebar = document.getElementById('leftsidebar');
            var overlay = document.querySelector('.overlay');
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.style.display = 'none';
        });

        // Automatically make destructive actions clearer to assistive tech.
        var dangerButtons = document.querySelectorAll('.btn-danger, [data-action="delete"]');
        Array.prototype.forEach.call(dangerButtons, function (button) {
            if (!button.getAttribute('aria-label')) {
                button.setAttribute('aria-label', (button.textContent || 'Hapus').trim());
            }
        });
    });

    // DataTables are initialized in legacy views. Improve labels globally before
    // those page scripts run, without re-initializing any table.
    if ($ && $.fn && $.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 10,
            autoWidth: false,
            language: {
                search: '',
                searchPlaceholder: 'Cari data…',
                lengthMenu: 'Tampilkan _MENU_',
                info: '_START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Belum ada data',
                zeroRecords: 'Data tidak ditemukan',
                paginate: { previous: '‹', next: '›' }
            }
        });
    }
})(window, document, window.jQuery);
