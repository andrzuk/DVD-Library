<?php

/*
 * View - generuje treść podstrony na podstawie zebranych danych
 */
class Disc_Map_View
{
	private $segments;
	private $site_content;
	
	public function __construct()
	{
		$this->segments = array('Muzyka', 'Filmy', 'Wideoklipy', 'Sport');
	}
	
	public function ShowLibrary($rows_list, $import)
	{
		$this->site_content = NULL;

		foreach ($import as $i_k => $i_v)
		{
			foreach ($i_v as $i_key => $i_value)
			{
				if ($i_key == 'type') $type = $i_value;
				if ($i_key == 'discs') $discs = $i_value;
				if ($i_key == 'folders') $folders = $i_value;
				if ($i_key == 'files') $files = $i_value;
				if ($i_key == 'size') $size = $i_value;
			}
			if ($type == '*')
			{
				$m_discs = $discs;
				$m_folders = $folders;
				$m_files = $files;
				$m_size = $size;
			}
		}
		
		$this->site_content .= '<p>';
		$this->site_content .= '<b>Moja biblioteka</b> (płyt: <b>'.$m_discs.'</b>, folderów: <b>'.$m_folders.'</b>, plików: <b>'.$m_files.'</b>, rozmiar: <b>'.$m_size.'</b>)';
		$this->site_content .= '</p>';
		
		foreach ($this->segments as $key => $value)
		{
			$library_size = 0;
			
			foreach ($rows_list as $k => $v)
			{
				foreach ($v as $i => $j)
				{
					if ($i == 'content_type') $content_type = $j;
					if ($i == 'licznik') $content_count = $j;
				}
				if ($content_type == $value) $library_size = $content_count;
			}
			
			foreach ($import as $i_k => $i_v)
			{
				foreach ($i_v as $i_key => $i_value)
				{
					if ($i_key == 'type') $type = $i_value;
					if ($i_key == 'discs') $discs = $i_value;
					if ($i_key == 'folders') $folders = $i_value;
					if ($i_key == 'files') $files = $i_value;
					if ($i_key == 'size') $size = $i_value;
				}
				if ($type == $value)
				{
					$m_discs = $discs;
					$m_folders = $folders;
					$m_files = $files;
					$m_size = $size;
				}
			}
			
			$this->site_content .= '<ul>';
			$this->site_content .= '<li class="Lib_Segment">';
		//	$this->site_content .= '<a onclick="GetSegmentDiscs(\''.$value.'\')"><b>'.$value.' ('.$library_size.')</b></a>';
			$this->site_content .= '<a onclick="GetSegmentDiscs(\''.$value.'\')">'.$value.' (płyt: <b>'.$m_discs.'</b>, folderów: <b>'.$m_folders.'</b>, plików: <b>'.$m_files.'</b>, rozmiar: <b>'.$m_size.'</b>)</a>';
			$this->site_content .= '<ul id="Segment_'.$value.'">';
			$this->site_content .= '</ul>';
			$this->site_content .= '</li>';
			$this->site_content .= '</ul>';
		}

		return $this->site_content;
	}	
}

?>
