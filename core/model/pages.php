<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Pages_Model
{
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	private $user_id;
	
	public function __construct()
	{
		$this->table_name = 'pages'; // nazwa głównej tabeli modelu w bazie
		
		$status = new Status();
		$this->user_id = $status->get_value('user_id');

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND ' . $this->table_name . '.id = ' . intval($limit);
		
		$filter = empty($value) ? NULL : " AND (title LIKE '%" . $value . "%' OR contents LIKE '%" . $value . "%' OR caption LIKE '%" . $value . "%')";

		$query = 	"SELECT COUNT(*) AS licznik FROM " . $this->table_name . 
					" INNER JOIN categories ON categories.id = " . $this->table_name . ".category_id" .
					" INNER JOIN users ON users.id = " . $this->table_name . ".author_id" .
					" WHERE system_page = 0" . $condition . $filter;
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

		$query = 	"SELECT pages.*, categories.caption, users.user_login" .
					" FROM " . $this->table_name . 
					" INNER JOIN categories ON categories.id = " . $this->table_name . ".category_id" .
					" INNER JOIN users ON users.id = " . $this->table_name . ".author_id" .
					" WHERE " . $this->table_name . ".id=" . intval($id);
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
		$condition = empty($limit) ? NULL : ' AND ' . $this->table_name . '.id = ' . intval($limit);
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (title LIKE '%" . $_SESSION['list_filter'] . "%' OR contents LIKE '%" . $_SESSION['list_filter'] . "%' OR caption LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT pages.id, pages.title, pages.contents," .
					" categories.caption, users.user_login, pages.modified, pages.visible" .
					" FROM " . $this->table_name . 
					" INNER JOIN categories ON categories.id = " . $this->table_name . ".category_id" .
					" INNER JOIN users ON users.id = " . $this->table_name . ".author_id" .
					" WHERE system_page = 0" . $condition . $filter .
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
	
	public function Add($record_item)
	{
		$record_item['author_id'] = $this->user_id;
		
		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$record_item['main_page'] . "', '" . 
					$record_item['system_page'] . "', '" . 
					$record_item['category_id'] . "', '" . 
					mysql_real_escape_string(trim($record_item['title'])) . "', '" . 
					mysql_real_escape_string(trim($record_item['contents'])) . "', '" . 
					$record_item['author_id'] . "', '" . 
					$record_item['visible'] . "', '" . 
					$this->mySqlDateTime . "')";
		mysql_query($query);
		
		return mysql_affected_rows();
	}
	
	public function Edit($record_item, $id)
	{
		$record_item['author_id'] = $this->user_id;
		
		$query = "UPDATE " . $this->table_name . 
					" SET main_page='" . $record_item['main_page'] . 
					"', system_page='" . $record_item['system_page'] . 
					"', category_id='" . $record_item['category_id'] . 
					"', title='" . mysql_real_escape_string(trim($record_item['title'])) . 
					"', contents='" . mysql_real_escape_string(trim($record_item['contents'])) . 
					"', author_id='" . $record_item['author_id'] . 
					"', visible='" . $record_item['visible'] . 
					"', modified='" . $this->mySqlDateTime . 
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
			$this->row_item = $row;
			mysql_free_result($result);
		}
		return $this->row_item;
	}
	
	public function Remove($id)
	{
		$category_id = $this->GetCategoryId($id);
		
		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysql_query($query);

		$query = "DELETE FROM categories WHERE id=" . intval($category_id);
		mysql_query($query);

		return mysql_affected_rows();
	}
		
	public function GetAuthors()
	{
		$this->rows_list = array();

		$query = 	"SELECT id, user_login FROM users" .
					" ORDER BY id";

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
	
	public function GetCategories()
	{
		$this->rows_list = array();

		$query = 	"SELECT id, caption FROM categories" .
					" ORDER BY id";

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
		
	public function GetCategoryId($id)
	{
		$query = "SELECT category_id FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$category_id = $row['category_id'];
			mysql_free_result($result);
		}
		return $category_id;
	}	
}

?>