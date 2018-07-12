<?php
require_once(__DIR__ . '/../module/autoload.php');

$sql = file_get_contents("schema.sql");
con()->set_charset("utf8");
if (con()->multi_query($sql)) {
	while (con()->next_result()) {;}
	if (con()->errno) {
		echo "Error: " . con()->error;
	} else {
		echo "Success";
	}
} else {
	echo "Error: " . con()->error;
}
