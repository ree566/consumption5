<?php
require_once(__DIR__ . '/../module/autoload.php');

$rows = get_rows("SELECT * FROM users");

foreach ($rows as $row) {
	$id = $row["id"];
	$hash = pw_hash($id);
	Q("UPDATE users SET pass_hash = '$hash' WHERE id = '$id'");
}

echo "Success";
