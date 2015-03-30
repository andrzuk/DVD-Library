<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Profile_View
{
	/*
	 * Formularz
	 */
	 
	public function ShowDetails($row, $columns)
	{
		include APP_DIR . 'view/users.php';

		$user_object = new Users_View();

		$result = $user_object->ShowDetails($row, $columns);
		
		return $result;
	}	
}

?>
