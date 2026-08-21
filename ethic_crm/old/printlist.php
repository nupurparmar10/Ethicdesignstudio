<?php
	ob_start();
	 session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ethic Design Studio</title> 
<link rel="icon" href="logo3.png" type="image/x-icon" />
<script type="text/javascript">
function printlist()
  {
  window.print()
  }
</script>
</head>
<body onload="printlist();">
		<?php echo "$_REQUEST[query]"; ?>
</body>
</html>
