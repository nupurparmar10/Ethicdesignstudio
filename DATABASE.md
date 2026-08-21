# Database: `u622759878_ethicstore`

## 1. Catalog & Inventory
- **`item_details`**: Master products (`item_id` PK, `pname`, `status`).
- **`variant`**: SKUs (`v_id` PK, `item_id` FK, `color`, `size`, `edsellrate`, `webstock`).
- **`variant_pic`**: Media gallery (`v_id` FK).
- **`producttype`, `pro_subcategory`, `collection`, `material_type`, `color_code`**: Categorization & mappings.

## 2. Customer & Orders
- **`checkout`**: Orders/Invoices (`order_id`/`check_id` PK, `status`, `payment_type`, `razorpay_order_id`, `amount`, `shiprocket_shipment_id`, `shiprocket_order_id`, `shiprocket_awb_code`, `courier_name`, `shiprocket_status`, `tracking_url`, `label_url`, `manifest_url`).
- **`order_item`**: Line items (`check_id` FK, `v_id` FK, `quantity`, `base_price`).
- **`users`**: Customer web accounts.

## 3. CRM Modules
- **`login`**: Admin credentials (MD5).
- **`billbook` & `bill_items`**: Finalized POS/website invoices.
- **`ledger`, `transaction`**: Double-entry financial ledgers.
- **`sales`, `purchase`, `manu`**: POS, vendor bills, manufacturing.
- **`emp`, `salary`, `attendance`**: HR & Payroll.
- **`matter`, `contact_info`, `faq`**: CMS and store details.

## 4. Key Logic & Rules
- **Relationships**: `variant.item_id` -> `item_details.item_id`; `order_item.check_id` -> `checkout.order_id`.
- **Storefront Catalog**: Filtered by `webstock > 0` and `status = 1`.
- **POS Invoicing**: Modifies `webstock` directly.
