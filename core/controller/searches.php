<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'searches');

$content_title = 'Wyszukiwania użytkowników';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Searches_Model();

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Searches_View();

$list_columns = array(
	array('db_name' => 'id', 			'column_name' => 'Id', 				'sorting' => 1),
	array('db_name' => 'agent', 		'column_name' => 'Agent',			'sorting' => 1),
	array('db_name' => 'user_ip', 		'column_name' => 'Adres IP',		'sorting' => 1),
	array('db_name' => 'search_text',	'column_name' => 'Szukany tekst', 	'sorting' => 1),
	array('db_name' => 'search_time',	'column_name' => 'Godzina', 		'sorting' => 1),
);

if (isset($_GET['mode'])) $_SESSION['mode'] = intval($_GET['mode']);

include 'main/navi.php';

$navi_object = new Navi();

$navi_params = $navi_object->init($list_columns);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

// dane z bazy potrzebne do kontrolek formularza:

$data_import = array();

// komplet danych przekazywanych do głównego operatora:

$objects = array(
	'model_object' => $model_object,
	'view_object' => $view_object,
	'record_object' => $record_object,
	'navi_object' => $navi_object,
	'db_params' => $db_params,
	'list_params' => $list_params,
	'list_columns' => $list_columns,
	'data_import' => $data_import,
);

include APP_DIR . 'controller/main/operator.php';

$controller_object = new Operator($objects);

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;

$content_options = array (
	array (
		'address' => 'index.php?route=admin',
		'caption' => 'Zamknij',
		'icon' => 'img/stop.png'
	),
);

$params = array(
	'content_title' => $content_title,
	'content_options' => $content_options
);

$access = array(ADMIN);

$acl = new AccessControlList(MODULE_NAME);
			
$controller_object->DrawList($params, $access, $acl->available());
			
$content_title = $controller_object->Get('content_title');
$content_options = $controller_object->Get('content_options');
$site_content = $controller_object->Get('site_content');
$site_message = $controller_object->Get('site_message');
$site_dialog = $controller_object->Get('site_dialog');

/*
 * Przechodzi do wygenerowania strony
 */

include 'main/route.php';

?>
