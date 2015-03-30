<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Registers_Model
{
	private $rows_list;
	private $row_item;
	private $table_name;
	
	public function __construct()
	{
		$this->table_name = 'registers'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
				
		$data_type = isset($_SESSION['mode']) ? ($_SESSION['mode'] == 1 ? ' AND result > 0' : ' AND result = 0') : NULL;
		
		$filter = empty($value) ? NULL : " AND (login LIKE '%" . $value . "%' OR imie LIKE '%" . $value . "%' OR nazwisko LIKE '%" . $value . "%' OR email LIKE '%" . $value . "%' OR password LIKE '%" . $value . "%' OR agent LIKE '%" . $value . "%' OR user_ip LIKE '%" . $value . "%')";

		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $data_type. $filter;
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
			$row['password'] = PASS_MASK;
			$this->row_item = $row;
			mysql_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$data_type = isset($_SESSION['mode']) ? ($_SESSION['mode'] == 1 ? ' AND result > 0' : ' AND result = 0') : NULL;
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (login LIKE '%" . $_SESSION['list_filter'] . "%' OR imie LIKE '%" . $_SESSION['list_filter'] . "%' OR nazwisko LIKE '%" . $_SESSION['list_filter'] . "%' OR email LIKE '%" . $_SESSION['list_filter'] . "%' OR password LIKE '%" . $_SESSION['list_filter'] . "%' OR agent LIKE '%" . $_SESSION['list_filter'] . "%' OR user_ip LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT id, agent, user_ip, imie, nazwisko, login, email, password, result, register_time" .
					" FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$row['password'] = PASS_MASK;
				$row['login'] = substr($row['login'], 0, 10);
				$row['imie'] = substr($row['imie'], 0, 12);
				$row['nazwisko'] = substr($row['nazwisko'], 0, 16);
				$row['email'] = substr($row['email'], 0, 20);
				$this->rows_list[] = $row;
			} 
			mysql_free_result($result);
		}
		
		return $this->rows_list;
	}	
}

?>