<?php

/*
 * Klasa odpowiedzialna za generowanie elementów strony (nagłówek, stopka, ...)
 */

class Elements
{
	private $site_header;
	private $site_footer;

	public function set_header()
	{
		$header = NULL;

		$setting = new Settings();
		
		$logo_image = $setting->get_config_key('logo_image');
		$page_title = $setting->get_config_key('page_title');
		$page_subtitle = $setting->get_config_key('page_subtitle');

		$logo_image = !empty($logo_image) ? $logo_image : PAGE_LOGO;
		$page_title = !empty($page_title) ? $page_title : PAGE_TITLE;
		$page_subtitle = !empty($page_subtitle) ? $page_subtitle : PAGE_SUBTITLE;

		$header .= '<table cellpadding="0" cellspacing="0">';
		$header .= '<tr>';
		$header .= '<td class="LogoImage" rowspan="2">';
		$header .= '<a href="index.php"><img src="' . $logo_image . '" class="Logo" alt="logo" /></a>';
		$header .= '</td>';
		$header .= '<td class="LogoTitle">' . $page_title . '</td>';
		$header .= '</tr>';
		$header .= '<tr>';
		$header .= '<td class="LogoSubTitle">' . $page_subtitle . '</td>';
		$header .= '</tr>';
		$header .= '</table>';
		
		$this->site_header = $header;
	}

	public function set_footer()
	{
		$footer = NULL;
		
		$footer .= '<table class="Footer" width="100%" cellpadding="0">';
		$footer .= '<tr>';
		$footer .= '<td width="36%" style="text-align: left;">';
		$footer .= '© MyMVC '.date("Y").' <a href="https://plus.google.com/113303165754486219878?rel=author" class="MenuLink">Andrzej Żukowski</a>. Wszystkie prawa zastrzeżone.';
		$footer .= '</td>';
		$footer .= '<td width="41%" style="text-align: center;">';
		$footer .= '<a href="index.php?route=page&id=3" class="MenuLink">Regulamin serwisu</a> &nbsp; • &nbsp; ';
		$footer .= '<a href="index.php?route=page&id=4" class="MenuLink">Pomoc techniczna</a> &nbsp; • &nbsp; ';
		$footer .= '<a href="index.php?route=page&id=5" class="MenuLink">Polityka plików cookies</a>';
		$footer .= '</td>';
		$footer .= '<td width="23%" style="text-align: right;">';
		$footer .= 'Powered by: <a href="http://swoja-strona.eu" class="MenuLink">Swoja Strona</a> © 2011-'.date("Y");
		$footer .= '</td>';
		$footer .= '</tr>';
		$footer .= '</table>';

		$this->site_footer = $footer;
	}

	public function show_header()
	{
		return $this->site_header;
	}

	public function show_footer()
	{
		return $this->site_footer;
	}
}

?>
