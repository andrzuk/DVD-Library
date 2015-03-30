<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Map_Model
{
	private $row_item;
	private $rows_list;
	private $table_name;
	
	public function __construct()
	{
		$this->table_name = 'categories'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function GetTree()
	{
		$this->rows_list = array();

		$query = "SELECT * FROM " . $this->table_name . " ORDER BY id";

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
}

?>