<?php
include_once('connect.php');

if(isset($_POST["lname"]))
{
 $error = '';
 $success = '';
 $last='';
 $name=$_POST['lname'];
 $chk1=mysqli_query($con,"select * from ledger_accounts where name='$name'");
if($chk=mysqli_fetch_row($chk1))
{
    $error="Ledger Name Already Exists!!!";
}
else
{
    $person=$_POST['cperson'];
    $tinno=strtoupper($_REQUEST['tinno']);
    $mobile=$_POST['mobile'];
    $email=$_POST['email'];
    $opbal=0;
    
    $address=$_POST['address'];
    $g_id=mysqli_query($con,"select max(ledger_id) from ledger_accounts");
    $g=mysqli_fetch_row($g_id);
    $id=$g[0]+1;
    
    mysqli_query($con,"insert into ledger_accounts set ledger_id='$id', name='$name', group_id='26', opening_bal='$opbal'");
    
    if($person!=""  || $address!=""  || $tinno!=""  || $mobile!="" || $email!="")
    {
        mysqli_query($con,"insert into ledger_details set ledger_id=$id, contact_person='$person',  address='$address', tinno='$tinno', mobile='$mobile',  email='$email'");
    }
    $newrecord="<option value='$id'>$name</option>";
  $success = $newrecord;
  $last = $id;
 }
 $output = array(
  'success'  => $success,
  'error'   => $error,
  'last' => $last
 );
 echo json_encode($output);
}

?>