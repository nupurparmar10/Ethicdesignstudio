<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_POST['update'])) {
    $m_id = 12;

    $desp   = mysqli_real_escape_string($con, $_POST['desp'] ?? '');
    $oldPic = $_POST['pic_old'] ?? '';   // already stored as "assets/images/matter/xxx.ext"
    $newPic = $_FILES['pic']['name'] ?? '';

    $uploadDir = "../assets/images/matter/";   // physical path (for saving/reading files)
    $dbDir     = "assets/images/matter/";      // path to store in DB (no ../)

    $targetWidth  = 720;
    $targetHeight = 379;

    // Default: keep old picture path (already in DB format)
    $picPath = $oldPic;

    // If a new image is uploaded
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
                $sanitizedFilename = time() . '_12_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", pathinfo(basename($newPic), PATHINFO_FILENAME)) . '.' . $ext;
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
                    }
                }
            } else {
                $msg1 = "Only JPG, JPEG, and PNG images are allowed.";
            }
        } else {
            $msg1 = "Invalid image file uploaded.";
        }
    }

    mysqli_query($con, "UPDATE matter SET desp='$desp', pic='$picPath' WHERE m_id=$m_id");


    // Update m_id 13 to 16
    for ($i = 13; $i <= 16; $i++) {
        $title = mysqli_real_escape_string($con, $_POST["title$i"] ?? '');
        $desp  = mysqli_real_escape_string($con, $_POST["desp$i"] ?? '');

        mysqli_query($con, "UPDATE matter SET title='$title', desp='$desp' WHERE m_id=$i");
    }

    if (empty($msg1)) {
        $msg = "About Update Successfully";
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
        $smenu13 = "4";
        $ssmenu13 = "9";
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
                <li class="active">Update About</li>
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

                        $data = [];

                        $result = mysqli_query($con, "SELECT * FROM matter WHERE m_id BETWEEN 12 AND 20");

                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['m_id'];
                            $data[$id] = [
                                'title' => $row['title'] ?? '',
                                'desp'  => $row['desp'] ?? '',
                                'pic'   => $row['pic'] ?? ''
                            ];
                        }

                        ?>

                        <form class="form-horizontal" method="post" action="upabout.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Update</strong> About</h3>
                                </div>
                                <div class="panel-body">

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Pic</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control" name="pic" />

                                            <input type="hidden" name="pic_old" value="<?php echo htmlspecialchars($data[12]['pic']); ?>">

                                            <img class="img-fluid" src="../<?php echo htmlspecialchars($data[12]['pic']); ?>" style="height:150px; width:auto; margin-top:20px;" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Description</label>
                                        <div class="col-md-6">
                                            <textarea class="form-control" name="desp" required><?php echo htmlspecialchars($data[12]['desp']); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">

                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="title13" value="<?php echo htmlspecialchars($data[13]['title']); ?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <textarea class="form-control" rows="6" name="desp13" required><?php echo htmlspecialchars($data[13]['desp']); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="title14" value="<?php echo htmlspecialchars($data[14]['title']); ?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <textarea class="form-control" rows="6" name="desp14" required><?php echo htmlspecialchars($data[14]['desp']); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="title15" value="<?php echo htmlspecialchars($data[15]['title']); ?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <textarea class="form-control" rows="6" name="desp15" required><?php echo htmlspecialchars($data[15]['desp']); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="title16" value="<?php echo htmlspecialchars($data[16]['title']); ?>" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <textarea class="form-control" rows="6" name="desp16" required><?php echo htmlspecialchars($data[16]['desp']); ?></textarea>
                                        </div>
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