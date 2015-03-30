<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'disc_search');

$content_title = 'Wyszukiwanie';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Disc_Search_Model();

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Disc_Search_View();

// poszukiwana fraza:

if (isset($_POST['search_text'])) // wpisano nową frazę
{
	$search_value = htmlspecialchars(substr(trim($_POST['search_text']), 0, 64));
	
	$record_object = array(
		'search_text' => $search_value, 
	);
	
	include LIB_DIR . 'validator.php';
	
	$validator_object = new Validator();
	
	$check_result = $validator_object->check_security($search_value);
	
	if ($check_result) // kontrola bezpieczeństwa poprawna
	{
		$_SESSION['form_fields']['search_text'] = $search_value;

		$search_object = array('server' => $_SERVER, 'session' => $_SESSION);
		
		// rejestruje akcję wyszukiwania:
		$model_object->Store($record_object, $search_object);
	}
	else // nie przeszło kontroli bezpieczeństwa
	{
		$search_value = isset($_SESSION['form_fields']['search_text']) ? $_SESSION['form_fields']['search_text'] : NULL;

		// wyświetla komunikat:
		$site_message = array(
			'ERROR', 'Do pól formularza wprowadzono zabronione wyrażenia.'
		);
	}
}
else // nie wpisano nowej frazy - bierze pod uwagę dotychczasową
{
	$search_value = isset($_SESSION['form_fields']['search_text']) ? $_SESSION['form_fields']['search_text'] : NULL;
}

// pola 'db_name' muszą być zgodne co do nazwy i kolejności
// z polami zwracanymi przez Model w metodzie Search: 

$list_columns = array(
	array('db_name' => 'disc_name', 		'column_name' => 'Płyta', 		'sorting' => 1),
	array('db_name' => 'disc_type', 		'column_name' => 'Typ', 		'sorting' => 1),
	array('db_name' => 'content_type', 		'column_name' => 'Treść', 		'sorting' => 1),
	array('db_name' => 'folder_name', 		'column_name' => 'Folder', 		'sorting' => 1),
	array('db_name' => 'file_name', 		'column_name' => 'Nazwa pliku',	'sorting' => 1),
	array('db_name' => 'file_size',			'column_name' => 'Rozmiar',	 	'sorting' => 1),
	array('db_name' => 'modify_date', 		'column_name' => 'Data',	 	'sorting' => 1),
	array('db_name' => 'attribs',			'column_name' => 'Atr',	 		'sorting' => 1),
	array('db_name' => 'disc_id', 			'column_name' => NULL,		 	'sorting' => 0),
	array('db_name' => 'folder_id', 		'column_name' => NULL,		 	'sorting' => 0),
	array('db_name' => 'file_id', 			'column_name' => NULL,		 	'sorting' => 0),
);

include 'main/navi.php';

$navi_object = new Navi();

$navi_params = $navi_object->init($list_columns);

$navi_object->set_value($search_value);

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
$content_options = NULL;

$search_domain = 'pliki + foldery';
$_SESSION['search_type'] = 'simple';
$_SESSION['list_filter'] = NULL;

$params = array(
	'content_title' => $content_title,
	'content_options' => $content_options,
	'search_text' => $search_value,
	'search_kind' => $search_domain,
	'search_range' => NULL,
);

$access = array(ADMIN, OPERATOR, USER, GUEST);

$controller_object->FoundList($params, $access, TRUE);
			
$content_title = $controller_object->Get('content_title');
$site_content = $controller_object->Get('site_content');
$site_dialog = $controller_object->Get('site_dialog');

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
