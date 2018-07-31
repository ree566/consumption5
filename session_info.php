<?php
/**
 * Created by PhpStorm.
 * User: Wei.Cheng
 * Date: 2018/7/26
 * Time: 下午 03:53
 */
session_start();

require "module/autoload.php";

echo '<pre>' . print_r($_SESSION, TRUE) . '</pre>';

$base_dir = __DIR__; // Absolute path to your installation, ex: /var/www/mywebsite
$doc_root = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']); # ex: /var/www
$base_url = preg_replace("!^${doc_root}!", '', $base_dir); # ex: '' or '/mywebsite'
$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
$port = $_SERVER['SERVER_PORT'];
$disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";
$domain = $_SERVER['SERVER_NAME'];
$full_url = "${protocol}://${domain}${disp_port}${base_url}"; # Ex: 'http://example.com', 'https://example.com/mywebsite', etc.

$arr = [$base_dir, $doc_root, $base_url, $protocol, $port, $disp_port, $domain, $full_url];

foreach ($arr as $value) {
    echo $value . "<br/>";
}

echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]<br/>";

echo "http://$_SERVER[HTTP_HOST]" . WS_PATH . "<hr/>";