<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = $msg2 = "";
if (isset($_REQUEST['msg'])) {
    $msg = "Users Updated Successfully!!!";
}
if (isset($_REQUEST['u_id'])) {
    mysqli_query($con, "UPDATE users SET status = 0 WHERE u_id='$_REQUEST[u_id]'");
    $msg1 = "User Deleted Successfully!!!";
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
    <script type="text/javascript" language="javascript">
        function delete_row(row) {
            var box = $("#mb-remove-row");
            box.addClass("open");
            box.find(".mb-control-yes").on("click", function() {
                box.removeClass("open");
                delete_row1(row);
                $("#" + row).hide("slow", function() {
                    $(this).remove();
                });
            });
        }

        function delete_row1(row) {
            var path = "viewuser.php?u_id=" + row;
            window.open(path, "_self");
        }
    </script>
    <script src="js/jquery.min.js"></script>
</head>

<body>
    <!-- START PAGE CONTAINER -->
    <div class="page-container">
        <!-- START PAGE SIDEBAR -->
        <?php $menu16 = true;
        $smenu7 = "1";
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
                <li class="active">View Users</li>
            </ul>
            <!-- END BREADCRUMB -->

            <!-- PAGE TITLE -->
            <div class="page-title">
                <h2>View Users</h2>
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
                                <form class="form-horizontal" method="post" action="">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="text" class="form-control" name="name" placeholder="Name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="text" class="form-control" name="mobile" placeholder="Mobile" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6" style="margin-top: 20px;">
                                                <input type="text" class="form-control" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                            </div>
                                            <div class="col-md-2 col-xs-6 col-xs-6" style="margin-top: 20px;">
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
                                            <div class="col-md-3" style="margin-top: 20px;">
                                                <button class="btn btn-primary" type="submit" name="filter">Open</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <br>

                                <div class="table-responsive" id="display">
                                    <?php
                                    $conditions = ["status = 1"]; // Only active users
                                    if (isset($_POST['filter'])) {
                                        if (!empty($_POST['name'])) {
                                            $conditions[] = "name LIKE '%" . mysqli_real_escape_string($con, $_POST['name']) . "%'";
                                        }
                                        if (!empty($_POST['mobile'])) {
                                            $conditions[] = "mobile LIKE '%" . mysqli_real_escape_string($con, $_POST['mobile']) . "%'";
                                        }
                                        if (!empty($_POST['email'])) {
                                            $conditions[] = "email LIKE '%" . mysqli_real_escape_string($con, $_POST['email']) . "%'";
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

                                    $sql = "SELECT * FROM users";
                                    if (!empty($conditions)) {
                                        $sql .= " WHERE " . implode(" AND ", $conditions);
                                    }
                                    $sql .= " ORDER BY name";

                                    $result = mysqli_query($con, $sql);
                                    $table = "";

                                    if (mysqli_num_rows($result) == 0) {
                                        echo "There are no Users Available!!!";
                                    } else {
                                        $table = "<table width='100%' border='1' align='center' style='border-collapse:collapse;'>
                                            <caption><h1>Users</h1></caption>
                                            <tr>
                                                <th>S. No.</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Pincode</th>
                                                <th>Country</th>
                                            </tr>";

                                        echo '<table class="table table-bordered table-striped">';
                                        echo '<thead>
                                            <tr>
                                                <th>S. No.</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Pincode</th>
                                                <th>Country</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

                                        $j = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            // Fetch location names
                                            $city = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM cities WHERE id = '{$row['city']}'"))['name'] ?? '';
                                            $state = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM states WHERE id = '{$row['state']}'"))['name'] ?? '';
                                            $country = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM countries WHERE id = '{$row['country']}'"))['name'] ?? '';

                                            echo "<tr>
                                                    <td>$j</td>
                                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                                    <td>" . htmlspecialchars($row['mobile']) . "</td>
                                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                                    <td>" . htmlspecialchars($row['address']) . "</td>
                                                    <td>" . htmlspecialchars($city) . "</td>
                                                    <td>" . htmlspecialchars($state) . "</td>
                                                    <td>" . htmlspecialchars($row['pincode']) . "</td>
                                                    <td>" . htmlspecialchars($country) . "</td>
                                                    <td>
                                                        <button type='button' class='btn btn-info btn-sm' onclick=\"window.open('vieworder.php?u_id={$row['u_id']}','_self');\">View Orders</button>
                                                        <button type='button' class='btn btn-danger btn-sm' onclick=\"delete_row('{$row['u_id']}');\">Delete</button>
                                                    </td>
                                                </tr>";

                                            $table .= "<tr>
                                                            <td style='padding-left:10px;'>$j</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($row['name']) . "</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($row['mobile']) . "</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($row['email']) . "</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($row['address']) . "</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($city) . "</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($state) . "</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($row['pincode']) . "</td>
                                                            <td style='padding-left:10px;'>" . htmlspecialchars($country) . "</td>
                                                        </tr>";

                                            $j++;
                                        }

                                        echo '</tbody></table>';
                                        $table .= "</table>";
                                    ?>
                                        <br><br>
                                        <div class="col-md-1 col-xs-1">
                                            <form action="printlist.php" method="post" target="_blank">
                                                <input type="hidden" name="query" value="<?php echo htmlspecialchars($table); ?>" />
                                                <button class="btn btn-primary" type="submit" name="s11">Print</button>
                                            </form>
                                        </div>
                                        <div class="col-md-3 col-xs-3">
                                            <form action="excel.php" method="post">
                                                <input type="hidden" name="query" value="<?php echo htmlspecialchars($table); ?>" />
                                                <input type="hidden" name="fn" value="Users" />
                                                <button class="btn btn-primary" type="submit" name="s1">Excel Sheet</button>
                                            </form>
                                        </div>
                                    <?php } ?>
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

    <!-- MESSAGE BOX-->
    <div class="message-box animated fadeIn" data-sound="alert" id="mb-remove-row">
        <div class="mb-container">
            <div class="mb-middle">
                <div class="mb-title"><span class="fa fa-times"></span> Remove <strong>Data</strong> ?</div>
                <div class="mb-content">
                    <p>Are you sure you want to remove this row?</p>
                    <p>Press Yes if you sure.</p>
                </div>
                <div class="mb-footer">
                    <div class="pull-right">
                        <button class="btn btn-success btn-lg mb-control-yes">Yes</button>
                        <button class="btn btn-default btn-lg mb-control-close">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END MESSAGE BOX-->

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

    <!-- START THIS PAGE PLUGINS-->
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
    <script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
    <script type="text/javascript" src="js/ plugins/tableexport/html2canvas.js"></script>
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