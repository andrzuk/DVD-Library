<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Disc_Search_View
{
	public function __construct()
	{
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
}

?>