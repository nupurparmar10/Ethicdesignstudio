<?php
ob_start();
session_start();
include_once("connect.php");
$showPopup = !isset($_SESSION['discount_popup_closed']);
if(get_input('val', 'string', null, 'request') !== null)
{
    session_unset();
}
$msg="";
?>
<!doctype html>
<html lang="en" x-data :dir="$store.appStore.dir" x-cloak>

<head>

    <meta charset="utf-8" />
    <title>Ethic Design Studio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="" name="description" />
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
    <style>
        @media (max-width: 991.98px) {
            .kalles-banner-swiper .kalles-banner-parallax-layout-01 {
                min-height: 0;
                height: auto;
                background-size: 100% 100% !important;
                background-position: center center !important;
                background-color: #f4f4f4; 
                min-height: 10% !important;
            }
            .kalles-banner-swiper .kalles-banner-parallax-layout-01::before {
                content: "";
                display: block;
                padding-top: 75%;
            }
        }
        
        /* Marquee strip below the slider */
        .kalles-marquee-strip {
            overflow: hidden;
            background-color: #e87038;
            padding: 16px 0;
        }
        .kalles-marquee-track {
            display: flex;
            align-items: center;
            width: max-content;
            animation: kalles-marquee-scroll 22s linear infinite;
        }
        .kalles-marquee-strip:hover .kalles-marquee-track {
            animation-play-state: paused;
        }
        .kalles-marquee-item {
            display: inline-flex;
            align-items: center;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            white-space: nowrap;
            padding: 0 24px;
        }
        .kalles-marquee-item::after {
            content: "\2022";
            margin-left: 24px;
            color: #ffffff;
        }
        @keyframes kalles-marquee-scroll {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        @media (max-width: 767.98px) {
            .kalles-marquee-strip { padding: 12px 0; }
            .kalles-marquee-item { font-size: 13px; padding: 0 16px; }
            .kalles-marquee-item::after { margin-left: 16px; }
            .kalles-marquee-track { animation-duration: 16s; }
        }
        @media (max-width: 480px) {
            .kalles-marquee-item { font-size: 11.5px; padding: 0 12px; }
            .kalles-marquee-item::after { margin-left: 12px; }
            .kalles-marquee-track { animation-duration: 12s; }
        }

        /* Shipping / perks section cards */
        .kalles-section-type-shipping {
            padding: 20px 0;
        }
        .kalles-section-type-shipping .shipping-card {
            height: 100%;
            padding: 28px 22px;
            border: 1px solid rgba(232, 112, 56, 0.2);
            border-radius: 14px;
            background: linear-gradient(135deg, #fff7f1 0%, #fdece0 100%);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .kalles-section-type-shipping .shipping-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(232, 112, 56, 0.12);
            border-color: rgba(232, 112, 56, 0.45);
        }
        .kalles-section-type-shipping .shipping-card i {
            color: #e87038 !important;
        }
        @media (max-width: 767.98px) {
            .kalles-section-type-shipping { padding: 35px 0; }
            .kalles-section-type-shipping .shipping-card { padding: 22px 18px; }
        }

       .top-svg,
        .bottom-svg {
            height: 24px;
            object-fit: cover;
        }
        .top-svg {
            top: -12px;
        }
        .bottom-svg {
            bottom: -12px;
        }

        @media (max-width: 991.98px) {
            .top-svg,
            .bottom-svg {
                height: 18px;
            }
            .top-svg {
                top: -9px;
            }
            .bottom-svg {
                bottom: -9px;
            }
        }

        @media (max-width: 767.98px) {
            .top-svg,
            .bottom-svg {
                height: 14px;
            }
            .top-svg {
                top: -7px;
            }
            .bottom-svg {
                bottom: -7px;
            }
        }

        @media (max-width: 480px) {
            .top-svg,
            .bottom-svg {
                height: 10px;
            }
            .top-svg {
                top: -5px;
            }
            .bottom-svg {
                bottom: -5px;
            }
        }
        /* Responsive category grid (Shop by Category) */
        .col-5-cat {
            width: 20%;
            padding-right: calc(var(--bs-gutter-x) * .5);
            padding-left: calc(var(--bs-gutter-x) * .5);
        }
        @media (max-width: 991.98px) {
            .col-5-cat { width: 33.3333%; }
        }
        @media (max-width: 767.98px) {
            .col-5-cat { width: 33.3333%; }
        }
        @media (max-width: 480px) {
            .col-5-cat { width: 33.3333%; }
        }

        /* Category card: image on top, plain label below (matches mobile design) */
        .cat_grid_item {
            display: block;
        }
        .cat_grid_item .cat-grid-img-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 3 / 4;
            overflow: hidden;
            background: #f4f4f4;
            border-radius: 10px;
        }
        .cat_grid_item .cat-grid-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .cat_grid_item:hover .cat-grid-img {
            transform: scale(1.05);
        }
        .cat_grid_item .cat_grid_item__title {
            margin-top: 10px;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: inherit;
        }
        @media (max-width: 767.98px) {
            .cat_grid_item .cat-grid-img-wrap { border-radius: 8px; }
            .cat_grid_item .cat_grid_item__title { font-size: 10px; margin-top: 6px; letter-spacing: 0.2px; }
        }
        @media (max-width: 480px) {
            .cat_grid_item .cat_grid_item__title { font-size: 9px; }
        }
        .trending-product-img,
        .material-grid-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
    </style>
</head>

<body class="" x-data="{ showMenuScroll : false }">
<?php include_once("header.php"); ?>
<div class="backdrop-shadow d-none"></div><div>
    <?php
    $k1=mysqli_query($con,"select * from slider order by s_id");
    ?>
    <div class="swiper kalles-banner-swiper">
        <div class="swiper-wrapper">
            <?php
            while($k=mysqli_fetch_assoc($k1))
            {
                if($k['pic']=='') continue;
            ?>
            <div class="swiper-slide">
                <section class="kalles-banner-parallax-layout-01 min-vh-100 position-relative" style="background-image: url('<?php echo $k['pic']; ?>'); background-repeat: no-repeat; background-size: cover; background-position: center top;">
                    <div class="position-absolute top-50 start-50 translate-middle text-white text-center my-5">
                        <h1 class="text-uppercase fs-60"><?php echo $k['title']; ?></h1>
                        <h3 class="fs-18 font-secondary fst-italic"><?php echo $k['desp']; ?></h3>
                        <a href="<?php echo $k['link']; ?>" class="btn btn-custom-white text-white min-w-150 rounded-pill mt-4">Shop Now</a>
                    </div>
                </section>
            </div>
            <?php
            }
            ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>

    <?php
    $marqueeItems = ["Ethically Made", "Size Inclusive", "Sustainable", "Soft Fabrics"];
    ?>
    <div class="kalles-marquee-strip">
        <div class="kalles-marquee-track">
            <?php
            for ($r = 0; $r < 2; $r++) {
                foreach ($marqueeItems as $item) {
            ?>
            <span class="kalles-marquee-item"><?php echo htmlspecialchars($item); ?></span>
            <?php
                }
            }
            ?>
        </div>
    </div>

    <section class="cat-section">
        <div class="container-fluid mb-30">
            <div class="row g-xl-4 g-3">
                <?php
                $k1=mysqli_query($con,"select * from pro_subcategory order by s_id");
                while($k=mysqli_fetch_assoc($k1))
                {
                ?>
                <div class="col-5-cat">
                    <a href="shop?pt_id=<?php echo $k['pt_id']; ?>" class="cat_grid_item text-decoration-none">
                        <div class="cat-grid-img-wrap">
                            <img class="cat-grid-img" src="<?php echo $k['sub_pic']; ?>" alt="<?php echo htmlspecialchars($k['sname'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                        </div>
                        <div class="cat_grid_item__title"><?php echo $k['sname']; ?></div>
                    </a>
                </div><!--end col-->
                <?php
                }
                ?>
            </div><!--end row-->
        </div>
        <div class="container-fluid mb-30">
            <div class="row g-lg-4 g-3 align-items-center">
                <?php
                    $m1=mysqli_query($con,"select * from matter where m_id='26'");
                    if($m=mysqli_fetch_assoc($m1))
                    {
                ?>
                <div class="col-md-12">
                    <a href="#!" class="kalles-banner-promotion d-block">
                        <img src="<?php echo $m['pic']; ?>" alt="" class="img-fluid">
                        <div class="p-20 position-absolute bottom-0 left-0 text-body">
                            <h3 class="fs-35"><?php echo $m['title']; ?></h3>
                            <p class="text-muted mb-0"><?php echo $m['desp']; ?></p>
                        </div>
                    </a>
                </div><!--end col-->
                <?php
                    }
                ?>
            </div><!--end row-->
        </div><!--end container-->
    </section>

     <!-- TRENDING -->
    <section>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center">
                        <div class="mb-2">
                            <h3 class="section-title position-relative flex">
                                <span>TRENDING</span>
                            </h3>
                        </div>
                        <span class="section-subtitle sub-title font-secondary fst-italic fs-14 text-muted">Top view in
                            this week</span>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
            <div class="row g-lg-4 g-3 mt-4">
                <?php
                $p1=mysqli_query($con,"SELECT v.*,i.saledesp,GROUP_CONCAT(DISTINCT v.size ORDER BY v.size SEPARATOR ',') AS available_sizes,GROUP_CONCAT(DISTINCT vp.pic ORDER BY vp.id SEPARATOR ',') AS available_pics FROM variant v JOIN item_details i ON v.item_id=i.item_id LEFT JOIN variant_pic vp ON vp.v_id=v.v_id WHERE v.webstock>0 AND i.status=1 GROUP BY v.item_id ORDER BY RAND() LIMIT 4");
                while($p=mysqli_fetch_assoc($p1))
                {
                    $pics = !empty($p['available_pics']) ? explode(',', $p['available_pics']) : [];
                    $img1 = !empty($pics[0]) ? "ethic_crm/".$pics[0] : "assets/images/ethic-logo - Copy.png";
                    $img2 = !empty($pics[1]) ? "ethic_crm/".$pics[1] : $img1;
                ?>
                <div class="col-md-3 col-6">
                    <div x-data="{ imageUrl: '<?php echo $img1; ?>', isHovered: false }" class="topbar-product-card pb-3" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                        <div class="position-relative overflow-hidden" style="width: 100%; height: 350px; overflow: hidden; background: transparent;">
                            <img :src="isHovered ? '<?php echo $img2; ?>' : imageUrl" alt="" class="trending-product-img" style="z-index: 1; width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
                            
    
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="productdetail?item_id=<?php echo htmlspecialchars($p['item_id'], ENT_QUOTES, 'UTF-8'); ?>"  class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                            </div>  
                        </div>
                        <a href="productdetail?item_id=<?php echo htmlspecialchars($p['item_id'], ENT_QUOTES, 'UTF-8'); ?>" class="mt-3 d-block">
                            <h6 class="mb-1 text-center"><?php echo htmlspecialchars($p['saledesp'], ENT_QUOTES, 'UTF-8'); ?></h6>
                            <p class="mb-0 fs-14 text-muted text-center">
                                <span>&#8377;<?php echo htmlspecialchars($p['edsellrate'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </p>
                        </a>
                    </div>
                </div>
                <?php
                }
                ?>
            </div><!--end row-->
        </div><!--end container-->
    </section>

    <!-- lookbook -->
    <div class="banner-section position-relative" style="background-color: #fde9d7; margin-top:3%;">
        <img src="top.svg" alt="" class="position-absolute top-svg start-0 w-100" style="z-index: 2; pointer-events: none;">

        <div class="container-fluid">
            <div class="row g-4">
                <?php
                $m=mysqli_fetch_assoc(mysqli_query($con,"select * from matter where m_id='30'")); 
                ?>
                <div class="col-lg-6">
                    <a href="#!" class="position-relative hover-zoom d-block">
                        <img src="<?php echo $m['pic']; ?>" alt="" class="img-fluid hover-zoom-img">
                        <div class="position-absolute start-0 start-0 end-0 top-0 bottom-0 d-flex align-items-center justify-content-center">
                            <div class="text-center text-white">
                                <h4 class="fs-24"><?php echo $m['title']; ?></h4>
                                <h6 class="mb-0"><?php echo $m['desp']; ?></h6>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
                $m=mysqli_fetch_assoc(mysqli_query($con,"select * from matter where m_id='31'")); 
                ?>
                <div class="col-lg-6">
                    <a href="#!" class="position-relative hover-zoom d-block">
                        <img src="<?php echo $m['pic']; ?>" alt="" class="img-fluid hover-zoom-img">
                        <div class="position-absolute start-0 start-0 end-0 top-0 bottom-0 d-flex align-items-center justify-content-center">
                            <div class="text-center text-white">
                                <h6 class="text-capitalize mb-2"><?php echo $m['title']; ?></h6>
                                <h1 class="mb-0" style="font-size: 50px;"><?php echo $m['desp']; ?></h1>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <img src="bottom.svg" alt="" class="position-absolute bottom-svg start-0 w-100" style="z-index: 2; pointer-events: none;">
    </div>

    <section style="padding-top:50px;">
        <div class="container" dir="ltr">
            <div class="row justify-content-center ">
                <div class="col-lg-7">
                    <div class="text-center">
                        <div class="mb-2">
                            <h3 class="section-title position-relative flex">
                                <span>Best Pick Under 1500</span>
                            </h3>
                        </div>
                        <span class="section-subtitle sub-title font-secondary fst-italic fs-14 text-muted">Everyday Essential</span>
                    </div>
                </div>
            </div>
            <div class="row my-4 py-2 tranding-card" data-flickity='{"imagesLoaded": 0, "adaptiveHeight": 0, "contain": 1, "groupCells": "100%", "dragThreshold": 5, "cellAlign": "left", "wrapAround": true, "prevNextButtons": true, "percentPosition": 1, "pageDots": false, "autoPlay": 0, "pauseAutoPlayOnHover": true }' dir="ltr">
                <?php
                $p1=mysqli_query($con,"SELECT v.*,i.saledesp,GROUP_CONCAT(DISTINCT v.size ORDER BY v.size SEPARATOR ',') AS available_sizes,GROUP_CONCAT(DISTINCT vp.pic ORDER BY vp.id SEPARATOR ',') AS available_pics FROM variant v JOIN item_details i ON v.item_id=i.item_id LEFT JOIN variant_pic vp ON vp.v_id=v.v_id WHERE v.webstock>0 AND i.status=1 AND v.edsellrate<=1500 GROUP BY v.item_id ORDER BY RAND()");
                while($p=mysqli_fetch_assoc($p1))
                {
                    $pics = !empty($p['available_pics']) ? explode(',', $p['available_pics']) : [];
                    $img1 = !empty($pics[0]) ? "ethic_crm/".$pics[0] : "assets/images/ethic-logo - Copy.png";
                    $img2 = !empty($pics[1]) ? "ethic_crm/".$pics[1] : $img1;
                ?>
                <div class="col-md-3 col-6 px-lg-12 px-2">
                    <div x-data="{ imageUrl: '<?php echo $img1; ?>', isHovered: false }" class="topbar-product-card" x-on:mouseenter="isHovered = true" x-on:mouseleave="isHovered = false">
                        <div class="position-relative overflow-hidden" style="width: 100%; height: 300px; overflow: hidden; background: transparent;">
                            <img :src="isHovered ? '<?php echo $img2; ?>' : imageUrl" alt="" class="trending-product-img" style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
                            <div class="product-button d-none d-lg-flex flex-column gap-2">
                                <a href="productdetail?item_id=<?php echo htmlspecialchars($p['item_id'], ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                            </div>
                        </div>
                        <a href="productdetail?item_id=<?php echo htmlspecialchars($p['item_id'], ENT_QUOTES, 'UTF-8'); ?>" class="mt-3 d-block">
                            <h6 class="mb-1 text-center"><?php echo htmlspecialchars($p['saledesp'], ENT_QUOTES, 'UTF-8'); ?></h6>
                            <p class="mb-0 fs-14 text-muted text-center">
                                <span>&#8377;<?php echo htmlspecialchars($p['edsellrate'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </p>
                        </a>
                    </div>
                </div>
                <?php
                    }
                ?>

            </div>
        </div>
    </section>

    <div class="container-fluid">
        <hr class="my-0" style="border-top: 2px solid rgb(233 116 61);">
    </div>

    <div class="kalles-section-type-shipping" style="padding: 40px 0 40px;">
        <div class="container-fluid mb-30">
            <div class="row g-4">
                <div class="col-xl-3 col-md-6">
                    <?php
                        $k=mysqli_fetch_assoc(mysqli_query($con,"select * from matter where m_id='17'"));
                    ?>
                    <div class="shipping-card d-flex gap-3">
                        <i class="pegk pe-7s-car fs-36 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase"><?php echo $k['title']; ?></h6>
                            <p class="text-muted mb-0"><?php echo $k['desp']; ?></p>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-xl-3 col-md-6">
                    <?php
                        $k=mysqli_fetch_assoc(mysqli_query($con,"select * from matter where m_id='18'"));
                    ?>
                    <div class="shipping-card d-flex gap-3">
                        <i class="pegk pe-7s-help2 fs-36 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase"><?php echo $k['title']; ?></h6>
                            <p class="text-muted mb-0"><?php echo $k['desp']; ?></p>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-xl-3 col-md-6">
                    <?php
                        $k=mysqli_fetch_assoc(mysqli_query($con,"select * from matter where m_id='19'"));
                    ?>
                    <div class="shipping-card d-flex gap-3">
                        <i class="pegk pe-7s-refresh fs-36 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase"><?php echo $k['title']; ?></h6>
                            <p class="text-muted mb-0"><?php echo $k['desp']; ?></p>
                        </div>
                    </div>
                </div><!--end col-->
                <div class="col-xl-3 col-md-6">
                    <?php
                        $k=mysqli_fetch_assoc(mysqli_query($con,"select * from matter where m_id='20'"));
                    ?>
                    <div class="shipping-card d-flex gap-3">
                        <i class="pegk pe-7s-door-lock fs-36 flex-shrink-0"></i>
                        <div class="flex-grow-1">
                            <h6 class="text-uppercase"><?php echo $k['title']; ?></h6>
                            <p class="text-muted mb-0"><?php echo $k['desp']; ?></p>
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </div><!--end shipping-->

    <section>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center mb-4">
                        <div>
                            <h3 class="section-title position-relative flex text-uppercase">
                                <span>Explore Material</span>
                            </h3>
                        </div>
                    </div>
                </div><!--end col-->
            </div><!--end row-->
            <div class="row" data-flickity='{"imagesLoaded": 0,"adaptiveHeight": 1, "contain": 1, "groupCells": "100%", "dragThreshold" : 5, "cellAlign": "left","wrapAround": false,"prevNextButtons": true,"percentPosition": 1,"pageDots": false, "autoPlay" : 0, "pauseAutoPlayOnHover" : true, "rightToLeft": false }' dir="ltr">
                <?php
                $k1=mysqli_query($con,"select * from material_type order by m_id");
                while($m=mysqli_fetch_assoc($k1))
                {
                ?>
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="insta-card position-relative" >
                        <img src="<?php echo $m['pic']; ?>" alt="" class="img-fluid material-grid-img" style="width: 100%; height:180px; object-fit: cover; object-position: center; background: #f7f7f7; display: block;">
                         <a href="#!" class="card-spin position-02 position-absolute fs-14 text-white  fw-semibold d-flex align-items-center justify-content-center text-center" style="width: 50%; height: 25%;line-height:1.15; padding:6px; word-break:break-word; white-space:normal; text-decoration:none;background-color: rgb(255 255 255) !important;     color: black !important;"><?php echo htmlspecialchars($m['type'], ENT_QUOTES, 'UTF-8'); ?></a>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonials-section py-5" style="background-color: #f9f9f9; border-top: 1px solid #eee;">
        <div class="container" dir="ltr">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="text-center">
                        <div class="mb-2">
                            <h3 class="section-title position-relative flex">
                                <span>Customer Reviews</span>
                            </h3>
                        </div>
                        <span class="section-subtitle sub-title font-secondary fst-italic fs-14 text-muted">What our customers say about us</span>
                    </div>
                </div>
            </div>
            
            <?php
            $testimonialQuery = mysqli_query($con, "SELECT * FROM testimonial WHERE status = 1 ORDER BY t_id DESC");
            if (mysqli_num_rows($testimonialQuery) > 0) {
            ?>
            <div class="row my-4 py-2" data-flickity='{"imagesLoaded": 0, "adaptiveHeight": 0, "contain": 1, "groupCells": "100%", "dragThreshold": 5, "cellAlign": "left", "wrapAround": true, "prevNextButtons": true, "percentPosition": 1, "pageDots": true, "autoPlay": 4000, "pauseAutoPlayOnHover": true }' dir="ltr">
                <?php
                while ($t = mysqli_fetch_assoc($testimonialQuery)) {
                    $rating = (int)$t['rating'];
                    $rating = $rating > 5 ? 5 : ($rating < 1 ? 5 : $rating); // Ensure 1-5
                ?>
                <div class="col-lg-6 col-12 px-lg-3 px-2 mb-2">
                    <div class="card border-0 shadow-sm p-4 h-100" style="background-color: #fff; border-radius: 10px;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; font-size: 20px; font-weight: bold; background-color: #e9743d !important;">
                                    <?php echo strtoupper(substr($t['name'], 0, 1)); ?>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-semibold text-capitalize" style="font-size: 16px;"><?php echo htmlspecialchars($t['name']); ?></h5>
                                <div class="text-warning fs-14">
                                    <?php for($i=1; $i<=5; $i++) { ?>
                                        <i class="pegk pe-7s-star <?php echo $i <= $rating ? 'fw-bold' : ''; ?>" <?php echo $i <= $rating ? 'style="color: #ffb800;"' : 'style="color: #ccc;"'; ?>></i>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted fst-italic mb-0" style="line-height: 1.6; font-size: 14px;">
                            "<?php echo nl2br(htmlspecialchars($t['msg'])); ?>"
                        </p>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
            <?php
            } else {
            ?>
            <div class="text-center text-muted py-4">
                <p>No reviews available yet.</p>
            </div>
            <?php
            }
            ?>
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


<!-- JAVASCRIPT -->
<script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/libs/jquery/jquery.min.js" ></script>
<script src="assets/js/store.js" ></script>
<script src="assets/libs/jarallax/jarallax.min.js" ></script>
<script src="assets/libs/swiper/swiper-bundle.min.js" ></script>
<script >
    var bannerSwiper = new Swiper(".kalles-banner-swiper", {
        loop: true,
        effect: "fade",
        speed: 800,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".kalles-banner-swiper .swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".kalles-banner-swiper .swiper-button-next",
            prevEl: ".kalles-banner-swiper .swiper-button-prev",
        },
    });
</script>
<script src="assets/libs/alpinejs/cdn.min.js" ></script>
<script src="assets/libs/jquery-countdown/jquery.countdown.min.js" ></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js" ></script>
<script src="assets/js/product-slider.init.js" ></script>
<script src="assets/js/popup.js" ></script>

<script src="assets/libs/flickity/flickity.pkgd.min.js" ></script>
<script src="assets/js/main.js" ></script>
<script src="assets/js/app.js" ></script>

<script src="cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js" data-cf-settings="8a57402e6d1e875b1f03d525-|49" defer></script></body>


</html>