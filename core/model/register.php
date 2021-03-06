<?php

/*
 * Model - pobiera dane dla podstrony z bazy
 */
class Register_Model
{
	private $row_item;
	private $table_name;
	private $setting;
	
	private $mySqlDateTime;
	
	public function __construct()
	{
		$this->table_name = 'users'; // nazwa głównej tabeli modelu w bazie
		
		$this->setting = new Settings();

		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
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
	
	public function Exist($record_item)
	{
		$exist = 0;
		
		$query = 	"SELECT COUNT(*) AS licznik FROM " . $this->table_name . 
					" WHERE user_login = '" . $record_item['user_login'] .
					"' OR email = '" . $record_item['email'] .
					"' OR pesel = '" . $record_item['pesel'] . "'";
		$result = mysql_query($query);
		if ($result)
		{
			$row = mysql_fetch_assoc($result); 
			$exist = $row['licznik'];
			mysql_free_result($result);
		}
		return $exist;
	}
	
	public function Register($record_item)
	{
		$query = "INSERT INTO " . $this->table_name . " VALUES (NULL, '" . 
					mysql_real_escape_string($record_item['user_login']) . "', '" . 
					sha1($record_item['user_password']) . "', '" . 
					mysql_real_escape_string($record_item['imie']) . "', '" . 
					mysql_real_escape_string($record_item['nazwisko']) . "', '" . 
					mysql_real_escape_string($record_item['email']) . "', '3', '', '', '', '" .
					mysql_real_escape_string($record_item['pesel'])."', '', '" . 
					$this->mySqlDateTime . "', '', '', '', '1')";

		mysql_query($query);
		
		$num_rows = mysql_affected_rows();
		
		if ($num_rows == 1) // pomyslna rejestracja
		{
			$query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC LIMIT 0, 1";
			$result = mysql_query($query);

			$row = mysql_fetch_assoc($result); 
			$this->row_item = $row;
			mysql_free_result($result);
			
			$query = "SELECT * FROM admin_functions ORDER BY id";

			$result = mysql_query($query);

			if ($result)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$access = $row['module'] == 'users' ? 1 : 0;
					$sub_query = "INSERT INTO user_roles VALUES (NULL, '" . $this->row_item['id'] . "', '" . $row['id'] . "', '" . $access . "')";
					mysql_query($sub_query);
				} 
				mysql_free_result($result);
			}

			$base_domain = $this->setting->get_config_key('base_domain');
			$email_sender_name = $this->setting->get_config_key('email_sender_name');
			$email_sender_address = $this->setting->get_config_key('email_sender_address');
			$email_createcnt_subject = $this->setting->get_config_key('email_createcnt_subject');
			$email_createcnt_body_1 = $this->setting->get_config_key('email_createcnt_body_1');
			$email_createcnt_body_2 = $this->setting->get_config_key('email_createcnt_body_2');
			
			// wysyła e-mailem nowe hasło:
			$recipient = $record_item['email'];
			$mail_body = "Szanowny użytkowniku,\n\n".$email_createcnt_body_1."\n\n imię: ".$record_item['imie']."\n nazwisko: ".$record_item['nazwisko']."\n e-mail: ".$record_item['email']."\n\n login: ".$record_item['user_login']."\n hasło: ".$record_item['user_password']."\n\n".$email_createcnt_body_2."\n\nPozdrawiamy,\n\n".$base_domain."\n";
			$subject = $email_createcnt_subject;
			$header = "From: ". $email_sender_name . " <" . $email_sender_address . ">\r\n";
			$header = "MIME-Versio: 1.0\r\n" . "Content-type: text/html; charset=UTF-8\r\n" . $header;
			$mail_body = $this->convert_to_html($subject, $mail_body);
			mail($recipient, $subject, $mail_body, $header);

			return $this->row_item;
		}
		else // nieudana rejestracja
		{
			return NULL;
		}
	}
	
	private function convert_to_html($subject, $content)
	{
		$main_text = "<html><head><title>" . $subject . "</title></head><body><p>" . $content . "</p></body></html>";
		$main_text = str_replace("\n", "<br />", $main_text);
		return $main_text;
	}

	public function Store($record_object, $login_object)
	{
		foreach ($record_object as $k => $v)
		{
			if ($k == 'imie') $user_imie = $v;
			if ($k == 'nazwisko') $user_nazwisko = $v;
			if ($k == 'email') $user_email = $v;
			if ($k == 'user_login') $user_login = $v;
			if ($k == 'user_password') $user_password = $v;
		}
		foreach ($login_object as $k => $v)
		{
			if ($k == 'server') $record_item = $v;
			if ($k == 'session') $session_item = $v;
			if ($k == 'result') $result = empty($v) ? 0 : 1;
		}
		
		$query = "INSERT INTO registers VALUES (NULL, '" . 
					$record_item['HTTP_USER_AGENT'] . "', '" . 
					$record_item['REMOTE_ADDR'] . "', '" . 
					mysql_real_escape_string($user_imie) . "', '" . 
					mysql_real_escape_string($user_nazwisko) . "', '" . 
					mysql_real_escape_string($user_login) . "', '" . 
					mysql_real_escape_string($user_email) . "', '" . 
					mysql_real_escape_string($user_password) . "', '" . 
					$result . "', '" . 
					$this->mySqlDateTime . "')";
		mysql_query($query);
		
		return mysql_affected_rows();
	}
}

?>
