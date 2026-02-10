(function ($) {
    console.log('WCOC admin-orders.js loaded');

    if ( typeof WCOC_ORDERS === 'undefined' ) {
        console.warn('WCOC_ORDERS not found');
        return;
    }

    console.log('Unread map:', WCOC_ORDERS.unread);

    const unread = WCOC_ORDERS.unread || {};

    function getOrderIdFromRow($row) {

        // HPOS React (future-proof)
        let orderId = $row.data('order-id');
        if ( orderId ) return parseInt(orderId, 10);

        // Classic data-id
        orderId = $row.data('id');
        if ( orderId ) return parseInt(orderId, 10);

        // YOUR CASE: id="order-5966"
        const idAttr = $row.attr('id');
        if ( idAttr && idAttr.indexOf('order-') === 0 ) {
            return parseInt(idAttr.replace('order-', ''), 10);
        }

        // Last fallback: link
        const link = $row.find('a[href*="post="]').attr('href');
        if ( link ) {
            const match = link.match(/post=(\d+)/);
            if ( match ) return parseInt(match[1], 10);
        }

        return null;
    }

    function injectBadges() {

        $('tr').each(function () {

            const $row = $(this);
            const orderId = getOrderIdFromRow($row);

            if ( ! orderId || ! unread[ orderId ] ) return;

            const $firstCell = $row.find('td').first();
            if ( ! $firstCell.length ) return;

            if ( $firstCell.find('.wcoc-unread-badge').length ) return;

            $firstCell.append(
                '<span class="wcoc-unread-badge" title="' +
                unread[orderId] +
                ' unread messages"> ' +
                unread[orderId] +
                '</span>'
            );
        });
    }

    // React renders async → poll
    const interval = setInterval(injectBadges, 500);
    setTimeout(() => clearInterval(interval), 10000);

})(jQuery);