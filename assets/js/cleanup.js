/* Remove named upgrade CTAs while retaining upstream operational fee disclosures. */
(function () {
    'use strict';
    /**
     * Identify the audited upgrade destination.
     * @param {HTMLAnchorElement} link Candidate anchor.
     * @returns {boolean} Whether the link targets the exact WPForms upgrade page.
     */
    function upgradeLink(link) {
        try { var url = new URL(link.href, window.location.href); return url.hostname === 'wpforms.com' && url.pathname === '/lite-upgrade/'; }
        catch (error) { return false; }
    }
    /**
     * Remove named marketing clauses without removing fee information or unrelated notices.
     * @param {Element} root Audited WPForms page root.
     * @returns {void} Changes only the recognized fee notice markup.
     */
    function clean(root) {
        root.querySelectorAll('.wpforms-stripe-notice-info p, p.wpforms-stripe-notice-info, .wpforms-square-notice-info p, p.wpforms-square-notice-info, .wpforms-paypal-commerce-notice-info p, p.wpforms-paypal-commerce-notice-info').forEach(function (notice) {
            var link = Array.from(notice.querySelectorAll('a[href]')).find(upgradeLink);
            if (!link || link.parentElement !== notice) return;
            var range = document.createRange(); range.setStartBefore(link); range.setEnd(notice, notice.childNodes.length); range.deleteContents();
        });
        root.querySelectorAll('.wpforms-empty-payments-fees').forEach(function (notice) {
            var link = Array.from(notice.querySelectorAll('a[href]')).find(upgradeLink);
            var amount = notice.textContent.match(/\d+(?:[.,]\d+)?\s*%/);
            if (link && amount && window.pwflCleanup) notice.textContent = pwflCleanup.fees.replace('%s', amount[0]);
        });
    }
    var roots = document.querySelectorAll('.wpforms-admin-page');
    roots.forEach(function (root) {
        clean(root);
        var scheduled = false;
        new MutationObserver(function () {
            if (scheduled) return; scheduled = true;
            window.requestAnimationFrame(function () { scheduled = false; clean(root); });
        }).observe(root, { childList: true, subtree: true });
    });
}());
