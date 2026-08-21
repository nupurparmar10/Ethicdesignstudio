<?php
if (isset($_GET['logout'])) 
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index");
    exit;
}
if (isset($_POST['action']) && $_POST['action'] === 'update_cart_qty') 
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once("connect.php");
    header('Content-Type: application/json');
    
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $new_qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
    
    if (!isset($_SESSION['u_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Please log in to update your cart.']);
        exit;
    }
    
    $u_id = (int)$_SESSION['u_id'];
    
    // Fetch variant webstock
    $query = mysqli_query($con, "SELECT c.cart_id, v.webstock FROM cart c JOIN variant v ON c.v_id = v.v_id WHERE c.cart_id = $cart_id AND c.u_id = $u_id LIMIT 1");
    if ($row = mysqli_fetch_assoc($query)) {
        $webstock = (int)$row['webstock'];
        
        if ($new_qty <= 0) {
            // Treat as removing that product from the cart
            $delete = mysqli_query($con, "DELETE FROM cart WHERE cart_id = $cart_id AND u_id = $u_id");
            if ($delete) {
                echo json_encode(['status' => 'success', 'removed' => true]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to remove item.']);
            }
            exit;
        }
        
        if ($new_qty > $webstock) {
            echo json_encode(['status' => 'error', 'message' => "Requested quantity exceeds available stock. Only $webstock available."]);
            exit;
        }
        
        $update = mysqli_query($con, "UPDATE cart SET quantity = $new_qty WHERE cart_id = $cart_id AND u_id = $u_id");
        if ($update) {
            echo json_encode(['status' => 'success', 'qty' => $new_qty]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity.']);
        }
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cart item not found.']);
        exit;
    }
}
if (isset($_POST['action']) && $_POST['action'] == 'login') 
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once("connect.php");
    header('Content-Type: application/json');
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and Password are required.']);
        exit;
    }
    
    $email_escaped = mysqli_real_escape_string($con, $email);
    $q = mysqli_query($con, "SELECT u_id, password FROM user_login_det WHERE email = '$email_escaped' AND status = 1 LIMIT 1");
    if ($row = mysqli_fetch_assoc($q)) {
        if (password_verify($password, $row['password'])) {
            $logged_in_uid = (int)$row['u_id'];
            if (isset($_SESSION['guest_id']) && $_SESSION['guest_id'] > 0) {
                $guest_uid = (int)$_SESSION['guest_id'];
                $guest_cart_query = mysqli_query($con, "SELECT * FROM cart WHERE u_id = $guest_uid");
                if ($guest_cart_query) {
                    while ($gitem = mysqli_fetch_assoc($guest_cart_query)) {
                        $v_id = (int)$gitem['v_id'];
                        $qty = (int)$gitem['quantity'];
                        $check = mysqli_query($con, "SELECT cart_id, quantity FROM cart WHERE u_id = $logged_in_uid AND v_id = $v_id LIMIT 1");
                        if ($crow = mysqli_fetch_assoc($check)) {
                            $var_stock_q = mysqli_query($con, "SELECT webstock FROM variant WHERE v_id = $v_id LIMIT 1");
                            $var_stock_row = mysqli_fetch_assoc($var_stock_q);
                            $webstock = $var_stock_row ? (int)$var_stock_row['webstock'] : 999;
                            $new_qty = min($webstock, $crow['quantity'] + $qty);
                            mysqli_query($con, "UPDATE cart SET quantity = $new_qty WHERE cart_id = $crow[cart_id]");
                        } else {
                            mysqli_query($con, "UPDATE cart SET u_id = $logged_in_uid WHERE cart_id = $gitem[cart_id]");
                        }
                    }
                }
                mysqli_query($con, "DELETE FROM cart WHERE u_id = $guest_uid");
                unset($_SESSION['guest_id']);
            }
            $_SESSION['u_id'] = $logged_in_uid;
            echo json_encode(['status' => 'success']);
            exit;
        }
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['u_id']) && !isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = rand(10000000, 99999999);
}
$u_id_query = isset($_SESSION['u_id']) ? (int)$_SESSION['u_id'] : (isset($_SESSION['guest_id']) ? (int)$_SESSION['guest_id'] : 0);

if (isset($_GET['remove_cart_item'])) {
    $cart_id_remove = (int)$_GET['remove_cart_item'];
    if ($u_id_query > 0) {
        mysqli_query($con, "DELETE FROM cart WHERE cart_id = $cart_id_remove AND u_id = $u_id_query");
    }
    $redirect_url = strtok($_SERVER["REQUEST_URI"], '?');
    $get_params = $_GET;
    unset($get_params['remove_cart_item']);
    if (!empty($get_params)) {
        $redirect_url .= '?' . http_build_query($get_params);
    }
    header("Location: $redirect_url");
    exit;
}

$cart_items = [];
$total_cart_count = 0;
$total_cart_price = 0;
if ($u_id_query > 0) {
    $cart_res = mysqli_query($con, "SELECT c.*, v.edsellrate, v.webstock, v.item_id, i.pcode, i.saledesp FROM cart c JOIN variant v ON c.v_id = v.v_id JOIN item_details i ON v.item_id = i.item_id WHERE c.u_id = $u_id_query");
    if ($cart_res) {
        $grouped_items = [];
        while ($item = mysqli_fetch_assoc($cart_res)) {
            $v_id = (int)$item['v_id'];
            $pic_res = mysqli_query($con, "SELECT pic FROM variant_pic WHERE v_id = '$v_id' LIMIT 1");
            $pic_row = mysqli_fetch_assoc($pic_res);
            $pic = $pic_row ? (file_exists('ethic_crm/' . $pic_row['pic']) ? 'ethic_crm/' . $pic_row['pic'] : $pic_row['pic']) : 'pic_not_found.png';
            $item['pic'] = $pic;
            
            if (isset($grouped_items[$v_id])) {
                $grouped_items[$v_id]['quantity'] += (int)$item['quantity'];
            } else {
                $item['quantity'] = (int)$item['quantity'];
                $grouped_items[$v_id] = $item;
            }
        }
        foreach ($grouped_items as $item) {
            $cart_items[] = $item;
            $total_cart_count += $item['quantity'];
            $total_cart_price += $item['edsellrate'] * $item['quantity'];
        }
    }
}
?>
<!--head banner-->
<?php
$m=mysqli_fetch_assoc(mysqli_query($con,"select * from matter where m_id ='38'"));
    if($m['desp']!='')
    {
        
?>
<div x-data="{ isOpen: true }" class="">
    <div class="t_header fs-13 d-flex align-items-center" x-bind:class="{ 'd-none': !isOpen }">
        <div class="container-fluid">
            <div class="d-flex gap-2">
                <div class="col text-center text-white">
                    <?php echo $m['desp']; ?> <a href="shop" class="text-white">Hurry Up <i class="las la-arrow-right"></i></a>
                </div>
                <div class="col-auto mt-2 mt-md-0">
                    <a href="#" class="h_banner_close text-white" x-on:click.prevent="isOpen = false">close</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    }
?>
<style>
/* Below xl, the 5 contact items no longer fit centered in one line,
   so switch to a horizontally-scrollable strip instead of letting
   them overflow/clip the viewport. */
@media (max-width: 1199.98px) {
    .mobile-contact-scroll {
        justify-content: flex-start !important;
        overflow-x: auto;
        flex-wrap: nowrap;
        white-space: nowrap;
        width: 100%;
        gap: 0 !important;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 2px;

        /* Hide scrollbar */
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .mobile-contact-scroll::-webkit-scrollbar {
        display: none;
    }

    .mobile-contact-scroll a {
        flex: 0 0 auto;
        margin-right: 20px;
    }

    .mobile-contact-scroll a:last-child {
        margin-right: 0;
        padding-right: 12px;
    }
}

@media (max-width: 991.98px) {
    .mobile-contact-scroll { font-size: 14px !important; }
    .mobile-contact-scroll a { margin-right: 16px; }
    .mobile-contact-scroll i { font-size: 16px !important; }
}

@media (max-width: 575.98px) {
    .mobile-contact-scroll { font-size: 12px !important; }
    .mobile-contact-scroll a { margin-right: 12px; }
    .mobile-contact-scroll i { font-size: 14px !important; }
}

@media (min-width: 992px) {
    .header-user-dropdown:hover .dropdown-menu {
        display: block !important;
        margin-top: 0 !important;
    }
}
.header-user-dropdown .dropdown-toggle::after {
    display: none !important;
}
.header-user-dropdown .dropdown-menu {
    border: 1px solid rgba(232, 112, 56, 0.15) !important;
    box-shadow: 0 10px 30px rgba(232, 112, 56, 0.1) !important;
    border-radius: 8px !important;
    padding: 10px 0 !important;
    min-width: 160px !important;
    right: 0 !important;
    left: auto !important;
}
.header-user-dropdown .dropdown-item {
    font-size: 14px !important;
    color: #333333 !important;
    padding: 8px 20px !important;
    transition: all 0.2s ease !important;
}
.header-user-dropdown .dropdown-item:hover {
    background-color: #fdf3ee !important;
    color: #e87038 !important;
}
</style>
<!--end head banner-->



<div id="kalles-section-header_top" class="">
    <div class="h__top d-flex align-items-center">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-center py-3 py-xl-0">
                <div class="col-md-12 col-lg-12 col-12 d-md-block">
                    <div class="d-flex align-items-xl-center justify-content-center gap-3 mobile-contact-scroll" style="font-size:14px;">
                        <?php
                            $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=4'));
                            if($c[2])
                            {
                        ?>
                        <a href="tel:<?php echo $c[2]; ?>" class="mb-0 text-muted"><i class="pegk pe-7s-call fs-18 me-1 align-middle"></i>
                            <?php echo $c[2]; ?></a>
                        <?php
                            }
                            $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=3'));
                            if($c[2])
                            {
                        ?>
                        <a href="mailto:<?php echo $c[2]; ?>" class="mb-0 text-muted"><i class="pe-7s-mail pegk fs-18 me-1 align-middle"></i> <?php echo $c[2]; ?></a>
                        <?php
                            }
                            $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=5'));
                            if($c[2])
                            {
                        ?>
                        <a class="mb-0 text-muted"><i class="pegk pe-7s-clock fs-18 me-1 align-middle"></i><?php echo $c[2]; ?></a>
                        <?php
                            }
                             $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=11'));
                            if($c[2])
                            {
                        ?>
                        <a href="tel:<?php echo $c[2]; ?>" class="mb-0 text-muted"><i class="pegk pe-7s-phone fs-18 me-1 align-middle"></i>
                            <?php echo $c[2]; ?></a>
                        <?php
                            }
                             $c = mysqli_fetch_row(mysqli_query($con, 'Select * FROM contact_info where c_id=12'));
                            if($c[2])
                            {
                        ?>
                        <a href="mailto:<?php echo $c[2]; ?>" class="mb-0 text-muted"><i class="pe-7s-mail pegk fs-18 me-1 align-middle"></i> <?php echo $c[2]; ?></a>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-custom py-0 d-flex align-items-center">
        <div class="container-fluid">
             <a class="d-lg-none" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" viewBox="0 0 30 16">
                    <rect width="30" height="1.5"></rect>
                    <rect y="7" width="20" height="1.5"></rect>
                    <rect y="14" width="30" height="1.5"></rect>
                </svg>
            </a>
            <a class="navbar-brand" href="index"><img src="assets/images/ethic-logo.png" alt="" width="150"></a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="d-none d-lg-block mx-auto">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <?php
                            $c1 = mysqli_query($con, 'SELECT * FROM producttype');
                            while ($c = mysqli_fetch_row($c1)) 
                            {
                        ?>
                        <li class="nav-item ">
                            <a class="nav-link" href="shop?pt_id=<?php echo $c[0]; ?>"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></a>
                        </li>
                        <?php
                            }
                            $f1 = mysqli_query($con, 'SELECT * FROM collection');
                            if ($f = mysqli_fetch_row($f1)) 
                            {
                        ?>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Collection</a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <?php
                                    $c1 = mysqli_query($con, 'SELECT * FROM collection');
                                    while ($c = mysqli_fetch_row($c1)) 
                                    {
                                ?>
                                <li><a class="text-muted" href="shop?collect_id=<?php echo $c[0]; ?>" class="menu-link-text"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></a></li>
                                <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                            }
                        ?>
                        <li class="nav-item ">
                            <a class="nav-link" href="stores_events">Store</a>
                        </li>
                        <li class="nav-item dropdown dropdown-mega-lg">
                            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Get In Touch</a>
                            <ul class="dropdown-menu dropdown-sub-column">
                                <li><a class="text-muted" href="term-of-use" class="menu-link-text">Terms & Condition</a></li>
                                <li><a class="text-muted" href="return_refund" class="menu-link-text">Return & Refund Policy</a></li>
                                <li><a class="text-muted" href="cancellation_policy" class="menu-link-text">Cancellation Policy</a></li>
                                <li><a class="text-muted" href="shipping_policy" class="menu-link-text">Shipping Policy</a></li>
                                <li><a class="text-muted" href="privacy_policy" class="menu-link-text">Privacy Policy</a></li>
                                <li><a class="text-muted" href="disclaimer" class="menu-link-text">Disclaimer</a></li>
                                <li><a class="text-muted" href="faq" class="menu-link-text">FAQ's</a></li>
                                <li><a class="text-muted" href="contact" class="menu-link-text">Contact Us</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="navbar-nav header-offcanvas d-lg-none" tabindex="-1">
                    <!-- close icon -->
                    <a href="#!" class="btn offcanvas-close text-reset" data-bs-dismiss="offcanvas">
                        <i class="las la-times"></i>
                    </a>
                    <div class="offcanvas-body p-0">
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            <li class="nav-item " role="presentation">
                                <button class="nav-link active text-uppercase" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Menu</button>
                            </li>
                            <li class="nav-item col-6 p-0" role="presentation">
                                <button class="nav-link text-uppercase" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">categories</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                <div class="accordion" id="accordionPanelsStayOpenExample">
                                    <?php
                                        $c1 = mysqli_query($con, 'SELECT * FROM producttype');
                                        while ($c = mysqli_fetch_row($c1)) 
                                        {
                                    ?>
                                    <a href="shop?pt_id=<?php echo $c[0]; ?>" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" type="button"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></button>
                                    </a>
                                    <?php
                                        }
                                        $f1 = mysqli_query($con, 'SELECT * FROM collection');
                                        if ($f = mysqli_fetch_row($f1)) 
                                        {
                                    ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingFive">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFive" aria-expanded="false" aria-controls="panelsStayOpen-collapseFive">Collection</button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseFive" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFive">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <?php
                                                $c1 = mysqli_query($con, 'SELECT * FROM collection');
                                                while ($c = mysqli_fetch_row($c1)) 
                                                {  
                                                ?>
                                                <li><a href="shop?collect_id=<?php echo $c[0]; ?>"><?php echo htmlspecialchars($c[1], ENT_QUOTES, 'UTF-8'); ?></a></li>
                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                        }
                                    ?>
                                    <a href="stores_events" class="pill-item col-6 p-0" role="presentation">
                                        <button class="nav-link" type="button">Store</button>
                                    </a>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                                Get In Touch                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
                                            <ul class="accordion-nav-list list-unstyled mb-0">
                                                <li><a href="term-of-use">Terms & Condition</a></li>
                                                <li><a href="return_refund">Return & Refund Policy</a></li>
                                                <li><a href="cancellation_policy">Cancellation Policy</a></li>
                                                <li><a href="shipping_policy">Shipping Policy</a></li>
                                                <li><a href="privacy_policy">Privacy Policy</a></li>
                                                <li><a href="disclaimer">Disclaimer</a></li>
                                                <li><a href="faq">FAQ's</a></li>
                                                <li><a href="contact">Contact Us</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                   
                                    <!-- <a href="#!" class="pill-item col-6 p-0" role="presentation" data-bs-toggle="modal" data-bs-target="#searchModal">
                                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-search fw-medium me-1"></i> Search</button>
                                    </a> -->
                                     <?php if (isset($_SESSION['u_id'])): ?>
                                     <div class="accordion-item">
                                         <h2 class="accordion-header" id="panelsStayOpen-headingAccount">
                                             <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseAccount" aria-expanded="false" aria-controls="panelsStayOpen-collapseAccount">
                                                 <i class="iccl iccl-user fw-medium me-1"></i> My Account
                                             </button>
                                         </h2>
                                         <div id="panelsStayOpen-collapseAccount" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingAccount">
                                             <ul class="accordion-nav-list list-unstyled mb-0">
                                                 <li><a href="myprofile">My Profile</a></li>
                                                 <li><a href="order_history">Order History</a></li>
                                                 <li><a href="change_password">Change Password</a></li>
                                                 <li><a href="?logout=1">Logout</a></li>
                                             </ul>
                                         </div>
                                     </div>
                                     <?php else: ?>
                                     <a href="#accountOffcanvas" data-bs-toggle="offcanvas" class="pill-item col-6 p-0" role="presentation">
                                         <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"> <i class="iccl iccl-user fw-medium me-1"></i> Login /
                                             Register</button>
                                     </a>
                                     <?php endif; ?>

                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="topbar-toolbar d-flex align-items-center gap-3">
                <!-- <a data-bs-toggle="offcanvas" href="#searchOffcanvas" aria-controls="searchOffcanvas"><i class="iccl iccl-search"></i></a> -->
                <?php if (isset($_SESSION['u_id'])): ?>
                <div class="dropdown d-inline-block header-user-dropdown">
                    <a href="#" class="dropdown-toggle text-reset" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="iccl iccl-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuButton">
                        <li><a class="dropdown-item" href="myprofile">My Profile</a></li>
                        <li><a class="dropdown-item" href="order_history">Order History</a></li>
                        <li><a class="dropdown-item" href="change_password">Change Password</a></li>
                        <li><a class="dropdown-item" href="?logout=1">Logout</a></li>
                    </ul>
                </div>
                <?php else: ?>
                <a class="d-md-block" data-bs-toggle="offcanvas" href="#accountOffcanvas" aria-controls="accountOffcanvas"><i class="iccl iccl-user"></i></a>
                <?php endif; ?>
                <a data-bs-toggle="offcanvas" href="#shoppingCartOffcanvas" aria-controls="shoppingCartOffcanvas"><i class="iccl iccl-cart"></i><span class="tcount bg-dark text-white rounded-circle d-flex align-items-center justify-content-center"><?php echo $total_cart_count; ?></span></a>
            </div>
    </nav>
</div>