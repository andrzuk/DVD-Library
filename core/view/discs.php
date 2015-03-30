<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Discs_View
{
	public function __construct()
	{
	}
	
	/*
	 * Formularz
	 */
	 
	public function ShowForm($row, $required, $failed, $import)
	{
		$id = 0;
		$disc_name = isset($_SESSION['form_fields']['disc_name']) ? $_SESSION['form_fields']['disc_name'] : NULL;
		$disc_type = isset($_SESSION['form_fields']['disc_type']) ? $_SESSION['form_fields']['disc_type'] : NULL;
		$content_type = isset($_SESSION['form_fields']['content_type']) ? $_SESSION['form_fields']['content_type'] : NULL;
		$scan_result = isset($_SESSION['form_fields']['scan_result']) ? $_SESSION['form_fields']['scan_result'] : NULL;
		$scan_date = NULL;

		if (is_array($row))
		{
			$id = $row['id'];
			$disc_name = $row['disc_name'];
			$disc_type = $row['disc_type'];
			$content_type = $row['content_type'];
			$scan_date = $row['scan_date'];
			$scan_result = NULL;
		}
				
		// Form Generator:
		
		$form_inputs = Array();
		$form_hiddens = Array();
		$form_buttons = Array();
		
		require_once LIB_DIR . 'gener' . '/' . 'form.php';
		
		$main_form = new FormBuilder();
		
		if (is_array($row))
		{
			$form_title = 'Edycja';
			$form_image = 'img/32x32/list_edit.png';
		}
		else
		{
			$form_title = 'Skanowanie';
			$form_image = 'img/32x32/disc_package.png';
		}
		$form_width = '600px';
		$form_widths = Array('30%', '70%');
		
		$main_form->init($form_title, $form_image, $form_width, $form_widths);
		
		// action:
		
		if (is_array($row))
		{
			$form_action = 'index.php?route=' . MODULE_NAME . '&action=edit&id=' . $id;
		}		
		else
		{
			$form_action = 'index.php?route=' . MODULE_NAME . '&action=add';
		}
		
		$main_form->set_action($form_action);
		
		// required:
		
		$main_form->set_required($required);
		
		// failed:
		
		$main_form->set_failed($failed);

		// inputs:
		
		if (is_array($row))
		{
			$form_data = Array(
				Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $id, 'style' => '')
				);
			$form_input = Array('caption' => 'Id', 'data' => $form_data);
			$form_inputs[] = $form_input;

		}

		$sel = array('CD' => '', 'DVD' => '');
		$sel[$disc_type] = 'selected';
		
		$main_options = Array();
		$main_option = Array('value' => 'CD', 'caption' => 'CD', $sel['CD'] => $sel['CD']);
		$main_options[] = $main_option;
		$main_option = Array('value' => 'DVD', 'caption' => 'DVD', $sel['DVD'] => $sel['DVD']);
		$main_options[] = $main_option;
		
		$form_data = Array(
						Array('type' => 'select', 'id' => 'disc_type', 'name' => 'disc_type', 'option' => $main_options, 'description' => '', 'style' => 'width: 50%;')
						);
		$form_input = Array('caption' => 'Rodzaj płyty', 'data' => $form_data);
		$form_inputs[] = $form_input;
				
		$sel = array('Muzyka' => '', 'Filmy' => '', 'Wideoklipy' => '', 'Sport' => '');
		$sel[$content_type] = 'selected';
		
		$main_options = Array();
		$main_option = Array('value' => 'Muzyka', 'caption' => 'Muzyka', $sel['Muzyka'] => $sel['Muzyka']);
		$main_options[] = $main_option;
		$main_option = Array('value' => 'Filmy', 'caption' => 'Filmy', $sel['Filmy'] => $sel['Filmy']);
		$main_options[] = $main_option;
		$main_option = Array('value' => 'Wideoklipy', 'caption' => 'Wideoklipy', $sel['Wideoklipy'] => $sel['Wideoklipy']);
		$main_options[] = $main_option;
		$main_option = Array('value' => 'Sport', 'caption' => 'Sport', $sel['Sport'] => $sel['Sport']);
		$main_options[] = $main_option;
		
		$form_data = Array(
						Array('type' => 'select', 'id' => 'content_type', 'name' => 'content_type', 'option' => $main_options, 'description' => '', 'style' => 'width: 50%;')
						);
		$form_input = Array('caption' => 'Rodzaj treści', 'data' => $form_data);
		$form_inputs[] = $form_input;
				
		$form_data = Array(
						Array('type' => 'text', 'id' => 'disc_name', 'name' => 'disc_name', 'caption' => '', 'value' => $disc_name, 'style' => 'width: 96%;')
						);
		$form_input = Array('caption' => 'Nazwa lub tytuł płyty', 'data' => $form_data);
		$form_inputs[] = $form_input;

		if (is_array($row))
		{
			$form_data = Array(
							Array('type' => 'label', 'id' => '', 'name' => '', 'value' => $scan_date, 'style' => '')
							);
			$form_input = Array('caption' => 'Data skanowania', 'data' => $form_data);
			$form_inputs[] = $form_input;
		}

		$main_form->set_inputs($form_inputs);
		
		// hiddens (dodatkowe informacje fomularza):
		
		$form_data = Array(
						Array('type' => 'hidden', 'id' => 'scan_result', 'name' => 'scan_result', 'value' => $scan_result)
						);
		$form_hiddens[] = $form_data;

		$main_form->set_hiddens($form_hiddens);

		// buttons:
				
		if (is_array($row))
		{
			$form_data = Array('type' => 'submit', 'id' => 'save_button', 'name' => 'save_button', 'value' => 'Zapisz', 'style' => 'width: 80px;');
			$form_buttons[] = $form_data;
			$form_data = Array('type' => 'submit', 'id' => 'update_button', 'name' => 'update_button', 'value' => 'Zamknij', 'style' => 'width: 80px;');
			$form_buttons[] = $form_data;
		}
		else
		{
			$form_data = Array('type' => 'submit', 'id' => 'save_button', 'name' => 'save_button', 'value' => 'Uruchom', 'style' => 'width: 80px;', 'onclick' => "runDriveScan();");
			$form_buttons[] = $form_data;
		}
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
		
		$list_title = 'Lista - wszystkie pozycje';
		$list_image = 'img/32x32/application_side_list.png';

		$main_list->init($list_title, $list_image);
		
		$main_list->set_module(MODULE_NAME);
		
		$main_list->set_list($list);
		
		$main_list->set_columns($columns);
		
		$main_list->set_params($params);
		
		// kolumny wyświetlane:
		$col_attrib = array(
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '25%', 'align' => 'left', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '5%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
			array('width' => '13%', 'align' => 'right', 'visible' => '1'),
			array('width' => '15%', 'align' => 'center', 'visible' => '1'),
			array('width' => '0%', 'align' => 'left', 'visible' => '0'),
			array('width' => '0%', 'align' => 'left', 'visible' => '0'),
			array('width' => '10%', 'align' => 'center', 'visible' => '1'),
		);
		
		$main_list->set_attribs($col_attrib);
		
		// dostępne akcje:
		$col_actions = array(
			array('action' => 'view', 'icon' => 'info.png', 'title' => 'Podgląd'),
			array('action' => 'edit', 'icon' => 'edit.png', 'title' => 'Edytuj'),
			array('action' => 'delete', 'icon' => 'trash.png', 'title' => 'Usuń'),
		);
		
		$main_list->set_actions($col_actions);
		
		// render:
		
		$site_content = $main_list->build_list();
		
		// List Generator.
		
		return $site_content;
	}
	
	/*
	 * Szczegóły
	 */
	
	public function ShowRecord($row, $columns)
	{
		$stats = NULL;
		
		// View Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'tree.php';
		
		$main_view = new TreeBuilder();
		
		$view_title = 'Podgląd';
		$view_image = 'img/32x32/list_edit.png';
		$view_width = '600px';

		$main_view->init($view_title, $view_image, $view_width);

		$main_view->set_module(MODULE_NAME);
		
		$row['scan_result'] = '<a href="index.php?route=discs&action=tree&id='.$row['id'].'"><img src="img/tree.png" class="TopLinkIcon" title="Rozwiń" alt="data structure" /></a>';
		
		$stats .= '<table>';
		
		foreach ($row['statistics'] as $key => $value)
		{
			$stats .= '<tr>';
			foreach ($value as $k => $v)
			{
				switch ($k)
				{
					case 'type_name':
						$style = 'text-align: left; padding-right: 10px;';
						break;
					case 'count':
						$style = 'text-align: right; padding-left: 10px;';
						break;
				}
				$stats .= '<td style="'.$style.'">';
				$stats .= $v;
				$stats .= '</td>';
			}
			$stats .= '</tr>';
		}
		
		$stats .= '</table>';
		
		$row['statistics'] = $stats;
		
		$main_view->set_row($row);
		
		$main_view->set_columns($columns);
		
		$main_view->set_buttons(array('edit', 'cancel',));

		// render:
		
		$site_content = $main_view->build_view();
		
		// View Generator.
		
		return $site_content;
	}

	/*
	 * Drzewo
	 */
	
	public function ShowTree($row, $item, $rows)
	{
		// View Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'tree.php';
		
		$main_view = new TreeBuilder();
		
		$view_title = 'Zawartość';
		$view_image = 'img/32x32/node_tree.png';
		$view_width = '600px';

		$main_view->init($view_title, $view_image, $view_width);

		$main_view->set_module(MODULE_NAME);
		
		$main_view->set_row($row);
		
		$main_view->set_rows($rows);
		
		$main_view->set_buttons(array('edit', 'cancel',));

		// render:
		
		$site_content = $main_view->build_tree($item);
		
		// View Generator.
		
		return $site_content;
	}
	
	/*
	 * Szczegóły zbiorczo
	 */
	
	public function ShowDetails($row, $columns)
	{
		// View Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'view.php';
		
		$main_view = new ViewBuilder();
		
		$view_title = 'Szczegóły';
		$view_image = 'img/32x32/list_edit.png';
		$view_width = '600px';

		$main_view->init($view_title, $view_image, $view_width);

		$main_view->set_module(MODULE_NAME);
		
		$main_view->set_row($row);
		
		$columns = array(
			array('db_name' => 'disc_name', 	'column_name' => 'Tytuł płyty'),
			array('db_name' => 'disc_type',		'column_name' => 'Rodzaj'),
			array('db_name' => 'content_type',	'column_name' => 'Treść'),
			array('db_name' => 'scan_date',		'column_name' => 'Skanowanie'),
			array('db_name' => 'folder_name',	'column_name' => 'Folder'),
			array('db_name' => 'file_name',		'column_name' => 'Nazwa pliku'),
			array('db_name' => 'type_name',		'column_name' => 'Typ'),
			array('db_name' => 'file_size',		'column_name' => 'Rozmiar'),
			array('db_name' => 'modify_date',	'column_name' => 'Data modyfikacji'),
			array('db_name' => 'attribs',		'column_name' => 'Atrybuty'),
		);

		$main_view->set_columns($columns);
		
		$main_view->set_buttons(array('tree', 'back', 'cancel',));

		// render:
		
		$site_content = $main_view->build_details();
		
		// View Generator.
		
		return $site_content;
	}
	
	/*
	 * Szczegóły zbiorczo
	 */
	
	public function ShowFolder($row, $columns)
	{
		// View Generator:
		
		require_once LIB_DIR . 'gener' . '/' . 'view.php';
		
		$main_view = new ViewBuilder();
		
		$view_title = 'Szczegóły';
		$view_image = 'img/32x32/list_edit.png';
		$view_width = '600px';

		$main_view->init($view_title, $view_image, $view_width);

		$main_view->set_module(MODULE_NAME);
		
		$main_view->set_row($row);
		
		$columns = array(
			array('db_name' => 'disc_name', 	'column_name' => 'Tytuł płyty'),
			array('db_name' => 'disc_type',		'column_name' => 'Rodzaj'),
			array('db_name' => 'content_type',	'column_name' => 'Treść'),
			array('db_name' => 'scan_date',		'column_name' => 'Skanowanie'),
			array('db_name' => 'folder_name',	'column_name' => 'Folder'),
			array('db_name' => 'create_date',	'column_name' => 'Data utworzenia'),
			array('db_name' => 'attribs',		'column_name' => 'Atrybuty'),
		);

		$main_view->set_columns($columns);
		
		$main_view->set_buttons(array('tree', 'back', 'cancel',));

		// render:

		$site_content = $main_view->build_folder();

		// View Generator.
		
		return $site_content;
	}
}

?>