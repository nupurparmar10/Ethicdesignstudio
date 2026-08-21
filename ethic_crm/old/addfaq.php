<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (isset($_REQUEST['s1'])) {

    mysqli_query($con, "insert into faq set title='$_REQUEST[group]', title1='$_REQUEST[question]', desp='$_REQUEST[answer]'");

    $msg = "FAQ's added successfully!!!";
}
if (isset($_REQUEST['s3'])) {
    mysqli_query($con, "update faq set title='$_REQUEST[group]', title1='$_REQUEST[question]', desp='$_REQUEST[answer]' where f_id='$_REQUEST[f_id]'");
    header("Location: viewfaq.php?msg=set");
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
        $smenu13 = "4";
        $ssmenu13 = "10";
        $sssmenu13 = "1";
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
                <li class="active"><?php if (isset($_REQUEST['f_id'])) { ?>Modify FAQ's<?php } else { ?>Add FAQ's<?php } ?></li>
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
                        <form class="form-horizontal" method="post" action="addfaq.php" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?php
                                    if (isset($_REQUEST['f_id'])) {
                                        $c1 = mysqli_query($con, "select * from faq where f_id='$_REQUEST[f_id]'");
                                        $c = mysqli_fetch_row($c1);
                                        echo "<input type='hidden' name='f_id' value='$_REQUEST[f_id]'/>";
                                    ?>
                                        <h3 class="panel-title"><strong>Modify</strong> FAQ's</h3>
                                    <?php
                                    } else {
                                        $c[1] = $c[2] = $c[3] = $c[4] = "";
                                    ?>
                                        <h3 class="panel-title"><strong>Add New</strong> FAQ's</h3>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Group Name</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <select class="form-control" name="group" required>
                                                    <option value="">-- Select Group --</option>
                                                    <option value="Order Related" <?php if ($c[1] == 'Order Related') echo 'selected'; ?>>Order Related</option>
                                                    <option value="Shop Related" <?php if ($c[1] == 'Shop Related') echo 'selected'; ?>>Shop Related</option>
                                                    <option value="Cancellation related" <?php if ($c[1] == 'Cancellation related') echo 'selected'; ?>>Cancellation related</option>
                                                    <option value="Product Related" <?php if ($c[1] == 'Product Related') echo 'selected'; ?>>Product Related</option>
                                                    <option value="Return & Refund Related" <?php if ($c[1] == 'Return & Refund Related') echo 'selected'; ?>>Return & Refund Related</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Question</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="question" value='<?php echo $c[2]; ?>' required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 col-xs-12 control-label">Answer</label>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="input-group">
                                                <span class="input-group-addon"><span class="fa fa-pencil"></span></span>
                                                <input type="text" class="form-control" name="answer" value='<?php echo $c[3]; ?>' required />
                                            </div>
                                        </div>
                                    </div>
                                    <span id="mobileError" style="color: red; font-size: 14px;"></span>
                                </div>
                                <div class="panel-footer">
                                    <?php
                                    if (isset($_REQUEST['f_id'])) {
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