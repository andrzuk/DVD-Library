<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Profile_Model
{
	private $row_item;
	private $table_name;
	
	public function __construct()
	{
		$this->table_name = 'users'; // nazwa głównej tabeli modelu w bazie
	}
	
	public function GetOne($id)
	{
		include APP_DIR . 'model/users.php';

		$user_object = new Users_Model();

		$result = $user_object->GetOne($id);
		
		return $result;
	}
	
	public function GetDetails($id)
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
			$this->row_item = $row;
			mysql_free_result($result);
		}
		return $this->row_item;
	}	
}

?>
