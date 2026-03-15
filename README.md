WooCommerce Order Chat is a lightweight, robust communication tool that allows real-time messaging between customers and store administrators directly within WooCommerce orders. It eliminates the need for external support tickets by keeping order-related discussions inside the WordPress dashboard.

🚀 Key Features
For Store Administrators
Order List Badges: Integrated notification badges appear in the WooCommerce Orders list to highlight orders with unread customer messages.

Dedicated Chat Meta Box: A clean chat interface is added to the Order Edit screen, allowing admins to reply to customers instantly.

Permission-Based Access: Uses WordPress capability checks (manage_woocommerce) to ensure only authorized staff can access the admin chat features.

For Customers
My Account Integration: A modern chat box is automatically embedded into the "View Order" page.

Unread Indicators: Status updates appear in the "My Orders" table, showing the count of unread messages from the admin.

Mobile Responsive: The chat interface is fully responsive, ensuring a smooth experience on mobile devices.

Technical & Core Features
HPOS Compatible: Fully supports WooCommerce High-Performance Order Storage (HPOS) for modern, fast database performance.

Custom DB Architecture: Uses a dedicated database table (wp_wc_order_chat) for messages to keep the standard WordPress tables lean and fast.

Real-time Feel: Implements AJAX polling to fetch new messages automatically every 5 seconds.

Email Notifications: Sends automated HTML email alerts to both admins and customers when new messages are received.

🛠 Installation
Upload the woocommerce-order-chat folder to your /wp-content/plugins/ directory.

Activate the plugin through the 'Plugins' menu in WordPress.

The plugin will automatically create the necessary database tables upon activation.

📂 File Structure
includes/class-wcoc-db.php - Database schema and CRUD operations.

includes/wcoc-admin.php - Backend UI, meta boxes, and order list integration.

includes/wcoc-ajax.php - Core logic for sending/receiving messages and email triggers.

includes/wcoc-frontend.php - Customer-facing chat box and My Account hooks.

assets/ - Contains all CSS and JS for the admin and frontend interfaces.

🔒 Security
Nonce Verification: All AJAX requests are protected with WordPress nonces to prevent CSRF attacks.

Data Sanitization: All user input is sanitized using sanitize_textarea_field before being saved to the database.

Ownership Checks: Customers can only view and send messages for orders they personally placed.

Version: 1.0.0
