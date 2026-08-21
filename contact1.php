<?php
ob_start();
session_start();
include_once("connect.php");
$msg="";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <title>Ethic Design Studio</title>

    <meta name="author" content="Nupur Parmar">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="Ethic Design Studio">

   <!-- font -->
   <link rel="stylesheet" href="fonts/fonts.css">
   <link rel="stylesheet" href="fonts/font-icons.css">
   <link rel="stylesheet" href="css/bootstrap.min.css">
   <link rel="stylesheet" href="css/drift-basic.min.css">
   <link rel="stylesheet" href="css/photoswipe.css">
   <link rel="stylesheet" href="css/swiper-bundle.min.css">
   <link rel="stylesheet" href="css/animate.css">
   <link rel="stylesheet" href="sib-styles.css">
   <link rel="stylesheet" type="text/css" href="css/styles.css">

    <!-- Favicon and Touch Icons  -->
    <link rel="shortcut icon" href="images/logo/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="images/logo/favicon.png">

</head>

<body class="preload-wrapper">
    <!-- Scroll Top -->
    <button id="scroll-top">
        <svg width="24" height="25" viewbox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_15741_24194)">
            <path d="M3 11.9175L12 2.91748L21 11.9175H16.5V20.1675C16.5 20.3664 16.421 20.5572 16.2803 20.6978C16.1397 20.8385 15.9489 20.9175 15.75 20.9175H8.25C8.05109 20.9175 7.86032 20.8385 7.71967 20.6978C7.57902 20.5572 7.5 20.3664 7.5 20.1675V11.9175H3Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </g>
            <defs>
            <clippath id="clip0_15741_24194">
            <rect width="24" height="24" fill="white" transform="translate(0 0.66748)"></rect>
            </clippath>
            </defs>
        </svg> 
    </button>

    <!-- preload -->
    <div class="preload preload-container">
        <div class="preload-logo">
            <div class="spinner"></div>
        </div>
    </div>
    <!-- /preload -->

    <!-- #wrapper -->
    <div id="wrapper">

        <?php $customer_service = true; include_once("header.php"); ?>

        <!-- page-title -->
        <?php
        $query = mysqli_query($con, "SELECT * FROM banner WHERE b_id = 1");
        if ($row = mysqli_fetch_assoc($query)) {
            $banner = $row['pic'];
        }
        ?>
        <div class="page-title" style="background-image: url(<?php echo $banner; ?>);">
            <div class="container-full">
                <div class="row">
                    <div class="col-12">
                        <h3 class="heading text-center">Store Location</h3>
                        <ul class="breadcrumbs d-flex align-items-center justify-content-center">
                            <li>
                                <a class="link" href="index">Home</a>
                            </li>
                            <li>
                                <i class="icon-arrRight"></i>
                            </li>
                            <li>
                                <a class="link" href="#">Customer Service</a>
                            </li>
                            <li>
                                <i class="icon-arrRight"></i>
                            </li>
                            <li>
                                Contact Us
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- /page-title -->

        <!-- Store locations -->
        <section class="flat-spacing">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-md-5 col-12">
                        <div class="tf-store-list">
                            <?php
                            $j=1; $first_map=''; $first_pic='';
                            $p1=mysqli_query($con,"select * from stores where status='1'");
                            while($p=mysqli_fetch_assoc($p1))
                            {
                                if($j==1) { $first_map=$p['map'];  $first_pic=$p['pic']; }
                            ?>
                            <div class="tf-store-item" data-map="<?php echo htmlspecialchars_decode($p['map']); ?>"  data-img="<?php echo htmlspecialchars_decode($p['pic']); ?>" onclick="changeMap(this)">
                                <h6 class="tf-store-title text-center">
                                    <?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </h6>
                                <div class="tf-store-contact">
                                    <div class="tf-store-info">
                                        <p class="text-button">Opening Time:</p>
                                        <p class="text-secondary"><?php echo htmlspecialchars($p['open_timings'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="tf-store-info">
                                        <p class="text-button">Phone:</p>
                                        <p class="text-secondary"><?php echo htmlspecialchars($p['contact'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="tf-store-info">
                                        <p class="text-button">Email:</p>
                                        <p class="text-secondary"><?php echo htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                </div>
                                <div class="tf-store-address tf-store-info">
                                    <p class="text-button">Address:</p>
                                    <p class="text-secondary"><?php echo htmlspecialchars($p['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <?php
                            $j++;
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-xl-8 col-md-7 col-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="wrap-map" style="width: 100%; height: 500px; overflow: hidden; background: transparent; text-align: center; vertical-align:middle; line-height: 500px;" >
                                    <img  id="imgMap" src="<?php echo $first_pic; ?>" style="max-width: 100%; max-height: 100%; vertical-align: middle;" />
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="wrap-map">
                                    <iframe  id="storeMap" src="<?php echo $first_map; ?>" width="100%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </section>
        <!-- /Store locations -->

        <!-- Footer -->
        <?php include_once("footer.php"); ?>
        <!-- /Footer -->
        
    </div>
    <!-- /wrapper -->

    <!-- search -->
    <?php include_once('searchpopup.php'); ?>
    <!-- /search -->
      
    <!-- mobile menu -->
    <?php include_once("mobile_menu.php"); ?>
    <!-- /mobile menu -->

    <!-- Shopping Cart  -->
    <?php if(isset($_SESSION['user_account'])) include_once('shoppingcartmodal.php'); ?>
    <!-- /Shopping Cart -->
    
    <!-- Javascript -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="js/lazysize.min.js"></script>
    <script type="text/javascript" src="js/wow.min.js"></script>
    <script type="text/javascript" src="js/count-down.js"></script>
    <script type="text/javascript" src="js/swiper-bundle.min.js"></script>
    <script type="text/javascript" src="js/carousel.js"></script>
    <script type="text/javascript" src="js/multiple-modal.js"></script>
    <script type="text/javascript" src="js/main.js"></script>

    <script src="js/sibforms.js" defer=""></script>
    <script>
    function changeMap(element)
    {
        var mapUrl = element.getAttribute('data-map');
        var imgUrl = element.getAttribute('data-img');
        document.getElementById('storeMap').src = mapUrl;
        document.getElementById('imgMap').src = imgUrl;
    }
    </script>
    <script type="text/javascript" src="js/marker.js"></script>
    <script type="text/javascript" src="js/infobox.min.js"></script>
    
</body>

</html>