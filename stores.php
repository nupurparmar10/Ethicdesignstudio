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
                <h4 class="fs-30 fw-medium">Our Stores</h4>
            </div>
        </div>
    </div>
    <!-- end main slide -->

    <!-- Stores -->
    <section>
        <div class="container">
            <div class="my-4">
                <?php
                    $stores = mysqli_query($con, "select * from stores where status ='1' order by store_id desc");
                    if ($stores && mysqli_num_rows($stores) > 0) {
                ?>
                <div class="row kalles-blog-grid my-4 g-4">
                    <?php
                        $stores = mysqli_query($con, "select * from stores where status ='1' order by store_id desc");
                        while($m=mysqli_fetch_assoc($stores))
                        {
                            $pic = !empty($m['pic']) ?  $m['pic'] : "assets/images/default-store.png";
                    ?>
                    <div class="col-md-4 slideshow__slide " style="height:max-content !important ;">
                        <div class="blog_grid overflow-hidden">
                            <div class=" blog_grid_img w-100 position-relative" style="background: url('<?php echo $pic; ?>') center no-repeat; background-size: cover; height: 400px;">
                            </div>
                            <div class="card rounded-0 border-0 bg-black position-absolute m-4 bottom-0 start-0 end-0 z-3">
                                <div class="card-body text-center">
                                    <h2 class="fs-14 text-uppercase blog_grid_img_heading my-2">
                                        <span class="text-white"><?php echo htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </h2>
                                    <span>
                                        <span class="text-white-50">Timings: <?php echo htmlspecialchars($m['open_timings'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 text-center blog-grid-contain">
                            <p class="text-muted mb-2"><strong>Address:</strong> <?php echo htmlspecialchars($m['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="text-muted mb-3"><strong>Contact:</strong> <?php echo htmlspecialchars($m['contact'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php if (!empty($m['email'])) { ?> | <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8'); ?></a><?php } ?>
                            </p>
                            <?php if (!empty($m['map'])) { ?>
                            <a href="<?php echo htmlspecialchars($m['map'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-4 fw-semibold mx-auto mb-3">View on Map</a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
                <?php } else { ?>
                <p class="text-center text-muted mb-0">No stores found.</p>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- /Stores -->
    

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

<script >
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<script src="assets/libs/flickity/flickity.pkgd.min.js" ></script>
<script src="assets/js/main.js" ></script>
<script src="assets/js/app.js" ></script>

<script src="cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="2b1d929cf4eb8d82d86811fa-|49" defer></script></body>


</html> 
