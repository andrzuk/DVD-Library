<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Login_Model
{
	private $row_item;
	
	private $mySqlDateTime;
	
	public function __construct()
	{
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function Login($record_item)
	{
		// weryfikuje uzytkownika:
		
		$query = 	"SELECT * FROM users".
					" WHERE (user_login='". $record_item['user_login'] ."'".
					" OR email='". $record_item['user_login'] ."'".
					" OR pesel='". $record_item['user_login'] ."')".
					" AND user_password='". sha1($record_item['user_password']) ."'".
					" AND active='1'";

		$result = mysql_query($query);
		
		if ($result)
		{
			$num_rows = mysql_num_rows($result);

			if ($num_rows == 1) // pomyslne zalogowanie (weryfikacja OK)
			{
				$row = mysql_fetch_assoc($result); 
				$this->row_item = $row;
				mysql_free_result($result);

				// rejestruje date i czas logowania uzytkownika:
				
				$query = "UPDATE users SET data_logowania='".$this->mySqlDateTime."'".
						 " WHERE id=".intval($row['id']);
						 
				mysql_query($query);
				
				return $this->row_item;
			}
			else // nieudana weryfikacja
			{
				return NULL;
			}
		}
		else // nieudana weryfikacja
		{
			return NULL;
		}
	}
	
	public function Store($record_object, $login_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'user_login') $user_login = $v;
			if ($k == 'user_password') $user_password = $v;
		}
		foreach ($login_object as $k => $v)
		{
			if ($k == 'server') $record_item = $v;
			if ($k == 'session') $session_item = $v;
		}
		
		$query = "INSERT INTO logins VALUES (NULL, '" . 
					$record_item['HTTP_USER_AGENT'] . "', '" . 
					$record_item['REMOTE_ADDR'] . "', '" . 
					$session_item['user_id'] . "', '" . 
					mysql_real_escape_string($user_login) . "', '" . 
					mysql_real_escape_string($user_password) . "', '" . 
					$this->mySqlDateTime . "')";
		mysql_query($query);
		
		return mysql_affected_rows();
	}
}

?>
