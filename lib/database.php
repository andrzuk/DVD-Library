<?php

/*
 * Klasa odpowiedzialna za połączenie z bazą danych
 */

class Database
{
	private $host;
	private $database;
	private $user;
	private $password;

	public function __construct()
	{
		error_reporting(0);
	}
	
	public function init($host, $db, $usr, $pwd)
	{
		$this->host = $host;
		$this->database = $db;
		$this->user = $usr;
		$this->password = $pwd;
	}

	public function open()
	{
		mysql_connect($this->host, $this->user, $this->password) or $connection_error = TRUE;
		mysql_select_db($this->database) or $connection_error = TRUE;

		mysql_query ('SET NAMES utf8');
		mysql_query ('SET CHARACTER_SET utf8_unicode_ci');

		if (isset($connection_error))
		{
			include dirname(__FILE__) . '/../' . HELP_DIR . 'index.php';
			die;
		}
	}

	public function close()
	{
		mysql_close();
	}
}

?>
