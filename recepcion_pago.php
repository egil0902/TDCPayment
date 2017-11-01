<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Documento sin título</title>
</head>

<body>
	<?php 
	$response = "response=" .$_GET['response']."|responsetext=".$_GET['responsetext']."|authcode=".$_GET['authcode']."|transactionid=".$_GET['transactionid']."|hash=".$_GET['hash']."|avsresponse=".$_GET['avsresponse']."|cvvresponse=".$_GET['cvvresponse'].$_GET['cvvresponse']."|orderid=".$_GET['orderid']."|type=".$_GET['type']."|response_code=".$_GET['response_code']."|username=".$_GET['username']."|time=".$_GET['time']."|amount=".$_GET['amount'];

	$url_action = 'http://144.217.34.60/response_payment.php';
	?>
	<form id="responsePayment" name="responsePayment" action="<?= $url_action ?>" method="POST">
		<input type="hidden" name="response" id="response" value="<?= $response ?>">
	</form>

	<script type="text/javascript">
		var r= "<?php echo $response ?>";
		parent.document.getElementById("response").value=r;
		document.forms["responsePayment"].submit();
        //window.close();
    </script>
</body>
</html>

