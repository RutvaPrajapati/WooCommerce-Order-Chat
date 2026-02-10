jQuery(function($){
    'use strict';

    // Elements from wcoc-frontend.php
    const boxEl   = $('#wcoc-messages');
    const sendBtn = $('#wcoc-send');
    const textEl  = $('#wcoc-text');
    const root    = $('#wcoc-box');
    const orderId = root.data('order');

    if (!boxEl.length || !root.length) return;

    // Helper: sanitize text for insertion
    function escHtml(str) {
        return $('<div/>').text(str).html();
    }

    // Build bubble HTML (admin left, customer right)
    function renderMessages(messages) {
        boxEl.empty();
        messages.forEach(function(msg){
            // msg may be object or plain; normalize keys
            const role = (msg.sender_role || msg.sender || '').toString().toLowerCase();
            const text = msg.message || msg.msg || msg.text || '';
            const ts   = msg.created_at || msg.time || msg.created || '';

            const sideClass = (role === 'admin' || role === 'administrator') ? 'wcoc-admin' : 'wcoc-user';

            const html = '\
            <div class="wcoc-message '+ sideClass +'">\
                <div class="wcoc-bubble">'+ escHtml(text) +'</div>\
                <div class="wcoc-meta">'+ escHtml(ts) +'</div>\
            </div>';

            boxEl.append(html);
        });

        // scroll to bottom
        boxEl.scrollTop(boxEl[0].scrollHeight);
    }

    // Fetch messages (use POST because PHP expects POST)
    function loadMessages() {
        $.post(WCOC.ajax, {
            action: 'wcoc_get_messages',
            nonce: WCOC.nonce,
            order_id: orderId
        }, function(res){
            if (!res || !res.success) return;

            // support two response shapes:
            // 1) wp_send_json_success( array('messages' => $rows) ) -> res.data.messages
            // 2) wp_send_json_success( $rows ) -> res.data (array)
            const payload = (res.data && res.data.messages) ? res.data.messages : res.data;
            if (!Array.isArray(payload)) return;

            renderMessages(payload);
        }, 'json').fail(function(){ /* ignore errors silently */ });
    }

    // Send message
    function sendMessage() {
        const message = textEl.val().trim();
        if (!message) return;

        sendBtn.prop('disabled', true);

        $.post(WCOC.ajax, {
            action: 'wcoc_send_message',
            nonce: WCOC.nonce,
            order_id: orderId,
            message: message
        }, function(res){
            sendBtn.prop('disabled', false);
            if (res && res.success) {
                textEl.val('');
                loadMessages(); // refresh
            } else {
                alert((res && res.data && res.data.message) ? res.data.message : 'Could not send message');
            }
        }, 'json').fail(function(){
            sendBtn.prop('disabled', false);
            alert('Request failed. Check console/network.');
        });
    }

    // Bind events
    sendBtn.on('click', function(e){
        e.preventDefault();
        sendMessage();
    });

    // also send on Ctrl+Enter
    textEl.on('keydown', function(e){
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') sendMessage();
    });

    // initial load + polling
    loadMessages();
    setInterval(loadMessages, 5000);
});