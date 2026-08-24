<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_REQUEST['s1'])) {
    $g = mysqli_fetch_row(mysqli_query($con, "select max(s_id) from pro_subcategory"));
    $id = $g[0] + 1;

    $uploadDir = "../assets/images/products/";
    $picpath = '';

    if (!empty($_FILES['pic']['name']) && is_uploaded_file($_FILES['pic']['tmp_name'])) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024;
        $fileType = mime_content_type($_FILES['pic']['tmp_name']);
        $fileSize = $_FILES['pic']['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPEG, PNG, and GIF files are allowed.";
        } elseif ($fileSize > $maxFileSize) {
            $errors[] = "File size exceeds 5MB.";
        } else {
            $sanitizedFilename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES['pic']['name']));
            $tempPath = $_FILES['pic']['tmp_name'];
            if (move_uploaded_file($tempPath, $uploadDir . $sanitizedFilename)) {
                $picpath = "assets/images/products/" . $sanitizedFilename;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    mysqli_query($con, "insert into pro_subcategory set s_id='$id', sub_pic='$picpath', sname='$_REQUEST[sname]', weight='$_REQUEST[weight]',pt_id='$_REQUEST[pt_id]'");
    $msg = "Product Sub-category added successfully!!!";
}
if (isset($_REQUEST['s3'])) {
    $id = $_REQUEST['s_id'];

    $oldPic = $_POST['pic_old'] ?? '';
    $newPic = $_FILES['pic']['name'] ?? '';

    $uploadDir = "../assets/images/products/";
    $picpath = $oldPic;

    if (!empty($newPic) && is_uploaded_file($_FILES['pic']['tmp_name'])) {

        $oldFilePath = $uploadDir . basename($oldPic);
        if (!empty($oldPic) && file_exists($oldFilePath) && is_file($oldFilePath)) {
            unlink($oldFilePath);
        }

        $sanitizedFilename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($newPic));
        $tempPath = $_FILES['pic']['tmp_name'];

        if (move_uploaded_file($tempPath, $uploadDir . $sanitizedFilename)) {
            $picpath = "assets/images/products/" . $sanitizedFilename;
        }
    }

    mysqli_query($con, "update pro_subcategory set sname='$_REQUEST[sname]', weight='$_REQUEST[weight]', pt_id='$_REQUEST[pt_id]', sub_pic='$picpath' where s_id='$_REQUEST[s_id]'");
    header("Location: viewsubcategory.php?msg=set");
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
        <?php $menu2 = true;
        $smenu2 = "6";
        $ssmenu2 = "61";
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
                <li><a href="#">Masters</a></li>
                <li><a href="#">Product Sub-category </a></li>
                <li class="active">Add Product Sub-category</li>
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
                        <form class="form-horizontal" method="post" action="addsubcategory.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?php
                                    if (isset($_REQUEST['s_id'])) {
                                        $c = mysqli_fetch_row(mysqli_query($con, "select * from pro_subcategory where s_id='$_REQUEST[s_id]'"));
                                        echo "<input type='hidden' name='s_id' value='$_REQUEST[s_id]'/>";
                                    ?>
                                        <h3 class="panel-title"><strong>Modify</strong> Product Sub-category</h3>
                                    <?php
                                    } else {
                                        $c[1] = $c[2] = $c[4] = "";
                                    ?>
                                        <h3 class="panel-title"><strong>Add New</strong> Product Sub-category</h3>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="panel-body">

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Product Type</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <select class="form-control" name="pt_id" tabindex='1'>
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    $f1 = mysqli_query($con, "select * from producttype order by ptname");
                                                    while ($f = mysqli_fetch_row($f1)) {
                                                        if ($c[1] == $f[0])
                                                            echo "<option value='$f[0]' selected='selected'>$f[1]</option>";
                                                        else
                                                            echo "<option value='$f[0]'>$f[1]</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Sub-Category</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="sname" required value='<?php echo $c[2]; ?>' />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Weight</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="number" class="form-control" name="weight" required value='<?php echo $c[4]; ?>' />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Pic</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control" name="pic" accept="image/*" />

                                            <input type="hidden" name="pic_old" value="<?php echo htmlspecialchars($c[3]); ?>">

                                            <?php if (!empty($c[3]) && file_exists("../" . $c[3])) { ?>
                                                <img class="img-fluid" src="../<?php echo htmlspecialchars($c[3]); ?>" style="height:150px; width:auto; margin-top:20px;" />
                                            <?php } ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer">
                                    <?php
                                    if (isset($_REQUEST['s_id'])) {
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