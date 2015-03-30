<?php

/*
 * Klasa odpowiedzialna za obsługę ustawień konfiguracyjnych w bazie
 */

class Settings
{
	public function get_config_key($key)
	{
		$config_value = NULL;

		$query = "SELECT * FROM configuration WHERE key_name='" . $key . "'";
		$result = mysql_query($query);
		if ($result) 
		{
			$row = mysql_fetch_assoc($result);
			$config_value = $row['key_value'];
			mysql_free_result($result);
		}
		return $config_value;
	}
}

?>