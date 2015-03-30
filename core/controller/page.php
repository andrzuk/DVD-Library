<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'page');

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Page_Model();

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Page_View();

$id = isset($_GET['id']) ? intval($_GET['id']) : NULL;

// dane z bazy potrzebne na stronę:

$data_import = array(
	'authors' => $model_object->GetAuthors(),
);

// pobiera rekord o podanym id:
$record_object = $model_object->GetPageContent($id);

if ($record_object) // strona istnieje
{
	// wyświetla tytuł strony:
	$content_title = $view_object->ShowTitle($record_object);

	// wyświetla zawartość strony:
	$site_content = $view_object->ShowPage($record_object, $data_import);
}
else // strona nie istnieje
{
	$site_dialog = array(
		'ERROR',
		'Błąd podstrony',
		'Strona nie została znaleziona.'.
		'<br />Sprawdź, czy podany w adresie identyfikator strony jest prawidłowy.',
		array(
			array(
				'index.php', 'Zamknij'
			)
		)
	);
}

$content_title = !empty($content_title) ? $content_title : 'Strona nie znaleziona';

$site_content = !empty($site_content) ? $site_content : NULL;

// ścieżka strony:
$site_path = array (
    'index.php' => 'Strona główna',
    'index.php?route=' . MODULE_NAME . '&id=' . $id => $content_title
);

// opcje dla podstrony:
$content_options = array();

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
include 'main/route.php';

?>
