<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Discs_Model
{
	private $rows_list;
	private $row_item;
	
	private $mySqlDateTime;
	
	public function __construct()
	{
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function SetPages($value, $kind, $limit, $show_rows)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($value) ? NULL : " AND disc_name LIKE '%" . $value . "%'";

		$query = "SELECT COUNT(*) AS licznik FROM catalog_discs WHERE 1" . $condition . $filter;
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
		$this->rows_list = array();

		$query = "SELECT * FROM catalog_discs WHERE id=" . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$row['total_size'] = number_format($row['total_size'] / 1024 / 1024, 0, ',', '.') .' MB';
			$this->row_item = $row;
			mysql_free_result($result);
		}

		// wczytuje tablicę statystyk typów plików dla płyty:
		
		$query =	"SELECT type_name, count FROM catalog_typecounts" .
					" INNER JOIN catalog_filetypes ON catalog_filetypes.id = catalog_typecounts.type_id" .
					" WHERE disc_id=" . intval($id) .
					" ORDER BY count DESC";

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->rows_list[] = $row;
			} 
			mysql_free_result($result);
		}
		
		// dodaje statystykę do zwracanego rekordu:
		
		$this->row_item['statistics'] = $this->rows_list;

		return $this->row_item;
	}
	
	public function GetAll($limit, $params)
	{
		$condition = empty($limit) ? NULL : ' AND id = ' . intval($limit);
		
		$filter = empty($_SESSION['list_filter']) ? NULL : " AND disc_name LIKE '%" . $_SESSION['list_filter'] . "%'";

		$this->rows_list = array();

		$query = 	"SELECT * FROM catalog_discs WHERE 1" . $condition . $filter .
					" ORDER BY " . $params['sort_field'] . " " . $params['sort_order'] . 
					" LIMIT " . $params['start_from'] . ", " . $params['show_rows'];		

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$row['total_size'] = number_format($row['total_size'] / 1024 / 1024, 0, ',', '.') .' MB';
				$this->rows_list[] = $row;
			} 
			mysql_free_result($result);
		}
		
		return $this->rows_list;
	}
	
	public function GetTree($id)
	{
		$this->rows_list = array();
		
		$folders_list = array();
		$indexes = NULL;

		// pobiera rekordy-foldery:
		
		$query = 	"SELECT id, parent_id, folder_name AS name, 'Folder' AS type FROM catalog_folders" .
					" WHERE disc_id = " . intval($id);

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->rows_list[] = $row;
				$folders_list[] = $row['id'];
			} 
			mysql_free_result($result);
		}

		// pobiera rekordy-pliki:
		
		$indexes = implode(',', $folders_list);
		
		$query = 	"SELECT id, folder_id AS parent_id, file_name AS name, 'File' AS type FROM catalog_files" .
					" WHERE folder_id IN (" . $indexes . ")";

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
	
	public function GetDetails($id)
	{
		$this->row_item = array();

		$query = 	"SELECT disc_id, disc_name, disc_type, content_type, scan_date, folder_name," . 
					" file_name, type_name, file_size, modify_date, catalog_files.attribs" .
					" FROM catalog_files " . 
					" INNER JOIN catalog_folders ON catalog_folders.id = catalog_files.folder_id" .
					" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
					" INNER JOIN catalog_filetypes ON catalog_filetypes.id = catalog_files.type_id" .
					" WHERE catalog_files.id = " . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$row['id'] = $id;
			$row['file_size'] = number_format($row['file_size'] / 1024, 0, ',', '.') .' KB';
			$this->row_item = $row;
			mysql_free_result($result);
		}
		
		return $this->row_item;
	}
	
	public function GetFolder($id)
	{
		$this->row_item = array();

		$query = 	"SELECT disc_id, disc_name, disc_type, content_type, scan_date, folder_name," . 
					" create_date, attribs" .
					" FROM catalog_folders " . 
					" INNER JOIN catalog_discs ON catalog_discs.id = catalog_folders.disc_id" .
					" WHERE catalog_folders.id = " . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$row['id'] = $id;
			$this->row_item = $row;
			mysql_free_result($result);
		}
		
		return $this->row_item;
	}
	
	public function Add($record_item)
	{
		$disc_id = 0;
		$parent_id = 0;
		$folders_count = 0;
		$files_count = 0;
		$total_size = 0;
		$packet_data = $record_item['scan_result'];

		// dopisuje dysk i pobiera jego id:
		
		$query = "INSERT INTO catalog_discs VALUES (NULL, '" . 
					mysql_real_escape_string($record_item['disc_name']) . "', '" . 
					mysql_real_escape_string($record_item['disc_type']) . "', '" . 
					mysql_real_escape_string($record_item['content_type']) . "', '" . 
					"0', '0', '0', '" . $this->mySqlDateTime . "')";
		mysql_query($query);
		
		$query = "SELECT id FROM catalog_discs ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$disc_id = $row['id'];
			mysql_free_result($result);
		}
		
		// dopisuje folder root i pobiera jego id:

		$query = "INSERT INTO catalog_folders VALUES (NULL, '" . 
					$disc_id . "', '" . $parent_id . "', '/', '" . $this->mySqlDateTime . "', 'V')";
		mysql_query($query);
		
		$query = "SELECT id FROM catalog_folders ORDER BY id DESC LIMIT 0, 1";
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$parent_id = $row['id'];
			mysql_free_result($result);
		}

		// pobiera kolejne pakiety danych z całego zestawu danych:
		
		do
		{
			$packet_kind = substr($packet_data, 0, 3);
			switch ($packet_kind)
			{
				case '{D}': // directory
				
					$packet_end = strpos($packet_data, '{/D}') + 4;
					$packet_item = substr($packet_data, 0, $packet_end);
					$packet_data = substr($packet_data, $packet_end, strlen($packet_data));
					
					$p_i_begin = strpos($packet_item, '{P}') + 3;
					$p_i_end = strpos($packet_item, '{/P}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_parent_id = $p_i_value + $parent_id;

					$p_i_begin = strpos($packet_item, '{N}') + 3;
					$p_i_end = strpos($packet_item, '{/N}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_name = $p_i_value;
					
					$p_i_begin = strpos($packet_item, '{C}') + 3;
					$p_i_end = strpos($packet_item, '{/C}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_date = $p_i_value;
					
					$p_i_begin = strpos($packet_item, '{A}') + 3;
					$p_i_end = strpos($packet_item, '{/A}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_attrib = $p_i_value;
					
					// dopisuje folder:
					
					$query = "INSERT INTO catalog_folders VALUES (NULL, '" . 
								$disc_id . "', '" . 
								$current_parent_id . "', '" . 
								mysql_real_escape_string($current_name) . "', '" . 
								$current_date . "', '" .
								$current_attrib . "')";
					mysql_query($query);
					
					$folders_count++;

					break;

				case '{F}': // file
				
					$packet_end = strpos($packet_data, '{/F}') + 4;
					$packet_item = substr($packet_data, 0, $packet_end);
					$packet_data = substr($packet_data, $packet_end, strlen($packet_data));

					$p_i_begin = strpos($packet_item, '{P}') + 3;
					$p_i_end = strpos($packet_item, '{/P}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_parent_id = $p_i_value + $parent_id;

					$p_i_begin = strpos($packet_item, '{N}') + 3;
					$p_i_end = strpos($packet_item, '{/N}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_name = $p_i_value;
					
					$p_i_begin = strpos($packet_item, '{S}') + 3;
					$p_i_end = strpos($packet_item, '{/S}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_size = $p_i_value;
					
					$p_i_begin = strpos($packet_item, '{T}') + 3;
					$p_i_end = strpos($packet_item, '{/T}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_type = $p_i_value;
					
					$p_i_begin = strpos($packet_item, '{M}') + 3;
					$p_i_end = strpos($packet_item, '{/M}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_date = $p_i_value;
					
					$p_i_begin = strpos($packet_item, '{A}') + 3;
					$p_i_end = strpos($packet_item, '{/A}');
					$p_i_value = substr($packet_item, $p_i_begin, $p_i_end - $p_i_begin);
					
					$current_attrib = $p_i_value;

					// szuka w słowniku typu pliku:
					
					$query = "SELECT id FROM catalog_filetypes WHERE type_name = '" . $current_type . "'";
					$result = mysql_query($query);
					if ($result)
					{
						$row = mysql_fetch_assoc($result); 
						$current_type_id = $row['id'];
						mysql_free_result($result);
					}

					// jeśli nie znalazł, dopisuje i pobiera id:
					
					if (!isset($current_type_id))
					{
						$query = "INSERT INTO catalog_filetypes VALUES (NULL, '" . mysql_real_escape_string($current_type) . "')";
						mysql_query($query);

						$query = "SELECT id FROM catalog_filetypes ORDER BY id DESC LIMIT 0, 1";
						$result = mysql_query($query);
						if ($result)
						{
							$row = mysql_fetch_assoc($result); 
							$current_type_id = $row['id'];
							mysql_free_result($result);
						}
					}
					
					// dopisuje plik:
					
					$query = "INSERT INTO catalog_files VALUES (NULL, '" . 
								$current_parent_id . "', '" . 
								mysql_real_escape_string($current_name) . "', '" . 
								$current_size . "', '" .
								$current_type_id . "', '" .
								$current_date . "', '" .
								$current_attrib . "')";
					mysql_query($query);

					$files_count++;
					$total_size += $current_size;
					
					// szuka w statystyce płyty typu pliku:
					
					$query =	"SELECT id FROM catalog_typecounts" .
								" WHERE disc_id = " . $disc_id .
								" AND type_id = " . $current_type_id;
					$result = mysql_query($query);
					if ($result)
					{
						$row = mysql_fetch_assoc($result); 
						$type_count_id = $row['id'];
						mysql_free_result($result);
					}

					if (isset($type_count_id)) // znalazł statystykę typu pliku - powiększa ją
					{
						$query = "UPDATE catalog_typecounts SET count = count + 1 WHERE id = " . $type_count_id;
						mysql_query($query);
					}
					else // nie znalazł statystyki typu pliku - inicjuje ją
					{
						$query = "INSERT INTO catalog_typecounts VALUES (NULL, '" . $disc_id . "', '" . $current_type_id . "', '1')";
						mysql_query($query);
					}

					break;
			}
		}
		while (!empty($packet_data));
		
		// aktualizuje dane o dysku:
		
		$query = "UPDATE catalog_discs" .  
					" SET folders_count='" . $folders_count . 
					"', files_count='" . $files_count . 
					"', total_size='" . $total_size . 
					"' WHERE id=" . intval($disc_id);
		mysql_query($query);
		
		return mysql_affected_rows();
	}
	
	public function Edit($record_item, $id)
	{
		$query = "UPDATE catalog_discs" .  
					" SET disc_name='" . mysql_real_escape_string($record_item['disc_name']) . 
					"', disc_type='" . mysql_real_escape_string($record_item['disc_type']) . 
					"', content_type='" . mysql_real_escape_string($record_item['content_type']) . 
					"' WHERE id=" . intval($id);
		mysql_query($query);
		
		return mysql_affected_rows();
	}
	
	public function GetLast()
	{
		$this->row_item = array();

		$query = "SELECT * FROM catalog_discs ORDER BY id DESC LIMIT 0, 1";
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
		$folders_list = array();
		$indexes = NULL;
		
		$query = "SELECT id FROM catalog_folders WHERE disc_id = " . intval($id);
		$result = mysql_query($query);
		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$folders_list[] = $row['id'];
			} 
			mysql_free_result($result);
		}
		
		$indexes = implode(',', $folders_list);

		$query = "DELETE FROM catalog_typecounts WHERE disc_id = " . intval($id);
		mysql_query($query);
		
		$query = "DELETE FROM catalog_files WHERE folder_id IN (" . $indexes . ")";
		mysql_query($query);

		$query = "DELETE FROM catalog_folders WHERE disc_id = " . intval($id);
		mysql_query($query);
		
		$query = "DELETE FROM catalog_discs WHERE id = " . intval($id);
		mysql_query($query);

		return mysql_affected_rows();
	}
}

?>