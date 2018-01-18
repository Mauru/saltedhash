<?php
session_start();

function getDigest($user) {
	// hash hard-coded for demo purposes (350 rounds), comes from a db in reality :)
	return (strtolower($user)=="hello@world.org") ? "3ccc2a95f0b794cd070f196dde7833c919cb080ceed35cfffab89ddb564004e9" : "";		// TeSt123passWort
}

$REPLY_SALT= 	"S";
$REPLY_OK= 		"O";
$REPLY_ERROR= 	"E";
$REPLY_DATA= 	"D";
$REPLY_NOCLUE=	"?";

$HASH_ROUNDS= 350;

// return given string only if it doesn't contain other characters as specified in "pattern" (=white list)
function sanitize($val, $pattern) {
	return (preg_match($pattern, $val) ? $val : "");
}

function boldSHA256($h, $c) {
	for ($i=0;$i<$c;$i++) $h= hash("sha256", $h, false);
	return $h;
}

function genSalt() {
  $salt= "";
  srand((double)microtime()*1000000);
  $rand64= "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz_.-:,;*#!?()[]{}@~%><";
  $lauf= rand(50,80);
  for ($i=0;$i<$lauf;$i++) {
  	$random= rand();
  	$salt.= substr($rand64,$random%83,1);
  }
  return $salt;
}


$getc= isset($_GET['c']) ? $_GET['c'] : "";
$getu= isset($_GET['u']) ? $_GET['u'] : "";
$getd= isset($_GET['d']) ? $_GET['d'] : "";

$cmd= sanitize($getc, "/[A-Za-z0-9]/");
$user= sanitize($getu, "/[A-Za-z0-9_@.]/");
$clientDigest= sanitize($getd, "/[A-Za-z0-9]/");

switch ($cmd) {
	case "init":
		$_SESSION["salt"]= genSalt();
		$_SESSION["LOGIN"]= "";
		echo $REPLY_SALT."|".$_SESSION["salt"];
	break;
	case "login":
		$auth= false;
		$myDigest= boldSHA256(getDigest($user).$_SESSION["salt"], $GLOBALS["HASH_ROUNDS"]);
		$_SESSION["salt"]= "";
		if ($myDigest==$clientDigest) $auth= true;
		if ($auth) {
			echo $REPLY_OK."|"."OK";
			$_SESSION["LOGIN"]= true;
		} else {
			echo $REPLY_ERROR."|"."ERROR!";
			$_SESSION["LOGIN"]= "";
		}
	break;
	case "data":
	if ($_SESSION["LOGIN"]) {
		echo $REPLY_DATA."|"."data coming...";
	} else {
		echo $REPLY_ERROR."|"."access denied!";
	}
	break;
	default:
		echo $REPLY_NOCLUE."|"."say what?";
	break;
}

?>
