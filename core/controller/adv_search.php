<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'adv_search');

$content_title = 'Wyszukiwanie';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Adv_Search_Model();

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Adv_Search_View();

$site_content = NULL;
$content_options = NULL;

// routing:

if (isset($_GET['action'])) // formularz parametrów
{
	$search_kind = NULL;
	
	// wyświetla pusty formularz:
	$site_content = $view_object->ShowForm(NULL, NULL);
}
else if (isset($_POST['search_button'])) // uruchomiono wyszukiwanie
{
	$search_value = htmlspecialchars(substr(trim($_POST['search_text']), 0, 64));
	$search_kind = $_POST['search_kind'];
	$search_type = $_POST['search_type'];
	
	$record_object = array(
		'search_text' => $search_value, 
	);
	
	include LIB_DIR . 'validator.php';
	
	$validator_object = new Validator();
	
	$check_result = $validator_object->check_security($search_value);

	if ($check_result) // kontrola bezpieczeństwa poprawna
	{
		$_SESSION['form_fields']['search_text'] = $search_value;
		$_SESSION['form_fields']['search_kind'] = $search_kind;
		$_SESSION['form_fields']['search_type'] = $search_type;
		
		$search_object = array('server' => $_SERVER, 'session' => $_SESSION);
		
		// rejestruje akcję wyszukiwania:
		$model_object->Store($record_object, $search_object);
	}
	else // nie przeszło kontroli bezpieczeństwa
	{
		$search_value = isset($_SESSION['form_fields']['search_text']) ? $_SESSION['form_fields']['search_text'] : NULL;
		$search_kind = isset($_SESSION['form_fields']['search_kind']) ? $_SESSION['form_fields']['search_kind'] : 1;
		$search_type = isset($_SESSION['form_fields']['search_type']) ? $_SESSION['form_fields']['search_type'] : 1;
		
		// wyświetla pusty formularz:
		$site_content = $view_object->ShowForm(NULL, NULL);

		// wyświetla komunikat:
		$site_message = array(
			'ERROR', 'Do pól formularza wprowadzono zabronione wyrażenia.'
		);
	}
}
else if (isset($_POST['cancel_button'])) // anulowano wyszukiwanie
{
	$search_kind = 1;
	$search_type = 1;
	$_SESSION['form_fields']['search_text'] = NULL;
	$_SESSION['form_fields']['search_kind'] = $search_kind;
	$_SESSION['form_fields']['search_type'] = $search_type;
}
else // lista wyników wyszukiwania
{
	$search_value = isset($_SESSION['form_fields']['search_text']) ? $_SESSION['form_fields']['search_text'] : NULL;
	$search_kind = isset($_SESSION['form_fields']['search_kind']) ? $_SESSION['form_fields']['search_kind'] : 1;
	$search_type = isset($_SESSION['form_fields']['search_type']) ? $_SESSION['form_fields']['search_type'] : 1;
}

// pola 'db_name' muszą być zgodne co do nazwy i kolejności
// z polami zwracanymi przez Model w metodzie Search: 

switch ($search_kind)
{
	case 1:
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
		break;
	case 2:
		$list_columns = array(
			array('db_name' => 'disc_name', 		'column_name' => 'Płyta', 		'sorting' => 1),
			array('db_name' => 'disc_type', 		'column_name' => 'Typ', 		'sorting' => 1),
			array('db_name' => 'content_type', 		'column_name' => 'Treść', 		'sorting' => 1),
			array('db_name' => 'folder_name', 		'column_name' => 'Folder', 		'sorting' => 1),
			array('db_name' => 'create_date', 		'column_name' => 'Data',	 	'sorting' => 1),
			array('db_name' => 'attribs',			'column_name' => 'Atr',	 		'sorting' => 1),
			array('db_name' => 'disc_id', 			'column_name' => NULL,		 	'sorting' => 0),
			array('db_name' => 'folder_id', 		'column_name' => NULL,		 	'sorting' => 0),
		);
		break;
	default:
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
		break;
}

if (empty($site_content)) // generuje listę
{
	$search_domain = array('kind' => NULL, 'type' => NULL);
	
	switch ($search_kind)
	{
		case 1:
			$search_domain['kind'] = 'pliki';
			break;
		case 2:
			$search_domain['kind'] = 'foldery';
			break;
		case 3:
			$search_domain['kind'] = 'pliki + foldery';
			break;
		default:
			$search_domain['kind'] = NULL;
			break;
	}
	switch ($search_type)
	{
		case 1:
			$search_domain['type'] = 'muzyka';
			break;
		case 2:
			$search_domain['type'] = 'filmy';
			break;
		case 3:
			$search_domain['type'] = 'wideoklipy';
			break;
		case 4:
			$search_domain['type'] = 'sport';
			break;
		default:
			$search_domain['type'] = NULL;
			break;
	}
	
	$_SESSION['search_type'] = 'advanced';
	$_SESSION['list_filter'] = NULL;
	
	include 'main/navi.php';

	$navi_object = new Navi();

	$navi_params = $navi_object->init($list_columns);
	
	$navi_object->set_value($search_value);
	$navi_object->set_domain($search_domain);

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

	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options,
		'search_text' => $search_value,
		'search_range' => $search_domain,
	);

	$access = array(ADMIN, OPERATOR, USER, GUEST);

	switch ($search_kind)
	{
		case 1:
			$controller_object->FoundList($params, $access, TRUE);
			break;
		case 2:
			$controller_object->FoundFolders($params, $access, TRUE);
			break;
		default:
			$controller_object->FoundList($params, $access, TRUE);
			break;
	}

	$content_title = $controller_object->Get('content_title');
	$site_content = $controller_object->Get('site_content');
	$site_dialog = $controller_object->Get('site_dialog');
}

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
