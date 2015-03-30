<?php

/*
 * W kontrolerze zbierane są wszystkie dane potrzebne do pokazania na stronie
 */

define ('MODULE_NAME', 'disc_map');

include APP_DIR . 'model' . '/' . MODULE_NAME . '.php';

$model_object = new Disc_Map_Model();

include APP_DIR . 'view' . '/' . MODULE_NAME . '.php';

$view_object = new Disc_Map_View();

// pobiera wszystkie kategorie:
$records_list = $model_object->GetLibrary();

// dane z bazy potrzebne na stronę:
$data_import = $model_object->GetStatistics();

// wyświetla zawartość strony:
$site_content = $view_object->ShowLibrary($records_list, $data_import);

// wyświetla tytuł strony:
$content_title = 'Mapa serwisu';

// ścieżka strony:
$site_path = array (
    'index.php' => 'Strona główna',
    'index.php?route=' . MODULE_NAME => $content_title
);

// opcje dla podstrony:
$content_options = array();

/*
 * Przechodzi do skompletowania danych i wygenerowania strony
 */
include 'main/route.php';

?>
