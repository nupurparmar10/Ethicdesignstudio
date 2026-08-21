<?php
    ob_start();
    session_start();
    include_once("connect.php");
if(isset($_REQUEST['ptype']))
{
    $prod_type=mysqli_fetch_row(mysqli_query($con,"select pt_id from producttype where ptname='$_REQUEST[ptype]'"));
?>
<div class="form-group">
    <select class="form-control"  name="s_id<?php echo $_REQUEST['id']; ?>"  id="s_id<?php echo $_REQUEST['id']; ?>" >
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
echo ";;$_REQUEST[id]";
}
?>