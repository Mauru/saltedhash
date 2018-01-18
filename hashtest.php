<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>hash-test PHP vs. JS</title>
  <script type="text/javascript" src="sha256hash.js"></script>
</head>
<body>

<?php
$HASH_ROUNDS= 350;

function boldSHA256($h, $c) {
	for($i=0;$i<$c;$i++) $h= hash("sha256", $h, false);
	return $h;
}

$hash= isset($_POST['hash']) ? $_POST['hash'] : "";
if ($hash!="") {
	echo "PHP: ".boldSHA256($hash, $GLOBALS["HASH_ROUNDS"]);
	?>
	<br/>
	<script type="text/javascript">
	document.write("JS: &nbsp;&nbsp;&nbsp; "+boldSHA256("<?=$hash?>", <?=$HASH_ROUNDS?>));
	</script>
	<?php
}
?>
<br/><br/>

<form name="theform" action="hashtest.php" method="POST">
entry to hash: <br/>
<input type="text" size="15" name="hash" id="hash">
<br/><br/>
<input type="submit" name="submit" value="hash it!">
</form>

</body>
</html>
