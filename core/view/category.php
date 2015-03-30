<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Category_View
{
	public function ShowPage($row, $import)
	{
		$site_content = NULL;
		$site_modified = NULL;
		$author_login = NULL;
		
		foreach ($import as $i => $j)
		{
			if ($i == 'authors')
			{
				foreach ($j as $key => $value)
				{
					foreach ($value as $k => $v)
					{
						if ($k == 'id') $user_id = $v;
						if ($k == 'user_login') $user_login = $v;						
					}
					if ($user_id == $row['author_id']) $author_login = $user_login;
				}
			}
		}

		$site_content .= '<p style="text-align: justify;">';
		
		if (is_array($row))
		{
			foreach ($row as $key => $value)
			{
				if ($key == 'contents') $site_content .= $value;
				if ($key == 'modified') $site_modified .= $value;
			}
		}
		
		$site_content .= '</p>';

		$site_content .= '<p class="PageSignature">';
		$site_content .= $author_login . ', ' . $site_modified;
		$site_content .= '</p>';

		$site_content = empty($row) ? NULL : $site_content;
		
		return $site_content;
	}
	
	public function ShowTitle($row)
	{
		$site_title = NULL;
		
		if (is_array($row))
		{
			foreach ($row as $key => $value)
			{
				if ($key == 'title') $site_title .= $value;
			}
		}
		
		return $site_title;
	}
}

?>
