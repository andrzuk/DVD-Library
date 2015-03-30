<?php

require_once(dirname(__FILE__) . '/../../config/config.php');
require_once(dirname(__FILE__) . '/../../lib/database.php');

$connection = new Database();
$connection->init(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$connection->open();

$rows_list = array();

$query = "SELECT id, disc_name, disc_type, folders_count, files_count, total_size, scan_date FROM catalog_discs" .
         " WHERE content_type = '" . $_GET['segment'] . "'" .
		 " ORDER BY disc_name";

$result = mysql_query($query);

if ($result)
{
	while ($row = mysql_fetch_assoc($result))
	{
		$row['total_size'] = number_format($row['total_size'] / 1024 / 1024, 0, ',', '.') .' MB';
		$rows_list[] = $row;
	} 
	mysql_free_result($result);
}

echo json_encode($rows_list);

$connection->close();

?>
