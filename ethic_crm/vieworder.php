<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = $msg2 = "";
if (isset($_REQUEST['msg'])) {
    $msg = "Order edited successfully!!!";
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
    <script src="js/jquery.min.js"></script>
</head>

<body>
    <!-- START PAGE CONTAINER -->
    <div class="page-container">
        <!-- START PAGE SIDEBAR -->
        <?php $menu16 = true;
        $smenu7 = "2";
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
                <li class="active">View Orders</li>
            </ul>
            <!-- END BREADCRUMB -->

            <!-- PAGE TITLE -->
            <div class="page-title">
                <h2>View Orders</h2>
            </div>
            <!-- END PAGE TITLE -->

            <!-- PAGE CONTENT WRAPPER -->
            <div class="page-content-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($msg) {
                        ?>
                            <div class="alert alert-info" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg; ?></strong>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        if ($msg1) {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg1; ?></strong>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        if ($msg2) {
                        ?>
                            <div class="alert alert-warning" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg2; ?></strong>
                            </div>
                        <?php
                        }
                        ?>

                        <!-- START DATATABLE EXPORT -->
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <form class="form-horizontal" method="post" action="vieworder.php<?php echo isset($_GET['u_id']) ? '?u_id=' . htmlspecialchars($_GET['u_id']) : ''; ?>" name='frm2' enctype="multipart/form-data">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="text" class="form-control" name="order_id" placeholder="Order ID" value="<?php echo isset($_POST['order_id']) ? htmlspecialchars($_POST['order_id']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="date" class="form-control" name="date_from" placeholder="From Date" value="<?php echo isset($_POST['date_from']) ? htmlspecialchars($_POST['date_from']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="date" class="form-control" name="date_to" placeholder="To Date" value="<?php echo isset($_POST['date_to']) ? htmlspecialchars($_POST['date_to']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="text" class="form-control" name="mobile" placeholder="Mobile" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="text" class="form-control" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <select name="country" id="countrySelect" class="form-control" onchange="getstate(this.value, 'stateSelect1')">
                                                    <option value="">Select Country*</option>
                                                    <?php
                                                    $countries = mysqli_query($con, "SELECT * FROM countries");
                                                    while ($c = mysqli_fetch_assoc($countries)) {
                                                        $selected = ($c['id'] == $country) ? 'selected' : '';
                                                        echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <select name="state" id="stateSelect1" class="form-control" onchange="getcity(this.value, 'citySelect')">
                                                    <option value="">Select State*</option>
                                                    <?php
                                                    if ($country) {
                                                        $states = mysqli_query($con, "SELECT * FROM states WHERE country_id = $country");
                                                        while ($s = mysqli_fetch_assoc($states)) {
                                                            $selected = ($s['id'] == $state) ? 'selected' : '';
                                                            echo "<option value='{$s['id']}' $selected>{$s['name']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <select name="city" id="citySelect" class="form-control">
                                                    <option value="">Select City*</option>
                                                    <?php
                                                    if ($state) {
                                                        $cities = mysqli_query($con, "SELECT * FROM cities WHERE state_id = $state");
                                                        while ($ci = mysqli_fetch_assoc($cities)) {
                                                            $selected = ($ci['id'] == $city) ? 'selected' : '';
                                                            echo "<option value='{$ci['id']}' $selected>{$ci['name']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-xs-2" style="margin-top: 20px;">
                                                <button class="btn btn-primary" type="submit" name="open">Open</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <br>
                                <div class="table-responsive" id="display">
                                    <?php
                                    $conditions = [];
                                    if (isset($_GET['u_id']) && !empty($_GET['u_id'])) {
                                        $conditions[] = "u_id = '" . mysqli_real_escape_string($con, $_GET['u_id']) . "'";
                                    }
                                    if (isset($_POST['open'])) {
                                        if (!empty($_POST['order_id'])) {
                                            $conditions[] = "order_id = '" . mysqli_real_escape_string($con, $_POST['order_id']) . "'";
                                        }
                                        if (!empty($_POST['status'])) {
                                            $conditions[] = "status = '" . mysqli_real_escape_string($con, $_POST['status']) . "'";
                                        }
                                        if (!empty($_POST['date_from'])) {
                                            $conditions[] = "DATE(created_at) >= '" . mysqli_real_escape_string($con, $_POST['date_from']) . "'";
                                        }
                                        if (!empty($_POST['date_to'])) {
                                            $conditions[] = "DATE(created_at) <= '" . mysqli_real_escape_string($con, $_POST['date_to']) . "'";
                                        }
                                        if (!empty($_POST['mobile'])) {
                                            $conditions[] = "mobile LIKE '%" . mysqli_real_escape_string($con, $_POST['mobile']) . "%'";
                                        }
                                        if (!empty($_POST['email'])) {
                                            $conditions[] = "email LIKE '%" . mysqli_real_escape_string($con, $_POST['email']) . "%'";
                                        }
                                        if (!empty($_POST['name'])) {
                                            $conditions[] = "name LIKE '%" . mysqli_real_escape_string($con, $_POST['name']) . "%'";
                                        }
                                        if (!empty($_POST['city'])) {
                                            $conditions[] = "city = '" . mysqli_real_escape_string($con, $_POST['city']) . "'";
                                        }
                                        if (!empty($_POST['state'])) {
                                            $conditions[] = "state = '" . mysqli_real_escape_string($con, $_POST['state']) . "'";
                                        }
                                        if (!empty($_POST['country'])) {
                                            $conditions[] = "country = '" . mysqli_real_escape_string($con, $_POST['country']) . "'";
                                        }
                                    }

                                    $sql = "SELECT * FROM checkout";
                                    if (!empty($conditions)) {
                                        $sql .= " WHERE " . implode(" AND ", $conditions);
                                    }
                                    $sql .= " ORDER BY order_id";

                                    $result = mysqli_query($con, $sql);
                                    $table = "";
                                    if (mysqli_num_rows($result) == 0) {
                                        echo "There are no Orders Available!!!";
                                    } else {
                                        $table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
                                                <caption><h1>Orders</h1></caption>
                                                <tr>
                                                    <th width='58'><span>S. No.</span></th>
                                                    <th width='100'><span>Order ID</span></th>
                                                    <th width='150'><span>Name</span></th>
                                                    <th width='150'><span>Mobile</span></th>
                                                    <th width='150'><span>Email</span></th>
                                                    <th width='150'><span>Address</span></th>
                                                    <th width='150'><span>City</span></th>
                                                    <th width='150'><span>State</span></th>
                                                    <th width='150'><span>Country</span></th>
                                                    <th width='100'><span>Pincode</span></th>
                                                    <th width='100'><span>Order Status</span></th>
                                                    <th width='150'><span>Created At</span></th>
                                                     <th width='100'><span>payment Type</span></th>
                                                    <th width='100'><span>Payment ID</span></th>
                                                    <th width='100'><span>Razorpay Order ID</span></th>
                                                    <th width='100'><span>Shipping Charge</span></th>
                                                    <th width='100'><span>Total Amount</span></th>
                                                </tr>";
                                    ?>
                                        <table class="table datatable table-bordered table-striped table-actions">
                                            <thead>
                                                <tr>
                                                    <th width='58'><span>S. No.</span></th>
                                                    <th width='100'><span>Order ID</span></th>
                                                    <th width="80">Actions</th>
                                                    <th width='150'><span>Name</span></th>
                                                    <th width='150'><span>Mobile</span></th>
                                                    <th width='150'><span>Email</span></th>
                                                    <th width='150'><span>Address</span></th>
                                                    <th width='150'><span>City</span></th>
                                                    <th width='150'><span>State</span></th>
                                                    <th width='150'><span>Country</span></th>
                                                    <th width='100'><span>Pincode</span></th>
                                                    <th width='100'><span>Order Status</span></th>
                                                    <th width='150'><span>Created At</span></th>
                                                    <th width='100'><span>payment Type</span></th>
                                                    <th width='100'><span>Payment ID</span></th>
                                                    <th width='100'><span>Razorpay Order ID</span></th>
                                                    <th width='100'><span>Shipping Charge</span></th>
                                                    <th width='100'><span>Total Amount</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php



                                                if ($row = mysqli_fetch_row($result)) {
                                                    $j = 1;
                                                    do {
                                                ?>
                                                        <tr id="<?php echo $row[0]; ?>">
                                                            <?php
                                                            echo "<td>$j</td>";
                                                            echo "<td>" . htmlspecialchars($row[2]) . "</td>"; ?>
                                                            <td>
                                                                <button type="button" class="btn btn-info btn-rounded btn-condensed btn-sm"
                                                                    onClick="window.open('vieworder_detail.php?check_id=<?php echo $row[0]; ?>','_self');"
                                                                    title="Order Details">
                                                                    <span class="fa fa-eye"></span>
                                                                </button>
                                                            </td>
                                                            <?php
                                                            echo "<td>" . htmlspecialchars($row[4]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[3]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[5]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[6]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[7]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[8]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[9]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[10]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[18]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[11]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[13]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[16]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[17]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[15]) . "</td>";
                                                            echo "<td>" . htmlspecialchars($row[14]) . "</td>";
                                                            $table .= "<tr>
                                                                <td style='padding-left:10px;'>$j</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[2]) . "</td>
                                                            
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($row[4]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[3]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[5]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[6]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[7]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[8]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[9]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[10]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[18]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[11]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[13]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[16]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[17]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[15]) . "</td>
                                                                <td style='padding-left:10px;'>" . htmlspecialchars($row[14]) . "</td>
                                                                </tr>";
                                                            ?>
                                                        </tr>
                                                <?php
                                                        $j++;
                                                    } while ($row = mysqli_fetch_array($result));
                                                }
                                                $table .= "</table>";
                                                ?>
                                            </tbody>
                                        </table>
                                        <br><br>
                                        <div class="col-md-1 col-xs-1">
                                            <form action="printlist.php" method="post" target="_blank">
                                                <input type="hidden" value="<?php echo $table; ?>" name="query" />
                                                <button class="btn btn-primary" type="submit" name="s11">Print</button>
                                            </form>
                                        </div>
                                        <div class="col-md-3 col-xs-3">
                                            <form action="excel.php" method="post">
                                                <input type="hidden" name="query" value="<?php echo $table; ?>" />
                                                <input type="hidden" name="fn" value="Orders" />
                                                <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
                                            </form>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- END DATATABLE EXPORT -->
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT WRAPPER -->
        </div>
        <!-- END PAGE CONTENT -->
    </div>
    <!-- END PAGE CONTAINER -->

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

    <!-- START THIS PAGE PLUGINS-->
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>
    <!-- END THIS PAGE PLUGINS-->

    <!-- START TEMPLATE -->
    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
    <!-- END TEMPLATE -->
    <script>
        function getstate(country, stateid) {
            if (country) {
                $.ajax({
                    url: 'getcity_state.php',
                    type: 'POST',
                    data: {
                        country: country
                    },
                    success: function(response) {
                        $('#' + stateid).html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:");
                    }
                });
            }
        }
        //to select drop down of state as per country with value selected
        function getstate1(country, stateid, statevalue) {
            if (country) {
                $.ajax({
                    url: 'getcity_state.php',
                    type: 'POST',
                    data: {
                        country: country
                    },
                    success: function(response) {
                        $('#' + stateid).html(response);
                        document.getElementById(stateid).value = statevalue;
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:");
                    }
                });
            }
        }

        //to select drop down of city as per state without value
        function getcity(state, cityid) {
            if (state) {
                $.ajax({
                    url: 'getcity_state.php',
                    type: 'POST',
                    data: {
                        state: state
                    },
                    success: function(response) {
                        $('#' + cityid).html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:");
                    }
                });
            }
        }

        //to select drop down of city as per state with value selected
        function getcity1(state, cityid, cityvalue) {
            if (state) {
                $.ajax({
                    url: 'getcity_state.php',
                    type: 'POST',
                    data: {
                        state: state
                    },
                    success: function(response) {
                        $('#' + cityid).html(response);
                        document.getElementById(cityid).value = cityvalue;
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:");
                    }
                });
            }
        }
    </script>
</body>

</html>