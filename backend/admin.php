<?php
ob_start();
session_start();
include('common.php');
$db = new mysqli('localhost', 'root', 'admin1', 'projekt');
include('admin_functions.php');
include('html_functions.php');
if ($_SESSION['user_type']!='admin')
{
	html_header('Błąd ogólny - brak uprawnień!');
	html_menu();
	error_access_denied();
	html_footer();
}
else
{
	if (!isset($_GET['action']))
	{
		$_action='index';
	}
	else
	{
		$_action=$_GET['action'];
	}
	switch ($_action)
	{
		case 'admin_mgmt':
			if (isset($_POST['email']))
			{
				$wyn = admin_add($db);				
				html_header('Dodaj nowego użytkownika');
				html_menu();
				add_admin_box($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj nowego użytkownika');
				html_menu();
				add_admin_box(-1);
				html_footer();
			}			
			break;
		case 'admin_haslo':
			if (isset($_POST['pwd']))
			{
				$wyn = admin_zmhaslo($db);				
				html_header('Zmień hasło');
				html_menu();
				add_admin_box_haslo($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Zmień hasło');
				html_menu();
				add_admin_box_haslo(-1);
				html_footer();
			}			
			break;
		case 'index':
			html_header('Panel administratora');
			html_menu();
			html_welcome_user($_SESSION['user']);
			html_footer();
			break;
		default:
			html_header('Błąd ogólny - podana strona nie istnieje!');
			html_menu();
			error_action_unknown();
			html_footer();
	}
}
ob_end_flush();
?>