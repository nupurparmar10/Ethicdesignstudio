<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_POST['update'])) 
{
    $uploadDir = "../assets/images/matter/";   // physical path (for saving/reading files)
    $dbDir     = "assets/images/matter/";      // path to store in DB (no ../)

    $bannerIds = [26, 30, 31];

    // Target dimensions per banner
    $dimensions = [
        26 => ['width' => 1720, 'height' => 700],
        30 => ['width' => 720,  'height' => 379],
        31 => ['width' => 720,  'height' => 379],
    ];

    foreach ($bannerIds as $m_id) {
        $titleKey   = 'title' . $m_id;
        $despKey    = 'desp' . $m_id;
        $picOldKey  = 'pic_old' . $m_id;
        $picFileKey = 'pic' . $m_id;

        $title  = mysqli_real_escape_string($con, $_POST[$titleKey] ?? '');
        $desp   = mysqli_real_escape_string($con, $_POST[$despKey] ?? '');
        $oldPic = $_POST[$picOldKey] ?? '';   // already stored as "assets/images/matter/xxx.ext"
        $newPic = $_FILES[$picFileKey]['name'] ?? '';

        // Default: keep old picture path (already in DB format)
        $picPath = $oldPic;

        $targetWidth  = $dimensions[$m_id]['width'];
        $targetHeight = $dimensions[$m_id]['height'];

        // If a new image is uploaded
        if (!empty($newPic) && is_uploaded_file($_FILES[$picFileKey]['tmp_name'])) 
        {
            $tempPath = $_FILES[$picFileKey]['tmp_name'];

            // Validate actual image content/type
            $imageInfo = getimagesize($tempPath);

            if ($imageInfo !== false) 
            {
                $mime = $imageInfo['mime'];

                // Only JPG, JPEG, PNG are accepted
                $extMap = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                ];

                if (isset($extMap[$mime])) 
                {
                    $ext = $extMap[$mime];
                    $sanitizedFilename = time() . '_' . $m_id . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", pathinfo(basename($newPic), PATHINFO_FILENAME)) . '.' . $ext;
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

                    if ($srcImage !== false) 
                    {
                        $srcWidth  = imagesx($srcImage);
                        $srcHeight = imagesy($srcImage);

                        // Create target canvas at this banner's exact required size
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
                        }
                    }
                }
                else
                {
                    $msg1 = "Only JPG, JPEG, and PNG images are allowed.";
                }
            }
            else
            {
                $msg1 = "Invalid image file uploaded.";
            }
        }

        mysqli_query($con, "UPDATE matter SET title='$title', desp='$desp', pic='$picPath' WHERE m_id=$m_id");
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
        $ssmenu13 = "2";
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
                <li><a href="#">Home</a></li>
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

                        <form class="form-horizontal" method="post" action="uphomebanner.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Update</strong> Home Banners</h3>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $data = [];
                                    $result = mysqli_query($con, "SELECT * FROM matter WHERE m_id IN (26,30,31)");
                                    while ($row = mysqli_fetch_assoc($result)) 
                                    {
                                        $data[$row['m_id']] = $row;
                                    }

                                    $bannerIds = [26, 30, 31];
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>                                                    
                                                    <th style="width:27%;">Title</th>
                                                    <th style="width:28%;">Description</th>
                                                    <th style="width:20%;">Banner</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($bannerIds as $m_id) {
                                                    $pic = htmlspecialchars($data[$m_id]['pic'] ?? '');
                                                    $title = htmlspecialchars($data[$m_id]['title'] ?? '');
                                                    $desp = htmlspecialchars($data[$m_id]['desp'] ?? '');
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <textarea class="form-control" rows="3" name="title<?= $m_id ?>" ><?= $title ?></textarea>
                                                        </td>
                                                        <td>
                                                            <textarea class="form-control" rows="3" name="desp<?= $m_id ?>" ><?= $desp ?></textarea>
                                                        </td>
                                                        <td>
                                                            <input type="file" class="form-control" name="pic<?= $m_id ?>" />
                                                            <input type="hidden" name="pic_old<?= $m_id ?>" value="<?= $pic ?>" />
                                                            <?php if (!empty($pic)) { ?>
                                                                <img class="img-fluid" src="../<?= $pic ?>" style="height:150px; width:150px;" />
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