<?php
require("module/autoload.php");
check_permission(2);

$SESS = &get_session();
$TITLE = "領取耗材";
$PAGE = "order";
ob_start();
require("templates/$PAGE.php");
$BODY = ob_get_clean();

require("templates/layout.php");
