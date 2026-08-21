    <footer class="footer bg-light">
        <div class="container">
            <div class="row accordion" id="footer-accordion">
                <div class="col-md-4 col-lg-3 mb-2 footer-accordion-item accordion-item">
                    <button class="accordion-button footer-accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <h5>Get in touch</h5>
                    </button>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <a href="#!">
                            <img src="assets/images/ethic-logo.png" alt="" height="75">
                        </a>
    
                        <div class="mt-4 pt-2">
                            <?php
                                $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=2'));
                                if($c[2])
                                {
                            ?>
                            <p class="d-flex align-items-start text-muted gap-2">
                                <i class="pegk pe-7s-map-marker fs-24"></i>
                                <span><?php echo htmlspecialchars($c[2], ENT_QUOTES, 'UTF-8'); ?></span>
                            </p>
                            <?php
                                }
                                $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=3'));
                                if($c[2])
                                {
                            ?>
                            <p class="d-flex align-items-start text-muted gap-2">
                                <i class="pegk pe-7s-mail fs-24"></i>
                                <a href="mailto:<?php echo htmlspecialchars($c[2], ENT_QUOTES, 'UTF-8'); ?>" class="text-reset"><?php echo htmlspecialchars($c[2], ENT_QUOTES, 'UTF-8'); ?></a>
                            </p>
                            <?php
                                }
                                $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=4'));
                                if($c[2])
                                {
                            ?>
                            <p class="d-flex align-items-start text-muted gap-2">
                                <i class="pegk pe-7s-call fs-24"></i>
                                <span><?php echo htmlspecialchars($c[2], ENT_QUOTES, 'UTF-8'); ?> </span>
                            </p>
                            <?php
                                }
                            ?>
                            <div class="footer-social d-flex align-items-center gap-4 mt-4">
                                <?php
                                    $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=6'));
                                    if ($c[2]) {
                                ?>
                                <a href="<?php echo htmlspecialchars($c[2], ENT_QUOTES, 'UTF-8'); ?>" class="d-inline-block">
                                    <i class="facl facl-facebook"></i>
                                </a>
                                <?php
                                }
                                $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=8'));
                                if ($c[2]) {
                                ?>
                                <a href=<?php echo htmlspecialchars($c[2], ENT_QUOTES, 'UTF-8'); ?>" class="d-inline-block">
                                    <i class="facl facl-twitter"></i>
                                </a>
                                <?php
                                }
                                $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=7'));
                                if ($c[2]) {
                                ?>
                                <a href="<?php echo htmlspecialchars($c[2], ENT_QUOTES, 'UTF-8'); ?>" class="d-inline-block">
                                    <i class="facl facl-instagram"></i>
                                </a>
                                <a href="https://www.linkedin.com/" class="d-inline-block">
                                    <i class="facl facl-linkedin"></i>
                                </a>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-lg-2 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <h5>Categories</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Categories</h5>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <ul class="menu list-unstyled">
                                <?php
                                $c1 = mysqli_query($con, 'SELECT * FROM producttype');
                                while ($c = mysqli_fetch_row($c1)) 
                                {
                                ?>
                                <li class="menu-item">
                                    <a class="text-muted" href="shop?pt_id=<?php echo $c[0]; ?>"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></a>
                                </li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-md-3 col-lg-2 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <h5>Infomation</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Infomation</h5>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <ul class="menu list-unstyled">
                                <li class="menu-item">
                                    <a href="aboutus" class="text-muted">About Us</a>
                                </li>
                                <li class="menu-item">
                                    <a href="faq" class="text-muted">FAQ's</a>
                                </li>
                                <li class="menu-item">
                                    <a href="sizeguide" class="text-muted">Size Guide</a>
                                </li>
                                <li class="menu-item">
                                    <a href="contact" class="text-muted">Contact us</a>
                                </li>
                                <li class="menu-item"><a href="payment_policy" class="text-muted">Payment Policy</a></li>
                                <li class="menu-item">
                                    <a href="store_events" class="text-muted">Store & Events</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-md-3 col-lg-2 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <h5>Customer Services</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Customer Services</h5>
                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <ul class="menu list-unstyled">
                                <li class="menu-item"><a href="term-of-use" class="text-muted">Terms & Condition</a></li>
                                <li class="menu-item"><a href="return_policy" class="text-muted">Return & Refund Policy</a></li>
                                <li class="menu-item"><a href="cancellation_policy" class="text-muted">Cancellation Policy</a></li>
                                <li class="menu-item"><a href="shipping_policy" class="text-muted">Shipping Policy</a></li>
                                <li class="menu-item"><a href="privacy_policy" class="text-muted">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-md-10 col-lg-3 mb-2 accordion-item footer-accordion-item">
                    <button class="accordion-button footer-accordion-button px-0 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        <h5>Newsletter Signup</h5>
                    </button>
                    <h5 class="fw-medium d-none d-md-block">Newsletter Signup</h5>
                    <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="mt-md-4 pt-md-2">
                            <p class="text-muted">Subscribe to our newsletter and get 10% off your first purchase</p>
                            <form class="d-block form-newsletter style-black" method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="text" name="email_address_check" value="" autocomplete="off" tabindex="-1" style="display:none">
                                <div class="footer-subscribe position-relative">
                                    <input type="email" name="EMAIL" placeholder="Your email address" value="" class="border-dark input-text form-control w-100 rounded-pill" required>
                                    <button type="submit" name="newletter_submit" value="1" class="btn btn-dark position-absolute rounded-pill">
                                        <span>Subscribe</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </footer>
    
    <div class="footer-alt mb-lg-0">
        <div class="container">
            <div class="row justify-between text-center text-lg-start">
                <div class="col-lg-12 text-muted text-center">
                    &copy; Copyright <?php echo date("Y"); ?> Ethic Design Studio. All Rights Reserved.Developed By <a href="http://www.technoknitters.com" target='_blank' style="font-weight:bold;color: white;">Technoknitters</a>
                </div>
            </div>
        </div>
    </div>

    <!--search offcanavas-->
<!-- <div class="offcanvas offcanvas-end" tabindex="-1" id="searchOffcanvas" aria-labelledby="searchOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="searchOffcanvasLabel">Search Out Site</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="px-3 py-4">
        <div>
            <form action="#!">
                <select class="form-select rounded-pill mb-3" name="search_option">
                    <option value="-1">All Categories</option>
                    <?php
                        // $c1 = mysqli_query($con, 'SELECT * FROM producttype');
                        // while ($c = mysqli_fetch_row($c1)) 
                        // {
                    ?>
                    <option value="<?php echo $c[0]; ?>"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php
                        // }
                    ?>
                </select>
                <div class="search-box position-relative">
                    <input type="text" class="form-control rounded-pill" id="exampleFormControlsearch2" name="search_text" placeholder="Search for products">
                    <button type="submit" class="btn" name="search_btn" ><i class="iccl iccl-search"></i></button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<!--account offcanavas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="accountOffcanvas" aria-labelledby="accountOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="accountOffcanvasLabel">LOGIN</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <form id="offcanvas-login-form" class="mb-4">
                <div id="login-error-msg" class="alert alert-danger d-none py-2 fs-14 mb-3" role="alert"></div>
                <div class="mb-3">
                    <label for="emailInputOffcanvas" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="emailInputOffcanvas" name="email" required>
                </div>
                <div class="mb-3 pb-1">
                    <label for="current-password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="current-password" name="password" autocomplete="off" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-info w-100 rounded-pill">SIGN IN</button>
                </div>
            </form>
            <p class="text-muted">New customer? <a href="register" class="product-title">Create your account</a></p>
            <p class="text-muted">Lost password? <a href="forget_password" class="product-title">Recover password</a></p>
        </div>
    </div>
</div>





<!--search Modal -->
<div class="modal fade modal-overl " id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen header-search-model">
        <div class="modal-content" style="height: 25%;">
            <div class="modal-body p-0 mb-4">
                <form class="row g-2 mx-2 mx-md-5 my-4 pb-3 pt-4" action="#">
                    <div class="col-md-3">
                        <select class="form-select rounded-pill w-100">
                            <option value="*">All Product Type</option>
                            <?php
                            $c1 = mysqli_query($con, 'SELECT * FROM producttype');
                            while ($c = mysqli_fetch_row($c1)) 
                            {
                            ?>
                            <option value="<?php echo $c[0]; ?>"><?php echo $c[1]; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <div class="search-box position-relative">
                            <input type="text" class="form-control rounded-pill" name="searchtext" id="" placeholder="Search for products">
                            <button type="submit" class="btn" name="search_btn_submit"><i class="iccl iccl-search"></i></button>
                        </div>
                    </div>
                </form>
                <button type="button" class="btn search-model-close" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php
    $m1 = mysqli_query($con,"SELECT * from matter  where m_id=21");
    if($m=mysqli_fetch_assoc($m1))
    {
?>
<!----POPUP----->
<div class="modal fade" id="CODE15OFF" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0 position-relative">
            <div class="modal-body p-0">
            <button type="button" class="btn-close position-absolute end-0 p-0 lh-lg bg-white rounded-circle" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="row g-0">
            <div class="col-lg-12">
                <a href="#!" class="position-relative d-block h-100 copycode-left">
                    <img src="<?php echo $m['pic']; ?>" alt="" class="img-fluid w-100 h-100 recommended_products">
                    <div class="p-4 position-absolute d-flex top-0 m-auto h-100 w-100 flex-column justify-content-center align-items-center text-white text-center">
                        <h3 class="fs-32"><?php echo $m['title']; ?></h3>                    
                        <p class="mb-3 px-4"><?php echo $m['desp']; ?></p>
                        <button class="btn btn-lg py-2 px-5 lh-lg rounded-pill fs-18 btn-primary">SHOP NOW</button>
                    </div>
                </a>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
<?php
    }
?>
<!--Shopping Cart offcanavas-->
<div class="offcanvas offcanvas-end" tabindex="-1" id="shoppingCartOffcanvas" aria-labelledby="shoppingCartnvasLabel" style="width: 30%;">
    <div class="offcanvas-header p-3">
        <h5 class="offcanvas-title text-uppercase" id="shoppingCartnvasLabel">Shopping Cart</h5>
        <button type="button" class="btn-close btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php if (!empty($cart_items)): ?>
            <?php foreach ($cart_items as $citem): ?>
            <div class="p-20 border-bottom">
                <div class="row">
                    <div class="col-5">
                        <img src="<?php echo htmlspecialchars($citem['pic'], ENT_QUOTES, 'UTF-8'); ?>" alt="" class="img-fluid">
                    </div>
                    <div class="col-7">
                        <h6 class="mb-1"><a href="productdetail?item_id=<?php echo $citem['item_id']; ?>" class="product-title"><?php echo htmlspecialchars($citem['saledesp'] ?: $citem['pcode'], ENT_QUOTES, 'UTF-8'); ?></a></h6>
                        <p class="text-muted fs-12"><?php echo htmlspecialchars($citem['color'] . ($citem['size'] ? ' / ' . $citem['size'] : ''), ENT_QUOTES, 'UTF-8'); ?></p>

                        <p class="fs-14 text-muted d-flex align-items-center gap-2 mb-1">
                            <span class="text-danger">₹<?php echo number_format($citem['edsellrate'], 2); ?></span>
                        </p>

                        <div class="d-flex align-items-center gap-3">
                            <div class="quantity fs-14 position-relative border border-dark rounded-pill" style="max-width: 120px; height: 38px;">
                                <input id="cart-qty-input-<?php echo $citem['cart_id']; ?>" value="<?php echo $citem['quantity']; ?>" type="number" class="input-text text-center w-100" style="border: none; background: transparent; padding: 0 30px; height: 100%;" min="0" max="<?php echo $citem['webstock']; ?>" onchange="updateCartQtyDirect(<?php echo $citem['cart_id']; ?>, this.value, <?php echo $citem['webstock']; ?>)">
                                <button type="button" class="minus position-absolute start-0 ps-3 border-0 bg-transparent" onclick="changeCartQtyBtn(<?php echo $citem['cart_id']; ?>, -1, <?php echo $citem['webstock']; ?>)" style="top: 50%; transform: translateY(-50%);">
                                    <i class="facl facl-minus"></i>
                                </button>
                                <button type="button" class="plus position-absolute end-0 pe-3 border-0 bg-transparent" onclick="changeCartQtyBtn(<?php echo $citem['cart_id']; ?>, 1, <?php echo $citem['webstock']; ?>)" style="top: 50%; transform: translateY(-50%);">
                                    <i class="facl facl-plus"></i>
                                </button>
                            </div>

                            <a href="?remove_cart_item=<?php echo $citem['cart_id']; ?>" class="main_link" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Remove this item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="square-20" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="p-20 text-center text-muted">
                <p>Your cart is empty.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="offcanvas-footer p-20 border-top">
        <div class="d-flex align-items-center mb-3">
            <h5 class="mb-0 flex-grow-1 fs-18">Subtotal:</h5>
            <a href="#!" class="cart_tot_price fs-18 texrt-reset">₹ <?php echo number_format($total_cart_price, 2); ?></a>
        </div>
        <div class="mt-3 vstack gap-3">
            <a href="cart"><button type="button" class="btn btn-light w-100 rounded-pill text-uppercase fw-semibold" style="letter-spacing: 4px; font-size: 11px;">View cart</button></a>
            <a href="checkout">
                <button type="button" class="btn btn-info w-100 rounded-pill text-uppercase fw-semibold" style="letter-spacing: 4px; font-size: 11px;">Check out</button>
            </a>
        </div>
    </div>
</div>

<script>
    function changeCartQtyBtn(cartId, delta, webstock) {
        const input = document.getElementById("cart-qty-input-" + cartId);
        if (!input) return;
        let currentQty = parseInt(input.value) || 0;
        let newQty = currentQty + delta;
        if (newQty < 0) newQty = 0;
        updateCartQtyDirect(cartId, newQty, webstock);
    }

    function updateCartQtyDirect(cartId, qty, webstock) {
        const input = document.getElementById("cart-qty-input-" + cartId);
        if (qty > webstock) {
            alert("Requested quantity exceeds available stock (" + webstock + ").");
            if (input) input.value = webstock;
            qty = webstock;
        }

        const formData = new FormData();
        formData.append("action", "update_cart_qty");
        formData.append("cart_id", cartId);
        formData.append("qty", qty);

        fetch("header", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const url = new URL(window.location.href);
                url.searchParams.set("cart", "1");
                window.location.href = url.toString();
            } else {
                alert(data.message);
                if (input) {
                    window.location.reload();
                }
            }
        })
        .catch(() => {
            alert("An error occurred updating the cart.");
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('cart') === '1') {
            const cartEl = document.getElementById('shoppingCartOffcanvas');
            if (cartEl) {
                const bsCart = new bootstrap.Offcanvas(cartEl);
                bsCart.show();
                urlParams.delete('cart');
                const cleanSearch = urlParams.toString();
                const cleanUrl = window.location.pathname + (cleanSearch ? '?' + cleanSearch : '');
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }

        const loginForm = document.getElementById("offcanvas-login-form");
        if (loginForm) {
            loginForm.addEventListener("submit", function(e) {
                e.preventDefault();
                const email = document.getElementById("emailInputOffcanvas").value.trim();
                const password = document.getElementById("current-password").value;
                const errorMsg = document.getElementById("login-error-msg");

                errorMsg.classList.add("d-none");
                errorMsg.textContent = "";

                const formData = new FormData();
                formData.append("action", "login");
                formData.append("email", email);
                formData.append("password", password);

                fetch("header", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => 
                {
                    if (data.status === "success") {
                        window.location.href = "index";
                    } else {
                        errorMsg.classList.remove("d-none");
                        errorMsg.textContent = data.message;
                    }
                })
                .catch(() => {
                    errorMsg.classList.remove("d-none");
                    errorMsg.textContent = "An error occurred. Please try again.";
                });
            });
        }
    });
</script>
