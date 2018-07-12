<?php
require("module/autoload.php");
check_permission(2);

$SESS = &get_session();
$TITLE = "帳號設定";
$PAGE = "account";
ob_start();
require("templates/$PAGE.php");
$BODY = ob_get_clean();

require("templates/layout.php");
