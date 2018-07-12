<?php
require("module/autoload.php");
check_permission(4);

$SESS = &get_session();
$TITLE = "管理";
$PAGE = "control";
ob_start();
require("templates/$PAGE.php");
$BODY = ob_get_clean();

require("templates/layout.php");
