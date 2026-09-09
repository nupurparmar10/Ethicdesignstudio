<?php
	ob_start();
	session_start();
	include_once("connect.php");
    if(isset($_REQUEST['party']))
    {
?>
    <select class="form-control" name="aginvno" id="aginvo" onchange="getitem(this.value);">
        <option value="">Select Invoice No.</option>
        <?php
        $id=$_REQUEST['party'];
        $s=mysqli_query($con,"select * from billbook where party='$id' order by invdate desc");
        while($p=mysqli_fetch_row($s))
        {
            echo "<option value=\"" . $p[0] . "\">" . $p[3] . "</option>";
        }
?>
    </select>
<?php
    }
    else if(isset($_REQUEST['sale_id']))
    {
        $id = $_REQUEST['sale_id'];
        $s = mysqli_query($con,"select * from bill_items where sale_id='$id'");
        $options = "";
        while($b = mysqli_fetch_row($s))
        {
            $f1 = mysqli_query($con,"select * from variant where v_id='$b[1]'");
            if($f = mysqli_fetch_row($f1))
            {
                $c = mysqli_fetch_row(mysqli_query($con,"select * from item_details where item_id='$f[1]'"));
                $options .= "<option value='$f[0]-$f[5]-$c[7]-$f[6]' data-vid='$f[10]'>".htmlspecialchars("$c[1]-$c[5] $f[2] $f[3]")."</option>";
            }
        }
        if($options == "")
        {
            echo "<option value=''>No products found</option>";
        }
        else
        {
            echo $options;
        }
    }
?>