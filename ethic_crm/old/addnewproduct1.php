<?php
include_once("connect.php");
?>
<script>
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

    function more5() {
        var $table = $('#input_fields5');
        var $tr = $table.find('tr').eq(1).clone();
        $tr.appendTo($table).find('input').val('');
        $tr.appendTo($table).find('select').val('');
        $("#input_fields5").append($tr);
        $tr.appendTo($table).find('input').eq(0).focus();
    }

    function calc2() {
        var tot = 0;
        var ptype = document.getElementById("ptype").value;
        if (ptype == 'Garments') {
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
            for (i = 0; i < size1qty.length; i++) {
                tot += size1qty[i].value * 1;
                tot += size2qty[i].value * 1;
                tot += size3qty[i].value * 1;
                tot += size4qty[i].value * 1;
                tot += size5qty[i].value * 1;
                tot += size6qty[i].value * 1;
                tot += size7qty[i].value * 1;
                tot += size8qty[i].value * 1;
                tot += size9qty[i].value * 1;
                tot += size10qty[i].value * 1;
            }
        } else {
            var nqty = document.getElementsByName("nqty[]");
            for (i = 0; i < nqty.length; i++) {
                tot += nqty[i].value * 1;
            }
        }
        document.getElementById("nqtytot").value = tot;
    }

    function gettable(ptype) {
        if (ptype == 'Garments') {
            var str = "<thead><tr><th>Color</th><th>XS</th><th>S</th><th>M</th><th>L</th><th>XL</th><th>2XL</th><th>3XL</th><th>4XL</th><th>5XL</th><th>6XL</th></tr></thead><tbody><tr><td><div class='form-group'><input class='form-control' name='color[]' type='text'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size1qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size2qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size3qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size4qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size5qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size6qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size7qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size8qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size9qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size10qty[]' onkeyup='calc2();'/></div></td></tr></tbody>";
        } else {
            var str = "<thead><tr><th>Color</th><th>Qty</th></tr></thead><tbody><tr><td><div class='form-group'><input class='form-control' name='color[]' type='text'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='nqty[]' onkeyup='calc2();'/></div></td></tr></tbody>";
        }
        $("#input_fields5").html(str);
    }
</script>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-actions">
                <tbody>
                    <tr>
                        <th width="15%">Product Type</th>
                        <td>
                            <div class="form-group">
                                <select class="form-control" name="ptype" required id="ptype" onchange='gettable(this.value); getsubcategory(this.value);'>
                                    <option value=''>--Select--</option>
                                    <?php
                                    $f1 = mysqli_query($con, "select * from producttype");
                                    while ($f = mysqli_fetch_row($f1)) {
                                        echo "<option value='$f[1]'>$f[1]</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>
                        <th width="10%">Tax</th>
                        <td>
                            <div class="form-group">
                                <select class="form-control" name="tax" required id="tax" onchange="calc();">
                                    <option value=''>--Select--</option>
                                    <?php
                                    $f1 = mysqli_query($con, "select * from taxes where status='1'");
                                    while ($f = mysqli_fetch_row($f1)) {
                                        if ($f[2] == '5') {
                                            echo "<option value='$f[0]:$f[2]' selected>$f[1]</option>";
                                        } else {
                                            echo "<option value='$f[0]:$f[2]'>$f[1]</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width="15%">Product Sub-category</th>
                        <td id="subcategory">
                            <div class="form-group">
                                <select class="form-control" name="s_id" id="s_id">
                                    <option value=''>--Select--</option>
                                </select>
                            </div>
                        </td>
                        <th width="10%">Or Add New Sub-category</th>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" name="new_sub" id="new_sub" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width="10%">Sales Description</th>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" name="saledesp" id="saledesp" />
                            </div>
                        </td>
                        <th width="10%">Ethic Selling Price</th>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" name="edrate" id="edrate" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width="15%">Material type</th>
                        <td>

                            <?php
                            $material = "SELECT m_id, type FROM material_type WHERE status = 1 ORDER BY type ASC";
                            $material_res = mysqli_query($con, $material);
                            ?>
                            <div class="form-group">
                                <select class="form-control" name="material_id" id="material_id">
                                    <option value="">--Select--</option>
                                    <?php
                                    if ($material_res && mysqli_num_rows($material_res) > 0) {
                                        while ($row = mysqli_fetch_assoc($material_res)) {
                                            echo "<option value='{$row['m_id']}'>" . htmlspecialchars($row['type']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                        </td>
                        <th width="10%">Or Add New Material Type</th>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" name="new_material_type" id="new_material_type" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th width="15%">Collection type</th>
                        <td>

                            <?php
                            $collection = "SELECT c_id, name FROM collection WHERE status = 1 ORDER BY name ASC";
                            $collection_res = mysqli_query($con, $collection);
                            ?>
                            <div class="form-group">
                                <select class="form-control" name="collection_id" id="collection_id">
                                    <option value="">--Select--</option> 
                                    <?php
                                    if ($collection_res && mysqli_num_rows($collection_res) > 0) {
                                        while ($row = mysqli_fetch_assoc($collection_res)) {
                                            echo "<option value='{$row['c_id']}'>" . htmlspecialchars($row['name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </td>
                        <th width="10%">Or Add New Collection</th>
                        <td>
                            <div class="form-group">
                                <input type="text" class="form-control" name="new_collection_name" id="new_collection_name" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='4' style="max-width:300px; overflow-x:auto;">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-actions" id="input_fields5">

                                </table>
                            </div>
                            <button class="btn btn-primary" onClick="more5();" type="button">Add More</button>
                        </td>
                    </tr>

                </tbody>
                <tr>
                    <th width="10%">Total Qty</th>
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control" name="nqtytot" id="nqtytot" onkeyup="calc2();" />
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>