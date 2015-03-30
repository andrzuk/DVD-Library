<?php

require_once(dirname(__FILE__) . '/../../config/config.php');
require_once(dirname(__FILE__) . '/../../lib/database.php');

$connection = new Database();
$connection->init(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$connection->open();

$rows_list = array();

$query = "SELECT id, folder_name, 'folder' AS type FROM catalog_folders" .
         " WHERE parent_id = " . intval($_GET['id']) .
		 " ORDER BY folder_name";

$result = mysql_query($query);

if ($result)
{
	while ($row = mysql_fetch_assoc($result))
	{
		$rows_list[] = $row;
	} 
	mysql_free_result($result);
}

$query = "SELECT id, file_name AS folder_name, 'file' AS type FROM catalog_files" .
         " WHERE folder_id = " . intval($_GET['id']) .
		 " ORDER BY folder_name";

$result = mysql_query($query);

if ($result)
{
	while ($row = mysql_fetch_assoc($result))
	{
		$rows_list[] = $row;
	} 
	mysql_free_result($result);
}

echo json_encode($rows_list);

$connection->close();

?>
