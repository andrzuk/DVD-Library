<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Images_Model
{
	private $rows_list;
	private $row_item;
	private $table_name;
	
	private $mySqlDateTime;
	
	public function __construct()
	{
		$this->table_name = 'images'; // nazwa głównej tabeli modelu w bazie
		
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$data_type = isset($_SESSION['mode']) ? ' AND section_id = ' . $_SESSION['mode'] : NULL;
		
		$filter = empty($value) ? NULL : " AND (picture_description LIKE '%" . $value . "%' OR file_name LIKE '%" . $value . "%')";

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
		
		$data_type = isset($_SESSION['mode']) ? ' AND section_id = ' . $_SESSION['mode'] : NULL;
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND (picture_description LIKE '%" . $_SESSION['list_filter'] . "%' OR file_name LIKE '%" . $_SESSION['list_filter'] . "%')";

		$this->rows_list = array();

		$query = 	"SELECT * FROM " . $this->table_name . " WHERE 1" . $condition . $data_type . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$row['file_size'] = number_format($row['file_size'] / 1024, 0, ',', '.') .' KB';
				$this->rows_list[] = $row;
			} 
			mysql_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function Add($record_item)
	{
		// dopisuje rekord do bazy:
		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					$record_item['section_id'] . "', '" . 
					$record_item['owner_id'] . "', '" . 
					NULL . "', '" . 
					NULL . "', '" . 
					NULL . "', '" . 
					NULL . "', '" . 
					NULL . "', '" . 
					mysql_real_escape_string($record_item['picture_description']) . "', '" . 
					$record_item['active'] . "', '" . 
					$this->mySqlDateTime . "')";
		mysql_query($query);
					
		return mysql_affected_rows();
	}
	
	public function AddFile($record_item, $file_item)
	{
		if (substr($file_item['type'], 0, 5) == 'image') // plik graficzny
		{
			// odczytuje rozmiar oryginalnego obrazka:
			list($width, $height) = getimagesize($file_item['tmp_name']); 
			
			// dopisuje rekord do bazy:
			$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
						$record_item['section_id'] . "', '" . 
						$record_item['owner_id'] . "', '" . 
						$file_item['type'] . "', '" . 
						$file_item['name'] . "', '" . 
						$file_item['size'] . "', '" . 
						$width . "', '" . 
						$height . "', '" . 
						mysql_real_escape_string($record_item['picture_description']) . "', '" . 
						$record_item['active'] . "', '" . 
						$this->mySqlDateTime . "')";
			mysql_query($query);
			
			// odczytuje id dodanego rekordu:
			$query = "SELECT id FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
			$result = mysql_query($query);
			if ($result)
			{
				$row = mysql_fetch_assoc($result); 
				$obrazek_id = $row['id'];
				mysql_free_result($result);
			}
			
			// zapisuje plik na dysku:
			$target = GALLERY_DIR . IMG_DIR . $obrazek_id;

			// zapisuje oryginalny obrazek na serwer:
			move_uploaded_file($file_item['tmp_name'], $target);
			
			return mysql_affected_rows();
		}
		else // nie graficzny plik
		{
			return -1;
		}
	}
	
	public function Edit($record_item, $id)
	{
		$query = "UPDATE " . $this->table_name . 
					" SET section_id='" . $record_item['section_id'] . 
					"', owner_id='" . $record_item['owner_id'] . 
					"', picture_description='" . mysql_real_escape_string($record_item['picture_description']) . 
					"', active='" . $record_item['active'] . 
					"', modified='" . $this->mySqlDateTime . 
					"' WHERE id=" . intval($id);
		mysql_query($query);

		return mysql_affected_rows();
	}
	
	public function EditFile($record_item, $file_item, $id)
	{
		if (substr($file_item['type'], 0, 5) == 'image') // plik graficzny
		{
			// odczytuje rozmiar oryginalnego obrazka:
			list($width, $height) = getimagesize($file_item['tmp_name']); 
			
			// uaktualnia rekord w bazie:
			$query = "UPDATE " . $this->table_name . 
						" SET section_id='" . $record_item['section_id'] . 
						"', owner_id='" . $record_item['owner_id'] . 
						"', file_format='" . $file_item['type'] . 
						"', file_name='" . $file_item['name'] . 
						"', file_size='" . $file_item['size'] . 
						"', picture_width='" . $width . 
						"', picture_height='" . $height . 
						"', picture_description='" . mysql_real_escape_string($record_item['picture_description']) . 
						"', active='" . $record_item['active'] . 
						"', modified='" . $this->mySqlDateTime . 
						"' WHERE id=" . intval($id);
			mysql_query($query);
			
			// zapisuje plik na dysku:
			$target = GALLERY_DIR . IMG_DIR . $id;

			// zapisuje oryginalny obrazek na serwer:
			move_uploaded_file($file_item['tmp_name'], $target);
			
			return mysql_affected_rows();
		}
		else // nie graficzny plik
		{
			return -1;
		}
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
		$query = "DELETE FROM " . $this->table_name . " WHERE id=" . intval($id);
		mysql_query($query);

		// usuniecie pliku z dysku serwera:
		$delete_result = unlink(GALLERY_DIR . IMG_DIR . $id);
		
		return mysql_affected_rows();
	}	
	
	public function Download($id)
	{
		// pobiera informacje o pliku:
		$query = "SELECT file_name, file_format FROM " . $this->table_name . " WHERE id=" . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$file_name = $row['file_name'];
			$file_format = $row['file_format'];
			mysql_free_result($result);
		}
		$file_name = str_replace(" ", "_", $file_name);
		
		$picture_name = GALLERY_DIR . IMG_DIR . $id;
		
		// wczytuje plik z serwera:
		$fp = fopen($picture_name, 'rb');
		$image_data = fread($fp, filesize($picture_name));
		fclose($fp);
		
		// wysyła plik do przeglądarki:
		header('Content-disposition: attachment; filename='. $file_name);
		header('Content-type: '. $file_format .'; charset=utf-8');
		
		// wysyła dane:
		if (IsSet($image_data)) echo $image_data;
		
		// przerywa, aby nie dołączać treści strony:
		die;
	}
}

?>