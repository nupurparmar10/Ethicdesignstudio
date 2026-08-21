<?php
ob_start();
session_start();
if (!isset($_SESSION['u_id'])) {
    header("Location: index");
    exit;
}
include_once("connect.php");
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>

<head>

    <meta charset="utf-8" />
    <title>Ethic Design Studio </title>
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
</head>

<body class="" x-data="{ showMenuScroll : false }">
<!--head banner-->
<?php include_once("header.php"); ?>

<div class="backdrop-shadow d-none"></div>
<div>
    <!-- main slide -->
    <?php
        $query = mysqli_query($con, "SELECT * FROM banner WHERE b_id = 14");
        if ($row = mysqli_fetch_assoc($query)) {
            $banner = $row['pic'];
        }
    ?>
    <div style="background-image: url('<?php echo $banner; ?>'); background-size: cover; background-position: center;" class="position-relative">
        <div class="position-absolute top-0 start-0 right-0 bottom-0 bg-dark w-100 opacity-50"></div>
        <div class=" container">
            <div class="text-white text-center py-51 position-relative">
                <h4 class="fs-20 fw-medium">
                    SHOPPING CART</h4>
            </div>
        </div>
    </div>
    <!-- product data -->
   
    <section class="py-5">
        <div class="container">
            <?php if (!empty($cart_items)): ?>
                <div class="row d-none d-lg-flex border-bottom pb-3 mb-3">
                    <div class="col-6">
                        <h6 class="text-uppercase fw-semibold text-muted">Product</h6>
                    </div>
                    <div class="col-2">
                        <h6 class="text-uppercase fw-semibold text-muted">Price</h6>
                    </div>
                    <div class="col-2 text-center">
                        <h6 class="text-uppercase fw-semibold text-muted">Quantity</h6>
                    </div>
                    <div class="col-2 text-end">
                        <h6 class="text-uppercase fw-semibold text-muted">Total</h6>
                    </div>
                </div>

                <?php foreach ($cart_items as $citem): ?>
                <div class="row align-items-center py-4 border-bottom g-3">
                    <!-- Product Details (Image, Title, Selection, Remove) -->
                    <div class="col-lg-6 col-12">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="<?php echo htmlspecialchars($citem['pic'], ENT_QUOTES, 'UTF-8'); ?>" alt="" class="img-fluid rounded" style="width: 80px; height: 100px; object-fit: cover;">
                            <div>
                                <h6 class="fs-16 mb-1">
                                    <a href="productdetail?item_id=<?php echo $citem['item_id']; ?>" class="text-dark text-decoration-none fw-semibold">
                                        <?php echo htmlspecialchars($citem['saledesp'] ?: $citem['pcode'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h6>
                                <p class="text-muted fs-13 mb-2">
                                    <?php echo htmlspecialchars($citem['color'] . ($citem['size'] ? ' / ' . $citem['size'] : ''), ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <a href="?remove_cart_item=<?php echo $citem['cart_id']; ?>" class="text-danger fs-13 text-decoration-none d-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="square-16" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    <span>Remove</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Price (Desktop & Mobile view handled dynamically) -->
                    <div class="col-lg-2 col-6 text-start text-lg-start">
                        <span class="d-lg-none text-muted me-2">Price:</span>
                        <span class="fs-15 fw-medium text-dark">₹<?php echo number_format($citem['edsellrate'], 2); ?></span>
                    </div>

                    <!-- Quantity controls -->
                    <div class="col-lg-2 col-6 text-end text-lg-center">
                        <div class="d-inline-flex align-items-center gap-2">
                            <span class="d-lg-none text-muted me-1">Qty:</span>
                            <div class="quantity fs-14 position-relative border border-dark rounded-pill" style="width: 120px; height: 38px;">
                                <input id="cart-qty-input-<?php echo $citem['cart_id']; ?>" value="<?php echo $citem['quantity']; ?>" type="number" class="input-text text-center w-100" style="border: none; background: transparent; padding: 0 30px; height: 100%;" min="0" max="<?php echo $citem['webstock']; ?>" onchange="updateCartQtyDirect(<?php echo $citem['cart_id']; ?>, this.value, <?php echo $citem['webstock']; ?>)">
                                <button type="button" class="minus position-absolute start-0 ps-3 border-0 bg-transparent" onclick="changeCartQtyBtn(<?php echo $citem['cart_id']; ?>, -1, <?php echo $citem['webstock']; ?>)" style="top: 50%; transform: translateY(-50%);">
                                    <i class="facl facl-minus"></i>
                                </button>
                                <button type="button" class="plus position-absolute end-0 pe-3 border-0 bg-transparent" onclick="changeCartQtyBtn(<?php echo $citem['cart_id']; ?>, 1, <?php echo $citem['webstock']; ?>)" style="top: 50%; transform: translateY(-50%);">
                                    <i class="facl facl-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Total Column -->
                    <div class="col-lg-2 col-12 text-end">
                        <span class="d-lg-none text-muted float-start">Total:</span>
                        <span class="fs-15 fw-semibold text-teal">₹<?php echo number_format($citem['edsellrate'] * $citem['quantity'], 2); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Subtotal & Checkout -->
                <div class="row pt-5 justify-content-end">
                    <div class="col-md-5 col-12 text-end">
                        <div class="border p-4 bg-light rounded shadow-sm">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="mb-0 flex-grow-1 text-start fs-18 fw-semibold">Subtotal:</h5>
                                <span class="fs-20 fw-bold text-teal">₹<?php echo number_format($total_cart_price, 2); ?></span>
                            </div>
                            <p class="text-muted fs-13 mb-4">Taxes, shipping, and discount codes will be calculated at checkout.</p>
                            
                            <a href="checkout" class="w-100">
                                <button type="button" class="btn btn-teal w-100 rounded-pill py-3 text-uppercase fw-bold" style="letter-spacing: 2px;">
                                    Proceed to Checkout
                                </button>
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="py-5 text-center text-muted">
                    <i class="iccl iccl-cart fs-60 mb-3 d-block text-secondary"></i>
                    <h4 class="mb-3">Your Cart is Empty</h4>
                    <p class="mb-4">Before you can checkout, you must add some products to your shopping cart.</p>
                    <a href="shop" class="btn btn-teal rounded-pill px-5 py-3 text-uppercase fw-semibold" style="letter-spacing: 1px;">
                        Return to Shop
                    </a>
                </div>
            <?php endif; ?>
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


<!-- custome header -->
<?php include_once("custom_header.php"); ?>

<script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/libs/jquery/jquery.min.js" ></script>
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
<script src="assets/js/app.js" ></script>

<script src="cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="6494d56b805cd9197614acad-|49" defer></script></body>


</html>