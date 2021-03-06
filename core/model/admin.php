<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Admin_Model
{
	private $table_params;
	private $table_counter;
	
	public function __construct()
	{
		$this->table_counter = array();
		
		$this->table_params = array(
			array(
				'module' => 'Konfiguracja',
				'table' => 'configuration',
				'condition' => '1',
			),
			array(
				'module' => 'Użytkownicy',
				'table' => 'users',
				'condition' => '1',
			),
			array(
				'module' => 'Odwiedziny',
				'table' => 'visitors',
				'condition' => '1',
			),
			array(
				'module' => 'Galeria',
				'table' => 'images',
				'condition' => '1',
			),
			array(
				'module' => 'Dokumenty',
				'table' => 'documents',
				'condition' => '1',
			),
			array(
				'module' => 'Kategorie',
				'table' => 'categories',
				'condition' => '1',
			),
			array(
				'module' => 'Strony',
				'table' => 'pages',
				'condition' => 'system_page = 0',
			),
			array(
				'module' => 'Opisy',
				'table' => 'pages',
				'condition' => 'system_page = 1',
			),
			array(
				'module' => 'Wiadomości',
				'table' => 'user_messages',
				'condition' => 'requested = 1',
			),
			array(
				'module' => 'Wyszukiwania',
				'table' => 'searches',
				'condition' => '1',
			),
			array(
				'module' => 'Rejestracje',
				'table' => 'registers',
				'condition' => 'result = 1',
			),
			array(
				'module' => 'Logowania',
				'table' => 'logins',
				'condition' => 'user_id > 0',
			),
			array(
				'module' => 'Hasła',
				'table' => 'reminds',
				'condition' => '1',
			),
			array(
				'module' => 'Funkcje',
				'table' => 'admin_functions',
				'condition' => '1',
			),
			array(
				'module' => 'Role',
				'table' => 'user_roles',
				'condition' => 'access = 1',
			),
			array(
				'module' => 'Albumy',
				'table' => 'catalog_discs',
				'condition' => '1',
			),
		);
	}
	
	public function GetTablesCounts()
	{
		foreach ($this->table_params as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'module') $module = $value;
				if ($key == 'table') $table = $value;
				if ($key == 'condition') $condition = $value;
			}
			
			$query = "SELECT COUNT(*) AS licznik FROM " . $table . " WHERE " . $condition;
			$result = mysql_query($query);
			if ($result)
			{
				$row = mysql_fetch_assoc($result); 
				$this->table_counter[] = $row['licznik'];
				mysql_free_result($result);
			}
		}
		
		return $this->table_counter;
	}
}

?>