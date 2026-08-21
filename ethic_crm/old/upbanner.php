<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";

if (isset($_POST['update'])) {
    $uploadDir = "../images/banner/";

    $result = mysqli_query($con, "SELECT b_id FROM banner");
    while ($row = mysqli_fetch_assoc($result)) {
        $i = $row['b_id'];

        $title = mysqli_real_escape_string($con, $_POST["title$i"] ?? '');
        $oldPic = $_POST["pic_old$i"] ?? '';
        $newPic = $_FILES["pic$i"]['name'] ?? '';

        $picPath = $oldPic;

        // If a new image is uploaded
        if (!empty($newPic) && is_uploaded_file($_FILES["pic$i"]['tmp_name'])) {
            $oldFilePath = $uploadDir . basename($oldPic);
            if (!empty($oldPic) && file_exists($oldFilePath) && is_file($oldFilePath)) {
                unlink($oldFilePath);
            }

            $sanitizedFilename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($newPic));
            $tempPath = $_FILES["pic$i"]['tmp_name'];

            if (move_uploaded_file($tempPath, $uploadDir . $sanitizedFilename)) {
                $picPath = $sanitizedFilename;
            }
        }

        $updateSQL = "UPDATE banner SET title='$title', pic='$picPath' WHERE b_id=$i";
        mysqli_query($con, $updateSQL);
    }

    echo "<script>alert('Banner updated successfully.'); window.location.href='your-banner-page.php';</script>";
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
        $ssmenu13 = "4";
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
                <li class="active">Update Banner</li>
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
                        $result = mysqli_query($con, "SELECT * FROM banner");

                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['b_id'];
                            $data[$id] = [
                                'title' => $row['title'] ?? '',
                                'pic'   => $row['pic'] ?? '',
                            ];
                        }
                        ?>

                        <form class="form-horizontal" method="post" action="upbanner.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Update</strong> Banner</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php foreach ($data as $i => $banner) { ?>
                                            <div class="col-md-6" style="margin-bottom: 30px;">
                                                <input class="form-control" style="color: black;" name="title<?= $i ?>" value="<?= htmlspecialchars($banner['title']) ?>" readonly />


                                                <input type="file" class="form-control" style="margin-top: 30px;" name="pic<?= $i ?>" />
                                                <input type="hidden" name="pic_old<?= $i ?>" value="<?= htmlspecialchars($banner['pic']) ?>">

                                                <?php if (!empty($banner['pic'])): ?>
                                                    <img class="img-fluid" src="../images/banner/<?= htmlspecialchars($banner['pic']) ?>" style="height:150px; width:auto; margin-top:10px;" />
                                                <?php else: ?>
                                                    <p style="margin-top:10px;">No image uploaded</p>
                                                <?php endif; ?>
                                            </div>
                                        <?php } ?>
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