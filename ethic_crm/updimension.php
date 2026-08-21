<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";

if (isset($_GET['update_id'])) {
    $id = intval($_GET['update_id']);

    $min_quantity = mysqli_real_escape_string($con, $_GET['min_quantity'] ?? '');
    $max_quantity = mysqli_real_escape_string($con, $_GET['max_quantity'] ?? '');
    $height = mysqli_real_escape_string($con, $_GET['height'] ?? '');
    $width = mysqli_real_escape_string($con, $_GET['width'] ?? '');
    $length = mysqli_real_escape_string($con, $_GET['length'] ?? '');

    $check_query = "SELECT * FROM dimension 
                    WHERE min_quantity = '$min_quantity' 
                      AND max_quantity = '$max_quantity' 
                      AND d_id != $id";
    $check_result = mysqli_query($con, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $msg1 = "This dimension range already exists.";
    } else {
        $sql = "UPDATE dimension SET 
                    min_quantity = '$min_quantity',
                    max_quantity = '$max_quantity',
                    height = '$height',
                    width = '$width',
                    length = '$length'
                WHERE d_id = $id";

        if (mysqli_query($con, $sql)) {
            $msg = "Dimension Updated Successfully";
        } else {
            echo "Error updating value: " . mysqli_error($con);
        }
    }
}


// Handle delete
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($con, "DELETE FROM dimension WHERE d_id = $id");
    $msg = "Dimension Deleted Successfully";
}

// Handle add
if (isset($_POST['submit'])) {
    $min_quantity = mysqli_real_escape_string($con, $_POST['min_quantity'] ?? '');
    $max_quantity = mysqli_real_escape_string($con, $_POST['max_quantity'] ?? '');
    $height = mysqli_real_escape_string($con, $_POST['height'] ?? '');
    $width = mysqli_real_escape_string($con, $_POST['width'] ?? '');
    $length = mysqli_real_escape_string($con, $_POST['length'] ?? '');

    if (!empty($min_quantity) && !empty($max_quantity)) {
        $check = mysqli_query($con, "SELECT * FROM dimension 
                                     WHERE min_quantity = '$min_quantity' 
                                       AND max_quantity = '$max_quantity'");

        if (mysqli_num_rows($check) > 0) {
            $msg1 = "This dimension range already exists.";
        } else {
            mysqli_query($con, "INSERT INTO dimension (min_quantity, max_quantity, height, width, length) 
                                VALUES ('$min_quantity', '$max_quantity', '$height', '$width', '$length')");
            $msg = "Dimension Added Successfully";
        }
    } else {
        $msg1 = "Please provide minimum and maximum quantity.";
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

    <script src="js/jquery.min.js"></script>
    <script type="text/javascript">
        function delete_row(row) {
            var box = $("#mb-remove-row");
            box.addClass("open");
            box.find(".mb-control-yes").off("click").on("click", function() {
                box.removeClass("open");
                window.location.href = "updimension.php?delete_id=" + row;
            });
        }

        function updateValue(id) {
            const min = document.querySelector(`input[name="min_quantity_${id}"]`);
            const max = document.querySelector(`input[name="max_quantity_${id}"]`);
            const height = document.querySelector(`input[name="height_${id}"]`);
            const width = document.querySelector(`input[name="width_${id}"]`);
            const length = document.querySelector(`input[name="length_${id}"]`);

            if (!min || !max || !height || !width || !length) {
                alert("Required input fields not found for ID " + id);
                return;
            }

            const url = `updimension.php?update_id=${id}` +
                `&min_quantity=${encodeURIComponent(min.value)}` +
                `&max_quantity=${encodeURIComponent(max.value)}` +
                `&height=${encodeURIComponent(height.value)}` +
                `&width=${encodeURIComponent(width.value)}` +
                `&length=${encodeURIComponent(length.value)}`;

            window.location.href = url;
        }
    </script>
</head>

<body>
    <div class="page-container">
        <?php $menu13 = true;
        $smenu13 = "2";
        $ssmenu13 = "4";
        include_once("sidebar.php"); ?>
        <div class="page-content">
            <?php include_once("topheader.php"); ?>
            <ul class="breadcrumb">
                <li><a href="#" onclick="window.location=document.referrer;">Back</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#">Master</a></li>
                <li class="active">Add Material Type</li>
            </ul>
            <div class="page-content-wrap">
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($msg) { ?>
                            <div class="alert alert-success" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg; ?></strong>
                            </div>
                        <?php } ?>
                        <?php if ($msg1) { ?>
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                <strong><?php echo $msg1; ?></strong>
                            </div>
                        <?php } ?>

                        <?php
                        $result = mysqli_query($con, "SELECT * FROM dimension ORDER BY min_quantity ASC");
                        $data = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['d_id'];
                            $data[$id] = [
                                'min_quantity' => $row['min_quantity'] ?? '',
                                'max_quantity' => $row['max_quantity'] ?? '',
                                'height' => $row['height'] ?? '',
                                'width' => $row['width'] ?? '',
                                'length' => $row['length'] ?? ''

                            ];
                        }
                        ?>

                        <form class="form-horizontal" method="post" action="updimension.php">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><strong>Add</strong> Material Type</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Min Quantity</th>
                                                    <th>Max Quantity</th>
                                                    <th>Height</th>
                                                    <th>Width</th>
                                                    <th>Length</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data as $id => $type) { ?>
                                                    <tr>
                                                        <td><input type="number" class="form-control" name="min_quantity_<?php echo $id; ?>" value="<?php echo htmlspecialchars($type['min_quantity']); ?>" required /></td>
                                                        <td><input type="number" class="form-control" name="max_quantity_<?php echo $id; ?>" value="<?php echo htmlspecialchars($type['max_quantity']); ?>" required /></td>
                                                        <td><input type="number" class="form-control" name="height_<?php echo $id; ?>" value="<?php echo htmlspecialchars($type['height']); ?>" required /></td>
                                                        <td><input type="number" class="form-control" name="width_<?php echo $id; ?>" value="<?php echo htmlspecialchars($type['width']); ?>" required /></td>
                                                        <td><input type="number" class="form-control" name="length_<?php echo $id; ?>" value="<?php echo htmlspecialchars($type['length']); ?>" required /></td>
                                                        <td>
                                                            <button type="button" onclick="updateValue('<?php echo $id; ?>')" class="btn btn-primary btn-block">
                                                                <i class="fa fa-save"></i>
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button type="button" onclick="delete_row('<?php echo $id; ?>')" class="btn btn-danger btn-block">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                                <!-- Add New Row -->
                                                <tr>
                                                    <td>
                                                        <input type="number" class="form-control" name="min_quantity" placeholder="Min Quantity" />
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="max_quantity" placeholder="Max Quantity" />
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="height" placeholder="Height (cm)" />
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="width" placeholder="Width (cm)" />
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="length" placeholder="Length (cm)" />
                                                    </td>
                                                    <td style="width:10%">
                                                        <button type="submit" name="submit" class="btn btn-primary btn-block">Add</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Box -->
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
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
</body>

</html>