<?php
require("module/autoload.php");
check_permission(3, 4);

$SESS = &get_session();
$TITLE = "管理儲存庫";
$PAGE = "repository";
ob_start();
require("templates/$PAGE.php");
$BODY = ob_get_clean();

require("templates/layout.php");
