<?php

/*
 * Klasa odpowiedzialna za rejestrację wejść na stronę
 */

class Visitors
{
	private $mySqlDateTime;
	
	public function __construct()
	{
		$timestampInSeconds = $_SERVER['REQUEST_TIME'];
		$this->mySqlDateTime = date("Y-m-d H:i:s", $timestampInSeconds);
	}
	
	public function register()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$ref = $_SERVER['HTTP_REFERER'];
		$uri = $_SERVER["REQUEST_URI"];

		$query = "INSERT INTO visitors VALUES (NULL, '".$ip."', '".$ref."', '".$uri."', '".$this->mySqlDateTime."')";
		// dopisanie otwarcia strony do bazy:
		mysql_query($query);
	}
	
	public function get_licznik_info()
	{
		$CResult = array();

		$query = "SELECT id, time FROM visitor_counter WHERE visitor_ip = '". $_SERVER['REMOTE_ADDR'] ."'";
		$result = mysql_query($query);
		if ($result) 
		{
			$row = mysql_fetch_assoc($result);
			$guest_id = $row['id'];
			$guest_time = $row['time'];
			mysql_free_result($result);
		}
		if (empty($guest_id) && empty($guest_time)) // nie znalazl w tablicy - trzeba dopisac
		{
			$query = "INSERT INTO visitor_counter VALUES ";
			$query .= "(NULL, '".$_SERVER['REMOTE_ADDR']."', '1', '".time()."', '".$this->mySqlDateTime."');";
			mysql_query($query);
		}
		else // znalazl w tablicy - trzeba zwiekszyc licznik
		{
			if ($guest_time < time() - 60 * 10) // odstep czasowy 10 min
			{
				$query = "UPDATE visitor_counter SET count = (count + 1), time = '".time()."', date = '".$this->mySqlDateTime."'";
				$query .= " WHERE id = ". $guest_id;
				mysql_query($query);
			}
		}
		$query = "SELECT SUM(count) AS licznik FROM visitor_counter";
		$result = mysql_query($query);
		if ($result) 
		{
			$row = mysql_fetch_assoc($result);
			$licznik = $row['licznik'];
			mysql_free_result($result);
		}
		$query = "SELECT COUNT(*) AS licznik FROM visitor_counter WHERE date > '". date('Y-m-d') ."'";
		$result = mysql_query($query);
		if ($result) 
		{
			$row = mysql_fetch_assoc($result);
			$dzisiaj = $row['licznik'];
			mysql_free_result($result);
		}

		$CResult[0] = 0; // odslony
		$CResult[1] = $licznik; // unikalne
		$CResult[2] = $dzisiaj; // dzisiaj
		$CResult[3] = 0; // wczoraj
		$CResult[4] = 0; // on-line
		$CResult[5] = ''; // najwiecej - kiedy
		$CResult[6] = 0; // najwiecej - ile

		return $CResult;
	}
	
	public function get_visitors($count)
	{
		$list = array();
		$item = array();
		
		$query = "SELECT visitor_ip, COUNT(visitor_ip) AS licznik FROM visitors GROUP BY visitor_ip ORDER BY id DESC LIMIT 0, ".$count;
		$result = mysql_query($query);
		if ($result) 
		{
			while ($row = mysql_fetch_assoc($result)) 
			{
				$item = array(
					$row['visitor_ip'], 
					$row['licznik']
				);
				$list[] = $item;
			}
			mysql_free_result($result);
		}
		
		return $list;
	}
	
	public function get_online()
	{
		$session = session_id();
		$time = time();
		$time_check = $time - 60 * 10; // ustawia czas sesji na 10 min

		$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
				
		if ($session != "")
		{
			$sql = "SELECT * FROM user_online WHERE session = '$session'";
			$result = mysql_query($sql);

			$count = mysql_num_rows($result);

			if ($count == "0")
			{
				$sql1 = "INSERT INTO user_online (session, time, user_id) VALUES ('$session', '$time', '". $user_id ."')";
				$result1 = mysql_query($sql1);
			}
			else 
			{
				$sql2 = "UPDATE user_online SET time = '$time', user_id = '". $user_id ."' WHERE session = '$session'";
				$result2 = mysql_query($sql2);
			}
		}

		$sql3 = "SELECT * FROM user_online";
		$result3 = mysql_query($sql3);

		$count_user_online = mysql_num_rows($result3);
		
		// po 10 min usuwa sesje
		$sql4 = "DELETE FROM user_online WHERE time < $time_check";
		$result4 = mysql_query($sql4);

		return $count_user_online;
	}
	
	public function get_logged()
	{
		$logged_user_list = array();
		
		$sql = "SELECT user_login FROM user_online".
				" INNER JOIN users ON user_online.user_id = users.id";
		$result = mysql_query($sql);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$logged_user_list[] = $row['user_login'];
			}
			mysql_free_result($result);
		}
		
		return $logged_user_list;
	}
}

?>
