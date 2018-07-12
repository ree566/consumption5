<?php
require("module/autoload.php");
check_permission(5);

$SESS = &get_session();
$TITLE = "樓層總覽";
$PAGE = "floor";
ob_start();
require("templates/$PAGE.php");
$BODY = ob_get_clean();

require("templates/layout.php");
