<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Adv_Search_View
{
	public function __construct()
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $import)
	{
		$search_text = isset($_SESSION['form_fields']['search_text']) ? $_SESSION['form_fields']['search_text'] : NULL;
		$search_kind = isset($_SESSION['form_fields']['search_kind']) ? $_SESSION['form_fields']['search_kind'] : 1;
		$search_type = isset($_SESSION['form_fields']['search_type']) ? $_SESSION['form_fields']['search_type'] : 1;

		// Form Generator:
		
		$form_inputs = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		$form_title = 'Szczegóły';
		$form_image = 'img/32x32/search.png';
		$form_width = '600px';
		$form_widths = Array('30%', '70%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		$form_action = 'index.php?route=' . MODULE_NAME;
		
		$main_form->set_action($form_action);
		
		// inputs:
		
		$form_data = Array(
						Array('type' => 'text', 'id' => 'search_text', 'name' => 'search_text', 'caption' => '', 'value' => $search_text, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Poszukiwana nazwa', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$sel = array('1' => '', '2' => '', '3' => '');
		$sel[$search_kind] = 'selected';
		
		$main_options = Array();
		$main_option = Array('value' => '1', 'caption' => 'Pliki', $sel['1'] => $sel['1']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'Foldery', $sel['2'] => $sel['2']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => 'Pliki + Foldery', $sel['3'] => $sel['3']);
		$main_options[] = $main_option;
		
		$form_data = Array(
						Array('type' => 'select', 'id' => 'search_kind', 'name' => 'search_kind', 'option' => $main_options, 'description' => '', 'style' => 'width: 50%;')
						);
		$form_input = Array('caption' => 'Wyszukiwane obiekty', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$sel = array('1' => '', '2' => '', '3' => '', '4' => '');
		$sel[$search_type] = 'selected';
		
		$main_options = Array();
		$main_option = Array('value' => '1', 'caption' => 'Muzyka', $sel['1'] => $sel['1']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '2', 'caption' => 'Filmy', $sel['2'] => $sel['2']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '3', 'caption' => 'Wideoklipy', $sel['3'] => $sel['3']);
		$main_options[] = $main_option;
		$main_option = Array('value' => '4', 'caption' => 'Sport', $sel['4'] => $sel['4']);
		$main_options[] = $main_option;
		
		$form_data = Array(
						Array('type' => 'select', 'id' => 'search_type', 'name' => 'search_type', 'option' => $main_options, 'description' => '', 'style' => 'width: 50%;')
						);
		$form_input = Array('caption' => 'Rodzaj treści', 'data' => $form_data);
		$form_inputs[] = $form_input;
		
		$main_form->set_inputs($form_inputs);
		
		// buttons:
				
		$form_data = Array('type' => 'submit', 'id' => 'search_button', 'name' => 'search_button', 'value' => 'Szukaj', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		$form_data = Array('type' => 'submit', 'id' => 'cancel_button', 'name' => 'cancel_button', 'value' => 'Anuluj', 'style' => 'width: 80px;');
		$form_buttons[] = $form_data;
		
		$main_form->set_buttons($form_buttons, 'right');

		// render:
		
		$site_content = $main_form->build_form();
		
		// Form Generator.
		
		return $site_content;
	}
	
	/*
	 * Lista
	 */
	 
	public function ShowList($list, $columns, $params)
	{
		// List Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'list.php';
		
		$main_list = new ListBuilder();
		
		$list_title = 'Znalezione pozycje';
		$list_image = 'img/32x32/search.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);

		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '13%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '22%', 'align' => 'left', 'visible' => '1'),
			array('width' => '27%', 'align' => 'left', 'visible' => '1'),
			array('width' => '13%', 'align' => 'right', 'visible' => '1'),
			array('width' => '13%', 'align' => 'center', 'visible' => '1'),
			array('width' => '2%', 'align' => 'right', 'visible' => '1'),
			array('width' => '0%', 'align' => 'left', 'visible' => '0'),
			array('width' => '0%', 'align' => 'left', 'visible' => '0'),
			array('width' => '0%', 'align' => 'center', 'visible' => '0'),
		);
		
		$main_list->set_attribs($col_attrib);
		
		// dostępne akcje:
		$col_actions = array();
		
		$main_list->set_actions($col_actions);

		// render:
		
		$site_content = $main_list->build_discs_list();
		
		// List Generator.

		return $site_content;
	}
	
	/*
	 * Lista
	 */
	 
	public function ShowFolders($list, $columns, $params)
	{
		// List Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'list.php';
		
		$main_list = new ListBuilder();
		
		$list_title = 'Znalezione pozycje';
		$list_image = 'img/32x32/search.png';

		$main_list->init($list_title, $list_image);

		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);

		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '15%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '50%', 'align' => 'left', 'visible' => '1'),
			array('width' => '15%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'right', 'visible' => '1'),
			array('width' => '0%', 'align' => 'left', 'visible' => '0'),
			array('width' => '0%', 'align' => 'left', 'visible' => '0'),
			array('width' => '0%', 'align' => 'center', 'visible' => '0'),
		);
		
		$main_list->set_attribs($col_attrib);
		
		// dostępne akcje:
		$col_actions = array();
		
		$main_list->set_actions($col_actions);

		// render:
		
		$site_content = $main_list->build_folders_list();
		
		// List Generator.

		return $site_content;
	}
}

?>