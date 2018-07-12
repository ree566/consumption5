<?php

function dbc(){
	$mysqli = new mysqli(DB_SERV, DB_USER, DB_PASS, DB_NAME);
	if ($mysqli->connect_errno){
		// connect failed
		return false;
	}
	
	if(!$mysqli->set_charset("utf8")){
		// set charset failed
		return false;
	}
	
	return $mysqli;
}

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include($filename);
        return ob_get_clean();
    }
    return false;
}

function error_dump($v=null){
	ob_start();
	var_dump($v);
	$s = ob_get_clean();
	error_log($s);
}

function pw_hash($s){
	return md5(PW_SALT . $s);
}

function json_en($v){
	return json_encode($v, JSON_UNESCAPED_UNICODE);
}
