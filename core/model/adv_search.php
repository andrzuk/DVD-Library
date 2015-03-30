<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Adv_Search_Model
{
	private $user_status;
	private $rows_list;
	
	private $mySqlDateTime;
	
	public function __construct()
	{
		$status = new Status();
		$this->user_status = $status->get_value('user_status');

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $range, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);

		switch ($range['type'])
		{
			case 'muzyka':
				$content_type = 'Muzyka';
				break;
			case 'filmy':
				$content_type = 'Filmy';
				break;
			case 'wideoklipy':
				$content_type = 'Wideoklipy';
				break;
			case 'sport':
				$content_type = 'Sport';
				break;
		}
		switch ($range['kind'])
		{
			case 'pliki':
				$query = 	"SELECT COUNT(*) AS licznik" . 
							" FROM catalog_files " . 
							" INNER JOIN catalog_folders ON catalog_folders.id = catalog_files.folder_id" .
							" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
							" WHERE catalog_files.file_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%'" . 
							" AND content_type = '" . $content_type . "'" . $condition;
				break;
			case 'foldery':
				$query = 	"SELECT COUNT(*) AS licznik" . 
							" FROM catalog_folders " . 
							" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
							" WHERE catalog_folders.folder_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%'" .
							" AND content_type = '" . $content_type . "'" . $condition;
				break;
			default:
				$query = 	"SELECT COUNT(*) AS licznik" . 
							" FROM catalog_files " . 
							" INNER JOIN catalog_folders ON catalog_folders.id = catalog_files.folder_id" .
							" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
							" WHERE (catalog_files.file_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%'" .
							" OR catalog_folders.folder_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%')" .
							" AND content_type = '" . $content_type . "'" . $condition;
				break;
		}

		$result = mysql_query($query);

		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$_SESSION['result_capacity'] = $row['licznik'];
			$_SESSION['page_counter'] = intval($row['licznik'] / $show_rows) + ($row['licznik'] % $show_rows > 0 ? 1 : 0);
			mysql_free_result($result);
		}
	}
	
	public function Search($value, $range, $limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$this->rows_list = array();

		switch ($range['type'])
		{
			case 'muzyka':
				$content_type = 'Muzyka';
				break;
			case 'filmy':
				$content_type = 'Filmy';
				break;
			case 'wideoklipy':
				$content_type = 'Wideoklipy';
				break;
			case 'sport':
				$content_type = 'Sport';
				break;
		}
		switch ($range['kind'])
		{
			case 'pliki':
				$query = 	"SELECT disc_name, disc_type, content_type, folder_name," . 
							" file_name, file_size, modify_date, catalog_files.attribs," .
							" catalog_discs.id AS disc_id, catalog_folders.id AS folder_id, catalog_files.id AS file_id" .
							" FROM catalog_files " . 
							" INNER JOIN catalog_folders ON catalog_folders.id = catalog_files.folder_id" .
							" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
							" WHERE catalog_files.file_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%'" . 
							" AND content_type = '" . $content_type . "'" . $condition .
							" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
							" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];
				break;
			case 'foldery':
				$query = 	"SELECT disc_name, disc_type, content_type, folder_name, create_date, attribs," . 
							" catalog_discs.id AS disc_id, catalog_folders.id AS folder_id" .
							" FROM catalog_folders " . 
							" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
							" WHERE catalog_folders.folder_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%'" . 
							" AND content_type = '" . $content_type . "'" . $condition .
							" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
							" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];
				break;
			default:
				$query = 	"SELECT disc_name, disc_type, content_type, folder_name," . 
							" file_name, file_size, modify_date, catalog_files.attribs," .
							" catalog_discs.id AS disc_id, catalog_folders.id AS folder_id, catalog_files.id AS file_id" .
							" FROM catalog_files " . 
							" INNER JOIN catalog_folders ON catalog_folders.id = catalog_files.folder_id" .
							" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
							" WHERE (catalog_files.file_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%'" .
							" OR catalog_folders.folder_name LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%')" .
							" AND content_type = '" . $content_type . "'" . $condition .
							" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
							" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];
				break;
		}

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				if (isset($row['file_size']))
					$row['file_size'] = number_format($row['file_size'] / 1024, 0, ',', '.') .' KB';
				$this->rows_list[] = $row;
			} 
			mysql_free_result($result);
		}
		
		return $this->rows_list;
	}	

	public function Store($record_object, $login_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'search_text') $search_text = $v;
		}
		foreach ($login_object as $k => $v)
		{
			if ($k == 'server') $record_item = $v;
			if ($k == 'session') $session_item = $v;
		}
		
		$query = "INSERT INTO searches VALUES (NULL, '" . 
					$record_item['HTTP_USER_AGENT'] . "', '" . 
					$record_item['REMOTE_ADDR'] . "', '" . 
					mysql_real_escape_string($search_text) . "', '" . 
					$this->mySqlDateTime . "')";
		mysql_query($query);
		
		return mysql_affected_rows();
	}
}

?>
