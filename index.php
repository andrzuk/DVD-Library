<?php

session_start();

include 'config/config.php';
include LIB_DIR . 'database.php';

$connection = new Database();
$connection->init(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$connection->open();

include LIB_DIR . 'settings.php';
include LIB_DIR . 'visitors.php';

if (isset($_GET['route'])) 
{
	$routing = explode('&', $_GET['route']);
	$route_controller = APP_DIR . 'controller/' . trim($routing[0]) . '.php';
}
else
{
	$route_controller = APP_DIR . 'controller/index.php';
}

if (!file_exists($route_controller)) 
{
	$route_controller = APP_DIR . 'controller/not_found.php';
}

include APP_DIR . 'controller/main/status.php';
		
include APP_DIR . 'controller/main/acl.php';

if (!file_exists('install/index.php')) 
{
	include $route_controller;
}
else
{
	$installation = TRUE;
	include APP_DIR . 'controller/index.php';
}

$visitor = new Visitors();
$visitor->register();

$connection->close();

?>
