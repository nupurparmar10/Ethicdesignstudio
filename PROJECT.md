# Ethic Design Studio (Store Website & CRM)

## 1. Overview
- **Frontend**: E-Commerce portal.
- **CRM (`/ethic_crm`)**: POS, purchases, ledgers, HR, CMS.
- **Tech Stack**: PHP (`mysqli`), MariaDB, HTML5, CSS3, jQuery, Bootstrap.

## 2. Key Files
- `.htaccess`: Rewrites extensionless URLs.
- `connect.php`: DB/Shiprocket config, sanitization (`sanitize_input`), central `get_valid_shiprocket_token()` interceptor.
- `header.php`: Navigation, auth, cart merge.
- `checkout.php` & `checkout_ajax.php`: Checkout, Razorpay integration, finalize payment.
- `productdetail.php`: Variant viewing, server-side cart.
- `ethic_crm/`: Backend portal scripts (includes `shiprocket_actions.php` & `shiprocket_api.php` for centralized Shiprocket API integration).

## 3. Flows & Business Rules
- **Checkout Flow**: Submit address (AJAX) -> Insert pending `checkout` & `order_item` -> Generate Razorpay Order -> Finalize payment (AJAX) -> Insert `billbook` -> Decrement `webstock` -> Mark `checkout` paid.
- **Shipment Flow**: View Order Details (`vieworderdetails.php`) -> Dynamic action buttons -> `shiprocket_actions.php` (AJAX) -> `shiprocket_api.php` generates AWB/Pickups/Labels/Manifest -> Updates `checkout` table.
- **Invoice IDs**: Auto-incremented (e.g., `EDS/YYYY-MM/XXXX`).
- **Cart Grouping**: Items grouped by `v_id` to prevent duplicate order rows.
- **Security**: CRM requires `$_SESSION['account']`. Passwords use `md5()`.

*For Database schema, see DATABASE.md.*
