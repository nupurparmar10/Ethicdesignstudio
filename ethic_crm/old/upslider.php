<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
    if (isset($_POST['update'])) {
        $uploadDir = "../images/slider/";

        for ($i = 1; $i <= 2; $i++) {
            $s_id = $i;

            $title = mysqli_real_escape_string($con, $_POST["title$i"] ?? '');
            $desp = mysqli_real_escape_string($con, $_POST["desp$i"] ?? '');
            $link = mysqli_real_escape_string($con, $_POST["link$i"] ?? '');
            $oldPic = $_POST["pic_old$i"] ?? '';
            $newPic = $_FILES["pic$i"]['name'] ?? '';

            $picPath = $oldPic;

            // If a new image is uploaded
            if (!empty($newPic) && is_uploaded_file($_FILES["pic$i"]['tmp_name'])) {
                $oldFilePath = $uploadDir . basename($oldPic);
                if (!empty($oldPic) && file_exists($oldFilePath) && is_file($oldFilePath)) {
                    unlink($oldFilePath);
                }

            $sanitizedFilename = uniqid(time() . '_') . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($newPic));
                $tempPath = $_FILES["pic$i"]['tmp_name'];

                if (move_uploaded_file($tempPath, $uploadDir . $sanitizedFilename)) {
                    $picPath = $sanitizedFilename;
                }
            }

            $updateSQL = "UPDATE slider SET title='$title', desp='$desp', link='$link', pic='$picPath' WHERE s_id=$s_id";
            mysqli_query($con, $updateSQL);
        }

        $msg = "Sliders updated successfully.";
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
        $ssmenu13 = "1";
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
                <li><a href="#">Master</a></li>
                <li class="active">Update Slider</li>
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

                        $result = mysqli_query($con, "SELECT * FROM slider");

                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['s_id'];
                            $data[$id] = [
                                'title' => $row['title'] ?? '',
                                'desp'  => $row['desp'] ?? '',
                                'pic'   => $row['pic'] ?? '',
                                'link'   => $row['link'] ?? ''
                            ];
                        }

                        ?>

                        <form class="form-horizontal" method="post" action="upslider.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Update</strong> Slider</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">Title</label>
                                            <div class="col-md-12">
                                                <input class="form-control" name="title1" required value="<?php echo htmlspecialchars($data[1]['title']); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Link</label>
                                            <div class="col-md-12">
                                                <input class="form-control" name="link1" required value="<?php echo htmlspecialchars($data[1]['link']); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="control-label">Description</label>
                                            <div class="col-md-12">
                                                <textarea class="form-control" name="desp1" required><?php echo htmlspecialchars($data[1]['desp']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="control-label">Pic</label>
                                            <div class="col-md-12">
                                                <input type="file" class="form-control" name="pic1" />

                                                <input type="hidden" name="pic_old1" value="<?php echo htmlspecialchars($data[1]['pic']); ?>">

                                                <img class="img-fluid" src="../images/slider/<?php echo htmlspecialchars($data[1]['pic']); ?>" style="height:150px; width:auto; margin-top:20px;" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">Title</label>
                                            <div class="col-md-12">
                                                <input class="form-control" name="title2" required value="<?php echo htmlspecialchars($data[2]['title']); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Link</label>
                                            <div class="col-md-12">
                                                <input class="form-control" name="link2" required value="<?php echo htmlspecialchars($data[2]['link']); ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="control-label">Description</label>
                                            <div class="col-md-12">
                                                <textarea class="form-control" name="desp2" required><?php echo htmlspecialchars($data[2]['desp']); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <label class="control-label">Pic</label>
                                            <div class="col-md-12">
                                                <input type="file" class="form-control" name="pic2" />

                                                <input type="hidden" name="pic_old2" value="<?php echo htmlspecialchars($data[2]['pic']); ?>">

                                                <img class="img-fluid" src="../images/slider/<?php echo htmlspecialchars($data[2]['pic']); ?>" style="height:150px; width:auto; margin-top:20px;" />
                                            </div>
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