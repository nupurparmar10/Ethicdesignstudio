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
                <h4 class="fs-30 fw-medium">Contact Us</h4>
            </div>
        </div>
    </div>
    <!-- end main slide -->

    <!-- Contact -->
    <section>
        <div class="container">
            <div class="my-4 my-md-5">
                <div class="text-center mb-4">
                    <h3 class="mb-2">Contact Us</h3>
                    <p class="text-muted mb-0">Visit our store or reach out to the team for assistance.</p>
                </div>
                <?php
                    $stores = mysqli_query($con,"select * from stores where status='1'");
                    if ($stores && mysqli_num_rows($stores) > 0) {
                ?>
                <div class="row g-4 align-items-start">
                    <div class="col-xl-4 col-lg-5">
                        <div class="nav flex-column nav-pills gap-3" id="store-location-tab" role="tablist" aria-orientation="vertical">
                            <?php
                                $j=1;
                                while($p=mysqli_fetch_assoc($stores))
                                {
                                    $storeTabId = 'store-location-' . $j;
                            ?>
                            <button class="nav-link text-start border rounded-0 p-3 <?php if($j==1) echo 'active'; ?>" id="<?php echo $storeTabId; ?>-tab" data-bs-toggle="pill" data-bs-target="#<?php echo $storeTabId; ?>" type="button" role="tab" aria-controls="<?php echo $storeTabId; ?>" aria-selected="<?php echo $j==1 ? 'true' : 'false'; ?>">
                                <span class="d-block fs-16 fw-medium text-uppercase text-center mb-3"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="d-flex gap-2 mb-2">
                                    <i class="pegk pe-7s-clock fs-14 fs-20 flex-shrink-0"></i>
                                    <span><span class="fw-medium">Opening Time:</span> <?php echo htmlspecialchars($p['open_timings'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                                <span class="d-flex gap-2 mb-2">
                                    <i class="pegk pe-7s-call fs-14 fs-20 flex-shrink-0"></i>
                                    <span><span class="fw-medium">Phone:</span> <?php echo htmlspecialchars($p['contact'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                                <span class="d-flex gap-2 mb-2">
                                    <i class="pegk pe-7s-mail fs-14 fs-20 flex-shrink-0"></i>
                                    <span><span class="fw-medium">Email:</span> <?php echo htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                                <span class="d-flex gap-2">
                                    <i class="pegk pe-7s-map-marker fs-20 flex-shrink-0"></i>
                                    <span><span class="fw-medium">Address:</span> <?php echo htmlspecialchars($p['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                            </button>
                            <?php
                                    $j++;
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7">
                        <div class="tab-content" id="store-location-tabContent">
                            <?php
                                $j=1;
                                $stores = mysqli_query($con,"select * from stores where status='1'");
                                while($p=mysqli_fetch_assoc($stores))
                                {
                                    $storeTabId = 'store-location-' . $j;
                            ?>
                            <div class="tab-pane fade <?php if($j==1) echo 'show active'; ?>" id="<?php echo $storeTabId; ?>" role="tabpanel" aria-labelledby="<?php echo $storeTabId; ?>-tab">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="border bg-light d-flex align-items-center justify-content-center overflow-hidden"  style="width: 100%; height: 420px; overflow: hidden; background: transparent; text-align: center; vertical-align:middle; line-height: 420px ;">
                                            <img src="<?php echo htmlspecialchars($p['pic'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 100%; max-height: 100%; vertical-align: middle;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="border overflow-hidden" style="min-height: 420px;">
                                            <iframe src="<?php echo htmlspecialchars_decode($p['map']); ?>" width="100%" height="420" frameborder="0" style="border:0; display:block;" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                    $j++;
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <p class="text-center text-muted mb-0">No store locations found.</p>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- /Contact -->
    

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

<!-- card model -->
<div class="modal fade modal-overl mx-auto" id="cardModal" tabindex="-1" role="dialog" aria-labelledby="cardLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered h-auto" role="document">
        <div class="modal-content position-relative p-1 mx-auto" style="max-width: 340px;">
            <div class="modal-body">
                <a href="#!" data-bs-dismiss="modal" class="fs-35 close position-absolute top-0 end-0" aria-label="Close">
                    <i class="pe-7s-close pegk"></i>
                </a>

                <div class="row">
                    <div class="col-4">
                        <img src="assets/images/quick_shop/p_qs_01.jpg" class="img-fluid" alt="">
                    </div>
                    <div class="col-8">
                        <h6><a class="cd chp" href="product-detail-layout-01.html">Cluse La Boheme Rose Gold</a></h6>
                        <div class="d-flex mb-2 align-items-center">
                            <div class="fs-16  me-1">
                                <del class="text-muted">$60.00</del>
                                <span class="text-danger">$45.00</span>
                            </div>
                            <span class="bg-danger text-white p-1">-25%</span>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <!-- color -->
                        <div x-data="{ color: 'Grey' }">
                            <h6 class="text-uppercase fw-bold mb-3">Color: <span x-text="color"></span></h6>
                            <div class="product-color-list mt-2 gap-2 d-flex align-items-center justify-content-center">
                                <a href="#!" class="d-inline-block bg_color_pink rounded-circle square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pink" x-on:click.prevent="color = 'Pink'; $event.target.classList.add('active'); $event.target.nextElementSibling.classList.remove('active');"></a>
                                <a href="#!" class="d-inline-block bg-secondary bg-opacity-50 rounded-circle active square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Grey" x-on:click.prevent="color = 'Grey'; $event.target.classList.add('active'); $event.target.nextElementSibling.classList.remove('active');"></a>
                                <a href="#!" class="d-inline-block bg-dark rounded-circle square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Black" x-on:click.prevent="color = 'Black'; $event.target.classList.add('active'); $event.target.previousElementSibling.classList.remove('active');"></a>
                            </div>
                        </div>
                        <!-- size -->
                        <div x-data="{ size: 'M', color: '#fff' }" class="mb-4 pt-2">
                            <h6 class="text-uppercase fw-bold mt-3">Size: <span x-text="size"></span></h6>
                            <div class="product-color-list size mt-2 gap-2 d-flex align-items-center justify-content-center">
                                <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'S' }" x-on:click.prevent="size = 'S';">S</a>
                                <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'M' }" x-on:click.prevent="size = 'M';">M</a>
                                <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'L' }" x-on:click.prevent="size = 'L';">L</a>
                            </div>
                        </div>
                        <!-- - + -->
                        <div class="input-step border border-dark rounded-pill">
                            <button type="button" class="minus material-shadow text-dark fw-bold">–</button>
                            <input type="number" class="product-quantity fw-bold fs-6" value="1" min="0" max="100">
                            <button type="button" class="plus material-shadow text-dark fw-bold">+</button>
                        </div>
                        <div class="my-3">
                            <button type="submit" class="btn w-100 btn-teal rounded-pill text-uppercase px-4 fw-semibold">Add to
                                cart</button>
                        </div>
                        <a href="product-detail-layout-01.html" class="btn fs-16 fw-semibold detail_link">View full details<i class="facl facl-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
<!-- instahram sec model -->
<div class="modal fade modal-overl" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content overflow-hidden">
            <div class="modal-body p-0">
                <button type="button" class="btn-close position-absolute end-0 top-0 m-2" style="z-index: 99;" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="row">
                    <div class="col-md-7">
                        <div class="images">
                            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper productJewellry">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-01.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-02.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-03.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-04.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-05.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-06.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-07.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-08.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-09.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                    <div class="swiper-slide topbar-product-card">
                                        <div class="position-relative overflow-hidden">
                                            <span class="new-label bg-danger text-white rounded-circle"> -34% </span>
                                            <img src="assets/images/quick_view/pr-10.jpg" class="product-view-img w-100 object-fit-cover" />
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 overflow-y-auto overflow-x-hidden" style="height: 624px;">
                        <div>
                            <div class="pt-30 ps-4 ps-md-0 pe-4">
                                <h6 class="fs-20 mb-2"><a href="product-detail-layout-01.html" class="main_link">La Bohème
                                        Rose Gold</a></h6>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                                    <p class="mb-0 fs-16 text-muted flex-grow-1">
                                        <del>$60.00</del>
                                        <span class="text-danger">$40.00</span>
                                    </p>
                                    <a href="product-detail-layout-01.html" class="text-body flex-shrink-0">
                                        <div class="kalles-rating-result">
                                            <span class="kalles-rating-result__pipe">
                                                <span class="kalles-rating-result__start"></span>
                                                <span class="kalles-rating-result__start"></span>
                                                <span class="kalles-rating-result__start"></span>
                                                <span class="kalles-rating-result__start active"></span>
                                                <span class="kalles-rating-result__start de-active"></span>
                                            </span>
                                            <span class="kalles-rating-result__number">(12 reviews)</span>
                                        </div>
                                    </a>
                                </div>
                                <p class="text-muted">Go kalles this summer with this vintage navy and white striped v-neck
                                    t-shirt from the Nike. Perfect for pairing with denim and white kicks for a stylish
                                    kalles vibe.</p>
                                <div x-data="{ color: 'Pink' }">
                                    <h6 class="text-uppercase mb-3">Color: <span x-text="color"></span></h6>
                                    <div class="product-color-list mt-2 gap-2 d-flex align-items-center">
                                        <a href="#!" class="d-inline-block bg_color_pink rounded-circle active square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Pink" x-on:click.prevent="color = 'Pink'; $event.target.classList.add('active'); $event.target.nextElementSibling.classList.remove('active');"></a>
                                        <a href="#!" class="d-inline-block bg-dark rounded-circle square-xs" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Black" x-on:click.prevent="color = 'Black'; $event.target.classList.add('active'); $event.target.previousElementSibling.classList.remove('active');"></a>
                                    </div>
                                </div>

                                <div x-data="{ size: 'M' }" class="mt-4 pt-2">
                                    <h6 class="text-uppercase mb-3">Size: <span x-text="size"></span></h6>
                                    <div class="product-color-list size mt-2 gap-2 d-flex align-items-center">
                                        <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'XS' }" x-on:click.prevent="size = 'XS';">XS</a>
                                        <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'S' }" x-on:click.prevent="size = 'S';">S</a>
                                        <a href="#!" class="d-inline-block rounded-circle square-xs d-flex align-items-center justify-content-center" :class="{ 'active': size === 'M' }" x-on:click.prevent="size = 'M';">M</a>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex flex-wrap align-items-center pt-2 gap-2">
                                    <div x-data="{ quantity: 1 }" class="quantity fs-14 position-relative border-dark mb-0">
                                        <input x-bind:value="quantity" type="number" class="input-text text-center" readonly step="1" min="0" max="9999">
                                        <button type="button" class="minus position-absolute start-0 ps-3" x-on:click="quantity > 1 ? quantity-- : null">
                                            <i class="facl facl-minus"></i>
                                        </button>
                                        <button type="button" class="plus position-absolute end-0 pe-3" x-on:click="quantity++">
                                            <i class="facl facl-plus"></i>
                                        </button>
                                    </div>
                                    <button x-data="{ shake: false }" x-init="
                                        setInterval(() => { 
                                            shake = true; 
                                            setTimeout(() => { 
                                                shake = false; 
                                            }, 2000); 
                                        }, 6000);
                                    " :class="{ 'animation-shake': shake }" class="btn btn-info text-uppercase rounded-pill min-w-150">
                                        Add to Cart
                                    </button>
                                    <a href="#" class="btn square-40 btn-wishlistadd p-0 fs-16 d-flex align-items-center rounded-pill flex-shrink-0 justify-content-center"><i class="facl facl-heart-o"></i></a>
                                </div>

                                <div class="mt-3">
                                    <img src="assets/images/trust_img2.png" alt="" class="img-fluid">
                                </div>
                                <div class="mt-4">
                                    <p class="text-muted mb-1"><span class="text-body">SKU:</span> 4540967714955-1</p>
                                    <p class="text-muted mb-1"><span class="text-body">Categories:</span> <a href="#!" class="main_link text-muted">Accessories</a>, <a href="#!" class="main_link text-muted">All</a>, <a href="#!" class="main_link text-muted">Best seller</a>, <a href="#!" class="main_link text-muted">New
                                            Arrival</a>, <a href="#!" class="main_link text-muted">Sale</a>, <a href="#!" class="main_link text-muted">Watches</a>, <a href="#!" class="main_link text-muted">Women</a></p>
                                    <p class="text-muted mb-1"><span class="text-body">Tags:</span> <a href="#!" class="main_link text-muted">Color Black</a>, <a href="#!" class="main_link text-muted">Color
                                            Pink</a>, <a href="#!" class="main_link text-muted">Price $7-$50</a>, <a href="#!" class="main_link text-muted">Vendor Kalles</a>, <a href="#!" class="main_link text-muted">Watch</a>,
                                        <a href="#!" class="main_link text-muted">Women</a>
                                    </p>
                                </div>
                                <div>
                                    <div class="social-share mt-4 mb-3">
                                        <a href="https://www.facebook.com/">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-facebook">
                                                <g>
                                                    <path d="M22 5.16c-.406-.054-1.806-.16-3.43-.16-3.4 0-5.733 1.825-5.733 5.17v2.882H9v3.913h3.837V27h4.604V16.965h3.823l.587-3.913h-4.41v-2.5c0-1.123.347-1.903 2.198-1.903H22V5.16z" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://twitter.com/">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-twitter">
                                                <g>
                                                    <path d="M27.996 10.116c-.81.36-1.68.602-2.592.71a4.526 4.526 0 0 0 1.984-2.496 9.037 9.037 0 0 1-2.866 1.095 4.513 4.513 0 0 0-7.69 4.116 12.81 12.81 0 0 1-9.3-4.715 4.49 4.49 0 0 0-.612 2.27 4.51 4.51 0 0 0 2.008 3.755 4.495 4.495 0 0 1-2.044-.564v.057a4.515 4.515 0 0 0 3.62 4.425 4.52 4.52 0 0 1-2.04.077 4.517 4.517 0 0 0 4.217 3.134 9.055 9.055 0 0 1-5.604 1.93A9.18 9.18 0 0 1 6 23.85a12.773 12.773 0 0 0 6.918 2.027c8.3 0 12.84-6.876 12.84-12.84 0-.195-.005-.39-.014-.583a9.172 9.172 0 0 0 2.252-2.336" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://www.google.com/gmail/about">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-email kalles-social-media__btn">
                                                <g>
                                                    <g fill-rule="evenodd"></g>
                                                    <path d="M27 22.757c0 1.24-.988 2.243-2.19 2.243H7.19C5.98 25 5 23.994 5 22.757V13.67c0-.556.39-.773.855-.496l8.78 5.238c.782.467 1.95.467 2.73 0l8.78-5.238c.472-.28.855-.063.855.495v9.087z">
                                                    </path>
                                                    <path d="M27 9.243C27 8.006 26.02 7 24.81 7H7.19C5.988 7 5 8.004 5 9.243v.465c0 .554.385 1.232.857 1.514l9.61 5.733c.267.16.8.16 1.067 0l9.61-5.733c.473-.283.856-.96.856-1.514v-.465z">
                                                    </path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://www.pinterest.com/">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-pinterest_share">
                                                <g>
                                                    <path d="M7 13.252c0 1.81.772 4.45 2.895 5.045.074.014.178.04.252.04.49 0 .772-1.27.772-1.63 0-.428-1.174-1.34-1.174-3.123 0-3.705 3.028-6.33 6.947-6.33 3.37 0 5.863 1.782 5.863 5.058 0 2.446-1.054 7.035-4.468 7.035-1.232 0-2.286-.83-2.286-2.018 0-1.742 1.307-3.43 1.307-5.225 0-1.092-.67-1.977-1.916-1.977-1.692 0-2.732 1.77-2.732 3.165 0 .774.104 1.63.476 2.336-.683 2.736-2.08 6.814-2.08 9.633 0 .87.135 1.728.224 2.6l.134.137.207-.07c2.494-3.178 2.405-3.8 3.533-7.96.61 1.077 2.182 1.658 3.43 1.658 5.254 0 7.614-4.77 7.614-9.067C26 7.987 21.755 5 17.094 5 12.017 5 7 8.15 7 13.252z" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                        <a href="https://www.messenger.com/">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="at-icon at-icon-messenger">
                                                <g>
                                                    <path d="M16 6C9.925 6 5 10.56 5 16.185c0 3.205 1.6 6.065 4.1 7.932V28l3.745-2.056c1 .277 2.058.426 3.155.426 6.075 0 11-4.56 11-10.185C27 10.56 22.075 6 16 6zm1.093 13.716l-2.8-2.988-5.467 2.988 6.013-6.383 2.868 2.988 5.398-2.987-6.013 6.383z" fill-rule="evenodd"></path>
                                                </g>
                                            </svg>
                                        </a>
                                    </div>
                                    <a href="product-detail-layout-01.html" class="fw-medium detail_link ">View full details<i class="facl facl-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- custome header -->
<?php include_once("custom_header.php"); ?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="shoppingCartnvasLabel">
    <div class="offcanvas-header bg-black p-3">
        <h5 class="offcanvas-title text-white text-uppercase" id="shoppingCartnvasLabel">Filter</h5>
        <button type="button" class="btn-close text-white btn-close-none" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close text-white pegk"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-4 filter-box rounded-0 border-0 shadow-none">
            <div class="row g-4 g-sm-2">
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Vendor </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked1">
                            <label class="form-check-label" for="flexCheckChecked1" style="cursor: pointer;">
                                Ck
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked2">
                            <label class="form-check-label" for="flexCheckChecked2" style="cursor: pointer;">
                                H&M
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked3">
                            <label class="form-check-label" for="flexCheckChecked3" style="cursor: pointer;">
                                Kalles
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked4" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked4" style="cursor: pointer;">
                                Lavi's
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked5" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked5" style="cursor: pointer;">
                                Monki
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked6" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked6" style="cursor: pointer;">
                                Nike
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Size </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked11">
                            <label class="form-check-label" for="flexCheckChecked11" style="cursor: pointer;">
                                S <span class="ms-1 text-muted">(9)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked21">
                            <label class="form-check-label" for="flexCheckChecked21" style="cursor: pointer;">
                                M <span class="ms-1 text-muted">(12)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked31">
                            <label class="form-check-label" for="flexCheckChecked31" style="cursor: pointer;">
                                L <span class="ms-1 text-muted">(6)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked41" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked41" style="cursor: pointer;">
                                Xs <span class="ms-1 text-muted">(8)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked51" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked51" style="cursor: pointer;">
                                Xl <span class="ms-1 text-muted">(25)</span>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked61" style="cursor: pointer;">
                            <label class="form-check-label" for="flexCheckChecked61" style="cursor: pointer;">
                                Xxl <span class="ms-1 text-muted">(16)</span>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- color -->
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Vendor </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3 filter-category">
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-black border-black p-1" type="checkbox" value="" id="colo1">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color1">
                                Black
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-teal border-teal p-1" type="checkbox" value="" id="color2">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color2">
                                Cyan
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-green2 p-1" type="checkbox" value="" id="color3">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color3">
                                Green
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-cid-green border-cid-green p-1" type="checkbox" value="" id="color4">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color4">
                                Gray
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-pink2 border-pink2 p-1" type="checkbox" value="" id="color5">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color5">
                                Pink
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-sea border-sea p-1" type="checkbox" value="" id="color6">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color6">
                                Sea
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-blue-dark border-blue-dark p-1" type="checkbox" value="" id="color7">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color7">
                                Blue
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-red border-red p-1" type="checkbox" value="" id="color8">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color8">
                                red
                            </label>
                        </div>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input bg-orange p-1 border-orange" type="checkbox" value="" id="color9">
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color9">
                                Orange
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Category -->
                <div class="col-12 ">
                    <h5 class="mb-1 fw-medium"> By Category </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3 filter-category">
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate">
                            <label class="form-check-label" style="cursor: pointer;" for="cate">
                                Accessories
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate22">
                            <label class="form-check-label" style="cursor: pointer;" for="cate22">
                                Men
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate3">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate3">
                                Women
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate4">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate4">
                                Shoes
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate5">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate5">
                                T-Shirt
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate6">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate6">
                                Dress
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate7">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate7">
                                Jackets
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate8">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate8">
                                Boots
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate9">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate9">
                                Jewellery
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate10">
                            <label class="form-check-label" style="cursor: pointer;" for=" cate">
                                Tops
                            </label>
                        </div>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="cate11">
                            <label class="form-check-label" style="cursor:pointer; " for=" cate11">
                                Wallet
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <h5 class="mb-1 fw-medium"> By Price </h5>
                    <div class="filter-title"></div>
                    <form action="#" class="mt-5">
                        <div class="slider-area">
                            <div>
                                <div class="slider-area">
                                    <div id="slider-snap" class="slider"></div>
                                    <div class="d-flex align-items-center mt-4 py-2">
                                        <span class="text-muted">Price: </span>
                                        <h6 class="mb-0 mx-2">
                                            <span id="slider-snap-value-lower"></span>
                                        </h6>
                                        -
                                        <h6 class="mb-0 ms-2">
                                             <span id="slider-snap-value-upper"></span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-custom-dark  fw-medium min-w-150 ">FILTER</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--shop offcanavas-->
<div class="offcanvas offcanvas-end " tabindex="-1" id="shopOffcanvas" aria-labelledby="shopOffcanvasLabel">
    <div class="offcanvas-header border-bottom bg-black text-white">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="shopOffcanvasLabel">SIDEBAR</h5>
        <button type="button" class="btn-close btn-close-none text-white" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Short by </h5>
            <div class="filter-title"></div>
            <p class="text-teal mb-2 mt-3"> Featured</p>
            <p class="mb-2">Best selling </p>
            <p class="mb-2">Alphabetically, A-Z</p>
            <p class="mb-2">Alphabetically, Z-A</p>
            <p class="mb-2">Price, low to high</p>
            <p class="mb-2">Price, high to low</p>
            <p class="mb-2">Date, old to new</p>
            <p class="mb-2">Date, new to old</p>

        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Category </h5>
            <div class="filter-title"></div>
            <div class="mt-3 filter-category">
                <p class="mb-2">
                    Accessories
                </p>
                <p class="mb-2">
                    Men
                </p>
                <p class="mb-2">
                    Women
                </p>
                <p class="mb-2">
                    Shoes
                </p>
                <p class="mb-2">
                    T-Shirt
                </p>
                <p class="mb-2">
                    Dress
                </p>
                <p class="mb-2">
                    Jackets
                </p>
                <p class="mb-2">
                    Boots
                </p>
                <p class="mb-2">
                    Jewellery
                </p>
                <p class="mb-2">
                    Tops
                </p>
                <p class="mb-2">
                    Wallet
                </p>
            </div>
        </div>
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Filter by price </h5>
            <div class="filter-title"></div>
            <div class="mt-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $50-$100
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $100-$150
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $150-$200
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $200-$250
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $250-$300
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        $300-$350
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Sale Products </h5>
            <div class="filter-title"></div>
            <div class="row mt-3">
                <div class="col-4">
                    <img src="assets/images/shop/sidebar-product-01.jpg" class="img-fluid" alt="">
                </div>
                <div class="col-8 ps-0">
                    <h6>Skin Sweatpants</h6>
                    <p class="text-danger"><del class="text-muted">$75.00</del>$45.00</p>
                    <span class="bg-danger text-white px-2 py-1">-40%</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <img src="assets/images/shop/sidebar-product-02.jpg" class="img-fluid" alt="">
                </div>
                <div class="col-8 ps-0">
                    <h6>Cluse La Boheme Rose Gold</h6>
                    <p class="text-danger"><del class="text-muted">$60.00</del>$45.00</p>
                    <span class="bg-danger text-white px-2 py-1">-25%</span>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <img src="assets/images/shop/sidebar-product-03.jpg" class="img-fluid" alt="">
                </div>
                <div class="col-8 ps-0">
                    <h6>Felt Cowboy Hat</h6>
                    <p class="text-muted">$22.00</p>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Instagram </h5>
            <div class="filter-title"></div>
            <div class="row row-cols-3 g-1 mt-3 ">
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_1.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_2.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_5.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_4.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_5.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_6.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_7.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_8.jpg" alt="" class="img-fluid">
                </div>
                <div class="insta-card position-relative">
                    <img src="assets/images/instagram/ins1_4.jpg" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> Privacy & Delivery </h5>
            <div class="filter-title"></div>
            <div class="row mt-3">
                <div class="col-3">
                    <h1><i class="las la-truck"></i></h1>
                </div>
                <div class="col-8 ps-0">
                    <h6>FREE Privacy</h6>
                    <p class="text-muted">Free Privacy for all US order</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-3">
                    <h1><i class="las la-headset"></i></h1>
                </div>
                <div class="col-8 ps-0">
                    <h6>SUPPORT 24/7</h6>
                    <p class="text-muted">We support 24 hours a day</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-3">
                    <h1><i class="las la-exchange-alt"></i></h1>
                </div>
                <div class="col-8 ps-0">
                    <h6>30 DAYS RETURN</h6>
                    <p class="text-muted">You have 30 days to return</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!--filter offcanavas-->
<div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
    <div class="offcanvas-header border-bottom bg-black text-white">
        <h5 class="offcanvas-title fs-16 text-uppercase" id="filterOffcanvasLabel">FILTER</h5>
        <button type="button" class="btn-close btn-close-none text-white" data-bs-dismiss="offcanvas" aria-label="Close"><i class="pe-7s-close pegk"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Vendor </h5>
            <div class="filter-title"></div>
            <div class="mt-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Ck
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        H&amp;M
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Kalles
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Lavi's
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Monki
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Nike
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Size </h5>
            <div class="filter-title"></div>
            <div class="mt-3">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        S <span class="ms-1 text-muted">(9)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        M <span class="ms-1 text-muted">(12)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        L <span class="ms-1 text-muted">(6)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Xs <span class="ms-1 text-muted">(8)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Xl <span class="ms-1 text-muted">(25)</span>
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Xxl <span class="ms-1 text-muted">(16)</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Color </h5>
            <div class="filter-title"></div>
            <div class="mt-3 filter-category">
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-black p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Black
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-teal p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Cyan
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-green2 p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Green
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-cid-green: p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Gray
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-pink2 p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Pink
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-sea p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Sea
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-blue-dark p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Blue
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-red p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        red
                    </label>
                </div>
                <div class="round d-flex align-items-center mb-2">
                    <input class="form-check-input bg-orange p-1" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label ms-1" for="flexCheckChecked">
                        Orange
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border-0" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Category </h5>
            <div class="filter-title"></div>
            <div class="mt-3 filter-category">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Accessories
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Men
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked="">
                    <label class="form-check-label" for="flexCheckChecked">
                        Women
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Shoes
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        T-Shirt
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Dress
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Jackets
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Boots
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Jewellery
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Tops
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                    <label class="form-check-label" for="flexCheckChecked">
                        Wallet
                    </label>
                </div>
            </div>
        </div>
        <div class="filter-box p-3 border" style="box-shadow: none;">
            <h5 class="mb-1 fw-medium"> By Title </h5>
            <div class="filter-title mb-3"></div>
            <form class="form-inline mt-3 mb-2 my-lg-0 filter-search">
                <input class="form-control mr-sm-2" type="search" placeholder="Search for product title" aria-label="Search">
                <button class="btn btn-custom-dark  fw-medium min-w-150 mt-3">FILTER</button>
            </form>
        </div>
        <div class="p-3 ">
            <h5 class="mb-1 fw-medium"> By Price </h5>
            <div class="filter-title"></div>
            <form action="#" class="mt-5">
                <div class="slider">
                    <div class="progress"></div>
                </div>
                <div class="range-input">
                    <input type="range" class="range-min" min="32" max="100" value="32" step="1">
                    <input type="range" class="range-max" min="60" max="100" value="60" step="1">
                </div>
                <p class="fw-medium text-black fs-14 mt-4"><span class="text-muted fw-normal me-2">Price</span>$32.70
                    -
                    $60.19
                </p>
                <button class="btn btn-custom-dark  fw-medium min-w-150 ">FILTER</button>
            </form>
        </div>
    </div>
</div>


<!-- write review model -->
<div class="modal fade modal-overl mx-auto" id="rateUsModel" tabindex="-1" role="dialog" aria-labelledby="cardLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered h-auto" role="document">
        <div class="modal-content p-2" style="max-width: 420px;">
            <div class="modal-body p-4">
                <a href="#!" data-bs-dismiss="modal" class="fs-35 close position-absolute top-0 end-0" aria-label="Close">
                    <i class="pe-7s-close pegk"></i>
                </a>
                <h2 class="fs-22 mb-3">Rate Us</h2>
                <div class="border p-3 rounded-1">
                    <div class="d-flex align-items-center">
                        <div>
                            <img alt="" src="assets/images/single-product/layout-02/thumb-sticky.jpg" style="max-height: 75px; max-width: 65px; Width: auto; height: auto; vertical-align: middle;">
                        </div>
                        <div class="ms-2 w-100">
                            <h6 class="mb-1 fs-14 fw-bold">Striped Long Sleeve Top</h6>
                            <div class="d-flex align-items-center gap-2">
                                <div class="kalles-rating-result">
                                    <span class="kalles-rating-result__pipe ,b-1">
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                                        <span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>
                                        <span class="text-muted kalles-rating-result__start kalles-rating-result__start--big"></span>
                                    </span>
                                </div>
                                <p class="text-muted mb-0">13 Review</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4 mt-3">
                    <p class="text-muted mb-0 fw-bold">Quality</p>
                    <div class="kalles-rating-result">
                        <span class="kalles-rating-result__pipe ,b-1">
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big active"></span>
                            <span class="text-muted kalles-rating-result__start kalles-rating-result__start--lg kalles-rating-result__start--big"></span>
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="name" role="button" class="fw-medium mb-2 text-muted">Your Name*</label>
                    <input id="name" class="form-control form-control-sm py-2 rounded-0" placeholder="John Smith" type="text">
                </div>
                <div class="mb-3">
                    <label for="email" role="button" class="fw-medium mb-2 text-muted">Your Email*</label>
                    <input id="email" class="form-control form-control-sm py-2 rounded-0" placeholder="example@yourdomain.com" type="text">
                </div>
                <div class="mb-3">
                    <label for="title" role="button" class="fw-medium mb-2 text-muted">Review Title</label>
                    <input id="title" class="form-control form-control-sm py-2 rounded-0" placeholder="Look great" type="text">
                </div>
                <div class="mb-3">
                    <label for="review" role="button" class="fw-medium mb-2 text-muted">Review Content</label>
                    <textarea id="review" rows="9" class="form-control form-control-sm py-2 rounded-0" placeholder="Write something" type="text"></textarea>
                </div>
                <button type="button" data-bs-toggle="modal" data-bs-target="#rateUsModel012" class="btn btn-warning rounded-1 py-2 px-2 fw-semibold">
                    Submit Your Review
                </button>
            </div>
        </div>
    </div>
</div>

<!-- comment model -->
<div class="modal fade modal-overl mx-auto" id="commentModel" tabindex="-1" role="dialog" aria-labelledby="cardLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered h-auto" role="document">
        <div class="modal-content p-2" style="max-width: 420px;">
            <div class="modal-body p-4">
                <a href="#!" data-bs-dismiss="modal" class="fs-35 close position-absolute top-0 end-0" aria-label="Close">
                    <i class="pe-7s-close pegk"></i>
                </a>

                <div class="rounded-pill d-inline-block align-items-center p-1 bg-light mb-2">
                    <div class="d-flex align-items-center">
                        <p class="mb-0 rounded-pill  bg-warning text-white d-inline-block text-center d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;">P</p>
                        <span class="fw-bold mx-2">Peter</span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-2 gap-2">
                    <div class="kalles-rating-result my-2">
                        <span class="kalles-rating-result__pipe ,b-1">
                            <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--big"></span>
                            <span class="kalles-rating-result__start kalles-rating-result__start--big active"></span>
                            <span class="text-muted kalles-rating-result__start kalles-rating-result__start--big"></span>
                        </span>
                    </div>
                    <p class="text-muted mb-0 opacity-75 fs-14">1 month ago</p>
                </div>
                <h6 class="pb-1">Contrary to popular belief</h6>
                <p class="text-muted mb-2">It is a long established fact that a reader will be distracted by the readable content of a page</p>
                <div class="border-bottom py-2"></div>

                <div class="d-flex gap-3 mt-3">
                    <p class="mb-0 rounded-pill  bg-danger text-white d-inline-block text-center d-flex justify-content-center align-items-center" style="min-width: 30px; width: 30px; height: 30px;">A</p>
                    <div>
                        <div class="bg-light py-2 px-3 rounded-2">
                            <span class="fw-bold">AdamStore</span>
                            <span>It is a long established fact that a reader will be distracted by the readable content of a page</span>
                        </div>
                        <p class="text-muted mb-0 text-end mt-2 opacity-75 fs-14">1 month ago</p>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-3">
                    <p class="mb-0 rounded-pill  bg-primary text-white d-inline-block text-center d-flex justify-content-center align-items-center" style="min-width: 30px; width: 30px; height: 30px;">S</p>
                    <div>
                        <div class="bg-light py-2 px-3 rounded-2">
                            <span class="fw-bold">SevenAM</span>
                            <span>It is a long established fact that a reader will be distracted by the readable content of a page</span>
                        </div>
                        <p class="text-muted mb-0 text-end mt-2 opacity-75 fs-14">2 weeks ago</p>
                    </div>
                </div>

                <div class="border-bottom py-2 mb-3"></div>

                <div class="bg-light px-3 py-2 text-muted rounded-2" data-bs-toggle="modal" data-bs-target="#rateUsModel"><span class="fw-bold">Comment</span></div>
            </div>
        </div>
    </div>
</div>


<script data-cfasync="false" src=".cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/libs/jquery/jquery.min.js" ></script>
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
