<?php

/*
 * Klasa odpowiedzialna za obsługę nazw hostów na podst. adresów IP
 */

class Hosts
{
	public function find_host_name($host_address)
	{
		$host_name = NULL;

		$query = "SELECT server_name FROM hosts WHERE server_ip = '". $host_address ."'";
		$result = mysql_query($query);
		if ($result) 
		{
			if (mysql_num_rows($result) == 1)
			{
				$row = mysql_fetch_assoc($result);
				$host_name = $row['server_name'];
			}
			mysql_free_result($result);
		}
		if (empty($host_name)) // nie znalazł w tablicy - trzeba dopisać
		{
			$host_name = gethostbyaddr($host_address);
			$query = "INSERT INTO hosts VALUES (NULL, '". $host_address ."', '". $host_name ."')";
			mysql_query($query);
		}
		
		return $host_name;
	}
}

?>