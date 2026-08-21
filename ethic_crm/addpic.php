<?php
    include_once("connect.php");
?>
<script>
     function more5()
            {     
                var $table = $('#input_fields5');
                var $tr = $table.find('tr').eq(1).clone();
                $tr.appendTo($table).find('input').val('');
                $tr.appendTo($table).find('select').val('');
                $("#input_fields5").append($tr);
                $tr.appendTo($table).find('select').eq(0).focus();                
            }
    function calc2()
    {
        var tot=0;
        var ptype=document.getElementById("ptype").value;
        if(ptype=='Garments')
        {
            var size1qty=document.getElementsByName("size1qty[]");
			var size2qty=document.getElementsByName("size2qty[]");
			var size3qty=document.getElementsByName("size3qty[]");
			var size4qty=document.getElementsByName("size4qty[]");
			var size5qty=document.getElementsByName("size5qty[]");
			var size6qty=document.getElementsByName("size6qty[]");
			var size7qty=document.getElementsByName("size7qty[]");
			var size8qty=document.getElementsByName("size8qty[]");
			var size9qty=document.getElementsByName("size9qty[]");
			var size10qty=document.getElementsByName("size10qty[]");
            for(i=0;i<size1qty.length;i++)
            {
                tot += size1qty[i].value*1;
                tot += size2qty[i].value*1;
                tot += size3qty[i].value*1;
                tot += size4qty[i].value*1;
                tot += size5qty[i].value*1;
                tot += size6qty[i].value*1;
                tot += size7qty[i].value*1;
                tot += size8qty[i].value*1;
                tot += size9qty[i].value*1;
                tot += size10qty[i].value*1;
            }
        }
        else
        {
            var nqty = document.getElementsByName("nqty[]");
            for(i=0;i<nqty.length;i++)
            {
                tot += nqty[i].value*1;
            }
        }
        document.getElementById("nqtytot").value=tot;
    }
    function gettable(ptype)
    {
        if(ptype=='Garments')
        {
            var str="<thead><tr><th>Color</th><th>XS</th><th>S</th><th>M</th><th>L</th><th>XL</th><th>2XL</th><th>3XL</th><th>4XL</th><th>5XL</th><th>6XL</th></tr></thead><tbody><tr><td><div class='form-group'><input class='form-control' name='color[]' type='text'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size1qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size2qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size3qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size4qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size5qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size6qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size7qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size8qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size9qty[]' onkeyup='calc2();'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='size10qty[]' onkeyup='calc2();'/></div></td></tr></tbody>";
        }
        else
        {
            var str="<thead><tr><th>Color</th><th>Qty</th></tr></thead><tbody><tr><td><div class='form-group'><input class='form-control' name='color[]' type='text'/></div></td><td><div class='form-group'><input type='text' class='form-control' name='nqty[]' onkeyup='calc2();'/></div></td></tr></tbody>";
        }
        $("#input_fields5").html(str);
    }
</script>
<?php
    $v_id=$_REQUEST['v_id'];
    $v=mysqli_fetch_row(mysqli_query($con,"select * from variant where v_id='$v_id'"));
    $item=mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$v[1]'"));
?>
<div class="row" >
    <div class="col-md-12">
        <input type='hidden' name='v_id' value='<?php echo $v_id; ?>' id="v_id"/>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-actions">
            <tbody>  
                <tr>
                    
                    <th width="15%">Product Code</th>
                    <td><?php echo $item[1]; ?></td>
                    <th width="10%">HSN Code</th>
                    <td><?php echo $item[4]; ?></td>
                </tr>
                <tr>
                    <th width="10%">Purchase Description</th>
                    <td><?php echo $item[3]; ?></td>
                    <th width="10%">Sales Description</th>
                    <td><?php echo $item[5]; ?></td>
                </tr>
                <tr>
                    <th>Upload Photos</th>
                    <th colspan='3'><div class='form-group'><input type='file' class='form-control' name='pic[]' id="pic" multiple/></div></th>
                </tr>
                <?php
                    $pic1=mysqli_query($con,"select * from variant_pic where v_id='$v_id'");
                    if($pic=mysqli_fetch_row($pic1))
                    {
                ?>
                <tr>
                    <td colspan='4' style="max-width:300px; overflow-x:auto;">
                        <div class="table-responsive">
                        <table class="table table-bordered table-striped table-actions" >
                            <thead>
                                <th>Pic</th>
                                <th>Remove</th>
                            </thead>
                            <tbody>
                                <?php
                                    do{
                                ?>
                                <tr>
                                    <td><img src='<?php echo $pic[2]; ?>' height='100px' width='100px'/></td>
                                    <td><div class='form-group'><input type='checkbox' class='checkbox' name='remove[]' value="<?php echo $pic[0]; ?>"/></div></td>
                                </tr>
                                <?php
                                    }while($pic=mysqli_fetch_row($pic1));
                                ?>
                            </tbody>
                        </table>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>

    </div>
    </div>