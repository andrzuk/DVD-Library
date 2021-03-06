<?php

/*
 * Kontroler modułu 
 * 
 * zbierane są dane i parametry do podstawowych widoków (lista, edycja, podgląd)
 */

define ('MODULE_NAME', 'discs');

$content_title = 'Moje albumy';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=admin' => 'Panel administratora',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Discs_Model();

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Discs_View();

$list_columns = array(
	array('db_name' => 'id', 			'column_name' => 'Id', 				'sorting' => 1),
	array('db_name' => 'disc_name',		'column_name' => 'Symbol albumu', 	'sorting' => 1),
	array('db_name' => 'disc_type',		'column_name' => 'Typ', 			'sorting' => 1),
	array('db_name' => 'content_type',	'column_name' => 'Treść', 			'sorting' => 1),
	array('db_name' => 'folders_count',	'column_name' => 'Folderów', 		'sorting' => 1),
	array('db_name' => 'files_count',	'column_name' => 'Plików',	 		'sorting' => 1),
	array('db_name' => 'total_size',	'column_name' => 'Rozmiar',			'sorting' => 1),
	array('db_name' => 'scan_date',		'column_name' => 'Skanowanie', 		'sorting' => 1),
	array('db_name' => 'statistics',	'column_name' => 'Statystyka', 		'sorting' => 0),
	array('db_name' => 'scan_result',	'column_name' => 'Zawartość', 		'sorting' => 0),
);

if (isset($_GET['mode'])) $_SESSION['mode'] = intval($_GET['mode']);

include 'main/navi.php';

$navi_object = new Navi();

$navi_params = $navi_object->init($list_columns);

$record_object = $navi_params['record_object'];
$db_params = $navi_params['db_params'];
$list_params = $navi_params['list_params'];

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;
$item = isset($_GET['item']) ? intval($_GET['item']) : NULL;

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

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, $id);

if (isset($_GET['action'])) // add, view, edit, delete
{
	switch ($_GET['action'])
	{
		// dodawanie:
		
		case 'add':
		{
			$content_options = $page_options->get_options('add');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'disc_name',
					'scan_result',
				),
				'check' => array(
					'disc_name', 
					'scan_result',
				),
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME);

			$controller_object->Add($params, $access, $acl->available());
		}
		break;

		// edycja:
		
		case 'edit':
		{
			$content_options = $page_options->get_options('edit');

			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options,
				'required' => array(
					'disc_name', 
				),
				'check' => array(
					'disc_name', 
				),
			);
			
			$access = array(ADMIN, OPERATOR);
			
			$acl = new AccessControlList(MODULE_NAME);

			$controller_object->Edit($id, $params, $access, $acl->available());
		}
		break;

		// podgląd:
		
		case 'view':
		{
			$content_options = $page_options->get_options('view');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR, USER);
			
			$acl = new AccessControlList(MODULE_NAME);

			$controller_object->View($id, $params, $access, $acl->available());
		}
		break;

		// drzewo:
		
		case 'tree':
		{
			$content_options = $page_options->get_options('view');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR, USER);
			
			$acl = new AccessControlList(MODULE_NAME);

			$controller_object->Tree($id, $item, $params, $access, $acl->available());
		}
		break;

		// szczegóły:
		
		case 'details':
		{
			$content_options = $page_options->get_options('details');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR, USER);
			
			$acl = new AccessControlList(MODULE_NAME);

			$controller_object->Details($id, $params, $access, $acl->available());
		}
		break;

		// szczegóły:
		
		case 'folder':
		{
			$content_options = $page_options->get_options('details');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN, OPERATOR, USER);
			
			$acl = new AccessControlList(MODULE_NAME);

			$controller_object->Folder($id, $params, $access, $acl->available());
		}
		break;

		// usuwanie:
		
		case 'delete':
		{
			$content_options = $page_options->get_options('delete');
			
			$params = array(
				'content_title' => $content_title,
				'content_options' => $content_options
			);
			
			$access = array(ADMIN);
			
			$acl = new AccessControlList(MODULE_NAME);

			$controller_object->Delete($id, $params, $access, $acl->available());
		}
		break;
	}
}
else // list of all
{
	$content_options = $page_options->get_options('list');
	
	$params = array(
		'content_title' => $content_title,
		'content_options' => $content_options
	);
	
	$access = array(ADMIN, OPERATOR, USER);
	
	$acl = new AccessControlList(MODULE_NAME);

	$controller_object->DrawList($params, $access, $acl->available());
}
			
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
