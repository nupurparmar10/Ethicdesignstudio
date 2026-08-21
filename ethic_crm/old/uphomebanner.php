<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_POST['update'])) {
    $uploadDir = "../images/matter/";

    for ($m_id = 26; $m_id <= 30; $m_id++) {
        $titleKey = 'title' . $m_id;
        $despKey = 'desp' . $m_id;
        $picOldKey = 'pic_old' . $m_id;
        $picFileKey = 'pic' . $m_id;

        $title = mysqli_real_escape_string($con, $_POST[$titleKey] ?? '');
        $desp = mysqli_real_escape_string($con, $_POST[$despKey] ?? '');
        $oldPic = $_POST[$picOldKey] ?? '';
        $newPic = $_FILES[$picFileKey]['name'] ?? '';

        $picPath = $oldPic;

        // If a new image is uploaded
        if (!empty($newPic) && is_uploaded_file($_FILES[$picFileKey]['tmp_name'])) {
            // Remove old image
            $oldFilePath = $uploadDir . basename($oldPic);
            if (!empty($oldPic) && file_exists($oldFilePath) && is_file($oldFilePath)) {
                unlink($oldFilePath);
            }

            // Sanitize and rename new image
            $sanitizedFilename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($newPic));
            $tempPath = $_FILES[$picFileKey]['tmp_name'];

            // Move uploaded file
            if (move_uploaded_file($tempPath, $uploadDir . $sanitizedFilename)) {
                $picPath = $sanitizedFilename;
            }
        }

        mysqli_query($con, "UPDATE matter SET title='$title', desp='$desp', pic='$picPath' WHERE m_id=$m_id");
    }

    $msg = "Banner Updated Successfully";
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

                        <form class="form-horizontal" method="post" action="uphomebanner.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Update</strong> Home Banners</h3>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    $data = [];
                                    $result = mysqli_query($con, "SELECT * FROM matter WHERE m_id BETWEEN 26 AND 30");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $data[$row['m_id']] = $row;
                                    }

                                    for ($m_id = 26; $m_id <= 30; $m_id++) {
                                        $pic = htmlspecialchars($data[$m_id]['pic']);
                                        $title = htmlspecialchars($data[$m_id]['title']);
                                        $desp = htmlspecialchars($data[$m_id]['desp']);
                                    ?>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Banner</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control" name="pic<?= $m_id ?>" />
                                                <input type="hidden" name="pic_old<?= $m_id ?>" value="<?= $pic ?>" />
                                                <?php if (!empty($pic)) { ?>
                                                    <img class="img-fluid" src="../images/matter/<?= $pic ?>" style="height:150px; width:auto; margin-top:20px;" />
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Title</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control" name="title<?= $m_id ?>" required><?= $title ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Description</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control" name="desp<?= $m_id ?>" required><?= $desp ?></textarea>
                                            </div>
                                        </div>

                                        <hr />
                                    <?php } ?>
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