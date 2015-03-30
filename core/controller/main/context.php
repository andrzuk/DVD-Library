<?php

/*
 * Klasa odpowiedzialna za dobór opcji (links, menu, ...) w zależności od kontekstu strony
 */

class Context
{
	private $site_links;
	private $site_options;
	private $site_menu;
	private $user_status;
	private $current_category;
	
	/*
	 *	Inicjalizacja
	 */
	 
	public function init($user, $root)
	{
		$this->current_category = $root;
		
		/*
		 *	Top-Linki strony - ustalane na podstawie statusu użytkownika:
		 */

		foreach ($user as $key => $value)
		{
			if ($key == 'user_id') $user_id = $value;
			if ($key == 'user_status') $user_status = $value;
			if ($key == 'user_imie') $user_imie = $value;
			if ($key == 'user_nazwisko') $user_nazwisko = $value;
		}

		switch ($user_status) 
		{
			case 1: // admin
			{
				$links = array (
					array (
						'address' => 'index.php?route=admin',
						'caption' => 'Admin-panel',
						'icon' => 'img/top/panel.png'
					),
					array (
						'address' => 'index.php?route=profile',
						'caption' => 'Moje konto',
						'icon' => 'img/top/user.png'
					),
					array (
						'address' => 'index.php?route=adv_search&action=params',
						'caption' => 'Szukaj',
						'icon' => 'img/top/search.png'
					),
					array (
						'address' => 'index.php?route=disc_map',
						'caption' => 'Mapa serwisu',
						'icon' => 'img/top/folders.png'
					),
					array (
						'address' => 'index.php?route=contact',
						'caption' => 'Kontakt',
						'icon' => 'img/top/contact.png'
					)
				);
			}
			break;

			case 2: // operator
			{
				$links = array (
					array (
						'address' => 'index.php?route=admin',
						'caption' => 'Operator-panel',
						'icon' => 'img/top/panel.png'
					),
					array (
						'address' => 'index.php?route=profile',
						'caption' => 'Moje konto',
						'icon' => 'img/top/user.png'
					),
					array (
						'address' => 'index.php?route=adv_search&action=params',
						'caption' => 'Szukaj',
						'icon' => 'img/top/search.png'
					),
					array (
						'address' => 'index.php?route=disc_map',
						'caption' => 'Mapa serwisu',
						'icon' => 'img/top/folders.png'
					),
					array (
						'address' => 'index.php?route=contact',
						'caption' => 'Kontakt',
						'icon' => 'img/top/contact.png'
					)
				);
			}
			break;

			case 3: // user
			{
				$links = array (
					array (
						'address' => 'index.php?route=admin',
						'caption' => 'User-panel',
						'icon' => 'img/top/panel.png'
					),
					array (
						'address' => 'index.php?route=profile',
						'caption' => 'Moje konto',
						'icon' => 'img/top/user.png'
					),
					array (
						'address' => 'index.php?route=adv_search&action=params',
						'caption' => 'Szukaj',
						'icon' => 'img/top/search.png'
					),
					array (
						'address' => 'index.php?route=disc_map',
						'caption' => 'Mapa serwisu',
						'icon' => 'img/top/folders.png'
					),
					array (
						'address' => 'index.php?route=contact',
						'caption' => 'Kontakt',
						'icon' => 'img/top/contact.png'
					)
				);
			}
			break;

			default: // guest
			{
				$links = array (
					array (
						'address' => 'index.php?route=login',
						'caption' => 'Zaloguj',
						'icon' => 'img/top/login.png'
					),
					array (
						'address' => 'index.php?route=register',
						'caption' => 'Zarejestruj',
						'icon' => 'img/top/register.png'
					),
					array (
						'address' => 'index.php?route=adv_search&action=params',
						'caption' => 'Szukaj',
						'icon' => 'img/top/search.png'
					),
					array (
						'address' => 'index.php?route=disc_map',
						'caption' => 'Mapa serwisu',
						'icon' => 'img/top/folders.png'
					),
					array (
						'address' => 'index.php?route=contact',
						'caption' => 'Kontakt',
						'icon' => 'img/top/contact.png'
					)
				);
			}
			break;
		}
		
		/*
		 *	Menu strony:
		 */
		 
		$menu = array();
		
		$icons = array('img/folder.png', 'img/arrow_blue.png', 'img/arrow_green.png');

		// pobieramy dane z bazy:
		
		include APP_DIR . 'model/common/menu.php';

		$menu_object = new Menu();

		$record_list = $menu_object->GetAll($this->current_category);

		// listę rekordów konwertujemy w interfejsie:
				
		foreach ($record_list as $k => $v)
		{
			foreach ($v as $key => $value)
			{
				if ($key == 'id') $id = $value;
				if ($key == 'parent_id') $parent_id = $value;
				if ($key == 'link') $link = $value;
				if ($key == 'caption') $caption = $value;
				if ($key == 'level') $level = $value;
				if ($key == 'permission') $permission = $value;
				if ($key == 'page_id') $page_id = $value;
				if ($key == 'target') $target = $value;
			}
			$icon = $icons[$level];
			$menu_item = array(
				'id' => $id,
				'parent_id' => $parent_id,
				'address' => $link,
				'caption' => $caption,
				'level' => $level,
				'permission' => $permission,
				'icon' => $icon
			);
			$menu[] = $menu_item;
		}
		
		// przygotowane dane kierujemy na stronę:
		
		$this->site_links = $links;
		$this->site_menu = $menu;
		
		// zapamiętujemy status użytkownika:
		
		$this->user_status = $user_status;
	}
	
	/*
	 *	Ustawianie opcji
	 */
	 
	public function set_options($options)
	{
		$this->site_options = $options;
	}

	/*
	 *	Listwa górnych linków strony
	 */
	 
	public function get_links()
	{
		$output = NULL;

		foreach ($this->site_links as $k => $v) 
		{
			foreach ($v as $key => $value) 
			{
				if ($key == 'address') $address = $value;
				if ($key == 'caption') $caption = $value;
				if ($key == 'icon') $icon = $value;
			}
			$output .= '<span class="TopLinkItem">'.
				'<a href="'.$address.'" class="PathLink">'.
				'<img src="'.$icon.'" class="TopLinkIcon" alt="'.$caption.'" />'.
				$caption.'</a></span>';
		}

		return $output;
	}

	/*
	 *	Ustawianie aktualnej kategorii
	 */
	 
	public function set_current_category($category)
	{
		$this->current_category = $category;
	}

	/*
	 *	Wszystkie składniki (panele) kolumny menu
	 */
	 
	public function get_menu()
	{
		$output = NULL;
		
		$setting = new Settings();
		
		$options_panel_visible = $setting->get_config_key('options_panel_visible');
		$menu_panel_visible = $setting->get_config_key('menu_panel_visible');
		$search_panel_visible = $setting->get_config_key('search_panel_visible');
		$stats_panel_visible = $setting->get_config_key('stats_panel_visible');
		$facebook_panel_visible = $setting->get_config_key('facebook_panel_visible');

		// menu kontekstowe - dodatkowe opcje:
		
		if ($options_panel_visible == 'true')
		{
			$output .= $this->get_options();
		}
		
		// panel menu głównego strony (kategorie):
		
		if ($menu_panel_visible == 'true')
		{
			$output .= $this->get_categories();
		}
		
		// panel szukania:
		
		if ($search_panel_visible == 'true')
		{
			$output .= $this->get_search();
		}
		
		// panel statystyk:
		
		if ($stats_panel_visible == 'true')
		{
			$output .= $this->get_statistics();
		}
		
		// panel znajdź mnie na Facebooku:
		
		if ($facebook_panel_visible == 'true')
		{
			$output .= $this->get_facebook();
		}
		
		return $output;
	}
	
	/*
	 *	Panel opcji podstrony
	 */
	 
	public function get_options()
	{
		$output = NULL;

		if (!sizeof($this->site_options)) return $output;
		
		$setting = new Settings();
		
		$options_panel_title = $setting->get_config_key('options_panel_title');
		
		$output .= '<div class="WindowHeader">';
		$output .= $options_panel_title;
		$output .= '</div>';

		$output .= '<table width="100%" cellpadding="0" cellspacing="0">';

		foreach ($this->site_options as $k => $v) 
		{
			foreach ($v as $key => $value) 
			{
				if ($key == 'address') $address = $value;
				if ($key == 'caption') $caption = $value;
				if ($key == 'icon') $icon = $value;
			}
			$output .= '<tr>';
			$output .= '<td width="10%" class="MenuIco">';
			$output .= '<img src="'.$icon.'" class="TopLinkIcon" alt="ico" />';
			$output .= '</td>';
			$output .= '<td width="90%" class="MenuItem">';
			$output .= '<a href="'.$address.'" class="PathLink">'.$caption.'</a>';
			$output .= '</td>';
			$output .= '</tr>';
		}

		$output .= '</table>';
		
		$output .= '<div class="Separator"></div>';
		
		return $output;
	}

	/*
	 *	Panel menu głównego
	 */
	 
	public function get_categories()
	{
		$output = NULL;
		
		if (!sizeof($this->site_menu)) return $output;

		$setting = new Settings();
		
		$menu_panel_title = $setting->get_config_key('menu_panel_title');
		
		$output .= '<div class="WindowHeader">';
		$output .= $menu_panel_title;
		$output .= '</div>';

		$output .= '<table width="100%" cellpadding="0" cellspacing="0">';

		foreach ($this->site_menu as $k => $v) 
		{
			foreach ($v as $key => $value) 
			{
				if ($key == 'id') $id = $value;
				if ($key == 'parent_id') $parent_id = $value;
				if ($key == 'address') $address = $value;
				if ($key == 'caption') $caption = $value;
				if ($key == 'level') $level = $value;
				if ($key == 'permission') $permission = $value;
				if ($key == 'icon') $icon = $value;
			}
			
			if ($level == 0) // powrót
			{
				$offset = 0;
			}
			else if ($level == 1) // parent
			{
				$offset = 5;
			}
			else // child
			{
				$offset = 10;
			}
			
			if ($id == $this->current_category) // zaznaczona kategoria
			{
				$caption = '<span class="MenuItemSelected"><b>' . $caption . '</b></span>';
			}
			
			$output .= '<tr>';
			$output .= '<td width="10%" class="MenuIco" style="padding-left: '.$offset.'px;">';
			$output .= '<img src="'.$icon.'" class="TopLinkIcon" alt="menu" />';
			$output .= '</td>';
			$output .= '<td width="90%" class="MenuItem" style="padding-left: '.$offset.'px;">';

			$access = $this->user_status ? $this->user_status <= $permission : $permission == FREE;
			
			if ($access)
			{
				$output .= '<a href="'.$address.'" class="PathLink">'.$caption.'</a>';
			}
			else
			{
				$output .= '<a class="Disabled">'.$caption.'</a>';
			}
			
			$output .= '</td>';
			$output .= '</tr>';
		}

		$output .= '</table>';
		
		$output .= '<div class="Separator"></div>';
		
		return $output;
	}
	
	/*
	 *	Panel szukania
	 */
	 
	public function get_search()
	{
		$output = NULL;

		$setting = new Settings();
		
		$search_panel_title = $setting->get_config_key('search_panel_title');
		
		$output .= '<div class="WindowHeader">';
		$output .= $search_panel_title;
		$output .= '</div>';

		$output .= '<form action="index.php?route=disc_search" method="post">';
		$output .= '<table width="100%" cellpadding="0" cellspacing="0">';
		$output .= '<tr>';
		$output .= '<td>';
		$output .= '<input type="text" name="search_text" value="" class="FormInput" style="width: 95%;" />';
		$output .= '</td>';
		$output .= '</tr>';
		$output .= '<tr>';
		$output .= '<td class="SearchButton">';
		$output .= '<input type="submit" value="Znajdź" name="search_button" class="Button" style="width: 60px;" />';
		$output .= '</td>';
		$output .= '</tr>';
		$output .= '</table>';
		$output .= '</form>';
		
		$output .= '<div class="Separator"></div>';
		
		return $output;
	}
	
	/*
	 *	Panel statystyk
	 */
	 
	public function get_statistics()
	{
		$output = NULL;

		$setting = new Settings();
		
		$stats_panel_title = $setting->get_config_key('stats_panel_title');
		
		$output .= '<div class="WindowHeader">';
		$output .= $stats_panel_title;
		$output .= '</div>';

		$visitor = new Visitors();
		
		$licznik_odwiedzin = $visitor->get_licznik_info();
		$lista_ip = $visitor->get_visitors(5);
		$online_count = $visitor->get_online();
		$logged_list = $visitor->get_logged();
		
		$output .= '<div class="Stats">';
		$output .= 'Wejścia na stronę: <b>'. number_format($licznik_odwiedzin[1], 0, '', '.') .'</b>';
		$output .= '</div>';

		$output .= '<div class="Stats">';
		$output .= 'Wejścia dzisiejsze: <b>'. number_format($licznik_odwiedzin[2], 0, '', '.') .'</b>';
		$output .= '</div>';
		/*
		$output .= '<div class="Stats">';
		$output .= 'Ostatnie połączenia:';
		$output .= '<ul class="IpList">';
		foreach ($lista_ip as $key => $value)
		{
			$output .= '<li class="IpItem">';
			$output .= $value[0] .' ('. $value[1] .')';
			$output .= '</li>';
		}
		$output .= '</ul>';
		$output .= '</div>';
		*/
		$output .= '<div class="Stats">';
		$output .= 'Osób on-line: <b>'. $online_count .'</b>';
		$output .= '</div>';
		
		$output .= '<div class="Stats">';
		$output .= 'Zalogowani:';
		$output .= '<ul class="IpList">';
		if (sizeof($logged_list))
		{
			foreach ($logged_list as $key => $value)
			{
				$output .= '<li class="IpItem">';
				$output .= '<b>' . $value . '</b>';
				$output .= '</li>';
			}
		}
		else
		{
			$output .= '<li>';
			$output .= '(brak)';
			$output .= '</li>';
		}
		$output .= '</ul>';
		$output .= '</div>';

		$output .= '<div class="Separator"></div>';
		
		return $output;
	}

	/*
	 *	Panel Znajdź mnie na Facebooku
	 */
	 
	public function get_facebook()
	{
		$output = NULL;

		$setting = new Settings();
		
		$facebook_panel_title = $setting->get_config_key('facebook_panel_title');
		
		$output .= '<div class="WindowHeader">';
		$output .= $facebook_panel_title;
		$output .= '</div>';

		$output .= '<a href="https://www.facebook.com/Andrzej.Zukowski" target="_blank"><img src="img/facebook.png" style="border: 0px solid #fff;" alt="facebook" title="Znajdź mnie na Facebooku" /></a>&nbsp;';
		$output .= '<a href="http://www.linkedin.com/profile/view?id=93739159&trk=hb_tab_pro_top" target="_blank"><img src="img/linkedin.png" style="border: 0px solid #fff;" alt="linkedin" title="Znajdź mnie na LinkedIn" /></a>&nbsp;';
		$output .= '<a href="https://twitter.com/andy_zukowski" target="_blank"><img src="img/twitter.png" style="border: 0px solid #fff;" alt="twitter" title="Znajdź mnie na Twitterze" /></a>&nbsp;';
		$output .= '<a href="https://plus.google.com/u/0/113303165754486219878" target="_blank"><img src="img/google_plus.png" style="border: 0px solid #fff;" alt="google+" title="Znajdź mnie na Google Plus" /></a>';

		$output .= '<div class="Separator"></div>';
		
		return $output;
	}	
}

?>
