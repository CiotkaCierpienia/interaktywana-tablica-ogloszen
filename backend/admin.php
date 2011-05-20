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
			if (isset($_POST['pwd']) && isset($_POST['pwd_n']))
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
		case 'admin_konsultacje':
			if (isset($_POST['dzien']) && isset($_POST['od']) && isset($_POST['do']))
			{
				$wyn = admin_dodkonsultacje($db);				
				html_header('Dodaj konsultacje');
				html_menu();
				add_admin_box_konsultacje($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj konsultacje');
				html_menu();
				add_admin_box_konsultacje(-1);
				html_footer();
			}			
			break;
		case 'admin_przedmiot':
			if (isset($_POST['kod_kursu']) && isset($_POST['przedmiot']))
			{
				$wyn = admin_dodprzedmiot($db);				
				html_header('Dodaj przedmiot');
				html_menu();
				add_admin_box_przedmiot($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj przedmiot');
				html_menu();
				add_admin_box_przedmiot(-1);
				html_footer();
			}			
			break;
		case 'admin_grupa':
			if (isset($_POST['kod_grupy']))
			{
				$wyn = admin_dodgrupa($db);				
				html_header('Dodaj grupę');
				html_menu();
				add_admin_box_grupa($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj grupę');
				html_menu();
				add_admin_box_grupa(-1,$db);
				html_footer();
			}			
			break;
		case 'admin_grupacsv':
			if (isset($_POST['plik']))
			{
				$wyn = admin_dodgrupacsv($db);				
				html_header('Wczytaj grupę');
				html_menu();
				add_admin_box_grupacsv($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Wczytaj grupę');
				html_menu();
				add_admin_box_grupacsv(-1);
				html_footer();
			}			
			break;
		case 'admin_ocenycsv':
			if (isset($_POST['plik']))
			{
				$wyn = admin_dodocenycsv($db);				
				html_header('Wczytaj oceny');
				html_menu();
				add_admin_box_ocenycsv($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Wczytaj oceny');
				html_menu();
				add_admin_box_ocenycsv(-1);
				html_footer();
			}			
			break;
		case 'admin_ogloszenie':
			if (isset($_POST['ogloszenie']))
			{
				$wyn = admin_dodogloszenie($db);				
				html_header('Dodaj ogłoszenie');
				html_menu();
				add_admin_box_ogloszenie($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj ogłoszenie');
				html_menu();
				add_admin_box_ogloszenie(-1);
				html_footer();
			}			
			break;
		case 'admin_ogloszenie_grupa':
			if (isset($_POST['ogloszenie']))
			{
				$wyn = admin_dodogloszenie_grupa($db);				
				html_header('Dodaj ogłoszenie');
				html_menu();
				add_admin_box_ogloszenie_grupa($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj ogłoszenie');
				html_menu();
				add_admin_box_ogloszenie_grupa(-1,$db);
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