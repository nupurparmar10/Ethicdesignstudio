<?php
ob_start();
session_start();
include_once("connect.php");

$msg = "";
$errors = [];

if (isset($_POST['s1'])) 
{
    $name    = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    $address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);
    $timing  = filter_var($_POST['timing'] ?? '', FILTER_SANITIZE_STRING);
    $contact = filter_var($_POST['contact'] ?? '', FILTER_SANITIZE_STRING);
    $email   = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $map   = filter_var($_POST['map'] ?? '', FILTER_SANITIZE_EMAIL);

    if (empty($name) || empty($address) || empty($timing) || empty($contact) || empty($email)) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    $uploadDir = "../assets/images/store/";   // physical path (for saving/reading files)
    $dbDir     = "assets/images/store/";      // path to store in DB (no ../)

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

    if (empty($errors)) {
        $stmt = $con->prepare("INSERT INTO stores (name, address, open_timings, contact, email, pic, status,map) VALUES (?, ?, ?, ?, ?, ?, 1,?)");
        $stmt->bind_param("ssssssi", $name, $address, $timing, $contact, $email, $picPath,$map  );
        if ($stmt->execute()) {
            $msg = "Store added successfully!";
        } else {
            $errors[] = "Insert failed: " . $con->error;
        }
        $stmt->close();
    }
}


if (isset($_POST['s3'])) {
    $name     = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
    $address  = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);
    $timing   = filter_var($_POST['timing'] ?? '', FILTER_SANITIZE_STRING);
    $contact  = filter_var($_POST['contact'] ?? '', FILTER_SANITIZE_STRING);
    $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $store_id = filter_var($_POST['store_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $map   = filter_var($_POST['map'] ?? '', FILTER_SANITIZE_EMAIL);

    $oldPic = $_POST['pic_old'] ?? '';   // already stored as "assets/images/store/xxx.ext"
    $newPic = $_FILES['pic']['name'] ?? '';

    $uploadDir = "../assets/images/store/";   // physical path (for saving/reading files)
    $dbDir     = "assets/images/store/";      // path to store in DB (no ../)

    $targetWidth  = 720;
    $targetHeight = 379;

    // Default: keep old picture path (already in DB format)
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
                $sanitizedFilename = time() . '_' . $store_id . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", pathinfo(basename($newPic), PATHINFO_FILENAME)) . '.' . $ext;
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

    if (empty($errors)) {
        $query = "UPDATE stores 
              SET name='$name', address='$address', open_timings='$timing', 
                  contact='$contact', email='$email', pic='$picPath' ,map='$map'
              WHERE store_id=$store_id";

        if (mysqli_query($con, $query)) {
            $msg = "Store updated successfully!";
            header("Location: viewstore.php?msg=updated");
            exit;
        } else {
            $errors[] = "Failed to update store: " . mysqli_error($con);
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Ethic Design Studio</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="logo3.png" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css" />
</head>

<body>
    <div class="page-container">
        <?php $menu13 = true;
        $smenu13 = "5";
        $ssmenu13 = "1";
        $sssmenu13 = "1";
        include_once("sidebar.php"); ?>
        <div class="page-content">
            <?php include_once("topheader.php"); ?>
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Master</a></li>
                <li class="active"><?php echo isset($_GET['store_id']) ? 'Modify Store' : 'Add Store'; ?></li>
            </ul>
            <div class="page-content-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($msg) { ?>
                            <div class="alert alert-success" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo htmlspecialchars($msg); ?></strong>
                            </div>
                        <?php } ?>
                        <?php if (!empty($errors)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo htmlspecialchars(implode("<br>", $errors)); ?></strong>
                            </div>
                        <?php } ?>
                        <form class="form-horizontal" method="post" action="addstore.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?php
                                    $c = ['', '', '', '', '', '', '','','','','',''];
                                    if (isset($_GET['store_id'])) {
                                        $store_id = filter_var($_GET['store_id'], FILTER_SANITIZE_NUMBER_INT);
                                        $stmt = $con->prepare("SELECT * FROM stores WHERE store_id = ?");
                                        $stmt->bind_param("i", $store_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $c = $result->fetch_row() ?: $c;
                                        echo "<input type='hidden' name='store_id' value='" . htmlspecialchars($store_id) . "'/>";
                                    ?>
                                        <h3 class="panel-title"><strong>Modify</strong> Store</h3>
                                    <?php } else { ?>
                                        <h3 class="panel-title"><strong>Add New</strong> Store</h3>
                                    <?php } ?>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Name</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($c[1]); ?>" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Address</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($c[2]); ?>" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Timing</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="timing" value="<?php echo htmlspecialchars($c[3]); ?>" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Contact</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="tel" class="form-control" name="contact" value="<?php echo htmlspecialchars($c[4]); ?>" required pattern="[0-9]{10}" title="Enter a valid 10-digit phone number" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Email</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($c[5]); ?>" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Map</label>
                                        <div class="col-md-6 col-xs-12">
                                            <textarea class="form-control" name="map"><?php echo htmlspecialchars($c[9]); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Pic</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control" name="pic" accept="image/*" />

                                            <input type="hidden" name="pic_old" value="<?php echo htmlspecialchars($c[6]); ?>">

                                            <?php if (!empty($c[6]) && file_exists("../" . $c[6])) { ?>
                                                <img class="img-fluid" src="../<?php echo htmlspecialchars($c[6]); ?>" style="height:150px; width:auto; margin-top:20px;" />
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <span id="mobileError" style="color: red; font-size: 14px;"></span>
                                </div>
                                <div class="panel-footer">
                                    <?php if (isset($_GET['store_id'])) { ?>
                                        <button class="btn btn-primary" type="submit" name="s3">Modify</button>
                                    <?php } else { ?>
                                        <button class="btn btn-primary" type="submit" name="s1">Add</button>
                                    <?php } ?>
                                    <button class="btn btn-default" type="reset">Clear Form</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once("footer.php"); ?>
    <audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
    <audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
    <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
</body>

</html>