(function () {
    'use strict';

    var config = window.ipetCheckout || {};
    var updateQueued = false;

    function create(tag, className, text) {
        var node = document.createElement(tag);
        if (className) node.className = className;
        if (typeof text === 'string') node.textContent = text;
        return node;
    }

    function addAccountCard(main) {
        if (main.querySelector('.ipet-checkout-account')) return;

        var card = create('section', 'ipet-checkout-card ipet-checkout-account');
        var copy = create('div', 'ipet-checkout-account-copy');
        copy.appendChild(create('h2', '', config.isLoggedIn ? 'Welcome back' : 'Have an Account?'));
        copy.appendChild(create(
            'p',
            '',
            config.isLoggedIn && config.email
                ? 'You are signed in as ' + config.email + '.'
                : 'Sign in to check out faster or continue as a guest.'
        ));

        var link = create('a', 'ipet-checkout-account-button', config.isLoggedIn ? 'View Your Account' : 'Sign In or Create Account');
        link.href = config.isLoggedIn ? config.accountUrl : config.loginUrl;
        card.appendChild(copy);
        card.appendChild(link);
        main.insertBefore(card, main.firstChild);
    }

    function agreement() {
        var line = create('p', 'ipet-checkout-agreement');
        line.appendChild(document.createTextNode('By placing your order, you agree to our '));
        var privacy = create('a', '', 'Privacy Policy');
        privacy.href = config.privacyUrl || '#';
        line.appendChild(privacy);
        line.appendChild(document.createTextNode(' and '));
        var terms = create('a', '', 'Terms of Use');
        terms.href = config.termsUrl || '#';
        line.appendChild(terms);
        line.appendChild(document.createTextNode('.'));
        return line;
    }

    function addSidebarAction(sidebar) {
        if (sidebar.querySelector('.ipet-checkout-sidebar-action')) return;
        var panel = create('section', 'ipet-checkout-sidebar-action');
        panel.appendChild(agreement());
        var button = create('button', 'ipet-checkout-place-order', 'Place Order');
        button.type = 'button';
        button.addEventListener('click', function () {
            var realButton = document.querySelector('.wc-block-components-checkout-place-order-button');
            if (realButton && !realButton.disabled) realButton.click();
        });
        panel.appendChild(button);
        sidebar.insertBefore(panel, sidebar.firstChild);
    }

    function addTrustCard(sidebar) {
        if (sidebar.querySelector('.ipet-checkout-trust')) return;
        var card = create('section', 'ipet-checkout-trust');
        var heading = create('div', 'ipet-checkout-trust-heading');
        heading.appendChild(create('span', 'ipet-checkout-shield', '✓'));
        var headingCopy = create('div');
        headingCopy.appendChild(create('h3', '', 'Shop with Confidence'));
        headingCopy.appendChild(create('p', '', 'Your order is protected from checkout to delivery.'));
        heading.appendChild(headingCopy);
        card.appendChild(heading);
        var list = create('ul');
        ['Secure payment', '30-day easy returns', 'Trusted pet store', 'Customer support'].forEach(function (item) {
            list.appendChild(create('li', '', item));
        });
        card.appendChild(list);
        sidebar.appendChild(card);
    }

    function addMobileBar() {
        if (document.querySelector('.ipet-checkout-mobile-bar')) return;
        var bar = create('div', 'ipet-checkout-mobile-bar');
        var total = create('div', 'ipet-checkout-mobile-total');
        total.appendChild(create('span', '', 'Order Total'));
        total.appendChild(create('strong', '', '—'));
        var button = create('button', 'ipet-checkout-mobile-button', 'Place Order');
        button.type = 'button';
        button.addEventListener('click', function () {
            var realButton = document.querySelector('.wc-block-components-checkout-place-order-button');
            if (realButton && !realButton.disabled) realButton.click();
        });
        bar.appendChild(total);
        bar.appendChild(button);
        document.body.appendChild(bar);
    }

    function syncItemsCard() {
        var main = document.querySelector('.wc-block-checkout__main');
        var source = document.querySelector('.wc-block-checkout__sidebar .wc-block-components-order-summary');
        if (!main || !source) return;

        var card = main.querySelector('.ipet-checkout-items');
        if (!card) {
            card = create('section', 'ipet-checkout-card ipet-checkout-items');
            card.appendChild(create('h2', '', 'Your Items'));
            card.appendChild(create('div', 'ipet-checkout-items-content'));
            var actions = main.querySelector('.wc-block-checkout__actions');
            var terms = main.querySelector('.wc-block-checkout__terms');
            var anchor = terms || actions;
            var container = anchor && anchor.parentNode ? anchor.parentNode : main;
            container.insertBefore(card, anchor || null);
        }

        var signature = source.innerHTML;
        if (card._ipetSourceSignature === signature) return;
        card._ipetSourceSignature = signature;
        var content = card.querySelector('.ipet-checkout-items-content');
        var clone = source.cloneNode(true);
        clone.classList.remove('is-large');
        clone.classList.add('ipet-checkout-items-list');
        content.replaceChildren(clone);
    }

    function syncCheckoutControls() {
        updateQueued = false;
        syncItemsCard();
        var realButton = document.querySelector('.wc-block-components-checkout-place-order-button');
        var buttons = document.querySelectorAll('.ipet-checkout-place-order, .ipet-checkout-mobile-button');
        var realText = realButton && realButton.textContent.trim() ? realButton.textContent.trim() : 'Place Order';
        buttons.forEach(function (button) {
            var shouldDisable = !realButton || realButton.disabled;
            if (button.disabled !== shouldDisable) button.disabled = shouldDisable;
            if (button.textContent !== realText) button.textContent = realText;
        });

        var total = document.querySelector('.wc-block-components-totals-footer-item .wc-block-components-totals-item__value');
        var mobileTotal = document.querySelector('.ipet-checkout-mobile-total strong');
        if (total && mobileTotal) mobileTotal.textContent = total.textContent.trim();
    }

    function queueSync() {
        if (updateQueued) return;
        updateQueued = true;
        window.requestAnimationFrame(syncCheckoutControls);
    }

    function init() {
        var layout = document.querySelector('.wc-block-components-sidebar-layout');
        var main = document.querySelector('.wc-block-checkout__main');
        var sidebar = document.querySelector('.wc-block-checkout__sidebar');
        if (!layout || !main || !sidebar) {
            window.setTimeout(init, 120);
            return;
        }

        document.body.classList.add('ipet-checkout-ready');
        addAccountCard(main);
        addSidebarAction(sidebar);
        addTrustCard(sidebar);
        addMobileBar();
        syncItemsCard();
        syncCheckoutControls();

        new MutationObserver(queueSync).observe(layout, {
            subtree: true,
            childList: true,
            characterData: true,
            attributes: true,
            attributeFilter: ['disabled', 'aria-disabled']
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
