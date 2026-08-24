<?php
ob_start();
session_start();
include_once("connect.php");
$msg="";
$shop_pt_id=isset($_REQUEST['pt_id']) ? (int)$_REQUEST['pt_id'] : 0;
$shop_pt_name='';
if($shop_pt_id>0)
{
    $shop_pt_row=mysqli_fetch_assoc(mysqli_query($con,"SELECT ptname FROM producttype WHERE pt_id='$shop_pt_id'"));
    if(!empty($shop_pt_row['ptname']))
    {
        $shop_pt_name=mysqli_real_escape_string($con,$shop_pt_row['ptname']);
    }
}
$stock_pt_filter=$shop_pt_name!='' ? " AND i.ptype='$shop_pt_name'" : "";
$stock_filter="SELECT DISTINCT v.item_id FROM variant v JOIN item_details i ON i.item_id=v.item_id WHERE v.webstock > 0 AND i.status = 1 $stock_pt_filter";
$price=mysqli_fetch_assoc(mysqli_query($con,"SELECT MIN(v.edsellrate) AS min, MAX(v.edsellrate) AS max FROM variant v WHERE v.item_id IN ($stock_filter) AND v.edsellrate > 0"));
$shop_min_price=isset($price['min']) ? (float)$price['min'] : 0;
$shop_max_price=isset($price['max']) ? (float)$price['max'] : 0;
if($shop_max_price < $shop_min_price)
{
    $shop_max_price=$shop_min_price;
}
if(isset($_POST['shop_filter_ajax']))
{
    $allowed_sorts=array(
        'price_low'  => 'display_variant.edsellrate ASC',
        'price_high' => 'display_variant.edsellrate DESC',
        'date_old'   => 'i.item_id ASC',
        'date_new'   => 'i.item_id DESC'
    );
    $sort_key=isset($_POST['sort']) && isset($allowed_sorts[$_POST['sort']]) ? $_POST['sort'] : 'date_new';
    $order_by=$allowed_sorts[$sort_key];

    $where="WHERE v.webstock > 0 AND i.status = 1";
    if($shop_pt_name!='')
    {
        $where.=" AND i.ptype='$shop_pt_name'";
    }

    if(!empty($_POST['sub_category']))
    {
        $sub_categories=array_map('intval', (array)$_POST['sub_category']);
        $sub_categories=implode(',', array_filter($sub_categories));
        if($sub_categories!='')
        {
            $where.=" AND i.s_id IN ($sub_categories)";
        }
    }

    if(!empty($_POST['material']))
    {
        $materials=array_map('intval', (array)$_POST['material']);
        $materials=implode(',', array_filter($materials));
        if($materials!='')
        {
            $where.=" AND i.material_type IN ($materials)";
        }
    }

    if(!empty($_POST['size']))
    {
        $sizes=array();
        foreach((array)$_POST['size'] as $value)
        {
            $value=trim($value);
            if($value!='')
            {
                $sizes[]="'" . mysqli_real_escape_string($con, $value) . "'";
            }
        }
        if(!empty($sizes))
        {
            $where.=" AND v.size IN (" . implode(',', $sizes) . ")";
        }
    }

    if(!empty($_POST['color']))
    {
        $colors=array();
        foreach((array)$_POST['color'] as $value)
        {
            $value=trim($value);
            if($value!='')
            {
                $colors[]="'" . mysqli_real_escape_string($con, $value) . "'";
            }
        }
        if(!empty($colors))
        {
            $where.=" AND v.color IN (" . implode(',', $colors) . ")";
        }
    }

    if(isset($_POST['price_min']) && isset($_POST['price_max']))
    {
        $price_min=(float)$_POST['price_min'];
        $price_max=(float)$_POST['price_max'];
        $where.=" AND v.edsellrate >= $price_min AND v.edsellrate <= $price_max";
    }

    if(!empty($_POST['search_shop']))
    {
        $search_shop=mysqli_real_escape_string($con, trim($_POST['search_shop']));
        $where.=" AND i.saledesp LIKE '%$search_shop%'";
    }

    $shop_per_page=12;
    $shop_page=isset($_POST['page']) && $_POST['page']!=='' ? (int)$_POST['page'] : 1;
    if($shop_page<1)
    {
        $shop_page=1;
    }

    $count_row=mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(DISTINCT v.item_id) AS total FROM variant v JOIN item_details i ON i.item_id=v.item_id $where"));
    $shop_total_items=isset($count_row['total']) ? (int)$count_row['total'] : 0;
    $shop_total_pages=max(1, (int)ceil($shop_total_items / $shop_per_page));
    if($shop_page>$shop_total_pages)
    {
        $shop_page=$shop_total_pages;
    }
    $shop_offset=($shop_page-1)*$shop_per_page;

    $sql="SELECT i.*,display_variant.* FROM (SELECT v.item_id,SUBSTRING_INDEX(GROUP_CONCAT(v.v_id ORDER BY v.v_id ASC),',',1) AS display_v_id FROM variant v JOIN item_details i ON i.item_id=v.item_id AND i.status=1 AND i.website=1 $where GROUP BY v.item_id) product_variant JOIN item_details i ON i.item_id=product_variant.item_id JOIN variant display_variant ON display_variant.v_id=product_variant.display_v_id ORDER BY $order_by LIMIT $shop_per_page OFFSET $shop_offset";
    $m1=mysqli_query($con,$sql);
    if(mysqli_num_rows($m1)>0)
    {
        echo '<div class="row g-lg-4 g-3 shop-products-grid">';
        while($m=mysqli_fetch_assoc($m1))
        {
            $pic=mysqli_fetch_assoc(mysqli_query($con,"select pic from variant_pic where v_id='$m[v_id]'"));
            $main_pic_src='pic_not_found.png';
            if(!empty($pic['pic']) && file_exists('ethic_crm/'.$pic['pic']))
            {
                $main_pic_src='ethic_crm/'.$pic['pic'];
            }
            $sizes=array();
            $size1=mysqli_query($con,"select distinct size from variant where item_id='$m[item_id]' and webstock > 0 and size!='' order by size");
            while($size_row=mysqli_fetch_assoc($size1))
            {
                $sizes[]=$size_row['size'];
            }
?>
            <div class="col-md-3 col-6 col-lg-3">
                <div x-data="{ imageUrl: '<?php echo htmlspecialchars($main_pic_src, ENT_QUOTES, 'UTF-8'); ?>' }" class="topbar-product-card pb-3 w-100">
                    <div class="position-relative overflow-hidden" style="width: 100%; height: 350px; overflow: hidden; background: transparent;">
                        <img :src="imageUrl" src="<?php echo htmlspecialchars($main_pic_src, ENT_QUOTES, 'UTF-8'); ?>" alt="" class="shop-product-img" style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
                        <div class="product-button d-flex flex-column gap-2">
                            <a href="productdetail?item_id=<?php echo $m['item_id']; ?>" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <?php if(!empty($sizes)): ?>
                        <p class="shop-size-row mb-1 text-center" style="font-weight:700;">
                            <?php echo implode('<span class="shop-size-sep">&nbsp;&nbsp;</span>', array_map(function($s){ return '<span class="shop-size-item">' . htmlspecialchars($s, ENT_QUOTES, 'UTF-8') . '</span>'; }, $sizes)); ?>
                        </p>
                        <?php endif; ?>
                        <h6 class="mb-1 text-center"  style="font-weight:700;"><a href="productdetail?item_id=<?php echo $m['item_id']; ?>" class="product-title"><?php echo htmlspecialchars($m['saledesp'], ENT_QUOTES, 'UTF-8'); ?></a></h6>
                        <p class="mb-0 fs-14 text-center"  style="font-weight:700;"><span>&#8377;<?php echo htmlspecialchars($m['edsellrate'], ENT_QUOTES, 'UTF-8'); ?></span></p>
                    </div>
                </div>
            </div>
<?php
        }
        echo '</div>';

        if($shop_page<$shop_total_pages)
        {
            echo '<div class="load-more-products-wrap text-center py-4"><button type="button" class="btn btn-custom-dark fw-medium min-w-150 load-more-products" data-page="' . ($shop_page+1) . '">Load More Products</button></div>';
        }
    }
    else
    {
        echo '<div class="text-center py-5"><h4>No Product Found!!!</h4></div>';
    }
    exit;
}
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
    <link rel="stylesheet" href="assets/libs/nouislider/nouislider.min.css"></link>

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
        .load-more-products-wrap {
            width: 100%;
        }
        .shop-size-row {
            font-size: 12px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #6c757d;
        }
        .shop-size-item {
            color: #212529;
            font-weight: 500;
        }
        .shop-size-sep {
            color: #ced4da;
        }
        .shop-product-img {
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

<div class="backdrop-shadow d-none"></div>
<div>

    <!-- main slide -->
    <?php
        $query = mysqli_query($con, "SELECT * FROM banner WHERE b_id = 17");
        if ($row = mysqli_fetch_assoc($query)) {
            $banner = $row['pic'];
        }
    ?>
    <div style="background-image: url('<?php echo $banner; ?>'); background-position: center;" class="position-relative">
        <div class="position-absolute top-0 start-0 right-0 bottom-0 bg-dark w-100 opacity-50"></div>
        <div class=" container">
            <div class="text-white text-center py-51 position-relative">
                <h4 class="fs-20 fw-medium">Products</h4>
            </div>
        </div>
    </div>
    <!-- end main slide -->

    <!-- filter -->
    <div class="container">
        <div class=" mt-5 d-flex justify-content-between align-items-center">
            <a href="#!" class="text-muted fs-16 align-items-center d-none d-lg-flex" id="filter-icon">
                <i class="iccl fwb iccl-filter fwb me-2 fw-medium" id="icon-filter"></i>
                <i class="pe-7s-close pegk d-none me-2 fw-medium fw-semibold" id="icon-close" style="font-size: 24px;"></i>
                <p class="mb-0">Filter</p>
            </a>
            <div class="d-flex align-items-center d-lg-none fs-16 text-muted" data-bs-toggle="offcanvas" href="#filterOffcanvas">
                <i class="iccl fwb iccl-filter fwb me-2 fw-medium" id="icon-filter"></i>
                <i class="pe-7s-close pegk d-none me-2 fw-medium fw-semibold" id="icon-close" style="font-size: 24px;"></i>
                <p class="mb-0">Filter</p>
            </div>
            <div class="dropdown">
                <button class="btn d-flex align-items-center justify-content-between featurnBtn rounded-pill dropdown-toggle" type="button" id="shop-sort-btn" data-bs-toggle="dropdown" aria-expanded="false">
                    Date, new to old
                </button>
                <ul class="dropdown-menu filter-dropdown">
                    <li><a class="dropdown-item shop-sort-option" href="#" data-sort="price_low">Price, Low to High</a></li>
                    <li><a class="dropdown-item shop-sort-option" href="#" data-sort="price_high">Price, High to Low</a></li>
                    <li><a class="dropdown-item shop-sort-option" href="#" data-sort="date_old">Date, Old to New</a></li>
                    <li><a class="dropdown-item shop-sort-option" href="#" data-sort="date_new">Date, New to Old</a></li>
                </ul>
            </div>
        </div>
        <!-- filter option -->
        <div class="p-4 mt-4 filter-box d-none" id="shop-filter-box">
            <div class="row m-sm-2 g-4 g-sm-2">
                <div class=" col-sm-6 col-lg-3">
                    <h5 class="mb-1 fw-medium"> By Category  </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3">
                        <?php
                        if(isset($_REQUEST['pt_id']) && $_REQUEST['pt_id']!=0)
                        {
                            $sql="select * from pro_subcategory where pt_id='$_REQUEST[pt_id]'";
                        }
                        else
                        {
                            $sql="select * from pro_subcategory";
                        }
                        $j=1;
                        $k1=mysqli_query($con,$sql);
                        while($k=mysqli_fetch_assoc($k1))
                        {
                            $active=(isset($_REQUEST['s_id']) && ($_REQUEST['s_id']==$k['s_id'])) ? 'checked' : '';
                        ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input shop-filter-input" data-shop-filter="sub_category" <?php echo $active; ?> type="checkbox" value="<?php echo $k['s_id']; ?>" id="flexCheckChecked<?php echo $j; ?>">
                            <label class="form-check-label" for="flexCheckChecked<?php echo $j; ?>" style="cursor: pointer;">
                                <?php echo $k['sname']; ?>
                            </label>
                        </div>
                        <?php
                        $j++;
                        }
                        ?>
                    </div>
                </div>
                <div class=" col-sm-6 col-lg-3">
                    <h5 class="mb-1 fw-medium"> By Size </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3">
                        <?php
                        if(isset($_REQUEST['pt_id']) && $_REQUEST['pt_id']!=0)
                        {
                            $sql1="select distinct(size) as size from variant where webstock > 0 AND item_id IN (select item_id from item_details where status = 1 AND ptype IN (select ptname from producttype where pt_id='$_REQUEST[pt_id]'))";
                        }
                        else
                        {
                            $sql1="select distinct(size) as size from variant where webstock > 0 AND item_id IN ($stock_filter)";
                        }
                        $a=1;
                        $k1=mysqli_query($con,$sql1);
                        while($k=mysqli_fetch_assoc($k1))
                        {
                            if($k['size']!='')
                            {
                        ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input shop-filter-input" data-shop-filter="size" type="checkbox" value="<?php echo $k['size']; ?>" id="flexCheckChecked1<?php echo $a; ?>">
                            <label class="form-check-label" for="flexCheckChecked<?php echo $a; ?>" style="cursor: pointer;">
                                <?php echo $k['size']; ?>
                            </label>
                        </div>
                        <?php
                            }
                            $a++;
                        }
                        ?>
                    </div>
                </div>
                <!-- color -->
                <div class=" col-sm-6 col-lg-3">
                    <h5 class="mb-1 fw-medium"> By Color </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3 filter-category">
                        <?php
                        if(isset($_REQUEST['pt_id']) && $_REQUEST['pt_id']!=0)
                        {
                            $sql1=" SELECT * FROM color_code WHERE color_name IN(SELECT DISTINCT color FROM variant WHERE webstock > 0 AND color!='' AND item_id IN(SELECT item_id FROM item_details WHERE status = 1 AND ptype IN(SELECT ptname FROM producttype WHERE pt_id='$_REQUEST[pt_id]')))";
                        }
                        else
                        {
                            $sql1="select * from color_code WHERE color_name IN(SELECT DISTINCT color FROM variant WHERE webstock > 0 AND color!='' AND item_id IN ($stock_filter))";
                        }
                        $c=1;
                        $k1=mysqli_query($con,$sql1);
                        while($k=mysqli_fetch_assoc($k1))
                        {
                        ?>
                        <div class="round d-flex align-items-center pt-2 mb-2 gap-1">
                            <input class="form-check-input border-black p-1 shop-filter-input" data-shop-filter="color" type="checkbox" value="<?php echo $k['color_name']; ?>" id="color<?php echo $c; ?>" style="background-color:<?php echo $k['color_code']; ?>" >
                            <label class="form-check-label ms-1" style="cursor: pointer;" for="color<?php echo $c; ?>">
                               <?php echo $k['color_name']; ?>
                            </label>
                        </div>
                        <?php
                        $c++;
                        }
                        ?>
                    </div>
                </div>
                <!-- Category -->
                <div class=" col-sm-6 col-lg-3 ">
                    <h5 class="mb-1 fw-medium"> By Material </h5>
                    <div class="filter-title"></div>
                    <div class="mt-3 filter-category">
                        <?php
                        if(isset($_REQUEST['pt_id']) && $_REQUEST['pt_id'] != 0)
                        {
                            $sql1 = "SELECT * FROM material_type WHERE m_id IN (SELECT DISTINCT material_type FROM item_details WHERE status = 1 AND item_id IN ($stock_filter) AND ptype IN(SELECT ptname FROM producttype WHERE pt_id='$_REQUEST[pt_id]')) AND status='1'";
                        }
                        else
                        {
                            $sql1 = "SELECT * FROM material_type WHERE status='1' AND m_id IN (SELECT DISTINCT material_type FROM item_details WHERE status = 1 AND item_id IN ($stock_filter))";
                        }
                        $m=1;
                        $k1=mysqli_query($con,$sql1);
                        while($k=mysqli_fetch_assoc($k1))
                        {
                        ?>
                        <div class="form-check pt-2 mb-2">
                            <input class="form-check-input shop-filter-input" data-shop-filter="material" type="checkbox" value="<?php echo $k['m_id']; ?>" id="mt<?php echo $m; ?>">
                            <label class="form-check-label" style="cursor: pointer;" for="mt<?php echo $m; ?>">
                                <?php echo $k['type']; ?>
                            </label>
                        </div>
                        <?php
                        $m++;
                        }
                        ?>
                    </div>
                </div>
                <!-- title-->
                <div class=" col-sm-6 col-lg-3">
                    <h5 class="mb-1 fw-medium"> By Title </h5>
                    <div class="filter-title"></div>
                    <form class="form-inline my-2 my-lg-4 filter-search me-3">
                        <input class="form-control fs-12" type="search" placeholder="Search for product title" name="search_shop" aria-label="Search">
                    </form>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h5 class="mb-1 fw-medium">By Price</h5>
                    <div class="filter-title"></div>
                    <form action="#" class="mt-5">
                        <div class="slider-area">
                            <div id="slider-snap" class="slider" data-min="<?php echo $shop_min_price; ?>" data-max="<?php echo $shop_max_price; ?>"></div>
                            <div class="d-flex align-items-center justify-content-between mt-4 py-2">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted">Price: </span>
                                    <h6 class="mb-0 mx-2">
                                        <span id="slider-snap-value-lower"></span>
                                    </h6>
                                    -
                                    <h6 class="mb-0 ms-2">
                                        <span id="slider-snap-value-upper"></span>
                                    </h6>
                                    <span id="range" class="d-none"></span>
                                </div>
                                <button type="submit" class="btn btn-custom-dark fw-medium shop-filter-update">FILTER</button>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>

        </div>
        <!-- tab -->
        <div class="tab-content my-3 my-md-4" id="shop-pills-tabContent">
            <div class="tab-pane fade show active" id="best-pan1" role="tabpanel" aria-labelledby="best-pan1-tab" tabindex="0">
                <div class="row g-lg-4 g-3">
                    <div class="col-md-3 col-6 col-lg-2">
                        <div x-data="{ imageUrl: 'assets/images/products/pr-01.jpg' }" :class="{ 'w-100': true, 'h-100': true }" class="topbar-product-card pb-3 w-100">
                            <div class="position-relative overflow-hidden">
                                <span class="new-label bg-success text-white rounded-circle"> New </span>
                                <img :src="imageUrl" alt="" class="img-fluid w-100">

                                <div class="product-button d-none d-lg-flex flex-column gap-2">
                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn rounded-pill fs-14"><span>Quick View</span> <i class="iccl iccl-eye"></i></a>
                                    <button type="button" class="btn rounded-pill fs-14" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14"><span>Quick Shop</span>
                                        <i class="iccl iccl-cart"></i></button>
                                </div>
                                <div class="position-absolute d-lg-none bottom-0 end-0 d-flex flex-column bg-white rounded-pill m-2" style="z-index: 1;">
                                    <a href="#exampleModal" data-bs-toggle="modal" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;"><i class="iccl iccl-eye fw-semibold"></i></a>
                                    <button type="button" class="btn responsive-cart rounded-pill fs-14 p-2" style="width:36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#cardModal" class="btn rounded-pill fs-14">
                                        <i class="iccl iccl-cart fw-semibold"></i></button>
                                </div>
                                <p class="product-size mb-0 text-center text-white fw-medium">XS, S, M, L, XL</p>
                            </div>
                            <div class="mt-3">
                                <h6 class="mb-1"><a href="product-detail-layout-01.html" class="product-title">Analogue
                                        Resin Strap</a></h6>
                                <p class="mb-0 fs-14 text-muted">
                                    <span>$30.00</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--end tab pane-->
        </div>
    </div>

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
<script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/js/store.js"></script>
<script src="assets/libs/jarallax/jarallax.min.js"></script>
<script src="assets/libs/swiper/swiper-bundle.min.js"></script>
<script src="assets/libs/alpinejs/cdn.min.js"></script>
<script src="assets/libs/jquery-countdown/jquery.countdown.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/product-slider.init.js"></script>
<script src="assets/js/popup.js"></script>

<script src="assets/libs/wnumb/wNumb.js"></script>
<script src="assets/libs/nouislider/nouislider.js"></script>
<script src="assets/libs/flickity/flickity.pkgd.min.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/app.js"></script>
<script>
$(function () 
{
    var priceMin = <?php echo json_encode($shop_min_price); ?>;
    var priceMax = <?php echo json_encode($shop_max_price); ?>;
    var shopPtId = <?php echo json_encode($shop_pt_id); ?>;
    var slider = document.getElementById('slider-snap');
    var sliderMax = priceMax > priceMin ? priceMax : priceMin + 1;

    if(slider && window.noUiSlider)
    {
        if(slider.noUiSlider)
        {
            slider.noUiSlider.destroy();
        }

        noUiSlider.create(slider, {
            start: [priceMin, priceMax],
            connect: true,
            range: {
                min: priceMin,
                max: sliderMax
            },
            step: 1
        });

        slider.noUiSlider.on('update', function (values) {
            $('#slider-snap-value-lower').text(Math.round(values[0]));
            $('#slider-snap-value-upper').text(Math.round(values[1]));
        });
    }

    function selectedShopValues(name)
    {
        return $('.shop-filter-input[data-shop-filter="' + name + '"]:checked').map(function () {
            return $.trim($(this).val());
        }).get();
    }

    var shopCurrentPage = 1;
    var shopCurrentSort = 'date_new';

    function applyShopFilters(page, appendProducts)
    {
        shopCurrentPage = page && page > 0 ? page : 1;

        var priceValues = slider && slider.noUiSlider ? slider.noUiSlider.get() : [priceMin, priceMax];

        $.ajax({
            url: 'shop',
            type: 'POST',
            data: {
                shop_filter_ajax: 1,
                pt_id: shopPtId,
                sub_category: selectedShopValues('sub_category'),
                material: selectedShopValues('material'),
                size: selectedShopValues('size'),
                color: selectedShopValues('color'),
                search_shop: $.trim($('[name="search_shop"]').first().val()),
                price_min: Math.round(priceValues[0]),
                price_max: Math.round(priceValues[1]),
                sort: shopCurrentSort,
                page: shopCurrentPage
            },
            success: function (response) 
            {
                var $activePane = $('#best-pan1');
                var $response = $('<div>').html(response);
                $('#shop-pills-tabContent .tab-pane').removeClass('show active');
                $activePane.addClass('show active');

                if(appendProducts)
                {
                    $activePane.find('.load-more-products-wrap').remove();
                    $activePane.find('.shop-products-grid').append($response.find('.shop-products-grid').children());
                    $activePane.append($response.find('.load-more-products-wrap'));
                }
                else
                {
                    $activePane.html(response);
                }
            }
        });
    }

    $('body').on('click', '.shop-sort-option', function (event) {
        event.preventDefault();
        shopCurrentSort = $(this).data('sort');
        $('#shop-sort-btn').text($.trim($(this).text()));
        applyShopFilters(1, false);
    });

    $('.shop-filter-update').on('click', function (event) {
        event.preventDefault();
        applyShopFilters(1, false);

        $('#shop-filter-box').addClass('d-none');
        $('#icon-filter').removeClass('d-none');
        $('#icon-close').addClass('d-none');

        var $offcanvas = $(this).closest('.offcanvas');
        if($offcanvas.length && typeof bootstrap !== 'undefined')
        {
            var offcanvasInstance = bootstrap.Offcanvas.getInstance($offcanvas[0]) || bootstrap.Offcanvas.getOrCreateInstance($offcanvas[0]);
            offcanvasInstance.hide();
        }
    });

    $('#shop-pills-tabContent').on('click', '.load-more-products', function (event) 
    {
        event.preventDefault();
        var $button = $(this);
        var targetPage = parseInt($button.attr('data-page'), 10);
        if(!targetPage)
        {
            return;
        }
        $button.prop('disabled', true).text('Loading...');
        applyShopFilters(targetPage, true);
    });

    applyShopFilters(1, false);
});
</script>
</body>


</html>