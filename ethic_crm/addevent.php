<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_REQUEST['s1'])) 
{
    $uploadDir = "../assets/images/event/";   // physical path (for saving/reading files)
    $dbDir     = "assets/images/event/";      // path to store in DB (no ../)

    $targetWidth  = 720;
    $targetHeight = 379;

    $picPath = '';

    if (!empty($_FILES['pic']['name']) && is_uploaded_file($_FILES['pic']['tmp_name'])) 
    {
        $tempPath = $_FILES['pic']['tmp_name'];

        // Validate actual image content/type
        $imageInfo = getimagesize($tempPath);

        if ($imageInfo !== false) {
            $mime = $imageInfo['mime'];

            // Only JPG, JPEG, PNG are accepted
            $extMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
            ];

            if (isset($extMap[$mime])) {
                $ext = $extMap[$mime];
                $sanitizedFilename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", pathinfo(basename($_FILES['pic']['name']), PATHINFO_FILENAME)) . '.' . $ext;
                $destPath = $uploadDir . $sanitizedFilename;

                // Load source image based on mime type
                switch ($mime) {
                    case 'image/jpeg':
                        $srcImage = imagecreatefromjpeg($tempPath);
                        break;
                    case 'image/png':
                        $srcImage = imagecreatefrompng($tempPath);
                        break;
                }

                if ($srcImage !== false) {
                    $srcWidth  = imagesx($srcImage);
                    $srcHeight = imagesy($srcImage);

                    // Create target canvas at exact required size
                    $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

                    // Preserve transparency for PNG
                    if ($mime === 'image/png') {
                        imagecolortransparent($dstImage, imagecolorallocatealpha($dstImage, 0, 0, 0, 127));
                        imagealphablending($dstImage, false);
                        imagesavealpha($dstImage, true);
                    }

                    // Resize the FULL source image to fill the target exactly (no cropping)
                    imagecopyresampled(
                        $dstImage, $srcImage,
                        0, 0, 0, 0,
                        $targetWidth, $targetHeight,
                        $srcWidth, $srcHeight
                    );

                    // Save resized/compressed image based on mime type
                    $saved = false;
                    switch ($mime) {
                        case 'image/jpeg':
                            $saved = imagejpeg($dstImage, $destPath, 80); // 80 = compression quality
                            break;
                        case 'image/png':
                            $saved = imagepng($dstImage, $destPath, 6);  // 6 = compression level
                            break;
                    }

                    imagedestroy($srcImage);
                    imagedestroy($dstImage);

                    if ($saved) {
                        // Store DB-friendly path (without ../)
                        $picPath = $dbDir . $sanitizedFilename;
                    } else {
                        $errors[] = "Failed to upload image.";
                    }
                } else {
                    $errors[] = "Failed to process image.";
                }
            } else {
                $errors[] = "Only JPEG and PNG files are allowed.";
            }
        } else {
            $errors[] = "Invalid image file uploaded.";
        }
    }


    mysqli_query($con, "insert into event set title='$_REQUEST[title]', place='$_REQUEST[place]', event_date='$_REQUEST[event_date]', event_time='$_REQUEST[event_time]',address='$_REQUEST[address]',pic='$picPath',map='$_REQUEST[map]'");

    $msg = "Event added successfully!!!";
}
if (isset($_REQUEST['s3'])) 
{
     $uploadDir = "../assets/images/event/";   // physical path (for saving/reading files)
    $dbDir     = "assets/images/event/";      // path to store in DB (no ../)

    $targetWidth  = 720;
    $targetHeight = 379;

    $oldPic = $_REQUEST['pic_old'] ?? '';
    $newPic = $_FILES['pic']['name'] ?? '';
    $e_id = $_REQUEST['e_id'] ?? '';

    // Default: keep old picture path (already in DB format)
    if(!empty($oldPic)){
        $picPath = $oldPic;
    }
    else
    {
        $picPath= "";
    }

    if (!empty($newPic) && is_uploaded_file($_FILES['pic']['tmp_name'])) {
        $tempPath = $_FILES['pic']['tmp_name'];

        // Validate actual image content/type
        $imageInfo = getimagesize($tempPath);

        if ($imageInfo !== false) {
            $mime = $imageInfo['mime'];

            // Only JPG, JPEG, PNG are accepted
            $extMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
            ];

            if (isset($extMap[$mime])) {
                $ext = $extMap[$mime];
                $sanitizedFilename = time() . '_' . $e_id . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", pathinfo(basename($newPic), PATHINFO_FILENAME)) . '.' . $ext;
                $destPath = $uploadDir . $sanitizedFilename;

                // Load source image based on mime type
                switch ($mime) {
                    case 'image/jpeg':
                        $srcImage = imagecreatefromjpeg($tempPath);
                        break;
                    case 'image/png':
                        $srcImage = imagecreatefrompng($tempPath);
                        break;
                }

                if ($srcImage !== false) {
                    $srcWidth  = imagesx($srcImage);
                    $srcHeight = imagesy($srcImage);

                    // Create target canvas at exact required size
                    $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

                    // Preserve transparency for PNG
                    if ($mime === 'image/png') {
                        imagecolortransparent($dstImage, imagecolorallocatealpha($dstImage, 0, 0, 0, 127));
                        imagealphablending($dstImage, false);
                        imagesavealpha($dstImage, true);
                    }

                    // Resize the FULL source image to fill the target exactly (no cropping)
                    imagecopyresampled(
                        $dstImage, $srcImage,
                        0, 0, 0, 0,
                        $targetWidth, $targetHeight,
                        $srcWidth, $srcHeight
                    );

                    // Save resized/compressed image based on mime type
                    $saved = false;
                    switch ($mime) {
                        case 'image/jpeg':
                            $saved = imagejpeg($dstImage, $destPath, 80); // 80 = compression quality
                            break;
                        case 'image/png':
                            $saved = imagepng($dstImage, $destPath, 6);  // 6 = compression level
                            break;
                    }

                    imagedestroy($srcImage);
                    imagedestroy($dstImage);

                    if ($saved) {
                        // New file exists AND old file exists -> unlink old file only
                        $oldFilePath = $uploadDir . basename($oldPic);
                        if (!empty($oldPic) && file_exists($oldFilePath) && is_file($oldFilePath)) {
                            unlink($oldFilePath);
                        }

                        // Store DB-friendly path (without ../)
                        $picPath = $dbDir . $sanitizedFilename;
                    } else {
                        $errors[] = "Failed to upload image.";
                    }
                } else {
                    $errors[] = "Failed to process image.";
                }
            } else {
                $errors[] = "Only JPEG and PNG files are allowed.";
            }
        } else {
            $errors[] = "Invalid image file uploaded.";
        }
    }

    mysqli_query($con, "update event set title='$_REQUEST[title]', place='$_REQUEST[place]', event_date='$_REQUEST[event_date]', event_time='$_REQUEST[event_time]',address='$_REQUEST[address]', pic='$picPath',map='$_REQUEST[map]' where e_id='$_REQUEST[e_id]'");
    header("Location: viewevent.php?msg=set");
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <!-- META SECTION -->
    <title>Ethic Design Studio</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="logo3.png" type="image/x-icon" />
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css" />
    <!-- EOF CSS INCLUDE -->
    <script src="js\jquery.min.js"></script>
</head>

<body>
    <!-- START PAGE CONTAINER -->
    <div class="page-container">

        <!-- START PAGE SIDEBAR -->
        <?php $menu13 = true;
        $smenu13 = "5";
        $ssmenu13 = "2";
        $sssmenu13 = "1";
        include_once("sidebar.php"); ?>

        <!-- PAGE CONTENT -->
        <div class="page-content">

            <!-- START X-NAVIGATION VERTICAL -->
            <?php include_once("topheader.php"); ?>
            <!-- END X-NAVIGATION VERTICAL -->

            <!-- START BREADCRUMB -->
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Master</a></li>
                <li class="active"><?php if (isset($_REQUEST['e_id'])) { ?>Modify Event<?php } else { ?>Add Event<?php } ?></li>
            </ul>
            <!-- END BREADCRUMB -->

            <!-- PAGE CONTENT WRAPPER -->
            <div class="page-content-wrap">

                <div class="row">
                    <div class="col-md-12">

                        <?php
                        if ($msg) {
                        ?>
                            <div class="alert alert-success" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg; ?></strong>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        if ($msg1) {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg1; ?></strong>
                            </div>
                        <?php
                        }
                        ?>
                        <form class="form-horizontal" method="post" action="addevent.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?php
                                    if (isset($_REQUEST['e_id'])) {
                                        $c1 = mysqli_query($con, "select * from event where e_id='$_REQUEST[e_id]'");
                                        $c = mysqli_fetch_row($c1);
                                        echo "<input type='hidden' name='e_id' value='$_REQUEST[e_id]'/>";
                                    ?>
                                        <h3 class="panel-title"><strong>Modify</strong> Event</h3>
                                    <?php
                                    } else {
                                        $c[1] = $c[2] = $c[3] = $c[4] = $c[5]=$c[6]=$c[7]=$c[8]=$c[9]="";
                                    ?>
                                        <h3 class="panel-title"><strong>Add New</strong> Event</h3>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Title</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="title" value='<?php echo $c[1]; ?>' required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Place</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="place" value='<?php echo $c[2]; ?>' required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Address</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon text-start"><span class="fa fa-pencil"></span></span>
                                                <textarea type="text" class="form-control" name="address" required><?php echo $c[3]; ?> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Time</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="time" class="form-control" name="event_time" value='<?php echo $c[5]; ?>' required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Date</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="date" class="form-control" name="event_date" value='<?php echo $c[4]; ?>' required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Map</label>
                                        <div class="col-md-6 col-xs-12">
                                            <textarea class="form-control" name="map"><?php echo htmlspecialchars($c[8]); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Pic</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control" name="pic" accept="image/*" />

                                            <input type="hidden" name="pic_old" value="<?php echo htmlspecialchars($c[9]); ?>">

                                            <?php if (!empty($c[9]) && file_exists("../" . $c[9])) { ?>
                                                <img class="img-fluid" src="../<?php echo htmlspecialchars($c[9]); ?>" style="height:150px; width:auto; margin-top:20px;" />
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <span id="mobileError" style="color: red; font-size: 14px;"></span>
                                </div>
                                <div class="panel-footer">
                                    <?php
                                    if (isset($_REQUEST['e_id'])) {
                                    ?>
                                        <button class="btn btn-primary" type="submit" name="s3">Modify</button>
                                    <?php
                                    } else {
                                    ?>
                                        <button class="btn btn-primary" type="submit" name="s1">Add</button>
                                    <?php
                                    }
                                    ?>
                                    <button class="btn btn-default">Clear Form</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
            <!-- END PAGE CONTENT WRAPPER -->
        </div>
        <!-- END PAGE CONTENT -->
    </div>
    <!-- END PAGE CONTAINER -->

    <!-- MESSAGE BOX-->
    <?php include_once("footer.php"); ?>
    <!-- END MESSAGE BOX-->

    <!-- START PRELOADS -->
    <audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
    <audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
    <!-- END PRELOADS -->

    <!-- START SCRIPTS -->
    <!-- START PLUGINS -->
    <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
    <!-- END PLUGINS -->

    <!-- THIS PAGE PLUGINS -->
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>

    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
    <!-- END THIS PAGE PLUGINS -->

    <!-- START TEMPLATE -->


    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
    <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->


</body>

</html>