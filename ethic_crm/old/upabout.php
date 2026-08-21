<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_POST['update'])) {
    $m_id = 12;

    $desp = mysqli_real_escape_string($con, $_POST['desp'] ?? '');
    $oldPic = $_POST['pic_old'] ?? '';
    $newPic = $_FILES['pic']['name'] ?? '';

    $uploadDir = "../images/matter/";
    $picPath = $oldPic;

    // If a new image is uploaded
    if (!empty($newPic) && is_uploaded_file($_FILES['pic']['tmp_name'])) {
        // Remove old image
        $oldFilePath = $uploadDir . basename($oldPic);
        if (!empty($oldPic) && file_exists($oldFilePath) && is_file($oldFilePath)) {
            unlink($oldFilePath);
        }

        // Sanitize and rename new image
        $sanitizedFilename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($newPic));
        $tempPath = $_FILES['pic']['tmp_name'];

        // Move uploaded file
        if (move_uploaded_file($tempPath, $uploadDir . $sanitizedFilename)) {
            $picPath = $sanitizedFilename;
        }
    }

    mysqli_query($con, "UPDATE matter SET desp='$desp', pic='$picPath' WHERE m_id=$m_id");


    // Update m_id 13 to 16
    for ($i = 13; $i <= 16; $i++) {
        $title = mysqli_real_escape_string($con, $_POST["title$i"] ?? '');
        $desp = mysqli_real_escape_string($con, $_POST["desp$i"] ?? '');

        mysqli_query($con, "UPDATE matter SET title='$title', desp='$desp' WHERE m_id=$i");
    }

    // Update m_id 17 to 20
    for ($i = 17; $i <= 20; $i++) {
        $title = mysqli_real_escape_string($con, $_POST["title$i"] ?? '');
        $desp  = mysqli_real_escape_string($con, $_POST["desp$i"] ?? '');
        $pic   = mysqli_real_escape_string($con, $_POST["pic$i"] ?? '');

        mysqli_query($con, "UPDATE matter SET title='$title', desp='$desp', pic='$pic' WHERE m_id=$i");
    }

    $msg = "About Update Successfully";
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

                                            <img class="img-fluid" src="../images/matter/<?php echo htmlspecialchars($data[12]['pic']); ?>" style="height:150px; width:auto; margin-top:20px;" />
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

                                    <h3 class="my-3">Amenities</h3>



                                    <div class="row">

                                        <!-- m_id = 17 -->
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="title17" value="<?php echo htmlspecialchars($data[17]['title']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="desp17" value="<?php echo htmlspecialchars($data[17]['desp']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="pic17" value="<?php echo htmlspecialchars($data[17]['pic']); ?>" required />
                                        </div>

                                        <!-- m_id = 18 -->
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="title18" value="<?php echo htmlspecialchars($data[18]['title']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="desp18" value="<?php echo htmlspecialchars($data[18]['desp']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="pic18" value="<?php echo htmlspecialchars($data[18]['pic']); ?>" required />
                                        </div>

                                        <!-- m_id = 19 -->
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="title19" value="<?php echo htmlspecialchars($data[19]['title']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="desp19" value="<?php echo htmlspecialchars($data[19]['desp']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="pic19" value="<?php echo htmlspecialchars($data[19]['pic']); ?>" required />
                                        </div>

                                        <!-- m_id = 20 -->
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="title20" value="<?php echo htmlspecialchars($data[20]['title']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="desp20" value="<?php echo htmlspecialchars($data[20]['desp']); ?>" required />
                                        </div>
                                        <div class="col-md-4" style="margin-top: 20px;">
                                            <input type="text" class="form-control" name="pic20" value="<?php echo htmlspecialchars($data[20]['pic']); ?>" required />
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