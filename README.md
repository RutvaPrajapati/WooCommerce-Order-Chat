**WooCommerce Admin Customer Chat** is a lightweight, robust communication tool that allows real-time messaging between customers and store administrators directly within WooCommerce orders. It eliminates the need for external support tickets by keeping order-related discussions inside the WordPress dashboard.

**Key Features**
1. For Store Administrators
  a. Order List Badges: Integrated notification badges appear in the WooCommerce Orders list to highlight orders with unread customer messages.
  b. Dedicated Chat Meta Box: A clean chat interface is added to the Order Edit screen, allowing admins to reply to customers instantly.
  c. Permission-Based Access: Uses WordPress capability checks (manage_woocommerce) to ensure only authorized staff can access the admin chat features.

2. For Customers
  a. My Account Integration: A modern chat box is automatically embedded into the "View Order" page.
  b. Unread Indicators: Status updates appear in the "My Orders" table, showing the count of unread messages from the admin.
  c. Mobile Responsive: The chat interface is fully responsive, ensuring a smooth experience on mobile devices.

**Technical & Core Features**
1. HPOS Compatible: Fully supports WooCommerce High-Performance Order Storage (HPOS) for modern, fast database performance.
2. Custom DB Architecture: Uses a dedicated database table (wp_wc_order_chat) for messages to keep the standard WordPress tables lean and fast.
3. Real-time Feel: Implements AJAX polling to fetch new messages automatically every 5 seconds.
4. Email Notifications: Sends automated HTML email alerts to both admins and customers when new messages are received.

**Installation**
1. Upload the woocommerce-order-chat folder to your /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. The plugin will automatically create the necessary database tables upon activation.

**File Structure**
1. includes/class-wcoc-db.php - Database schema and CRUD operations.
2. includes/wcoc-admin.php - Backend UI, meta boxes, and order list integration.
3. includes/wcoc-ajax.php - Core logic for sending/receiving messages and email triggers.
4. includes/wcoc-frontend.php - Customer-facing chat box and My Account hooks.
5. assets/ - Contains all CSS and JS for the admin and frontend interfaces.

**Security**
1. Nonce Verification: All AJAX requests are protected with WordPress nonces to prevent CSRF attacks.
2. Data Sanitization: All user input is sanitized using sanitize_textarea_field before being saved to the database.
3. Ownership Checks: Customers can only view and send messages for orders they personally placed.
