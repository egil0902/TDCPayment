<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Documento sin título</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style type="text/css" media="screen">
	.loader {
		border: 16px solid #f3f3f3; /* Light grey */
		border-top: 16px solid #CC1500; /* Blue #3498db*/
		border-radius: 50%;
		width: 100px;
		height: 100px;
		animation: spin 2s linear infinite;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
</style>
</head>

<body>

	<?php

	$OrderId=$_REQUEST['orderid'];
//echo "Order:  " .$OrderId." <br>";
	$Amount = $_REQUEST['amount'];
//echo "Monto:  " .$Amount." <br>";
	$time = time();
//echo "Fecha Hora:  " .$time." <br>";
	$key = 'YdV27NXEB4TzCjK79GPTVf7Y4S2b3RtN';
	$key_id = '4896565'; //echo "El Key:  ".$key_id." <br>";
//echo ($OrderId."|".$Amount."|".$time."|".$key)."<br>";

	$hash=md5($OrderId."|".$Amount."|".$time."|".$key);

//echo "Encriptado de la info " .$hash."<br>"; 
//xNU4M4U9kxYVv86hhUb2De5p3736CzSU
//$_csrf = 'YdV27NXEB4TzCjK79GPTVf7Y4S2b3RtN'; echo $_csrf;
//$tc = $_POST['tc']; //echo "Tipo Tarjeta    ".$tc." <br>";
$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';

$ccnumber = $_REQUEST['ccnumber']; //echo "Numero    ".$ccnumber." <br>";
$ccexp = $_REQUEST['ccexp']; //echo "Fecha Exp:    ".$ccexp." <br>";
$checkname = $_REQUEST['checkname']; //echo "Nombre:    ".$checkname." <br>";
$firstname = $_REQUEST['firstname'];
$lastname = $_REQUEST['lastname'];
$cvv = $_REQUEST['cvv']; //echo "Pin:    ".$cvv." <br>";
$phone = $_REQUEST['phone']; //echo "Telefono:    ".$phone." <br>";
$address = $_REQUEST['address1']; //echo "Direccion:    ".$address." <br>";
$ipaddress = $_SERVER['REMOTE_ADDR'];
$type = $_REQUEST['type'];
$transactionid = $_REQUEST['transactionid'];
$processor_id = $_REQUEST['processor_id'];
$action2 = "https://firstlook.transactiongateway.com/api/transact.php";
$action3 = "https://paycom.credomatic.com:8443/PayComBackEndWeb/common/requestPaycomService.go";
$action = "https://credomatic.compassmerchantsolutions.com/api/transact.php";
?>
<!--<div class='progress'>
	<div class='progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100' style='width: 100%'></div>
</div>-->
<div style='width: 100%' align="center">
	<div class="loader"></div>
</div>
<form id="pago.php" name="metodo_pago"  action="<?php echo $action;?>" method="POST">
	<input type="hidden" name="Key" value="<?php echo $key;?>">
	<input type="hidden" name="type" value="<?php echo $type;?>">
	<input type="hidden" name="orderid" value="<?php echo $OrderId;?>">
	<input type="hidden" name="amount" value="<?php echo $Amount;?>">
	<input type="hidden" name="key_id" value="<?php echo $key_id;?>">
	<input type="hidden" name="hash" value="<?php echo $hash;?>">
	<input type="hidden" name="time" value="<?php echo $time;?>">
	<input type="hidden" name="redirect" value="<?php echo $protocol.$_SERVER['SERVER_NAME']."/recepcion_pago.php";?>">
	<input type="hidden" name="ccnumber" value="<?php echo $ccnumber;?>">
	<input type="hidden" name="ccexp" value="<?php echo $ccexp;?>">
	<input type="hidden" name="checkname" value="<?php echo $checkname;?>">
	<input type="hidden" name="firstname" value="<?php echo $firstname;?>">
	<input type="hidden" name="lastname" value="<?php echo $lastname;?>">
	<input type="hidden" name="cvv" value="<?php echo $cvv;?>">
	<input type="hidden" name="phone" value="<?php echo $phone;?>">
	<input type="hidden" name="address1" value="<?php echo $address;?>">
	<input type="hidden" name="ipaddress" value="<?php echo $ipaddress;?>">
	<input type="hidden" name="processor_id" value="<?php echo $processor_id;?>">
	<?php if($type!='auth' && $type!='sale' && $type!='credit'){?>
	<input type="hidden" name="Transactionid" value="<?php echo $transactionid;?>">
	<?php }?>
</form>
<script type="text/javascript">
	document.forms["metodo_pago"].submit();
</script> 

</body>
</html>
<!-- $_POST['transactionid']; -->
