<?php
ob_start();
session_start();
include_once("connect.php");
$msg = $msg1 = "";
if (!isset($_REQUEST['item_id'])) {
    header("Location:viewproduct1.php");
    die;
}
if (isset($_REQUEST['s3'])) {
    $id = $_REQUEST['item_id'];
    foreach ($_REQUEST as $key => $value) {
        $_REQUEST[$key] = str_replace("'", "\'", $_REQUEST[$key]);
        $_REQUEST[$key] = str_replace('"', '\"', $_REQUEST[$key]);
    }
    $hsn1 = mysqli_fetch_row(mysqli_query($con, "select hsn from producttype where ptname = '$_REQUEST[ptype]'"));
    $hsn = $hsn1[0];
    $ptype       = mysqli_real_escape_string($con, $_REQUEST['ptype']);
    $purdesp     = mysqli_real_escape_string($con, $_REQUEST['purdesp']);
    $saledesp    = mysqli_real_escape_string($con, $_REQUEST['saledesp']);
    $unit        = mysqli_real_escape_string($con, $_REQUEST['unit']);
    $tax         = mysqli_real_escape_string($con, $_REQUEST['tax']);
    $website     = mysqli_real_escape_string($con, $_REQUEST['website']);
    $s_id        = mysqli_real_escape_string($con, $_REQUEST['s_id']);
    $material    = mysqli_real_escape_string($con, $_REQUEST['material']);
    $collection  = mysqli_real_escape_string($con, $_REQUEST['collection']);
    $item_id     = mysqli_real_escape_string($con, $_REQUEST['item_id']);
    $pcode     = mysqli_real_escape_string($con, $_REQUEST['pcode']);
    $product_desp    = mysqli_real_escape_string($con, $_REQUEST['product_desp']);
    $query = "
    UPDATE item_details 
    SET 
        ptype='$ptype',
        purdesp='$purdesp',
        hsn='$hsn',
        saledesp='$saledesp',
        unit='$unit',
        tax='$tax',
        website='$website',
        s_id='$s_id',
        material_type='$material',
        collection='$collection',
        pcode='$pcode',
        product_desp='$product_desp'
    WHERE item_id='$item_id'
    ";

    mysqli_query($con, $query) or die(mysqli_error($con));

    $count = count($_REQUEST['stock']);
    for ($i = 0; $i < $count; $i++) 
        {
        $stock = $_REQUEST['stock'][$i];
        $color = $_REQUEST['color'][$i];
        $size = $_REQUEST['size'][$i];
        if ($stock != 0 && $color!='' && $size!='') 
        {
            $v_id = $_REQUEST['v_id'][$i];
            
            $stock = $_REQUEST['stock'][$i];
            $webstock = $_REQUEST['webstock'][$i];
            $purrate = $_REQUEST['purrate'][$i];
            $edsellrate = $_REQUEST['edsellrate'][$i];
            $standard_color = $_REQUEST['standard_color'][$i];

            $result = mysqli_query($con, "SELECT v_id FROM variant WHERE v_id='$v_id'");
            if (mysqli_num_rows($result) > 0) {
                mysqli_query($con, "update variant set  size='$size', color='$color',stock='$stock',webstock='$webstock',purrate='$purrate',edsellrate='$edsellrate',standard_color='$standard_color' where v_id='$v_id'");
            } else {
                mysqli_query($con, "insert into variant set item_id ='$_REQUEST[item_id]', size='$size', color='$color',stock='$stock',webstock='$webstock',purrate='$purrate',edsellrate='$edsellrate',standard_color='$standard_color'");
                $new_v_id = mysqli_insert_id($con);
                $barcode = encryptId($new_v_id);
                mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$new_v_id'");
            }
        }
    }
    header("Location: viewproduct1.php?msg=set");
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

    <script>
        function delete_row(row) {
            $("#" + row).remove();
            calc();
        }
    </script>
    <script>
        function getvalues(val) {
            var v_id = document.getElementsByName("v_id[]");
            var purrate = document.getElementsByName("purrate[]");
            var taxper = document.getElementsByName("taxper[]");
            var edsellrate = document.getElementsByName("edsellrate[]");
            for (i = 0; i < v_id.length; i++) {
                if (v_id[i].value == val) {
                    str = (v_id[i].value).split("-");
                    purrate[i].value = str[1];
                    taxper[i].value = str[2];
                    edsellrate[i].value = str[3];
                }
            }
        }

        function calc() {
            var taxabletot = 0;
            var taxtot = totpurvalue = 0;
            var tax1 = document.getElementById("tax").value;
            var str = tax1.split(":");
            var tax = str[1];
            var stock = document.getElementsByName("stock[]");
            var purrate = document.getElementsByName("purrate[]");
            var taxablevalue = document.getElementsByName("taxablevalue[]");
            var tax_amt = document.getElementsByName("tax_amt[]");
            var tot_pur_value = document.getElementsByName("tot_pur_value[]");
            var tpurrate = document.getElementsByName("tot_pur_rate[]");
            var selling_price = document.getElementsByName("selling_price[]");
            var edsellrate1 = document.getElementsByName("edsellrate[]");
            for (var i = 0; i < stock.length; i++) 
            {
                var sum = sum1 = sum2 = sum3 = sum4 = 0;
                var s1 = parseFloat(stock[i].value);
                var p1 = parseFloat(purrate[i].value);
                var edsellrate = parseFloat(edsellrate1[i].value);
                if (s1 > 0 && p1>0) 
                {
                    //Taxable Amt
                    sum = (p1 * s1) * 1;
                    taxablevalue[i].value = sum;
                    taxabletot += sum * 1;

                    //Tax Amt
                    sum1 = sum * tax / 100;
                    tax_amt[i].value = sum1.toFixed(2);
                    taxtot += sum1 * 1;

                    //Total Purchase Value
                    sum2 = sum + sum1;
                    tot_pur_value[i].value = sum2.toFixed(2);
                    totpurvalue += sum2;

                    //Total Purchase Rate
                    sum3 = sum2 / s1;
                    tpurrate[i].value = sum3.toFixed(2);

                    //Selling Price
                    sum4 = sum3 + (sum3 * 60) / 100;
                    selling_price[i].value = sum4.toFixed(2);

                }
            }
            document.getElementById("tot_tax_amt").value = taxtot * 1;
            document.getElementById("tot_taxable").value = taxabletot * 1;
            document.getElementById("tot_pur_value").value = totpurvalue * 1;

        }
    </script>
    <script>
        var counter = 1;
    </script>
    <script>
        function more() {
            var $table = $('#input_fields');
            var $tr = $table.find('tr').eq(1).clone();
            $tr.attr("id", counter);
            $tr.find('select').eq(0).attr("id", "v" + counter);
            $tr.appendTo($table).find('input').val('');
            $tr.appendTo($table).find('select').eq(0).val('');
            $tr.appendTo($table).find('select').eq(1).val('');
            $("#input_fields").append($tr);
            counter++;
        }

        function getsubcategory(val) {
            $.ajax({
                url: 'insert_data.php',
                type: 'POST',
                data: {
                    subcategory: val
                },
                success: ajaxSuccess1,
                error: ajaxError
            });
        }

        function ajaxSuccess1(response) {
            $('#subcategory').html(response);
        }

        function ajaxError() {
            alert("error");
        }
    </script>
</head>

<body>
    <!-- START PAGE CONTAINER -->
    <div class="page-container">

        <!-- START PAGE SIDEBAR -->
        <?php $menu2 = true;
        $smenu2 = "2";
        $ssmenu2 = "21";
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
                <li><a href="#">Product Master</a></li>
                <li class="active">Modify Product</li>
            </ul>
            <!-- END BREADCRUMB -->

            <!-- PAGE TITLE -->
            <div class="page-title">
                <h2><span class="fa fa-lsit"></span>Modify Product</h2>
                <span id="form_response"></span>
            </div>
            <!-- END PAGE TITLE -->

            <!-- PAGE CONTENT WRAPPER -->
            <div class="page-content-wrap">


                <div class="content-frame-body">
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
                            <form class="form-horizontal" method="post" action="editproduct.php" enctype="multipart/form-data" onsubmit="return confirm('Sure?');" name='frm2'>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?php
                                        if (isset($_REQUEST['item_id'])) {
                                            $c1 = mysqli_query($con, "select * from item_details where item_id='$_REQUEST[item_id]'");
                                            $c = mysqli_fetch_row($c1);
                                            echo "<input type='hidden' name='item_id' value='$_REQUEST[item_id]'/>";
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped table-actions">
                                                        <tbody>
                                                            <tr>
                                                                <th width="15%">Product Code</th>
                                                                <td colspan='3'><div class="form-group">
                                                                        <input type="text" class="form-control" name="pcode" value="<?php echo $c[1]; ?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th width="15%">Product Type</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control" name="ptype" required onchange=' getsubcategory(this.value);'>
                                                                            <option value=''>--Select--</option>
                                                                            <?php
                                                                            $f1 = mysqli_query($con, "select * from producttype ");
                                                                            while ($f = mysqli_fetch_row($f1)) {
                                                                                if ($c[2] == $f[1])
                                                                                    echo "<option value='$f[1]' selected='selected'>$f[1]</option>";
                                                                                else
                                                                                    echo "<option value='$f[1]'>$f[1]</option>";
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <th width="10%">Product Sub-Category</th>
                                                                <td id="subcategory">
                                                                    <?php if ($c[2] != 0) {
                                                                    ?>
                                                                        <div class="form-group">
                                                                            <select name="s_id" class="form-control" />
                                                                            <?php
                                                                            $f1 = mysqli_query($con, "select * from pro_subcategory where s_id='$c[10]' ");
                                                                            while ($f = mysqli_fetch_row($f1)) {
                                                                                if ($c[10] == $f[0])
                                                                                    echo "<option value='$f[0]' selected='selected'>$f[2]</option>";
                                                                                else
                                                                                    echo "<option value='$f[0]'>$f[2]</option>";
                                                                            }
                                                                            ?>
                                                                            </select>
                                                                        </div>
                                                                    <?php
                                                                    } else {
                                                                    ?>
                                                                        <div class="form-group">
                                                                            <select name="s_id" class="form-control" />
                                                                            <option value=''>--Select--</option>
                                                                            </select>
                                                                        </div>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th width="15%">Material Type</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control" name="material">
                                                                            <option value=''>--Select--</option>
                                                                            <?php
                                                                            $f1 = mysqli_query($con, "select * from material_type ");
                                                                            while ($f = mysqli_fetch_row($f1)) {
                                                                                if ($c[11] == $f[0])
                                                                                    echo "<option value='$f[0]' selected='selected'>$f[1]</option>";
                                                                                else
                                                                                    echo "<option value='$f[0]'>$f[1]</option>";
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <th width="15%">Collection</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control" name="collection">
                                                                            <option value=''>--Select--</option>
                                                                            <?php
                                                                            $f1 = mysqli_query($con, "select * from collection ");
                                                                            while ($f = mysqli_fetch_row($f1)) {
                                                                                if ($c[12] == $f[0])
                                                                                    echo "<option value='$f[0]' selected='selected'>$f[1]</option>";
                                                                                else
                                                                                    echo "<option value='$f[0]'>$f[1]</option>";
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th width="10%">Purchase Description</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="purdesp" value="<?php echo $c[3]; ?>" />
                                                                    </div>
                                                                </td>
                                                                <th width="10%">Product Name</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="saledesp" value="<?php echo $c[5]; ?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th width="10%">Tax</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control" name="tax" required id="tax" onchange="calc();">
                                                                            <option value=''>--Select--</option>
                                                                            <?php
                                                                            $f1 = mysqli_query($con, "select * from taxes where status='1'");
                                                                            while ($f = mysqli_fetch_row($f1)) {
                                                                                if ($c[7] == $f[0])
                                                                                    echo "<option value='$f[0]:$f[2]' selected='selected'>$f[1]</option>";
                                                                                else
                                                                                    echo "<option value='$f[0]:$f[2]'>$f[1]</option>";
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <th>Unit</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select class="form-control select" name="unit">
                                                                            <option value="PCS" <?php if ($c[6] == 'PCS') echo 'selected'; ?>>PCS</option>
                                                                            <option value="Meter" <?php if ($c[6] == 'Meter') echo 'selected'; ?>>Meter</option>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>General Purchase Rate</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <?php
                                                                        $min = 0;
                                                                        if (isset($_REQUEST['item_id'])) {
                                                                            $min_pur = mysqli_fetch_row(mysqli_query($con, "select min(purrate) from variant where item_id='$_REQUEST[item_id]'"));
                                                                            $min = $min_pur[0];
                                                                        }
                                                                        ?>
                                                                        <input type="text" class="form-control" value="<?php echo $min; ?>" id="gen_pur_rate" name="gen_pur_rate" onchange="setpurrate(this.value);" />

                                                                    </div>
                                                                </td>
                                                                <th width="10%">Show On Website</th>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <select name="website" class="form-control select" />
                                                                        <option value='1' <?php if ($c[8] == '1') echo 'selected'; ?>>Show</option>
                                                                        <option value='0' <?php if ($c[8] == '0') echo 'selected'; ?>>Hide</option>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <Tr>
                                                                <th>Product Description</th>
                                                                <td colspan='3'>
                                                                    <div class="form-group">
                                                                        <textarea class="form-control" name="product_desp" id="product_desp"><?php echo $c[13]; ?></textarea>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan='4' style="max-width:300px; overflow-x:auto;">

                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered table-striped table-actions" id="input_fields">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Size</th>
                                                                                    <th>Color</th>
                                                                                    <th>Standard Color</th>
                                                                                    <th>Stock In<br>Store</th>
                                                                                    <th>Stock In<br>Website</th>
                                                                                    <th>Purchase <br> Rate</th>
                                                                                    <th>Taxable <br> Value</th>
                                                                                    <th>Tax Amt.</th>
                                                                                    <th>Total <br> Purchase <br> Value</th>
                                                                                    <th>Total <br> Purchase <br> Rate</th>
                                                                                    <th>Selling <br> Price</th>
                                                                                    <th>Ethnic <br> Selling <br> Price</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php
                                                                                if (isset($_REQUEST['item_id'])) {
                                                                                    $k1 = mysqli_query($con, "select * from variant where item_id='$_REQUEST[item_id]'");
                                                                                    if ($k = mysqli_fetch_row($k1)) {
                                                                                        $a = 0;
                                                                                        do {
                                                                                            echo "<input type='hidden' value='$k[0]' name='v_id[]' />";
                                                                                ?>
                                                                                            <tr id="<?php echo $a; ?>">
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <select class="form-control" name="size[]">
                                                                                                            <option value="">--Select--</option>
                                                                                                            <option value="XS" <?php if ($k[2] == 'XS') echo 'selected'; ?>>XS</option>
                                                                                                            <option value="S" <?php if ($k[2] == 'S') echo 'selected'; ?>>S</option>
                                                                                                            <option value="M" <?php if ($k[2] == 'M') echo 'selected'; ?>>M</option>
                                                                                                            <option value="L" <?php if ($k[2] == 'L') echo 'selected'; ?>>L</option>
                                                                                                            <option value="XL" <?php if ($k[2] == 'XL') echo 'selected'; ?>>XL</option>
                                                                                                            <option value="2XL" <?php if ($k[2] == '2XL') echo 'selected'; ?>>2XL</option>
                                                                                                            <option value="3XL" <?php if ($k[2] == '3XL') echo 'selected'; ?>>3XL</option>
                                                                                                            <option value="4XL" <?php if ($k[2] == '4XL') echo 'selected'; ?>>4XL</option>
                                                                                                            <option value="5XL" <?php if ($k[2] == '5XL') echo 'selected'; ?>>5XL</option>
                                                                                                            <option value="6XL" <?php if ($k[2] == '6XL') echo 'selected'; ?>>6XL</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <input type="text" class="form-control"  name="color[]" value="<?php echo $k[3]; ?>" />
                                                                                                        
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <Select class="form-control" name="standard_color[]">
                                                                                                            <option value="">--Select--</option>
                                                                                                            <?php
                                                                                                            $c1=mysqli_query($con,"Select * from color_code order by color_name");
                                                                                                            while($c=mysqli_fetch_assoc($c1))
                                                                                                            {
                                                                                                                if($k[9]==$c['color_name'])
                                                                                                                { ?>
                                                                                                                <option value="<?php echo $c['color_name']; ?>" selected ><?php echo $c['color_name']; ?></option>
                                                                                                                 <?php       
                                                                                                                }
                                                                                                                else
                                                                                                                {
                                                                                                                ?>
                                                                                                                    <option value="<?php echo $c['color_name']; ?>" ><?php echo $c['color_name']; ?></option>
                                                                                                                <?php
                                                                                                                }
                                                                                                            }
                                                                                                            ?>
                                                                                                        </select>
                                                                                                        
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <input type="number" class="form-control" name="stock[]" value="<?php echo $k[6]; ?>" onkeyup="calc();" />
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <input type="number" class="form-control" name="webstock[]" value="<?php echo $k[7]; ?>" onkeyup="calc();" />
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <input type="text" class="form-control" name="purrate[]" value="<?php echo $k[4]; ?>" onkeyup="calc();" />
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <input type="text" class="form-control" name="taxablevalue[]" onkeyup="calc();" />
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="form-group">
                                                                                                        <input type="text" class="form-control" name="tax_amt[]" onkeyup="calc();" />
                                                                                                    </div>
                                                                                                </td>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="tot_pur_value[]" onkeyup="calc();" />
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="tot_pur_rate[]" onkeyup="calc();" />
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="selling_price[]" onkeyup="calc();" />
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control" name="edsellrate[]" onkeyup="calc();" value="<?php echo $k[5]; ?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                    <?php
                                                                                            $a++;
                                                                                        } while ($k = mysqli_fetch_row($k1));
                                                                                    }
                                                                                } else {

                                                    ?>
                                                    <tr id="0">
                                                        <td align="left" valign="middle">
                                                            <select class="form-control" name="size[]">
                                                                <option value="">--Select--</option>
                                                                <option value="XS">XS</option>
                                                                <option value="S">S</option>
                                                                <option value="M">M</option>
                                                                <option value="L">L</option>
                                                                <option value="XL">XL</option>
                                                                <option value="2XL">2XL</option>
                                                                <option value="3XL">3XL</option>
                                                                <option value="4XL">4XL</option>
                                                                <option value="5XL">5XL</option>
                                                                <option value="6XL">6XL</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                 <input type="text" class="form-control"  name="color[]"/>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <Select class="form-control" name="standard_color[]">
                                                                    <option value="">--Select--</option>
                                                                    <?php
                                                                    $c1=mysqli_query($con,"Select * from color_code order by color_name");
                                                                    while($c=mysqli_fetch_assoc($c1))
                                                                    {
                                                                        ?>
                                                                            <option value="<?php echo $c['color_name']; ?>" ><?php echo $c['color_name']; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                                
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" class="form-control" name="stock[]" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" class="form-control" name="webstock[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="purrate[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="taxablevalue[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="tax_amt[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="tot_pur_value[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="tot_pur_rate[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="selling_price[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input type="number" class="form-control" name="edsellrate[]" onkeyup="calc();" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php
                                                                                    echo "<script>tableid++;</script>";
                                                                                }
                                                ?>
                                                        </tbody>
                                                        <tr>
                                                            <td colspan='5' align='right'>Total</td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" name="tot_taxable" onkeyup="calc();" id="tot_taxable" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" name="tot_tax_amt" onkeyup="calc();" id="tot_tax_amt" />
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" name="tot_pur_value" id="tot_pur_value" onkeyup="calc();" />
                                                                </div>
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>

                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button class="btn btn-primary" onClick="more();" type="button">Add More</button>
                                                </td>
                                                </tr>
                                                </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer">
                                    <?php
                                    echo "<script>calc();</script>";
                                    ?>
                                    <button class="btn btn-primary" type="submit" name="s3" tabindex='1'>Modify</button>

                                </div>
                        </div>
                        </form>

                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT WRAPPER -->
        </div>
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

    <!-- START THIS PAGE PLUGINS-->
    <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
    <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
    <script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
    <script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>

    <!-- END THIS PAGE PLUGINS-->

    <!-- START TEMPLATE -->


    <script type="text/javascript" src="js/plugins.js"></script>
    <script type="text/javascript" src="js/actions.js"></script>
    <script src="js/model.js"></script>
    <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->
    <style>
        .dialogify.fixed {
            width: 80%;
        }

        .dialogify .dialogify__fixedwidth {
            max-width: 100%;
        }
    </style>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            $('#add_data').click(function() {
                var options = {
                    ajaxPrefix: ''
                };
                new Dialogify('addnewproduct.php', options)
                    .title('Add New Product')
                    .buttons([{
                            text: 'Cancel',
                            click: function(e) {
                                this.close();
                            }
                        },
                        {
                            text: 'Add',
                            type: Dialogify.BUTTON_PRIMARY,
                            click: function(e) {
                                if ($('#ptype').val() == "Garments") {
                                    var val1 = val2 = val3 = val4 = val5 = val6 = val7 = val8 = val9 = val10 = val11 = "";
                                    var color = document.getElementsByName("color[]");
                                    var size1qty = document.getElementsByName("size1qty[]");
                                    var size2qty = document.getElementsByName("size2qty[]");
                                    var size3qty = document.getElementsByName("size3qty[]");
                                    var size4qty = document.getElementsByName("size4qty[]");
                                    var size5qty = document.getElementsByName("size5qty[]");
                                    var size6qty = document.getElementsByName("size6qty[]");
                                    var size7qty = document.getElementsByName("size7qty[]");
                                    var size8qty = document.getElementsByName("size8qty[]");
                                    var size9qty = document.getElementsByName("size9qty[]");
                                    var size10qty = document.getElementsByName("size10qty[]");
                                    for (i = 0; i < color.length; i++) {
                                        val1 += color[i].value + ";";
                                        val2 += size1qty[i].value + ";";
                                        val3 += size2qty[i].value + ";";
                                        val4 += size3qty[i].value + ";";
                                        val5 += size4qty[i].value + ";";
                                        val6 += size5qty[i].value + ";";
                                        val7 += size6qty[i].value + ";";
                                        val8 += size7qty[i].value + ";";
                                        val9 += size8qty[i].value + ";";
                                        val10 += size9qty[i].value + ";";
                                        val11 += size10qty[i].value + ";";
                                    }
                                    var form_data = new FormData();
                                    form_data.append('ptype', $('#ptype').val());
                                    form_data.append('purdesp', $('#purdesp').val());
                                    form_data.append('saledesp', $('#saledesp').val());
                                    form_data.append('hsn', $('#hsn').val());
                                    form_data.append('color', val1);
                                    form_data.append('size1qty', val2);
                                    form_data.append('size2qty', val3);
                                    form_data.append('size3qty', val4);
                                    form_data.append('size4qty', val5);
                                    form_data.append('size5qty', val6);
                                    form_data.append('size6qty', val7);
                                    form_data.append('size7qty', val8);
                                    form_data.append('size8qty', val9);
                                    form_data.append('size9qty', val10);
                                    form_data.append('size10qty', val11);
                                    form_data.append('npurrate', $('#npurrate').val());
                                    form_data.append('tax', $('#tax').val());
                                    form_data.append('new_sub', $('#new_sub').val());
                                    form_data.append('s_id', $('#s_id').val());
                                } else {
                                    var val1 = val2 = "";
                                    var color = document.getElementsByName("color[]");
                                    var nqty = document.getElementsByName("nqty[]");
                                    for (i = 0; i < color.length; i++) {
                                        val1 += color[i].value + ";";
                                        val2 += nqty[i].value + ";";
                                    }
                                    var form_data = new FormData();
                                    form_data.append('ptype', $('#ptype').val());
                                    form_data.append('purdesp', $('#purdesp').val());
                                    form_data.append('saledesp', $('#saledesp').val());
                                    form_data.append('hsn', $('#hsn').val());
                                    form_data.append('color', val1);
                                    form_data.append('nqty', val2);
                                    form_data.append('npurrate', $('#npurrate').val());
                                    form_data.append('tax', $('#tax').val());
                                    form_data.append('new_sub', $('#new_sub').val());
                                    form_data.append('s_id', $('#s_id').val());
                                }
                                $.ajax({
                                    method: "POST",
                                    url: 'insert_data.php',
                                    data: form_data,
                                    dataType: 'json',
                                    contentType: false,
                                    cache: false,
                                    processData: false,
                                    success: function(data) {
                                        if (data.error != '') {
                                            $('#form_response').html('<div class="alert alert-danger">' + data.error + '</div>');
                                        } else {
                                            //$('#form_response').html('<div class="alert alert-success">'+data.success+'</div>');
                                            var v_id = document.getElementsByName("v_id[]");
                                            var str = data.success;
                                            var lastvalue = (data.last).split(";");
                                            var taxvalue = (data.tax).split(":");
                                            for (i = 0; i < v_id.length; i++) {
                                                var id = v_id[i].id;
                                                $('#' + id).append(str);
                                            }
                                            for (i = 0; i < lastvalue.length; i++) {
                                                last = counter - 1;
                                                newvalues = lastvalue[i].split(":");
                                                $('#v' + last).val(newvalues[0]);
                                                $('#' + last).find("input").eq(0).val(newvalues[1]);
                                                $('#' + last).find("input").eq(2).val(newvalues[2]);
                                                $('#' + last).find("input").eq(5).val(taxvalue[1]);
                                                $('#' + last).find("input").eq(3).val('0');
                                                $('#v' + last).focus();
                                                if (i < (lastvalue.length - 1)) more();

                                            }
                                            calc();
                                        }
                                    }
                                });
                            }
                        }
                    ]).showModal();
            });


        });
    </script>
</body>

</html>