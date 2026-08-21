<?php
ob_start();
session_start();
include_once("connect.php");
$msg="";
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>
<head>

    <meta charset="utf-8" />
    <title>Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="description" content="Ethic Design Studio">
    <meta content="The4" name="author" />
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
        $query = mysqli_query($con, "SELECT * FROM banner WHERE b_id = 1");
        if ($row = mysqli_fetch_assoc($query)) {
            $banner = $row['pic'];
        }
    ?>
    <div style="background-image: url('<?php echo $banner; ?>'); " class="position-relative">
        <div class="position-absolute top-0 start-0 right-0 bottom-0 bg-dark w-100 opacity-50"></div>
        <div class="px-4">
            <div class="text-white text-center py-51 position-relative">
                <h4 class="fs-30 fw-medium">About Us</h4>
            </div>
        </div>
    </div>
    <!-- end main slide -->

    <!-- about-us -->
    <section class="py-5">
        <div class="container">
            <?php
                $p = mysqli_fetch_assoc(mysqli_query($con, "select * from matter where m_id='12'"));
            ?>
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-4">
                    <div class="rounded-3 overflow-hidden" style="width: 100%; height: 350px; overflow: hidden; background: transparent; text-align: center; vertical-align:middle; line-height: 350px;">
                        <img class="lazyload img-fluid" data-src="<?php echo htmlspecialchars($p['pic'], ENT_QUOTES, 'UTF-8'); ?>" src="<?php echo htmlspecialchars($p['pic'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 100%; max-height: 100%; vertical-align: middle;     min-height: 350px;">
                    </div>
                </div>
                <div class="col-lg-8" style="margin-top:0px;">
                    <h4 class="fs-30 fw-medium mb-4"><?php echo htmlspecialchars($p['desp'], ENT_QUOTES, 'UTF-8'); ?></h4>

                    <ul class="nav nav-pills mb-3" id="about-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-uppercase" id="tab-intro-btn" data-bs-toggle="pill" data-bs-target="#tab-intro" type="button" role="tab" aria-controls="tab-intro" aria-selected="true">Introduction</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-uppercase" id="tab-vision-btn" data-bs-toggle="pill" data-bs-target="#tab-vision" type="button" role="tab" aria-controls="tab-vision" aria-selected="false">Our Vision</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-uppercase" id="tab-mission-btn" data-bs-toggle="pill" data-bs-target="#tab-mission" type="button" role="tab" aria-controls="tab-mission" aria-selected="false">Our Mission</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-uppercase" id="tab-commitment-btn" data-bs-toggle="pill" data-bs-target="#tab-commitment" type="button" role="tab" aria-controls="tab-commitment" aria-selected="false">Our Commitment</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="about-tabContent">
                        <div class="tab-pane fade show active" id="tab-intro" role="tabpanel" aria-labelledby="tab-intro-btn" tabindex="0">
                            <?php $t = mysqli_fetch_assoc(mysqli_query($con, "select * from matter where m_id='13'")); ?>
                            <p class="text-muted" style="word-wrap:break-word; white-space:pre-line; text-align:justify;"><?php echo htmlspecialchars($t['desp'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="tab-pane fade" id="tab-vision" role="tabpanel" aria-labelledby="tab-vision-btn" tabindex="0">
                            <?php $t = mysqli_fetch_assoc(mysqli_query($con, "select * from matter where m_id='14'")); ?>
                            <p class="text-muted" style="word-wrap:break-word; white-space:pre-line; text-align:justify;"><?php echo htmlspecialchars($t['desp'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="tab-pane fade" id="tab-mission" role="tabpanel" aria-labelledby="tab-mission-btn" tabindex="0">
                            <?php $t = mysqli_fetch_assoc(mysqli_query($con, "select * from matter where m_id='15'")); ?>
                            <p class="text-muted" style="word-wrap:break-word; white-space:pre-line; text-align:justify;"><?php echo htmlspecialchars($t['desp'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <div class="tab-pane fade" id="tab-commitment" role="tabpanel" aria-labelledby="tab-commitment-btn" tabindex="0">
                            <?php $t = mysqli_fetch_assoc(mysqli_query($con, "select * from matter where m_id='16'")); ?>
                            <p class="text-muted" style="word-wrap:break-word; white-space:pre-line; text-align:justify;"><?php echo htmlspecialchars($t['desp'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /about-us -->

    <!-- Iconbox -->
    <section class="py-5 border-top">
        <div class="container">
            <div class="row g-4 text-center">
                <?php
                    $icon_map = array('17' => 'pe-7s-refresh-2', '18' => 'pe-7s-plane', '19' => 'pe-7s-headphones', '20' => 'pe-7s-medal');
                    foreach ($icon_map as $mid => $icon_class) {
                        $m = mysqli_fetch_assoc(mysqli_query($con, "select * from matter where m_id='" . $mid . "'"));
                ?>
                <div class="col-6 col-md-3">
                    <div class="h-100 p-4 rounded-3 border" style="background-color: #eb8250; color:white;">
                        <i class="pegk <?php echo $icon_class; ?> fs-30 mb-3 d-block"></i>
                        <h6 class="mb-2" style="font-weight:700;"><?php echo htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8'); ?></h6>
                        <p class="mb-0 fs-14" style="color:white;"><?php echo htmlspecialchars($m['desp'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- /Iconbox -->
    

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

<script data-cfasync="false" src=".cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/libs/jquery/jquery.min.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/js/store.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/libs/jarallax/jarallax.min.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/libs/swiper/swiper-bundle.min.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/libs/alpinejs/cdn.min.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/libs/jquery-countdown/jquery.countdown.min.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/js/product-slider.init.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/js/popup.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>

<script type="2b1d929cf4eb8d82d86811fa-text/javascript">
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<script src="assets/libs/flickity/flickity.pkgd.min.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/js/main.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>
<script src="assets/js/app.js" type="2b1d929cf4eb8d82d86811fa-text/javascript"></script>

<script src="cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="2b1d929cf4eb8d82d86811fa-|49" defer></script></body>


</html>