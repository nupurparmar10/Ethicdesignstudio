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
                <h4 class="fs-30 fw-medium">Store & Events</h4>
            </div>
        </div>
    </div>
    <!-- end main slide -->

    <!-- Store & Events -->
    <section>
        <div class="container">
            <div class="my-4">
                <?php
                    $events = mysqli_query($con, "select * from event where status ='1' order by event_date desc, e_id desc");
                    if ($events && mysqli_num_rows($events) > 0) {
                ?>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="nav flex-column nav-pills gap-2" id="store-events-tab" role="tablist" aria-orientation="vertical">
                            <?php
                                $j=1;
                                while($m=mysqli_fetch_assoc($events))
                                {
                                    $eventTabId = 'event-' . (int)$m['e_id'];
                            ?>
                            <button class="nav-link text-start border <?php if($j==1) echo 'active'; ?>" id="<?php echo $eventTabId; ?>-tab" data-bs-toggle="pill" data-bs-target="#<?php echo $eventTabId; ?>" type="button" role="tab" aria-controls="<?php echo $eventTabId; ?>" aria-selected="<?php echo $j==1 ? 'true' : 'false'; ?>">
                                <span class="d-block fw-medium text-uppercase"><?php echo htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="d-block small mt-2">Place: <?php echo htmlspecialchars($m['place'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="d-block small">Event Date: <?php echo htmlspecialchars(date('d-m-Y', strtotime($m['event_date'])), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="d-block small">Event Time: <?php echo htmlspecialchars(date('h:i A', strtotime($m['event_time'])), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="d-block small">Address: <?php echo htmlspecialchars($m['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </button>
                            <?php
                                    $j++;
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="tab-content" id="store-events-tabContent">
                            <?php
                                $k=1;
                                $events = mysqli_query($con, "select * from event where status ='1' order by event_date desc, e_id desc");
                                while($m=mysqli_fetch_assoc($events))
                                {
                                    $eventTabId = 'event-' . (int)$m['e_id'];
                            ?>
                            <div class="tab-pane fade <?php if($k==1) echo 'show active'; ?>" id="<?php echo $eventTabId; ?>" role="tabpanel" aria-labelledby="<?php echo $eventTabId; ?>-tab">
                                <div class="row g-0 border">
                                    <div class="col-md-6 bg-light d-flex align-items-center justify-content-center" style="width: 100%; height: 350px; overflow: hidden; background: transparent; text-align: center; vertical-align:middle; line-height: 350px ;">
                                        <img src="<?php echo $m['pic']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 100%; max-height: 100%; vertical-align: middle;">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 h-100 d-flex flex-column justify-content-center">
                                            <h4 class="fw-medium mb-4 text-center"><?php echo htmlspecialchars($m['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                            <div class="mb-3">
                                                <h6 class="mb-1 fw-medium">Address:</h6>
                                                <p class="text-muted mb-0"><?php echo htmlspecialchars($m['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            </div>
                                            <div class="mb-3">
                                                <h6 class="mb-1 fw-medium">Place:</h6>
                                                <p class="text-muted mb-0"><?php echo htmlspecialchars($m['place'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            </div>
                                            <div class="mb-3">
                                                <h6 class="mb-1 fw-medium">Event Date:</h6>
                                                <p class="text-muted mb-0"><?php echo htmlspecialchars(date('d-m-Y', strtotime($m['event_date'])), ENT_QUOTES, 'UTF-8'); ?></p>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-medium">Event Time:</h6>
                                                <p class="text-muted mb-0"><?php echo htmlspecialchars(date('h:i A', strtotime($m['event_time'])), ENT_QUOTES, 'UTF-8'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                    $k++;
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <p class="text-center text-muted mb-0">No store events found.</p>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- /Store & Events -->
    

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
