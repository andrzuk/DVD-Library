<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Users_Model
{
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	public function __construct()
	{
		$this->table_name = 'users'; // nazwa głównej tabeli modelu w bazie
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($value) ? NULL : " AND (user_login LIKE '%" . $value . "%' OR imie LIKE '%" . $value . "%' OR nazwisko LIKE '%" . $value . "%' OR email LIKE '%" . $value . "%')";

		$query = "SELECT COUNT(*) AS licznik FROM " . $this->table_name . " WHERE 1" . $condition . $filter;
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
			switch ($row['status'])
			{
				case 1:
					$status = 'Administratorzy';
					break;
				case 2:
					$status = 'Operatorzy';
					break;
				case 3:
					$status = 'Użytkownicy';
					break;
			}
			$row['status'] = $status;
			$row['active'] = $row['active'] ? 'Tak' : 'Nie';
			$row['user_password'] = PASS_MASK;
			$this->row_item = $row;
			mysql_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (user_login LIKE '%" . $_SESSION['list_filter'] . "%' OR imie LIKE '%" . $_SESSION['list_filter'] . "%' OR nazwisko LIKE '%" . $_SESSION['list_filter'] . "%' OR email LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				switch ($row['status'])
				{
					case 1:
						$status = 'Adm';
						break;
					case 2:
						$status = 'Opr';
						break;
					case 3:
						$status = 'Usr';
						break;
				}
				$row['status'] = $status;
				$row['user_password'] = PASS_MASK;
				$row['user_login'] = substr($row['user_login'], 0, 10);
				$row['imie'] = substr($row['imie'], 0, 12);
				$row['nazwisko'] = substr($row['nazwisko'], 0, 16);
				$row['email'] = substr($row['email'], 0, 20);
				$this->rows_list[] = $row;
			} 
			mysql_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function Exist($record_item, $id)
	{
		$exist = 0;
		
		$query = 	"SELECT COUNT(*) AS licznik FROM " . $this->table_name . 
					" WHERE (user_login = '" . $record_item['user_login'] .
					"' OR email = '" . $record_item['email'] .
					"' OR pesel = '" . $record_item['pesel'] . "')" .
					" AND id <> " . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$exist = $row['licznik'];
			mysql_free_result($result);
		}
		return $exist;
	}
	
	public function Add($record_item)
	{
		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					mysql_real_escape_string($record_item['user_login']) . "', '" . 
					sha1($record_item['user_password']) . "', '" . 
					mysql_real_escape_string($record_item['imie']) . "', '" . 
					mysql_real_escape_string($record_item['nazwisko']) . "', '" . 
					mysql_real_escape_string($record_item['email']) . "', '" . 
					$record_item['status'] . "', '" . 
					mysql_real_escape_string($record_item['ulica']) . "', '" . 
					mysql_real_escape_string($record_item['kod']) . "', '" . 
					mysql_real_escape_string($record_item['miasto']) . "', '" . 
					mysql_real_escape_string($record_item['pesel']) . "', '" . 
					mysql_real_escape_string($record_item['telefon']) . "', '" . 
					$this->mySqlDateTime . "', '', '', '', '" . 
					$record_item['active'] . "')";

		mysql_query($query);
		
		$query = "SELECT id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query);
		$row = mysql_fetch_assoc($result);
		$user_id = $row['id'];
		mysql_free_result($result);
		
		$query = "SELECT * FROM admin_functions ORDER BY id";

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$access = $row['module'] == 'users' ? 1 : 0;
				$sub_query = "INSERT INTO user_roles VALUES (NULL, '" . $user_id . "', '" . $row['id'] . "', '" . $access . "')";
				mysql_query($sub_query);
			} 
			mysql_free_result($result);
		}

		return mysql_affected_rows();
	}
	
	public function Edit($record_item, $id)
	{
		$new_password = !empty($record_item['user_password']) ? sha1($record_item['user_password']) : NULL;
		$set_password = !empty($new_password) ? "', user_password='".$new_password : NULL;

		$query = "UPDATE " . $this->table_name . 
					" SET imie='" . mysql_real_escape_string($record_item['imie']) . 
					"', nazwisko='" . mysql_real_escape_string($record_item['nazwisko']) . 
					"', email='" . mysql_real_escape_string($record_item['email']) . 
					"', status='" . mysql_real_escape_string($record_item['status']) . 
					"', ulica='" . mysql_real_escape_string($record_item['ulica']) . 
					"', kod='" . mysql_real_escape_string($record_item['kod']) . 
					"', miasto='" . mysql_real_escape_string($record_item['miasto']) . 
					"', pesel='" . mysql_real_escape_string($record_item['pesel']) . 
					"', telefon='" . mysql_real_escape_string($record_item['telefon']) . 
					"', active='" . $record_item['active'] . $set_password .
					"', data_modyfikacji='" . $this->mySqlDateTime . 
					"' WHERE id=" . intval($id);
		mysql_query($query);
		
		return mysql_affected_rows();
	}
	
	public function GetLast()
	{
		$this->row_item = array();

		$query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$row['user_password'] = PASS_MASK;
			$this->row_item = $row;
			mysql_free_result($result);
		}
		return $this->row_item;
	}
	
	public function Remove($id)
	{
		$query = "DELETE FROM user_roles WHERE user_id=" . intval($id);
		mysql_query($query);

		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysql_query($query);

		return mysql_affected_rows();
	}
}

?>
