<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Search_Model
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
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		if ($this->user_status) // zalogowany
		{
			$condition .= ' AND categories.permission >= ' . intval($this->user_status);
		}
		else // gość
		{
			$condition .= ' AND categories.permission = 4';
		}

		$query = 	"SELECT COUNT(*) AS licznik" . 
					" FROM pages " . 
					" INNER JOIN categories ON categories.id = pages.category_id" .
					" INNER JOIN users ON users.id = pages.author_id" .
					" WHERE pages.contents LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%' AND pages.system_page = 0 AND pages.visible = 1" . $condition;
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$_SESSION['result_capacity'] = $row['licznik'];
			$_SESSION['page_counter'] = intval($row['licznik'] / $show_rows) + ($row['licznik'] % $show_rows > 0 ? 1 : 0);
			mysql_free_result($result);
		}
	}
	
	public function Search($value, $kind, $limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		if ($this->user_status) // zalogowany
		{
			$condition .= ' AND categories.permission >= ' . intval($this->user_status);
		}
		else // gość
		{
			$condition .= ' AND categories.permission = 4';
		}

		$this->rows_list = array();
		
		$query = 	"SELECT pages.title, pages.contents, pages.category_id," . 
					" categories.caption, users.user_login, pages.modified " . 
					" FROM pages " . 
					" INNER JOIN categories ON categories.id = pages.category_id" .
					" INNER JOIN users ON users.id = pages.author_id" .
					" WHERE pages.contents LIKE '%" . str_replace(' ', '%', mysql_real_escape_string($value)) . "%' AND pages.system_page = 0 AND pages.visible = 1" . $condition .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->rows_list[] = $row;
			} 
			mysql_free_result($result);
		}
		
		return $this->rows_list;
	}	

	public function Store($record_object, $search_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'search_text') $search_text = $v;
		}
		foreach ($search_object as $k => $v)
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
