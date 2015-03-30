<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Messages_Model
{
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	public function __construct()
	{
		$this->table_name = 'user_messages'; // nazwa głównej tabeli modelu w bazie
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
				
		$data_type = isset($_SESSION['mode']) ? ($_SESSION['mode'] == 1 ? ' AND requested = 1' : ' AND requested = 0') : NULL;
		
		$filter = empty($value) ? NULL : " AND (message_content LIKE '%" . $value . "%' OR client_name LIKE '%" . $value . "%' OR client_email LIKE '%" . $value . "%')";

		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter;
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$_SESSION['result_capacity'] = $row['licznik'];
			$_SESSION['page_counter'] = intval($row['licznik'] / $show_rows) + ($row['licznik'] % $show_rows > 0 ? 1 : 0);
			mysql_free_result($result);
		}
	}
	
	public function GetOne($id)
	{
		$this->row_item = array();

		$query = "SELECT * FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$this->row_item = $row;
			mysql_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$data_type = isset($_SESSION['mode']) ? ($_SESSION['mode'] == 1 ? ' AND requested = 1' : ' AND requested = 0') : NULL;
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (message_content LIKE '%" . $_SESSION['list_filter'] . "%' OR client_name LIKE '%" . $_SESSION['list_filter'] . "%' OR client_email LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter .
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
	
	public function Edit($record_item, $id)
	{
		$query = "UPDATE " . $this->table_name . 
					" SET client_name='" . mysql_real_escape_string($record_item['client_name']) . 
					"', client_email='" . mysql_real_escape_string($record_item['client_email']) . 
					"', message_content='" . mysql_real_escape_string($record_item['message_content']) . 
					"', requested='" . $record_item['requested'] . 
					"', close_date='" . $this->mySqlDateTime . 
					"' WHERE id=" . intval($id);
		mysql_query($query);
		
		return mysql_affected_rows();
	}
	
	public function Remove($id)
	{
		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysql_query($query);

		return mysql_affected_rows();
	}
}

?>