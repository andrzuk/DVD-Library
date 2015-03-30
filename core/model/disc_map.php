<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Disc_Map_Model
{
	private $row_item;
	private $rows_list;
	private $table_name;
	
	private $segments;
	
	public function __construct()
	{
		$this->table_name = 'catalog_discs'; // nazwa głównej tabeli modelu w bazie
		$this->segments = array('Muzyka', 'Filmy', 'Wideoklipy', 'Sport');
	}
	
	public function GetLibrary()
	{
		$this->rows_list = array();

		$query = "SELECT content_type, COUNT(content_type) AS licznik FROM " . $this->table_name . " GROUP BY content_type";

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
	
	public function GetStatistics()
	{
		$this->row_item = array();

		$all_total_discs = 0;
		$all_total_folders = 0;
		$all_total_files = 0;
		$all_total_size = 0;
		
		foreach ($this->segments as $k => $content_type)
		{
			$query = "SELECT COUNT(*) AS licznik FROM catalog_discs WHERE content_type = '" . $content_type . "'";
			$result = mysql_query($query);
			if ($result)
			{
				$row = mysql_fetch_assoc($result);
				$total_discs = $row['licznik'];
				mysql_free_result($result);
			}
			
			$query = "SELECT COUNT(*) AS licznik FROM catalog_folders WHERE disc_id IN (" . 
					 "SELECT id FROM catalog_discs WHERE content_type = '" . $content_type . "')";
			$result = mysql_query($query);
			if ($result)
			{
				$row = mysql_fetch_assoc($result);
				$total_folders = $row['licznik'];
				mysql_free_result($result);
			}
			
			$query = "SELECT COUNT(*) AS licznik FROM catalog_files WHERE folder_id IN (" . 
					 "SELECT id FROM catalog_folders WHERE disc_id IN (" .
					 "SELECT id FROM catalog_discs WHERE content_type = '" . $content_type . "'))";
			$result = mysql_query($query);
			if ($result)
			{
				$row = mysql_fetch_assoc($result);
				$total_files = $row['licznik'];
				mysql_free_result($result);
			}
			
			$query = "SELECT SUM(file_size) AS licznik FROM catalog_files WHERE folder_id IN (" . 
					 "SELECT id FROM catalog_folders WHERE disc_id IN (" .
					 "SELECT id FROM catalog_discs WHERE content_type = '" . $content_type . "'))";
			$result = mysql_query($query);
			if ($result)
			{
				$row = mysql_fetch_assoc($result);
				$total_size = $row['licznik'];
				mysql_free_result($result);
			}
			
			$segment_row = array(
				'type' => $content_type,
				'discs' => number_format($total_discs, 0, ',', '.'),
				'folders' => number_format($total_folders, 0, ',', '.'),
				'files' => number_format($total_files, 0, ',', '.'),
				'size' => number_format($total_size / 1024 / 1024, 0, ',', '.') .' MB',
			);
			
			$all_total_discs += $total_discs;
			$all_total_folders += $total_folders;
			$all_total_files += $total_files;
			$all_total_size += $total_size;
			
			$this->row_item[] = $segment_row;
		}
		
		$segment_row = array(
			'type' => '*',
			'discs' => number_format($all_total_discs, 0, ',', '.'),
			'folders' => number_format($all_total_folders, 0, ',', '.'),
			'files' => number_format($all_total_files, 0, ',', '.'),
			'size' => number_format($all_total_size / 1024 / 1024, 0, ',', '.') .' MB',
		);
		
		$this->row_item[] = $segment_row;
		
		return $this->row_item;
	}
}

?>