<?php
	ob_start();
	session_start();
	include_once("connect.php");
	
	session_unset();
	$msg="";
	if(isset($_REQUEST['msg']))
	{
		$msg=$_REQUEST['msg'];
	}
	if(isset($_REQUEST['s1']))
	{
		$l1=mysqli_query($con,"select acc_id from login where uname='".md5($_REQUEST['uname'])."' and password='".md5($_REQUEST['pwd'])."'");
		if($l=mysqli_fetch_row($l1))
		{
			$_SESSION['account']=$l[0];
			$_SESSION['Ethic']="studio";
			header("Location: dashboard.php"); die;
		}
		else
		{
			$msg="Invalid Username or Password!!!";
		}
	}
?>
<!DOCTYPE html>
<html lang="en" class="body-full-height">
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
        <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
        <!-- EOF CSS INCLUDE -->                                     
    </head>
    <body>
        
        <div class="login-container lightmode">
        
            <div class="login-box animated fadeInDown">
                <div class="login-logo"></div>
                <div class="login-body">
                   <div class="login-title"><strong>Welcome</strong>, Please login
					<?php
						if($msg)
						{
							echo "<br></br><span style='color:#FF0000; font-weight:bold;'>$msg</span>";
						}
					?></div>
                    <form action="index.php" class="form-horizontal" method="post">
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="text" class="form-control" placeholder="Username" name="uname" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="password" class="form-control" placeholder="Password" name="pwd" required/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <button class="btn btn-info btn-block" name="s1">Log In</button>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="login-footer">
                    <div align="center" style="font-size:14px;">
                       Developed By <a href="http://www.technoknitters.com" target="_blank"><strong>Technoknitters</strong></a>
                    </div>
                </div>
            </div>
            
        </div>
    
    </body>

</html>
