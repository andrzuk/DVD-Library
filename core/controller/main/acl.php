<?php

/*
 * Klasa odpowiedzialna za sprawdzanie praw dostępu dla użytkownika na podst. tzw. Access Control List
 */

class AccessControlList
{
	private $user_id;
	private $function_id;
	private $module;
	private $access;
	
	public function __construct($module)
	{
		$this->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
		$this->module = $module;
	}
	
	public function available()
	{
		$query =	"SELECT access FROM user_roles" .
					" INNER JOIN admin_functions ON admin_functions.id = user_roles.function_id" .
					" WHERE user_id = " . intval($this->user_id) . 
					" AND module = '" . $this->module . "'";
		
		$result = mysql_query($query);

		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$this->access = $row['access'];
			mysql_free_result($result);
		}
		
		return $this->access;
	}
}

?>