<?php
require("module/autoload.php");
check_permission(1);

$SESS = &get_session();
$TITLE = "報表";
$PAGE = "report";
ob_start();
require("templates/$PAGE.php");
$BODY = ob_get_clean();

require("templates/layout.php");
