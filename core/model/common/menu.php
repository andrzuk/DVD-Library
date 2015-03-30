<?php

/*
 * Klasa odpowiedzialna za pobieranie z bazy listy rekordów tabeli kategorie (menu główne)
 * kolejność rekordów jest ustalana na podstawie zależności parent - child
 */

class Menu
{
	private $rows_list;
	private $row_item;
	
	public function GetAll($parent)
	{
		$root_id = NULL;
		
		$this->rows_list = array();

		if ($parent) // aktywna jest jakaś kategoria
		{
			// szuka wspólnego rodzica:
			$query = "SELECT * FROM categories WHERE id = " . intval($parent);
			$result = mysql_query($query);
			if ($result)
			{
				$row = mysql_fetch_assoc($result); 
				$root_id = $row['parent_id'];
				if ($row['parent_id'])
				{
					$row['id'] = $row['parent_id'];
					$row['level'] = 0;
					$row['caption'] = '[ <b>..</b> ]';
					$row['link'] = 'index.php?route=category&id=' . $row['parent_id'];
					$this->rows_list[] = $row;
				}
				mysql_free_result($result);
			}
		}

		$query = 	"SELECT id, parent_id, caption, link, level, permission, page_id, target FROM categories".
					" WHERE parent_id = " . intval($root_id) . " AND visible = 1 ORDER BY item_order";

		$result = mysql_query($query);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$row['level'] = 1;
				$this->rows_list[] = $row;

				if ($parent == $row['id']) // aktywna jest dana kategoria
				{
					$sub_query = 	"SELECT id, parent_id, caption, link, level, permission, page_id, target FROM categories".
									" WHERE parent_id = ". $row['id'] ." AND visible = 1 ORDER BY item_order";

					$sub_result = mysql_query($sub_query);
					
					while ($sub_row = mysql_fetch_assoc($sub_result))
					{
						$sub_row['level'] = 2;
						$this->rows_list[] = $sub_row;
					}
				}
			}
			mysql_free_result($result);
		}
				
		return $this->rows_list;
	}
}

?>