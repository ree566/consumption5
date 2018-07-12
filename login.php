<?php
require("module/autoload.php");
// check_permission(0);

$SESS = &get_session();
if ($SESS) {
	if ($SESS["permission"] == 2) {
		header("Location: order.php");
	} else if ($SESS["permission"] == 3) {
		header("Location: repository.php");
	} else if ($SESS["permission"] >= 4) {
		header("Location: control.php");
	} else {
		header("Location: report.php");
	}
	die;
}

if(isset($_POST["uid"], $_POST["pwd"])){
	$user_id = escape($_POST["uid"]);
	$pass_hash = pw_hash($_POST["pwd"]);
	$row = login($user_id, $pass_hash);
	
	if ($row) {
		// Login success
		$_SESSION[NS] = $row;
		$_SESSION[NS]["uid"] = $row["id"];
		
		header("Location: login.php");
		die;
	} else {
		$LOGIN_FAILED = true;
	}
}

$TITLE = "登入";
$PAGE = "login";
ob_start();
require("templates/login.php");
$BODY = ob_get_clean();

require("templates/layout.php");
