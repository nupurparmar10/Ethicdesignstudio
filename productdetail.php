<?php
ob_start();
session_start();
include_once("connect.php");

$item_id = get_input('item_id', 'int', 0, 'request', ['min' => 1]);
if ($item_id <= 0) {
    header("Location: index");
    exit;
}

$product_stmt = mysqli_prepare($con, "SELECT * FROM item_details WHERE item_id = ? AND status = 1 LIMIT 1");
mysqli_stmt_bind_param($product_stmt, "i", $item_id);
mysqli_stmt_execute($product_stmt);
$product_result = mysqli_stmt_get_result($product_stmt);
$product = mysqli_fetch_assoc($product_result);
mysqli_stmt_close($product_stmt);

if (!$product) {
    header("Location: index");
    exit;
}

// Server-side validation and add to cart
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') 
{
    header('Content-Type: application/json');
    if (!isset($_SESSION['u_id'])) {
        echo json_encode(['status' => 'error', 'redirect' => 'register']);
        exit;
    }
    
    $color = isset($_POST['color']) ? trim($_POST['color']) : '';
    $size = isset($_POST['size']) ? trim($_POST['size']) : '';
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
    
    if ($qty <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Quantity must be at least 1.']);
        exit;
    }
    
    $color_escaped = mysqli_real_escape_string($con, $color);
    $size_escaped = mysqli_real_escape_string($con, $size);
    $item_id_escaped = mysqli_real_escape_string($con, $item_id);
    
    $debug_query_sql = "SELECT v_id, webstock FROM variant WHERE item_id = '$item_id_escaped' AND color = '$color_escaped' AND size = '$size_escaped' LIMIT 1 ";
    $query = mysqli_query($con, $debug_query_sql);
    if ($row = mysqli_fetch_assoc($query)) 
    {
        $v_id = (int)$row['v_id'];
        $webstock = (int)$row['webstock'];
        
        $u_id = (int)$_SESSION['u_id'];
        
        // Check existing cart quantity
        $check_query = mysqli_query($con, "SELECT cart_id, quantity FROM cart WHERE u_id = $u_id AND v_id = $v_id LIMIT 1");
        if ($row_cart = mysqli_fetch_assoc($check_query)) {
            $cart_id = (int)$row_cart['cart_id'];
            $existing_qty = (int)$row_cart['quantity'];
            $new_qty = $existing_qty + $qty;
            
            if ($new_qty > $webstock) {
                if ($existing_qty >= $webstock) {
                    echo json_encode(['status' => 'error', 'message' => "You already have the maximum available stock ($webstock) in your cart.", 'debug_query' => $debug_query_sql]);
                    exit;
                } else {
                    $new_qty = $webstock;
                }
            }
            
            $update = mysqli_query($con, "UPDATE cart SET quantity = $new_qty WHERE cart_id = $cart_id");
            if ($update) {
                echo json_encode(['status' => 'success', 'message' => 'Cart updated successfully.', 'debug_query' => $debug_query_sql]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update cart.', 'debug_query' => $debug_query_sql]);
            }
            exit;
        } else {
            if ($qty > $webstock) {
                $qty = $webstock;
            }
            
            $insert = mysqli_query($con, "INSERT INTO cart (u_id, v_id, size, color, quantity) VALUES ($u_id, $v_id, '$size_escaped', '$color_escaped', $qty)");
            if ($insert) {
                echo json_encode(['status' => 'success', 'message' => 'Product added to cart.', 'debug_query' => $debug_query_sql]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add to cart.', 'debug_query' => $debug_query_sql]);
            }
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Selected product variant is not available.', 'debug_query' => $debug_query_sql]);
        exit;
    }
}

if (isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    header('Content-Type: application/json');
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? strip_tags(trim($_POST['email'])) : '';
    $title = isset($_POST['title']) ? strip_tags(trim($_POST['title'])) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';
    $star = isset($_POST['star']) ? (int)$_POST['star'] : 0;
    
    // Sanitize using sanitize_input
    $name = sanitize_input($name, 'string');
    $email = sanitize_input($email, 'email');
    $title = sanitize_input($title, 'string');
    $message = sanitize_input($message, 'string');
    
    if (empty($name) || empty($email) || empty($title) || empty($message) || $star < 1 || $star > 5) {
        echo json_encode(['status' => 'error', 'message' => 'All fields and a valid star rating are required. HTML tags are not allowed.']);
        exit;
    }
    
    $name_escaped = mysqli_real_escape_string($con, $name);
    $email_escaped = mysqli_real_escape_string($con, $email);
    $title_escaped = mysqli_real_escape_string($con, $title);
    $message_escaped = mysqli_real_escape_string($con, $message);
    $item_id_escaped = mysqli_real_escape_string($con, $item_id);
    
    $insert = mysqli_query($con, "INSERT INTO reviews (name, email, title, message, star, status, item_id) VALUES ('$name_escaped', '$email_escaped', '$title_escaped', '$message_escaped', $star, 0, '$item_id_escaped')");
    if ($insert) {
        echo json_encode(['status' => 'success', 'message' => 'Review submitted successfully. Thank you!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit review. Please try again.']);
    }
    exit;
}

$variant_stock = [];
$variant_vids = [];
$variant_images = [];
$available_colors = [];
$available_sizes = [];
$display_price = $stock='';
$display_sku = $product['pcode'] ?? '';
$product_name = $product['saledesp'] ?? '';
$product_description = $product['product_desp'] ?? '';
$product_type=$product['ptype'] ?? '';

//material & collection
$material = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM material_type WHERE m_id='$product[material_type]'"));
$collection = mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM collection WHERE c_id='$product[collection]'"));

$variant_result = mysqli_query($con, "SELECT v.*, cc.color_code FROM variant v LEFT JOIN color_code cc ON cc.color_name = v.standard_color  WHERE v.item_id = '$item_id' AND v.webstock > 0 ORDER BY v.v_id ASC");
while ($variant = mysqli_fetch_assoc($variant_result)) 
{
    if ($display_price === '' && $variant['edsellrate'] !== '') {
        $display_price = $variant['edsellrate'];
    }

    $variant_stock[$variant['color']][$variant['size']] = $variant['webstock'];
    $variant_vids[$variant['color']][$variant['size']] = $variant['v_id'];
   
    if (!empty($variant['color'])) {
        $available_colors[$variant['color']] = $variant['color_code'] ?: '';
    }
    if (!empty($variant['size'])) {
        $available_sizes[$variant['size']] = $variant['size'];
    }

    $pic_result = mysqli_query($con, "SELECT pic FROM variant_pic WHERE v_id = '$variant[v_id]'");
    while ($pic = mysqli_fetch_assoc($pic_result)) {
        if (!empty($pic['pic'])) {
            $pic_src = file_exists('ethic_crm/' . $pic['pic']) ? 'ethic_crm/' . $pic['pic'] : $pic['pic'];
            $variant_images[$pic_src] = $pic_src;
        }
    }
}

if (empty($variant_images)) {
    $variant_images[] = 'pic_not_found.png';
}
$first_color = !empty($available_colors) ? array_key_first($available_colors) : '';
$first_size = !empty($available_sizes) ? reset($available_sizes) : '';
$first_stock = 0;
if ($first_color != '' && $first_size != '' && isset($variant_stock[$first_color][$first_size])) {
    $first_stock = $variant_stock[$first_color][$first_size];
}
?>

<?php
// Related / "You may also like" products
$related_products = [];
$related_stmt = mysqli_prepare($con, "SELECT item_id, saledesp FROM item_details WHERE item_id != ? AND status = 1 ORDER BY RAND() LIMIT 12");
mysqli_stmt_bind_param($related_stmt, "i", $item_id);
mysqli_stmt_execute($related_stmt);
$related_result = mysqli_stmt_get_result($related_stmt);
while ($rp = mysqli_fetch_assoc($related_result)) {
    $rp_id = (int) $rp['item_id'];

    $rp_price = '';
    $rp_sizes = [];
    $rp_images = [];

    $rp_variant_result = mysqli_query($con, "SELECT v_id, size, edsellrate FROM variant WHERE item_id = '$rp_id' AND webstock > 0 ORDER BY v_id ASC");
    while ($rv = mysqli_fetch_assoc($rp_variant_result)) {
        if ($rp_price === '' && $rv['edsellrate'] !== '') {
            $rp_price = $rv['edsellrate'];
        }
        if (!empty($rv['size'])) {
            $rp_sizes[$rv['size']] = $rv['size'];
        }

        // Collect distinct pictures across this item's variants so we can use
        // the 2nd image as the hover image when the item has more than 1.
        $rp_pic_result = mysqli_query($con, "SELECT pic FROM variant_pic WHERE v_id = '$rv[v_id]'");
        while ($rpic = mysqli_fetch_assoc($rp_pic_result)) {
            if (!empty($rpic['pic'])) {
                $rp_pic_src = file_exists('ethic_crm/' . $rpic['pic']) ? 'ethic_crm/' . $rpic['pic'] : $rpic['pic'];
                if (!in_array($rp_pic_src, $rp_images, true)) {
                    $rp_images[] = $rp_pic_src;
                }
            }
        }
    }

    if (empty($rp_images)) {
        $rp_images[] = 'pic_not_found.png';
    }

    $related_products[] = [
        'item_id'     => $rp_id,
        'name'        => $rp['saledesp'],
        'price'       => $rp_price,
        'sizes'       => $rp_sizes,
        'image'       => $rp_images[0],
        // Use 2nd image on hover only if this item actually has more than 1 image/variant pic
        'hover_image' => isset($rp_images[1]) ? $rp_images[1] : $rp_images[0],
    ];
}
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>

<head>

    <meta charset="utf-8" />
    <title>Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="Ethic Design Studio" name="description" />
    <meta content="Ethic Design Studio" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/k_favicon_32x.png">
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/flickity/flickity.min.css">
    <link rel="stylesheet" href="assets/libs/jarallax/jarallax.min.css">
    <link href="https://fonts.googleapis.com/css?family=Libre+Baskerville:300,300i,400,400i,500,500i&amp;display=swap" rel="stylesheet">
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/icons/font-icon.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <script>
    const variantStock = <?php echo json_encode($variant_stock); ?>;
    const variantVids = <?php echo json_encode($variant_vids); ?>;
    function updatestock() 
    {
        var selectedColor = '';
        var colorInput = document.querySelector('input[name="radio-control"]:checked');
 
        if (colorInput) {
            selectedColor = colorInput.value.trim();
        }
 
        var selectedSize = '';
        var activeSizeEl = document.querySelector('.product-color-list.size a.active');
 
        if (activeSizeEl) {
            selectedSize = activeSizeEl.textContent.trim();
        }
 
        let stock = 0;
 
        if (variantStock[selectedColor] && variantStock[selectedColor][selectedSize]) {
            stock = variantStock[selectedColor][selectedSize];
        }
 
        const qtyInput = document.getElementById("stock");
        qtyInput.max = stock;
 
        if (parseInt(qtyInput.value) > stock) {
            qtyInput.value = stock > 0 ? 1 : 0;
        }
        if (stock === 0 && selectedColor && selectedSize) 
        {
            alert("Product out of stock.");
        }
    }
 
    function addToCart(e) 
    {
        if (e) e.preventDefault();
        
        <?php if (!isset($_SESSION['u_id'])): ?>
            window.location.href = 'register';
            return;
        <?php endif; ?>

        var selectedColor = '';
        var colorInput = document.querySelector('input[name="radio-control"]:checked');
        if (colorInput) {
            selectedColor = colorInput.value.trim();
        }

        var selectedSize = '';
        var activeSizeEl = document.querySelector('.product-color-list.size a.active');
        if (activeSizeEl) {
            selectedSize = activeSizeEl.textContent.trim();
        }
        
        const qtyInput = document.getElementById("stock");
        const qty = parseInt(qtyInput.value) || 0;
        if (qty <= 0) {
            alert("Please enter a valid quantity.");
            return;
        }

        const btn = document.getElementById("add-to-cart-btn");
        if (btn) btn.disabled = true;

        const formData = new FormData();
        formData.append("action", "add_to_cart");
        formData.append("color", selectedColor);
        formData.append("size", selectedSize);
        formData.append("qty", qty);

        fetch('productdetail?item_id=<?php echo $item_id; ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(response => 
        {
             if (response.redirect) {
                 window.location.href = response.redirect;
                 return;
             }
             if (response.status === 'success') {
                 window.location.href = 'productdetail?item_id=<?php echo $item_id; ?>&cart=1';
             } else {
                 alert(response.message);
             }
        })
        .catch(() => {
            alert("Failed to add to cart. Please try again.");
        })
        .finally(() => {
            if (btn) btn.disabled = false;
        });
    }

    document.addEventListener("DOMContentLoaded", function() 
    {
        // Auto-open offcanvas if redirected with cart=1 parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('cart') === '1') {
            const cartEl = document.getElementById('shoppingCartOffcanvas');
            if (cartEl) {
                const bsCart = new bootstrap.Offcanvas(cartEl);
                bsCart.show();
                window.history.replaceState({}, document.title, 'productdetail?item_id=<?php echo $item_id; ?>');
            }
        }

        const qtyInput = document.getElementById("stock");
        if (qtyInput) {
            qtyInput.addEventListener('change', function() {
                let maxVal = parseInt(qtyInput.max) || 0;
                let curVal = parseInt(qtyInput.value) || 0;
                if (curVal > maxVal) {
                    qtyInput.value = maxVal;
                    alert("Requested quantity exceeds available stock (" + maxVal + ").");
                }
            });
            qtyInput.addEventListener('input', function() {
                let maxVal = parseInt(qtyInput.max) || 0;
                let curVal = parseInt(qtyInput.value) || 0;
                if (curVal > maxVal) {
                    qtyInput.value = maxVal;
                }
            });
        }

        // Review star rating interaction
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('star-btn')) {
                let rating = parseInt(e.target.getAttribute('data-value')) || 0;
                document.getElementById('review-rating').value = rating;
                document.querySelectorAll('.star-btn').forEach(btn => {
                    let val = parseInt(btn.getAttribute('data-value')) || 0;
                    if (val <= rating) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
            }
        });

        // Review form submission
        const reviewForm = document.getElementById("review-form");
        if (reviewForm) {
            reviewForm.addEventListener("submit", function(e) {
                e.preventDefault();
                
                const name = document.getElementById("review-name").value.trim();
                const email = document.getElementById("review-email").value.trim();
                const title = document.getElementById("review-title").value.trim();
                const message = document.getElementById("review-message").value.trim();
                const star = parseInt(document.getElementById("review-rating").value) || 0;
                const alertBox = document.getElementById("review-alert");

                alertBox.classList.add("d-none");
                alertBox.textContent = "";

                if (star === 0) {
                    alertBox.classList.remove("d-none", "alert-success");
                    alertBox.classList.add("alert-danger");
                    alertBox.textContent = "Please select a star rating.";
                    return;
                }

                // JS strip tags validation check
                const tagRegex = /(<([^>]+)>)/ig;
                if (tagRegex.test(name) || tagRegex.test(email) || tagRegex.test(title) || tagRegex.test(message)) {
                    alertBox.classList.remove("d-none", "alert-success");
                    alertBox.classList.add("alert-danger");
                    alertBox.textContent = "HTML tags are not allowed in any field.";
                    return;
                }

                const formData = new FormData();
                formData.append("action", "submit_review");
                formData.append("name", name);
                formData.append("email", email);
                formData.append("title", title);
                formData.append("message", message);
                formData.append("star", star);

                fetch('productdetail?item_id=<?php echo $item_id; ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(response => {
                    if (response.status === 'success') {
                        alertBox.classList.remove("d-none", "alert-danger");
                        alertBox.classList.add("alert-success");
                        alertBox.textContent = response.message;
                        reviewForm.reset();
                        document.querySelectorAll('.star-btn').forEach(btn => btn.classList.remove('active'));
                        document.getElementById('review-rating').value = 0;
                        setTimeout(() => {
                            const modalEl = document.getElementById('rateUsModel');
                            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modalInstance.hide();
                            alertBox.classList.add("d-none");
                        }, 2000);
                    } else {
                        alertBox.classList.remove("d-none", "alert-success");
                        alertBox.classList.add("alert-danger");
                        alertBox.textContent = response.message;
                    }
                })
                .catch(() => {
                    alertBox.classList.remove("d-none", "alert-success");
                    alertBox.classList.add("alert-danger");
                    alertBox.textContent = "An error occurred. Please try again.";
                });
            });
        }
    });
    </script>
    <style>
        .star-rating .star-btn.active {
            color: #f39c12 !important;
        }
        .related-product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
    </style>
</head>

<body class="" x-data="{ showMenuScroll : false }">
<!--head banner-->
<?php include_once("header.php"); ?>

<div class="backdrop-shadow d-none"></div><div>

    <div class="main-project-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 fs-13">
                        <li class="breadcrumb-item"><a href="index">Home</a></li>
                        <li class="breadcrumb-item"><a href="shop"><?php echo $product_type; ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8'); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <!-- main slider -->
    <section class="py-4">
        <div class="container">
            <div class="row py-3 gx-xl-1">
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-xl-2 mt-xl-3 mt-2 order-2 order-xl-1">
                            <div thumbsSlider="" class="swiper productSmall">
                                <div class="swiper-wrapper">
                                    <?php foreach ($variant_images as $image_src): ?>
                                    <div class="swiper-slide">
                                        <img src="<?php echo htmlspecialchars($image_src, ENT_QUOTES, 'UTF-8'); ?>" class="object-fit-cover" />
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-9 mt-3 order-1 order-xl-2">
                            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper productMain">
                                
                                <div class="swiper-wrapper">
                                    <?php foreach ($variant_images as $image_src): ?>
                                    <div class="swiper-slide">
                                        <img src="<?php echo htmlspecialchars($image_src, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100" />
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-6 mt-5 mt-md-0">
                    <h4 class="mb-3"><?php echo htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <div class="d-flex flex-wrap justify-content-between">
                        <p class="text-muted fs-18 mb-4">₹ <?php echo htmlspecialchars($display_price, ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php
                            $c1 = mysqli_query($con,"SELECT COUNT(*) AS total_reviews,
                                ROUND(AVG(star),1) AS average_star,
                                COUNT(CASE WHEN star='5' THEN 1 END) AS five_star,
                                COUNT(CASE WHEN star='4' THEN 1 END) AS four_star,
                                COUNT(CASE WHEN star='3' THEN 1 END) AS three_star,
                                COUNT(CASE WHEN star='2' THEN 1 END) AS two_star,
                                COUNT(CASE WHEN star='1' THEN 1 END) AS one_star
                                FROM reviews
                                WHERE item_id='$item_id' AND status='1'");

                            if($c = mysqli_fetch_assoc($c1))
                            {
                                if($c['total_reviews'] > 0)
                                {
                                    $rating = $c['average_star'];
                                    $full = floor($rating);
                                    $half = ($rating - $full) >= 0.5;
                            ?>
                            <a href="#tab_reviews_product">
                                <div class="kalles-rating-result">
                                    <span class="kalles-rating-result__pipe">
                                        <?php
                                        for($i = 1; $i <= 5; $i++)
                                        {
                                            if($i <= $full)
                                            {
                                                echo '<span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>';
                                            }
                                            elseif($half)
                                            {
                                                echo '<span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>';
                                                $half = false;
                                            }
                                            else
                                            {
                                                echo '<span class="kalles-rating-result__start kalles-rating-result__start--big de-active"></span>';
                                            }
                                        }
                                        ?>
                                    </span>
                                    <span>(<?php echo $c['total_reviews']; ?> reviews)</span>
                                </div>
                            </a>
                            <?php
                                }
                            }
                            ?>
                    </div>

                    <p class="text-muted">
                        <?php echo htmlspecialchars($product_description, ENT_QUOTES, 'UTF-8'); ?>
                    </p>

                    <?php if (!empty($available_colors)): ?>
                    <div class="custome-radio" x-data="{ colors: '<?php echo htmlspecialchars($first_color, ENT_QUOTES, 'UTF-8'); ?>' }">
                        <h6 class="text-uppercase fw-bold mb-3">Color: <span x-text="colors"></span></h6>
                        <div class="image_radio_button_control">
                            <?php foreach ($available_colors as $color_name => $color_code): ?>
                            <label class="radio-button-label" :class="{ 'active': colors === '<?php echo htmlspecialchars($color_name, ENT_QUOTES, 'UTF-8'); ?>' }" title="<?php echo htmlspecialchars($color_name, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="radio" name="radio-control" value="<?php echo htmlspecialchars($color_name, ENT_QUOTES, 'UTF-8'); ?>" x-model="colors" onclick="updatestock();">
                                <span class="d-inline-block rounded-circle border" style="width: 42px; height: 42px; background-color: <?php echo htmlspecialchars($color_code ?: $color_name, ENT_QUOTES, 'UTF-8'); ?>;"></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($available_sizes)): ?>
                    <div x-data="{ size: '<?php echo htmlspecialchars($first_size, ENT_QUOTES, 'UTF-8'); ?>', color: '#fff' }" class="pt-2 mb-4 pb-3">
                        <h6 class="text-uppercase fw-bold mt-3">Size: <span x-text="size"></span></h6>
                        <div class="product-color-list size mt-2 gap-2 d-flex align-items-center">
                            <?php foreach ($available_sizes as $size): ?>
                            <a href="#!"  onclick="updatestock();" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === '<?php echo htmlspecialchars($size, ENT_QUOTES, 'UTF-8'); ?>' }" x-on:click.prevent="size = '<?php echo htmlspecialchars($size, ENT_QUOTES, 'UTF-8'); ?>';"><?php echo htmlspecialchars($size, ENT_QUOTES, 'UTF-8'); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                        <div class="input-step border border-dark rounded-pill">
                            <button type="button" class="minus material-shadow text-dark fw-bold" >-</button>
                            <input type="number" id="stock" class="product-quantity fw-bold fs-6" value="1" min="1" max="<?php echo $first_stock; ?>"  step="1">
                            <button type="button" class="plus material-shadow text-dark fw-bold">+</button>

                        </div>
                        <button id="add-to-cart-btn" onclick="addToCart(event);" x-data="{ shake: false }" x-init="
                                    setInterval(() => { 
                                         shake = true; 
                                         setTimeout(() => { 
                                             shake = false; 
                                         }, 2000); 
                                     }, 6000);
                                 " :class="{ 'animation-shake': shake }" class="btn btn-teal text-uppercase rounded-pill min-w-150">
                            Add to Cart
                        </button>
                        <div class="product_wishlist square-40 rounded-circle border border-dark bg-transparent text-center" style="line-height: 40px;">
                            <a href="#"><i class="facl facl-heart-o"></i></a>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-3 text-nowrap flex-wrap row-gap-1">
                        <a href="#sizeGuidModal" data-bs-toggle="modal" class="text-black fw-semibold">Size Guide</a>
                        <a href="#deliveyReturnModal" data-bs-toggle="modal" class="text-black fw-semibold mx-2">Delivery and Return</a>
                    </div>
                    <div class="mt-4">
                        <p class="mb-2"><span>SKU :</span><span class="text-muted"> <?php echo htmlspecialchars($display_sku, ENT_QUOTES, 'UTF-8'); ?></span></p>
                        <?php
                            if(!empty($collection))
                            {
                        ?>
                        <p class="mb-2">
                            <span>Categories:</span>
                            <span class="text-muted">
                                <a href="#!" class="text-muted">All,</a>
                                <a href="#!" class="text-muted"><?php  echo $collection['name']; ?></a>
                            </span>
                        </p>
                        <?php
                            }
                        if (!empty($available_colors) && is_array($available_colors))
                        {
                        ?>
                            <p class="mb-2">
                                <span>Colors :</span>
                                <span class="text-muted">
                                    <?php
                                    $keys = array_keys($available_colors);

                                    foreach ($keys as $index => $color)
                                    {
                                    ?>
                                        <a href="#!" class="text-muted"><?php echo htmlspecialchars($color); ?></a><?php echo ($index < count($keys) - 1) ? ', ' : ''; ?>
                                    <?php
                                    }
                                    ?>
                                </span>
                            </p>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end main slider -->

    <!-- tab -->
    <div class="mt-4 py-5 d-none d-lg-block main-project-section">
        <div class="container">
            <ul class="nav tab_header justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill pill-border fw-medium active" id="best-seller-tab" data-bs-toggle="pill" data-bs-target="#best-seller" type="button" role="tab" aria-controls="best-seller" aria-selected="true">Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill pill-border fw-medium" id="featured-tab" data-bs-toggle="pill" data-bs-target="#featured" type="button" role="tab" aria-controls="featured" aria-selected="false">Additional Information</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill pill-border fw-medium" id="sale-tab" data-bs-toggle="pill" data-bs-target="#sale" type="button" role="tab" aria-controls="sale" aria-selected="false">Policies</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill pill-border fw-medium" id="top-review-tab" data-bs-toggle="pill" data-bs-target="#top-review" type="button" role="tab" aria-controls="top-review" aria-selected="false">Reviews</button>
                </li>
            </ul>

            <div class="tab-content mt-5" id="pills-tabContent">
                <div class="tab-pane fade show active" id="best-seller" role="tabpanel" aria-labelledby="best-seller-tab" tabindex="0">
                    <div class="row pt-3">
                        <div class="col-lg-12">
                            <p><?php echo $product['saledesp']; ?></p>
                            <p><?php echo $product['product_desp']; ?></p>
                        </div>
                    </div>
                </div><!--end tab pane-->
                <div class="tab-pane fade " id="featured" role="tabpanel" aria-labelledby="featured-tab" tabindex="0">
                    <table class="table table-bordered">
                        <tbody>
                            <?php
                            if(!empty($collection))
                            {
                            ?>
                            <tr>    
                                <th class="bg-transparent" scope="row"> Collection</th>
                                <td class="bg-transparent"> <?php  echo $collection['name']; ?></td>
                            </tr>
                            <?php
                            }
                            if(!empty($material))
                            {
                            ?>
                            <tr>
                                <th class="bg-transparent" scope="row"> Material</th>
                                <td class="bg-transparent"> <?php if(!empty($material)) echo $material['type']; ?></td>
                            </tr>
                            <?php
                            }
                            if(!empty($available_colors)) 
                            {
                            ?>
                            <tr>
                                <th class="bg-transparent" scope="row"> Color</th>
                                <td class="bg-transparent"> <?php echo implode(', ', array_keys($available_colors)); ?></td>
                            </tr>
                            <?php
                            }
                            if(!empty($available_sizes)) 
                            {
                            ?>
                            <tr>
                                <th class="bg-transparent" scope="row">Size</th>
                                <td class="bg-transparent"> <?php echo implode(', ', array_keys($available_sizes)); ?></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="sale" role="tabpanel" aria-labelledby="sale-tab" tabindex="0">
                    
                    <?php
                        if($product['product_policy']!='')
                        {
                        $arr=explode(',',$product['product_policy']);
                        for($l=0;$l<count($arr);$l++)
                        {
                            $policy=mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM product_policies WHERE p_id='$arr[$l]'"));
                    ?>
                    <h6 class="fw-bold"><?php echo $policy['title']; ?></h6>
                    <p class="mb-0">
                        <?php echo $policy['desp']; ?>
                    </p>
                    <?php
                        }
                        }
                    ?>
                </div>
                <div class="tab-pane fade" id="top-review" role="tabpanel" aria-labelledby="top-review-tab" tabindex="0">
                    <div class="row mb-3">
                        <?php
                            $c1 = mysqli_query($con,"SELECT COUNT(*) AS total_reviews,
                                ROUND(AVG(star),1) AS average_star,
                                COUNT(CASE WHEN star='5' THEN 1 END) AS five_star,
                                COUNT(CASE WHEN star='4' THEN 1 END) AS four_star,
                                COUNT(CASE WHEN star='3' THEN 1 END) AS three_star,
                                COUNT(CASE WHEN star='2' THEN 1 END) AS two_star,
                                COUNT(CASE WHEN star='1' THEN 1 END) AS one_star
                                FROM reviews
                                WHERE item_id='$item_id' AND status='1'");

                            if($c = mysqli_fetch_assoc($c1))
                            {
                                if($c['total_reviews'] > 0)
                                {
                                    $rating = $c['average_star'];
                                    $full = floor($rating);
                                    $half = ($rating - $full) >= 0.5;

                                    $five_per  = round(($c['five_star']  / $c['total_reviews']) * 100);
                                    $four_per  = round(($c['four_star']  / $c['total_reviews']) * 100);
                                    $three_per = round(($c['three_star'] / $c['total_reviews']) * 100);
                                    $two_per   = round(($c['two_star']   / $c['total_reviews']) * 100);
                                    $one_per   = round(($c['one_star']   / $c['total_reviews']) * 100);
                        ?>
                        <div class="col-md-2 text-center">
                            
                            <p class="mb-0">Average</p>
                            <h2 class="fw-bold my-1"><?php echo $c['average_star']; ?></h2>
                            <div class="kalles-rating-result">
                                <span class="kalles-rating-result__pipe ,b-1">
                                    <?php
                                        for($i = 1; $i <= 5; $i++)
                                        {
                                            if($i <= $full)
                                            {
                                                echo '<span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>';
                                            }
                                            elseif($half)
                                            {
                                                echo '<span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>';
                                                $half = false;
                                            }
                                            else
                                            {
                                                echo '<span class="kalles-rating-result__start kalles-rating-result__start--big de-active"></span>';
                                            }
                                        }
                                        ?>
                                </span>
                            </div>
                            <p class="text-muted"><?php echo $c['total_reviews']; ?> Review</p>
                            

                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center my-2">
                                <p class="mb-0 me-2 text-nowrap">Excellent</p>
                                <input type="range" min="0" max="100" value="<?php echo $five_per; ?>" class="slider slider1 flex-grow-1" disabled>
                            </div>

                            <div class="d-flex align-items-center my-2">
                                <p class="mb-0 me-2 text-nowrap">Very Good</p>
                                <input type="range" min="0" max="100" value="<?php echo $four_per; ?>" class="slider slider1 flex-grow-1" disabled>
                            </div>

                            <div class="d-flex align-items-center my-2">
                                <p class="mb-0 me-2 text-nowrap">Average</p>
                                <input type="range" min="0" max="100" value="<?php echo $three_per; ?>" class="slider slider1 flex-grow-1" disabled>
                            </div>

                            <div class="d-flex align-items-center my-2">
                                <p class="mb-0 me-2 text-nowrap">Poor</p>
                                <input type="range" min="0" max="100" value="<?php echo $two_per; ?>" class="slider slider1 flex-grow-1" disabled>
                            </div>

                            <div class="d-flex align-items-center my-2">
                                <p class="mb-0 me-2 text-nowrap">Terrible</p>
                                <input type="range" min="0" max="100" value="<?php echo $one_per; ?>" class="slider slider1 flex-grow-1" disabled>
                            </div>                            
                        </div>
                        <?php
                            }
                        }
                        ?>
                        <div class="col-md-2">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#rateUsModel" class="btn btn-warning py-1 px-2">
                                <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="18.37" height="17.8" viewBox="0 0 21.682 21.602">
                                    <g id="Symbol_32_2" data-name="Symbol 32 – 2" transform="translate(-961.98 -374.155)">
                                        <path d="M0,0H4V11.2L1.937,14h0L0,11.2Z" transform="translate(979.891 381.756) rotate(40)" fill="none" stroke="#ffffff" stroke-linejoin="round" stroke-width="1"></path>
                                        <path d="M0,0H4" transform="translate(972.692 390.335) rotate(40)" fill="none" stroke="#ffffff" stroke-width="1"></path>
                                        <g transform="translate(981.126 380.964) rotate(40)" fill="none" stroke="#ffffff" stroke-width="1">
                                            <rect width="3.128" height="1.4" stroke="none"></rect>
                                            <rect x="0.5" y="0.5" width="2.128" height="0.4" fill="none"></rect>
                                        </g>
                                        <path d="M2858.324,3384.6h7.412" transform="translate(-1891.1 -3003.987)" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"></path>
                                        <path d="M2858.324,3384.6h7.412" transform="translate(-1891.1 -2999.611)" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-width="1"></path>
                                        <path d="M8.952,0H15a2,2,0,0,1,2,2V15a2,2,0,0,1-2,2H2a2,2,0,0,1-2-2V12.162" transform="translate(979.48 391.655) rotate(180)" fill="none" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"></path>
                                    </g>
                                </svg> Write a review</button>
                        </div>
                    </div>
                    <div class="row g-3 review-container">
                        <?php
                            $c1 = mysqli_query($con,"SELECT *  FROM reviews WHERE item_id='$item_id' AND status='1' order by r_id desc");
                            while($c = mysqli_fetch_assoc($c1))
                            {
                        ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="rounded-pill d-inline-block align-items-center p-1 bg-light">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 rounded-pill  bg-info text-white d-inline-block text-center d-flex justify-content-center align-items-center" style="width: 25px; height: 25px;"><?php echo substr($c['name'], 0, 1); ?></p>
                                            <span class="fw-bold mx-2"><?php echo $c['name']; ?></span>
                                        </div>
                                    </div>
                                    <div class="kalles-rating-result my-2">
                                        <span class="kalles-rating-result__pipe">
                                            <?php
                                            for($i = 1; $i <= 5; $i++)
                                            {
                                                if($i <= $c['star'])
                                                {
                                                    echo '<span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>';
                                                }
                                                else
                                                {
                                                    echo '<span class="kalles-rating-result__start kalles-rating-result__start--big de-active"></span>';
                                                }
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <h6><?php echo $c['title']; ?></h6>
                                    <p class="text-muted mb-2"><?php echo $c['message']; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accordion -->
    <section class="pt-5 py-lg-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="text-center mb-lg-4">
                            <h3 class="pb-lg-2">Products You May like</h3>
                        </div>
                    </div><!--end col-->
                </div><!--end row-->
                <div class="row mt-4 my-sm-4 pt-2 py-sm-2" data-flickity='{"imagesLoaded": 0,"adaptiveHeight": 0, "contain": 1, "groupCells": "100%", "dragThreshold" : 5, "cellAlign": "left","wrapAround": true,"prevNextButtons": false,"percentPosition": 1,"pageDots": false, "autoPlay" : 0, "pauseAutoPlayOnHover" : true, "rightToLeft": false }'>
                    
   
                    <?php foreach ($related_products as $rp) : ?>
                    <div class="col-md-3 col-6 px-lg-12 px-2">
                        <div x-data="{ imageUrl: '<?php echo htmlspecialchars($rp['image'], ENT_QUOTES, 'UTF-8'); ?>', hoverUrl: '<?php echo htmlspecialchars($rp['hover_image'], ENT_QUOTES, 'UTF-8'); ?>', isHovered: false }" class="topbar-product-card pb-3" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                            <div class="position-relative" style="width: 100%; height: 350px; overflow: hidden; background: transparent;">
                                <img :src="isHovered ? hoverUrl : imageUrl" alt="<?php echo htmlspecialchars($rp['name'], ENT_QUOTES, 'UTF-8'); ?>" class="related-product-img" style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
                                <a href="#" class="d-lg-none position-absolute " style="z-index: 1; top:10px; left:10px;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                                <a href="#" class="wishlistadd d-none d-lg-flex position-absolute" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Add to Wishlist"><i class="facl facl-heart-o text-white"></i></a>
                
                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                    <a href="productdetail?item_id=<?php echo $rp['item_id']; ?>" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                        <i class="iccl iccl-cart"></i></button>
                                </div>
                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                    <a href="productdetail?item_id=<?php echo $rp['item_id']; ?>" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                </div>
                                <?php if (!empty($rp['sizes'])) : ?>
                                <p class="product-size mb-0 text-center fw-medium"><?php echo htmlspecialchars(implode(', ', $rp['sizes']), ENT_QUOTES, 'UTF-8'); ?></p>
                                <?php endif; ?>
                            </div>
                            <a href="productdetail?item_id=<?php echo $rp['item_id']; ?>" class="mt-3 d-block">
                                <h6 class="mb-1"><?php echo htmlspecialchars($rp['name'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                <p class="mb-0 fs-14 text-muted">
                                    <span>₹ <?php echo htmlspecialchars($rp['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </p>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>            
            </div>
    </section>
    
    <?php include_once("footer.php"); ?>
    
    <a href="#" x-on:click.prevent="
          window.scrollTo({
             top: 0,
             behavior: 'smooth'
          });
       " class="position-fixed bg-white border rounded d-flex align-items-center justify-content-center shadow" id="nt_backtop">
        <i class="pr pegk pe-7s-angle-up"></i>
    </a>

    <div class="backdrop-shadow d-none"></div>    
  
</div>

<!-- Modals -->

<!-- Size Guid modal -->
<div class="modal fade modal-overl" id="sizeGuidModal" tabindex="-1" aria-labelledby="sizeGuidModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="text-end position-fixed top-0 end-0">
                <button type="button" class="btn-close btn-close1 p-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class=" container p-5">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <iframe 
                                src="WhatsApp API.pdf#toolbar=0&navpanes=0&scrollbar=0"
                                width="100%"
                                height="700px"
                                style="border:none;">
                            </iframe>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Delivey & Return modal -->
<div class="modal fade modal-overl" id="deliveyReturnModal" tabindex="-1" aria-labelledby="deliveyReturnModalLabel" aria-hidden="true">
    <div class="text-end position-fixed top-0 end-0">
        <button type="button" class="btn-close btn-close1 p-4" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-lg position-relative">
        <div class="modal-content">
            <div class="p-4">
            <?php
            if($product['product_policy']!='')
            {
                $arr=explode(',',$product['product_policy']);
                for($l=0;$l<count($arr);$l++)
                {
                    $policy=mysqli_fetch_assoc(mysqli_query($con,"SELECT * FROM product_policies WHERE p_id='$arr[$l]'"));
            ?>
            <h5 class="ps-1"><?php echo $policy['title']; ?></h5>
            <p class="mb-0">
                <?php echo $policy['desp']; ?>
            </p>
            <?php
                }
            }
            ?>
            </div>
        </div>
    </div>
</div>


 

<!-- custome header -->
<?php include_once("custom_header.php"); ?>
<!--search offcanavas-->


<!-- write review model -->
<div class="modal fade modal-overl mx-auto" id="rateUsModel" tabindex="-1" role="dialog" aria-labelledby="cardLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered h-auto" role="document">
        <div class="modal-content p-2" style="max-width: 420px;">
            <form id="review-form" method="POST">
                <div class="modal-body p-4">
                    <a href="#!" data-bs-dismiss="modal" class="fs-35 close position-absolute top-0 end-0" aria-label="Close">
                        <i class="pe-7s-close pegk"></i>
                    </a>
                    <h2 class="fs-22 mb-3">Write a Review</h2>
                    
                    <div id="review-alert" class="alert d-none py-2 fs-14 mb-3" role="alert"></div>

                    <div class="mb-3">
                        <label for="review-name" role="button" class="fw-medium mb-2 text-muted">Your Name*</label>
                        <input id="review-name" name="name" class="form-control form-control-sm py-2 rounded-0" placeholder="John Smith" type="text" required>
                    </div>
                    <div class="mb-3">
                        <label for="review-email" role="button" class="fw-medium mb-2 text-muted">Your Email*</label>
                        <input id="review-email" name="email" class="form-control form-control-sm py-2 rounded-0" placeholder="example@yourdomain.com" type="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="review-title" role="button" class="fw-medium mb-2 text-muted">Review Title*</label>
                        <input id="review-title" name="title" class="form-control form-control-sm py-2 rounded-0" placeholder="Look great" type="text" required>
                    </div>
                    <div class="mb-3">
                        <label for="review-message" role="button" class="fw-medium mb-2 text-muted">Review Content*</label>
                        <textarea id="review-message" name="message" rows="5" class="form-control form-control-sm py-2 rounded-0" placeholder="Write something" required></textarea>
                    </div>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <p class="text-muted mb-0 fw-bold">Rating*</p>
                        <div class="star-rating fs-24" style="color: #ccc; cursor: pointer; display: flex; gap: 4px;">
                            <i class="las la-star star-btn" data-value="1"></i>
                            <i class="las la-star star-btn" data-value="2"></i>
                            <i class="las la-star star-btn" data-value="3"></i>
                            <i class="las la-star star-btn" data-value="4"></i>
                            <i class="las la-star star-btn" data-value="5"></i>
                        </div>
                        <input type="hidden" id="review-rating" name="star" value="0">
                    </div>
                    <button type="submit" class="btn btn-warning rounded-pill py-2 px-4 fw-semibold text-uppercase">
                        Submit Your Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- JAVASCRIPT -->
<script src="assets/libs/jquery/jquery.min.js" ></script>
<script src="assets/js/store.js" ></script>
<script src="assets/libs/jarallax/jarallax.min.js" ></script>
<script src="assets/libs/swiper/swiper-bundle.min.js" ></script>
<script src="assets/libs/alpinejs/cdn.min.js" ></script>
<script src="assets/libs/jquery-countdown/jquery.countdown.min.js" ></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js" ></script>
<script src="assets/js/product-slider.init.js" ></script>
<script src="assets/js/popup.js" ></script>

<script src="assets/libs/flickity/flickity.pkgd.min.js" ></script>
<script src="assets/js/main.js" ></script>
<!-- <script src="assets/js/app.js" ></script> -->

<script src="cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="10c0cd14b1c35614d98c5f39-|49" defer></script></body>


</html>