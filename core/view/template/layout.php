<?php

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';

echo '<head>';

echo '<link rel="stylesheet" type="text/css" href="'.$page_data->get_editor().'contents.css" />';
echo '<link rel="stylesheet" type="text/css" href="css/default.css" />';
echo '<link rel="stylesheet" type="text/css" href="css/jquery.dynDateTime/calendar-az.css" />';
echo '<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">';
echo '<link rel="icon" href="img/favicon.ico" type="image/x-icon">';
echo '<script language="JavaScript" type="text/javascript" src="'.$page_data->get_editor().'ckeditor.js"></script>';		
echo '<script language="JavaScript" type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>';
echo '<script language="JavaScript" type="text/javascript" src="js/jquery.dynDateTime/jquery.dynDateTime.js"></script>';
echo '<script language="JavaScript" type="text/javascript" src="js/jquery.dynDateTime/lang/calendar-en.js"></script>';
echo '<script language="JavaScript" type="text/javascript" src="js/catalog.js"></script>';
echo '<meta http-equiv="Content-Language" content="pl" />';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
echo '<meta name="keywords" content="'.$page_data->get_keywords().'" />';
echo '<meta name="description" content="'.$page_data->get_description().'" />';
echo '<meta name="author" content="'.$page_data->get_author().'" />';
echo '<meta name="robots" content="index, follow, all" />';
echo '<meta name="googlebot" content="index, follow, all" />';
echo '<meta name="distribution" content="global" />';
echo '<meta name="revisit-after" content="2 days" />';
echo '<meta name="copyright" content="'.$page_data->get_copyright().'" />';
echo '<meta name="classification" content="'.$page_data->get_classification().'" />';
echo '<meta name="publisher" content="'.$page_data->get_publisher().'" />';
echo '<meta name="page-topic" content="'.$page_data->get_topic().'" />';
echo '<meta name="rating" content="general" />';
echo '<meta name="security" content="public" />';
echo '<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />';
echo '<title>'.$page_data->get_title().'</title>';
echo '<base href="'.$page_data->get_domain().'" target="_self" />';

// Google-Analytics script:
echo "<script>";
echo "  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){";
echo "  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),";
echo "  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)";
echo "  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');";
echo "  ga('create', 'UA-16941734-18', 'mvc-project.pl');";
echo "  ga('send', 'pageview');";
echo "</script>";

echo '</head>';

echo '<body>';

// Facebook Javascript SDK:
echo '<div id="fb-root"></div>';
echo '<script>(function(d, s, id) {';
echo 'var js, fjs = d.getElementsByTagName(s)[0];';
echo 'if (d.getElementById(id)) return;';
echo 'js = d.createElement(s); js.id = id;';
echo 'js.src = "//connect.facebook.net/pl_PL/all.js#xfbml=1";';
echo 'fjs.parentNode.insertBefore(js, fjs);';
echo '}(document, \'script\', \'facebook-jssdk\'));</script>';

echo '<div id="container" style="width: '.$page_data->get_site_width().';">';

	echo '<div id="header">';

		echo '<div class="PageHeader">';
		
			echo '<span class="PageLogo">';
			
				echo $page_elements->show_header();
			
			echo '</span>';

			echo '<span class="PageUtilities">';
			
				echo '<div class="PageLinks">';
				
					echo $page_data->get_links();
					
				echo '</div>';

				echo '<div class="PageUser">';
				
					echo $page_data->get_user();
					
				echo '</div>';
				
				echo '<div class="PagePath">';
				
					echo $page_data->get_path();
					
				echo '</div>';

			echo '</span>';
			
		echo '</div>';
				
		echo '<div class="Clear"></div>';
		
	echo '</div>';
	
	echo '<div id="center">';

		echo '<div id="menu" style="width: '.$page_data->get_menu_width().';">';

			echo '<div class="PageMenu">';
			
				echo $page_data->get_menu();
				
			echo '</div>';

		echo '</div>';

		echo '<div id="content" style="width: '.$page_data->get_content_width().';">';

			echo '<div class="PageContent">';
			
				echo $page_data->get_content();
				
			echo '</div>';

		echo '</div>';
		
		echo '<div class="Clear"></div>';
		
	echo '</div>';

	echo '<div id="footer">';

		echo '<div class="PageFooter">';
		
			echo $page_elements->show_footer();
			
		echo '</div>';

	echo '</div>';

echo '</div>';

echo '</body>';
echo '</html>';

?>
