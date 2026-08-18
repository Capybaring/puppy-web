(function () {
    'use strict';

    var previousFocus = null;

    function buildLogoutDialog() {
        var dialog = document.createElement('div');
        dialog.className = 'ipet-logout-dialog';
        dialog.hidden = true;
        dialog.innerHTML = '<div class="ipet-logout-backdrop" data-logout-cancel></div>' +
            '<section class="ipet-logout-modal" role="dialog" aria-modal="true" aria-labelledby="ipet-logout-title" aria-describedby="ipet-logout-copy">' +
                '<span class="ipet-logout-modal-icon" aria-hidden="true">↪</span>' +
                '<h2 id="ipet-logout-title">Log out of your account?</h2>' +
                '<p id="ipet-logout-copy">You will need to sign in again to view your orders and account details.</p>' +
                '<div class="ipet-logout-actions">' +
                    '<button type="button" class="ipet-logout-cancel" data-logout-cancel>Stay signed in</button>' +
                    '<a class="ipet-logout-confirm" href="#">Log out</a>' +
                '</div>' +
            '</section>';
        document.body.appendChild(dialog);
        return dialog;
    }

    function closeDialog(dialog) {
        dialog.hidden = true;
        document.body.classList.remove('ipet-dialog-open');
        if (previousFocus) previousFocus.focus();
    }

    function openDialog(dialog, url, trigger) {
        previousFocus = trigger;
        dialog.querySelector('.ipet-logout-confirm').href = url;
        dialog.hidden = false;
        document.body.classList.add('ipet-dialog-open');
        dialog.querySelector('.ipet-logout-cancel').focus();
    }

    function init() {
        var dialog = buildLogoutDialog();
        var menuToggle = document.querySelector('.ipet-account-menu-toggle');
        var navigation = document.querySelector('.ipet-account-navigation');

        if (menuToggle && navigation) {
            menuToggle.addEventListener('click', function () {
                var open = navigation.classList.toggle('is-open');
                menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        document.addEventListener('click', function (event) {
            var logout = event.target.closest && event.target.closest('a[href*="customer-logout"], a[href*="action=logout"]');
            if (logout && !logout.classList.contains('ipet-logout-confirm')) {
                event.preventDefault();
                openDialog(dialog, logout.href, logout);
                return;
            }
            if (event.target.closest && event.target.closest('[data-logout-cancel]')) {
                event.preventDefault();
                closeDialog(dialog);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !dialog.hidden) closeDialog(dialog);
            if (event.key === 'Tab' && !dialog.hidden) {
                var focusable = dialog.querySelectorAll('button, a[href]');
                var first = focusable[0];
                var last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
}());
