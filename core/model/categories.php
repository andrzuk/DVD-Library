<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Categories_Model
{
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	private $user_id;

	public function __construct()
	{
		$this->table_name = 'categories'; // nazwa głównej tabeli modelu w bazie
		
		$status = new Status();
		$this->user_id = $status->get_value('user_id');

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($value) ? NULL : " AND (caption LIKE '%" . $value . "%' OR link LIKE '%" . $value . "%')";

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
			$this->row_item = $row;
			mysql_free_result($result);
		}
		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (caption LIKE '%" . $_SESSION['list_filter'] . "%' OR link LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $filter .
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
		$target = isset($record_item['target']) ? 1 : 0;

		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$record_item['level'] . "', '" . 
					$record_item['parent_id'] . "', '" . 
					$record_item['permission'] . "', '" . 
					$record_item['item_order'] . "', '" . 
					mysql_real_escape_string(trim($record_item['caption'])) . "', '" . 
					mysql_real_escape_string(trim($record_item['link'])) . "', '" . 
					$record_item['icon_id'] . "', '" . 
					$record_item['page_id'] . "', '" . 
					$record_item['visible'] . "', '" . 
					$target . "', '" . 
					$this->mySqlDateTime . "')";
		mysql_query($query);
		
		if (mysql_affected_rows()) $result = $this->UpdateLink();
		
		$query = "SELECT id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$category_id = $row['id'];
			mysql_free_result($result);
		}

		$query = "INSERT INTO pages VALUES (NULL, '0', '0', '" . $category_id . "', '" . 
					mysql_real_escape_string(trim($record_item['caption'])) . "', '', '" . 
					$this->user_id . "', '1', '" . 
					$this->mySqlDateTime . "')";
		mysql_query($query);

		return $result;
	}
	
	public function Edit($record_item, $id)
	{
		$target = isset($record_item['target']) ? 1 : 0;
		
		$query = "UPDATE " . $this->table_name . 
					" SET level='" . $record_item['level'] . 
					"', parent_id='" . $record_item['parent_id'] . 
					"', permission='" . $record_item['permission'] . 
					"', item_order='" . $record_item['item_order'] . 
					"', caption='" . mysql_real_escape_string(trim($record_item['caption'])) . 
					"', link='" . mysql_real_escape_string(trim($record_item['link'])) . 
					"', visible='" . $record_item['visible'] . 
					"', target='" . $target . 
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
	
	public function UpdateLink()
	{
		$query = "SELECT id, link FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$id = $row['id'];
			$link = $row['link'];
			mysql_free_result($result);
		}
		
		$sub_link = 'index.php?route=category&id=';
		
		if (strpos($link, $sub_link) !== FALSE)
		{
			$link = $sub_link . intval($id);

			$query = "UPDATE " . $this->table_name . " SET link='". $link ."' WHERE id=" . intval($id);
			mysql_query($query);
		}

		return mysql_affected_rows();
	}
	
	public function Remove($id)
	{
		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysql_query($query);

		$query = "DELETE FROM pages WHERE category_id=" . intval($id);
		mysql_query($query);

		return mysql_affected_rows();
	}
	
	public function MoveUp($id)
	{
		$query = "SELECT item_order FROM " . $this->table_name . " WHERE id = ". intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result);
			$item_order = $row['item_order'];
			mysql_free_result($result);
		}
		
		if ($item_order > 1)
		{
			$query = "UPDATE " . $this->table_name . " SET item_order = ". intval($item_order - 1) . 
						", modified='" . $this->mySqlDateTime . "' WHERE id = ". intval($id);
			$result = mysql_query($query);
		}

		return mysql_affected_rows();
	}
	
	public function MoveDown($id)
	{
		$query = "SELECT item_order FROM " . $this->table_name . " WHERE id = ". intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result);
			$item_order = $row['item_order'];
			mysql_free_result($result);
		}

		$query = "SELECT COUNT(*) as items_count FROM " . $this->table_name;
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result);
			$items_count = $row['items_count'];
			mysql_free_result($result);
		}
		
		if ($item_order < $items_count)
		{
			$query = "UPDATE " . $this->table_name . " SET item_order = ". intval($item_order + 1) . 
						", modified='" . $this->mySqlDateTime . "' WHERE id = ". intval($id);
			$result = mysql_query($query);
		}

		return mysql_affected_rows();
	}
	
	public function GetParents()
	{
		$this->rows_list = array();

		$query = 	"SELECT id, caption FROM " . $this->table_name .
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
	
	public function GetOrders()
	{
		$this->rows_list = array();

		$query = 	"SELECT item_order FROM " . $this->table_name .
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
	
	public function GetPageId($id)
	{
		$query = "SELECT id FROM pages WHERE category_id=" . intval($id) . " ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$page_id = $row['id'];
			mysql_free_result($result);
		}
		return $page_id;
	}	
}

?>