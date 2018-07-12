<?php
function check_permission($p, $q = null) {
	if (!$p) {
		return;
	}
	
	$s = &get_session();
	if (!$s) {
		header("Location: login.php");
		die;
	}
	
	if ($s["permission"] < $p) {
		header("Location: logout.php");
		die;
	}
	
	if ($q && $s["permission"] > $q) {
		header("Location: logout.php");
		die;
	}
}

function &get_session() {
	// wat the hack?
	static $session_id = null;
	static $null_guard = null;
	
	if (!$session_id) {
		$session_id = session_start();
	}
	
	if (isset($_SESSION[NS])) {
		return $_SESSION[NS];
	}
	
	return $null_guard;
}

function clean_session() {
	$s = &get_session();
	$s = null;
}


