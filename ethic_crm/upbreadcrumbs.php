<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";

if (isset($_POST['update'])) 
{
    $uploadDir = "../assets/images/banner/";   // physical path (for saving/reading files)
    $dbDir     = "assets/images/banner/";      // path to store in DB (no ../)

    // Fetch all banners to process their updates
    $bannerQuery = mysqli_query($con, "SELECT b_id, pic FROM banner");
    $bannerData = [];
    while ($r = mysqli_fetch_assoc($bannerQuery)) {
        $bannerData[$r['b_id']] = $r['pic'];
    }

    $targetWidth  = 1275;
    $targetHeight = 510;

    foreach ($bannerData as $b_id => $currentPic) {
        $picOldKey  = 'pic_old' . $b_id;
        $picFileKey = 'pic' . $b_id;

        $oldPic = $_POST[$picOldKey] ?? $currentPic;
        $newPic = $_FILES[$picFileKey]['name'] ?? '';

        $picPath = $oldPic; // Default: keep old picture path

        // If a new image is uploaded
        if (!empty($newPic) && is_uploaded_file($_FILES[$picFileKey]['tmp_name'])) 
        {
            $tempPath = $_FILES[$picFileKey]['tmp_name'];
            $imageInfo = getimagesize($tempPath);

            if ($imageInfo !== false) 
            {
                $mime = $imageInfo['mime'];
                $extMap = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif'
                ];

                if (isset($extMap[$mime])) 
                {
                    $ext = $extMap[$mime];
                    $sanitizedFilename = time() . '_' . $b_id . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", pathinfo(basename($newPic), PATHINFO_FILENAME)) . '.' . $ext;
                    $destPath = $uploadDir . $sanitizedFilename;

                    // Load source image
                    switch ($mime) {
                        case 'image/jpeg':
                            $srcImage = imagecreatefromjpeg($tempPath);
                            break;
                        case 'image/png':
                            $srcImage = imagecreatefrompng($tempPath);
                            break;
                        case 'image/gif':
                            $srcImage = imagecreatefromgif($tempPath);
                            break;
                    }

                    if ($srcImage !== false) 
                    {
                        $srcWidth  = imagesx($srcImage);
                        $srcHeight = imagesy($srcImage);

                        // Create target canvas exactly 1275x510
                        $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

                        if ($mime === 'image/png') {
                            imagecolortransparent($dstImage, imagecolorallocatealpha($dstImage, 0, 0, 0, 127));
                            imagealphablending($dstImage, false);
                            imagesavealpha($dstImage, true);
                        }

                        // Resize
                        imagecopyresampled(
                            $dstImage, $srcImage,
                            0, 0, 0, 0,
                            $targetWidth, $targetHeight,
                            $srcWidth, $srcHeight
                        );

                        // Save image
                        $saved = false;
                        switch ($mime) {
                            case 'image/jpeg':
                                $saved = imagejpeg($dstImage, $destPath, 80);
                                break;
                            case 'image/png':
                                $saved = imagepng($dstImage, $destPath, 6);
                                break;
                            case 'image/gif':
                                $saved = imagegif($dstImage, $destPath);
                                break;
                        }

                        imagedestroy($srcImage);
                        imagedestroy($dstImage);

                        if ($saved) {
                            $picPath = $dbDir . $sanitizedFilename;

                            // Check if the old picture is used by any other banner record
                            if (!empty($oldPic)) {
                                $oldFilePath = "../" . $oldPic; // physical path of old pic
                                $usageCheck = mysqli_query($con, "SELECT COUNT(*) as count FROM banner WHERE pic = '$oldPic' AND b_id != $b_id");
                                $usageRow = mysqli_fetch_assoc($usageCheck);
                                
                                if ($usageRow['count'] == 0) {
                                    // Not used anywhere else in banner table, safe to delete
                                    if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                                        unlink($oldFilePath);
                                    }
                                }
                            }
                        } else {
                            $msg1 = "Failed to save resized image.";
                        }
                    } else {
                        $msg1 = "Failed to process image.";
                    }
                }
                else
                {
                    $msg1 = "Only JPG, JPEG, PNG, and GIF images are allowed.";
                }
            }
            else
            {
                $msg1 = "Invalid image file uploaded.";
            }
        }

        // Update database with the (possibly new) picture path. No other fields updated.
        if ($picPath !== $currentPic) {
            mysqli_query($con, "UPDATE banner SET pic='$picPath' WHERE b_id=$b_id");
        }
    }

    if (empty($msg1)) {
        $msg = "Banner Updated Successfully";
    }
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
        $smenu13 = "1";
        $ssmenu13 = "4";
        include_once("sidebar.php"); ?>
        <!-- END PAGE SIDEBAR -->

        <!-- PAGE CONTENT -->
        <div class="page-content">

            <!-- START X-NAVIGATION VERTICAL -->
            <?php include_once("topheader.php"); ?>
            <!-- END X-NAVIGATION VERTICAL -->

            <!-- START BREADCRUMB -->
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Policy</a></li>
                <li class="active">Update Home Banners</li>
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

                        <form class="form-horizontal" method="post" action="upbreadcrumbs.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Update</strong> Breadcrumbs Banner</h3>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $data = [];
                                    $result = mysqli_query($con, "SELECT * FROM banner");
                                    while ($row = mysqli_fetch_assoc($result)) 
                                    {
                                        $data[$row['b_id']] = $row;
                                    }
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>                                                    
                                                    <th style="width:30%;">Page Title</th>
                                                    <th style="width:70%;">Breadcrumbs Banner Image (Exactly 1275x510)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $b_id => $bannerRow) {
                                                    $pic = htmlspecialchars($bannerRow['pic'] ?? '');
                                                    $title = htmlspecialchars($bannerRow['title'] ?? '');
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= $title ?></strong>
                                                        </td>
                                                        <td>
                                                            <input type="file" class="form-control" name="pic<?= $b_id ?>" />
                                                            <input type="hidden" name="pic_old<?= $b_id ?>" value="<?= $pic ?>" />
                                                            <?php if (!empty($pic)) { ?>
                                                                <br>
                                                                <img class="img-fluid" src="../<?= $pic ?>" style="height:150px; width:auto;" />
                                                            <?php } else { ?>
                                                                <span class="text-muted">No image</span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="panel-footer">
                                    <button type="submit" class="btn btn-primary" name="update">Update</button>
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
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
    <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->
    <script>
        ClassicEditor
            .create(document.querySelector('#mytextarea0'))
            .catch(error => {
                console.error(error);
            });
    </script>

</body>

</html>