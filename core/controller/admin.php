<?php

/*
 * Admin Panel
 *
 * Kontroler kieruje ruchem; zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'admin');

$content_title = 'Panel administratora';

$site_path = array (
    'index.php' => 'Strona główna',
	'index.php?route=' . MODULE_NAME => $content_title
);

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Admin_Model();

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Admin_View();

$status = new Status();
$user_status = $status->get_value('user_status');

$_SESSION['mode'] = NULL;
$_SESSION['sort_field'] = NULL;
$_SESSION['sort_order'] = NULL;
$_SESSION['list_filter'] = NULL;

/*
 * Przechodzi do skompletowania danych
 */

$site_content = NULL;
$content_options = NULL;

include APP_DIR . 'view/template/options.php';

$page_options = new Options(MODULE_NAME, NULL);

$content_options = $page_options->get_options('admin');

$access = array(ADMIN, OPERATOR, USER);

if (in_array($user_status, $access)) // są uprawnienia
{
	// odczytuje dane statystyczne:

	$record_object = $model_object->GetTablesCounts();
	
	// wczytuje dodatkowe statystyki:
	
	include APP_DIR . 'model' . '/' . 'style' . '.php';

	$additional_object = new Style_Model();
	
	$additional_record = $additional_object->GetSize();
	
	// pokazuje admin-panel:

	$panel_items = array(
		array(
			'group' => 'System',
			'items' => array(
				array(
					'address' => 'index.php?route=config',
					'label' => 'Konfiguracja'.' ('.$record_object[0].')',
					'icon' => 'img/48x48/14.png',
					'access' => in_array($user_status, array(ADMIN)),
				),
				array(
					'address' => 'index.php?route=style',
					'label' => 'Wygląd'.' ('.$additional_record.')',
					'icon' => 'img/48x48/36.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR)),
				),
				array(
					'address' => 'index.php?route=users',
					'label' => 'Użytkownicy'.' ('.$record_object[1].')',
					'icon' => 'img/48x48/07.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR, USER)),
				),
				array(
					'address' => 'index.php?route=roles',
					'label' => 'Access Control List'.' ('.$record_object[14].')',
					'icon' => 'img/48x48/09.png',
					'access' => in_array($user_status, array(ADMIN)),
				),
				array(
					'address' => 'index.php?route=visitors',
					'label' => 'Odwiedziny'.' ('.$record_object[2].')',
					'icon' => 'img/48x48/24.png',
					'access' => in_array($user_status, array(ADMIN)),
				),
			),
		),
		array(
			'group' => 'Zasoby',
			'items' => array(
				array(
					'address' => 'index.php?route=images',
					'label' => 'Galeria'.' ('.$record_object[3].')',
					'icon' => 'img/48x48/22.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR)),
				),
				array(
					'address' => 'index.php?route=discs',
					'label' => 'Albumy'.' ('.$record_object[15].')',
					'icon' => 'img/48x48/35.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR, USER)),
				),
				array(
					'address' => 'index.php?route=categories',
					'label' => 'Kategorie'.' ('.$record_object[5].')',
					'icon' => 'img/48x48/29.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR)),
				),
				array(
					'address' => 'index.php?route=pages',
					'label' => 'Strony'.' ('.$record_object[6].')',
					'icon' => 'img/48x48/04.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR)),
				),
				array(
					'address' => 'index.php?route=sites',
					'label' => 'Opisy'.' ('.$record_object[7].')',
					'icon' => 'img/48x48/13.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR)),
				),
			),
		),
		array(
			'group' => 'Raporty',
			'items' => array(
				array(
					'address' => 'index.php?route=messages',
					'label' => 'Wiadomości'.' ('.$record_object[8].')',
					'icon' => 'img/48x48/10.png',
					'access' => in_array($user_status, array(ADMIN, OPERATOR)),
				),
				array(
					'address' => 'index.php?route=searches',
					'label' => 'Wyszukiwania'.' ('.$record_object[9].')',
					'icon' => 'img/48x48/27.png',
					'access' => in_array($user_status, array(ADMIN)),
				),
				array(
					'address' => 'index.php?route=registers',
					'label' => 'Rejestracje'.' ('.$record_object[10].')',
					'icon' => 'img/48x48/28.png',
					'access' => in_array($user_status, array(ADMIN)),
				),
				array(
					'address' => 'index.php?route=logins',
					'label' => 'Logowania'.' ('.$record_object[11].')',
					'icon' => 'img/48x48/25.png',
					'access' => in_array($user_status, array(ADMIN)),
				),
				array(
					'address' => 'index.php?route=reminds',
					'label' => 'Hasła'.' ('.$record_object[12].')',
					'icon' => 'img/48x48/26.png',
					'access' => in_array($user_status, array(ADMIN)),
				),
			),
		),
	);

	$panel_title = 'Funkcje administracyjne';
	$panel_image = 'img/32x32/webmaster_tools.png';
	$panel_width = '90%';

	// inicjuje panel:
	$view_object->init($panel_width, $panel_image, $panel_title);

	// wyświetla panel z ikonami funkcji:
	$site_content = $view_object->ShowPanel($panel_items);
}
else // brak uprawnień
{
	$site_dialog = array(
		'ERROR',
		'Brak uprawnień',
		'Uruchomiona funkcja wymaga zalogowania do serwisu na konto o profilu administratora.',
		array(
			array(
				'index.php?route=login', 'Zaloguj'
			),
			array(
				'index.php', 'Zamknij'
			),
		)
	);
}

/*
 * Przechodzi do wygenerowania strony
 */
 
include 'main/route.php';

?>
