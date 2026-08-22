<?php
include_once('connect.php');

if(isset($_POST["ptype"]))
{
 $error = '';
 $success = '';
 $last='';
 $tax='';
 if($error == '')
 {
    $x=explode(":",$_POST['tax']);
    $pid1=mysqli_query($con,"select max(item_id) from item_details");
    $pid=mysqli_fetch_row($pid1);
    $itemid=$pid[0]+1;       
    $pcode="ED";
    if($itemid<10) $pcode.="0".$itemid; else $pcode.=$itemid;
    $newrecord="";
    $last='';

    if($_POST['ptype']=="Fabric") $unit="MTR"; else $unit="PCS";

    $hsn1=mysqli_fetch_row(mysqli_query($con,"select hsn from producttype where ptname = '$_POST[ptype]'"));
    $hsn=$hsn1[0];
    $npurrate=$_POST['npurrate'];
    if($_POST['ptype']=="Garments")
    {
        $color=explode(";",$_POST['color']);
        $standard_color = explode(";", $_POST['standard_color']);
        $size1qty=explode(";",$_POST['size1qty']);
        $size2qty=explode(";",$_POST['size2qty']);
        $size3qty=explode(";",$_POST['size3qty']);
        $size4qty=explode(";",$_POST['size4qty']);
        $size5qty=explode(";",$_POST['size5qty']);
        $size6qty=explode(";",$_POST['size6qty']);
        $size7qty=explode(";",$_POST['size7qty']);
        $size8qty=explode(";",$_POST['size8qty']);
        $size9qty=explode(";",$_POST['size9qty']);
        $size10qty=explode(";",$_POST['size10qty']);
        $count = count($color);
        for($i=0;$i<$count-1;$i++)
        {
            $c = $color[$i];
            $sc = $standard_color[$i];
            if($c!='')
            {
                $b=$npurrate;

                $a=$size1qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='XS', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='XS' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size2qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='S', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='S' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size3qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='M', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='M' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size4qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='L', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='L' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size5qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size6qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='2XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='2XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size7qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='3XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='3XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size8qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='4XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='4XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size9qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='5XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='5XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
                $a=$size10qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='6XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='6XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
                }
            }
        }
    }
    else
    {
        $color=explode(";",$_POST['color']);
        $standard_color = explode(";", $_POST['standard_color']);
        $nqty=explode(";",$_POST['nqty']);
        $npurrate=$_POST['npurrate'];
        $count = count($color);
        for($i=0;$i<$count-1;$i++)
        {
            $c = $color[$i];
            $sc = $standard_color[$i];
            if($c!='')
            {
                $a=$nqty[$i];
                $b=$npurrate;
                mysqli_query($con,"insert into variant set item_id =$itemid, size='', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='0',standard_color='$sc'");
                $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='' and color='$c'"));
                $barcode = encryptId($f[0]);
                mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$b;";
            }
        }
    }
    if($last!='')
    {
        if($_POST['new_sub']!='')
        {
            $chk1=mysqli_query($con,"select max(s_id) from pro_subcategory");
            $chk=mysqli_fetch_row($chk1);
            $chk[0]++;
            $prod_type=mysqli_fetch_row(mysqli_query($con,"select pt_id from producttype where ptname='$_POST[ptype]'"));
            mysqli_query($con,"insert into pro_subcategory set s_id='$chk[0]',pt_id='$prod_type[0]',sname='$_POST[new_sub]'");
            $s_id=$chk[0];
        }
        else
        {
            $s_id=$_POST['s_id'];
        }       
        mysqli_query($con,"insert into item_details set item_id='$itemid', pcode='$pcode',ptype='$_POST[ptype]',purdesp='$_POST[purdesp]',hsn='$hsn',saledesp='$_POST[saledesp]',unit='$unit',tax='$x[0]',website='1',status='1',s_id='$s_id',product_desp='$_POST[product_desp]'");
        $tax=$_POST['tax'];
    }
  $success = $newrecord;
 }
 $output = array(
  'success'  => $success,
  'error'   => $error,
  'last' => $last,
  'tax' => $tax
 );
 echo json_encode($output);
}

if(isset($_REQUEST['subcategory']))
{
    $prod_type=mysqli_fetch_row(mysqli_query($con,"select pt_id from producttype where ptname='$_REQUEST[subcategory]'"));
?>
<div class="form-group">
    <select class="form-control"  name="s_id"  id="s_id" >
        <option value=''>--Select--</option>
        <?php
            $f1=mysqli_query($con,"select * from pro_subcategory where pt_id='$prod_type[0]' order by sname");
            while($f=mysqli_fetch_row($f1))
            {
                echo "<option value='$f[0]'>$f[2]</option>";
            }
        ?>	
    </select>
</div>
<?php
}
?>