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
    $hsn1=mysqli_fetch_row(mysqli_query($con,"select hsn from producttype where ptname = '$_POST[ptype]'"));
    $hsn=$hsn1[0];
    if($_POST['ptype']=="Fabric") $unit="MTR"; else $unit="PCS";

    $npurrate=0;
    $edrate=$_POST['edrate'];
    if($_POST['ptype']=="Garments")
    {
        $color=explode(";",$_POST['color']);
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
            if($c!='')
            {
                $b=$npurrate;

                $a=$size1qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='XS', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='XS' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size2qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='S', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='S' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size3qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='M', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='M' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size4qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='L', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='L' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size5qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size6qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='2XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='2XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size7qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='3XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='3XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size8qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='4XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='4XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size9qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='5XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='5XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
                $a=$size10qty[$i];
                if((int)$a>0)
                {
                    mysqli_query($con,"insert into variant set item_id =$itemid, size='6XL', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                    $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='6XL' and color='$c'"));
                    $barcode = encryptId($f[0]);
                    mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                    $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                    $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
                }
            }
        }
    }
    else
    {
        $color=explode(";",$_POST['color']);
        $nqty=explode(";",$_POST['nqty']);
        $npurrate=0;
        $edrate=$_POST['edrate'];
        $count = count($color);
        for($i=0;$i<$count-1;$i++)
        {
            $c = $color[$i];
            if($c!='')
            {
                $a=$nqty[$i];
                $b=$npurrate;
                mysqli_query($con,"insert into variant set item_id =$itemid, size='', color='$c',stock='0',webstock='0',purrate='$b',edsellrate='$edrate'");
                $f=mysqli_fetch_row(mysqli_query($con,"select * from variant where item_id='$itemid' and size='' and color='$c'"));
                $barcode = encryptId($f[0]);
                mysqli_query($con, "UPDATE variant SET barcode='$barcode' WHERE v_id='$f[0]'");
                $newrecord.="<option value='$f[0]-$f[4]-$x[1]-$f[5]'>$pcode - $_POST[saledesp] $f[2] $f[3]</option>";
                $last.="$f[0]-$f[4]-$x[1]-$f[5]:$a:$edrate;";
            }
        }
    }
    if($last!='')
    {
            $material_id = $_POST['material_id'];
            $new_material = trim($_POST['new_material_type']);

            if ($new_material !== '') {
                $safe_new_material = mysqli_real_escape_string($con, $new_material);
                $check_material = mysqli_query($con, "SELECT m_id FROM material_type WHERE LOWER(type) = LOWER('$safe_new_material') AND status = 1 LIMIT 1");

                if (mysqli_num_rows($check_material) > 0) {
                    echo json_encode(['status' => 'error', 'message' => 'This Entity value already exists']);
                    exit;
                } else {
                    mysqli_query($con, "INSERT INTO material_type (type, status) VALUES ('$safe_new_material', 1)");
                    $material_id = mysqli_insert_id($con);

                    $collection_id = $_POST['collection_id'];
                    $new_collection = trim($_POST['new_collection_name']);

                    if ($new_collection !== '') {
                        $safe_new_collection = mysqli_real_escape_string($con, $new_collection);

                        $check_collection = mysqli_query($con, "SELECT c_id FROM collection WHERE LOWER(name) = LOWER('$safe_new_collection') AND status = 1 LIMIT 1");

                        if (mysqli_num_rows($check_collection) > 0) {
                            echo json_encode(['status' => 'error', 'message' => 'This Entity value already exists']);
                            exit;
                        } else {
                            mysqli_query($con, "INSERT INTO collection (name, status) VALUES ('$safe_new_collection', 1)");
                            $collection_id = mysqli_insert_id($con);
                        }
                    }
                }
            }

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

       
        mysqli_query($con,"insert into item_details set item_id='$itemid', pcode='$pcode',ptype='$_POST[ptype]',purdesp='',hsn='$hsn',saledesp='$_POST[saledesp]',unit='$unit',tax='$x[0]',website='0',status='1',s_id='$s_id',material_type = '$material_id',
        collection = '$collection_id'");
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