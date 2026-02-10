jQuery(document).ready(function($){

    console.log("WCOC admin JS loaded");

    const root = $('#wcoc_admin_box');

    if (!root.length) {
        console.log("Chat box not found");
        return;
    }

    const orderId = root.data('order-id');
    console.log("Order ID:", orderId);

    const box    = $('#wcoc_admin_messages');
    const input  = $('#wcoc_admin_message');
    const button = $('#wcoc_admin_send');

    function loadMessages() {
        console.log("Fetching messages...");

        $.post(WCOC_ADMIN.ajax_url, {
            action: 'wcoc_get_messages',
            nonce: WCOC_ADMIN.nonce,
            order_id: orderId
        }, function(res){
            console.log("Response:", res);

            if (!res.success) return;

            box.empty();

            // res.data.forEach(function(msg){
            //     box.append('<p><strong>'+msg.sender_role+':</strong> '+msg.message+'</p>');
            // });
            res.data.forEach(msg => {

    let sideClass = (msg.sender === 'admin') ? 'wcoc-admin' : 'wcoc-user';
    let label     = (msg.sender === 'admin') ? 'Admin' : 'Customer';

    const html = `
        <div class="wcoc-message ${sideClass}">
            <div class="wcoc-bubble">
                ${msg.message}
            </div>
            <div class="wcoc-time">${msg.created_at}</div>
        </div>
    `;

    box.append(html);

});
        }, 'json');
    }

    function sendMessage(){
        const text = input.val().trim();
        if (!text) return;

        $.post(WCOC_ADMIN.ajax_url, {
            action: 'wcoc_send_message',
            nonce: WCOC_ADMIN.nonce,
            order_id: orderId,
            message: text
        }, function(res){
            console.log("Sent:", res);
            if (res.success) {
                input.val('');
                loadMessages();
            }
        }, 'json');
    }

    button.on('click', function(e){
        e.preventDefault();
        sendMessage();
    });

    loadMessages();
});