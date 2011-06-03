<?php
ob_start();
session_start();
include('common.php');
$db = new mysqli('localhost', 'root', 'admin1', 'projekt');
$query = "SET CHARSET utf8";
$wynik = $db->query($query);
$query = "SET NAMES `utf8` COLLATE `utf8_polish_ci`"; 
$wynik = $db->query($query);
include('admin_student.php');
include('admin_dane.php');
include('admin_konsultacje.php');
include('admin_kurs.php');
include('admin_grupa.php');
include('admin_ogloszenie.php');
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
		case 'jestem':
			jestem($db);
			html_header('Ustawiono status');
			html_menu();
			status('jestem');
			html_footer();
			break;
		case 'jestem_zajety':
			jestem_zajety($db);
			html_header('Ustawiono status');
			html_menu();
			status('jestem zajęty');
			html_footer();
			break;
		case 'nie_ma':
			nie_ma($db);
			html_header('Ustawiono status');
			html_menu();
			status('wyszedłem');
			html_footer();
			break;
		case 'admin_mgmt':
			if (isset($_POST['email']) && isset($_POST['imie']) && isset($_POST['nazwisko']) && isset($_POST['stopien_naukowy']) && isset($_POST['pwd']) && isset($_POST['email']) && isset($_POST['pwd_conf']))
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
		case 'admin_mgmt_edytuj':
			if (isset($_POST['email']) && isset($_POST['imie']) && isset($_POST['nazwisko']) && isset($_POST['stopien_naukowy']) && isset($_POST['pwd']) && isset($_POST['email']))
			{
				$wyn = admin_add_edytuj($db);				
				html_header('Edytuj użytkownika');
				html_menu();
				add_admin_box_edytuj($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Edytuj użytkownika');
				html_menu();
				add_admin_box_edytuj(-1,$db);
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
		case 'admin_konsultacje_wyswietl':
			html_header('Wyświetl konsultacje');
			html_menu();
			konsultacje($db);
			html_footer();
			break;
		case 'admin_konsultacje_edytuj':
			if (isset($_POST['dzien']) && isset($_POST['od']) && isset($_POST['do']))
			{
				$wyn = admin_edytujkonsultacje($db);				
				html_header('Edytuj konsultacje');
				html_menu();
				add_admin_box_edytuj_konsultacje($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Edytuj konsultacje');
				html_menu();
				add_admin_box_edytuj_konsultacje(-1,$db);
				html_footer();
			}			
			break;
		case 'admin_konsultacje_usun':
			if (isset($_POST['id_konsultacji']))
			{
				$wyn = admin_usunkonsultacje($db);				
				html_header('Usuń konsultacje');
				html_menu();
				add_admin_box_usun_konsultacje($wyn,$db);
				html_footer();	
			}
			else
			{
				html_header('Usuń konsultacje');
				html_menu();
				add_admin_box_usun_konsultacje(-1,$db);
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
		case 'admin_przedmiot_edytuj':
			if (isset($_POST['przedmiot']))
			{
				$wyn = admin_edytujprzedmiot($db);				
				html_header('Edytuj przedmiot');
				html_menu();
				add_admin_box_edytuj_przedmiot($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Edytuj przedmiot');
				html_menu();
				add_admin_box_edytuj_przedmiot(-1,$db);
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
		case 'admin_grupa_edytuj':
			if (isset($_POST['termin']))
			{
				$wyn = admin_edytujgrupa($db);				
				html_header('Edytuj grupę');
				html_menu();
				add_admin_box_edytuj_grupa($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Edytuj grupę');
				html_menu();
				add_admin_box_edytuj_grupa(-1,$db);
				html_footer();
			}			
			break;
		case 'admin_grupa_usun':
			if (isset($_POST['id_grupy']))
			{
				$wyn = admin_usungrupa($db);	
				html_header('Usuń grupę');
				html_menu();
				add_admin_box_usun_grupa($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Usuń grupę');
				html_menu();
				add_admin_box_usun_grupa(-1,$db);
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
		case 'admin_ogloszenie_wyswietl':
			html_header('Wyświetl ogłoszenia');
			html_menu();
			ogloszenia($db);
			html_footer();
			break;
		case 'admin_przedmiot_wyswietl':
			html_header('Wyświetl kursy');
			html_menu();
			przedmioty($db);
			html_footer();
			break;
		case 'admin_grupa_wyswietl':
			html_header('Wyświetl grupy');
			html_menu();
			grupy($db);
			html_footer();
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
		case 'admin_ogloszenie_student':
			if (isset($_POST['ogloszenie']))
			{
				$wyn = admin_dodogloszenie_student($db);				
				html_header('Dodaj ogłoszenie');
				html_menu();
				add_admin_box_ogloszenie_student($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj ogłoszenie');
				html_menu();
				add_admin_box_ogloszenie_student(-1,$db);
				html_footer();
			}			
			break;
		case 'admin_student':
			if (isset($_POST['indeks']) && isset($_POST['nazwisko']) && isset($_POST['nazwisko']))
			{
				$wyn = admin_dod_student($db);				
				html_header('Dodaj studenta');
				html_menu();
				add_admin_box_student($wyn);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj studenta');
				html_menu();
				add_admin_box_student(-1);
				html_footer();
			}			
			break;
		case 'admin_student_edytuj':
			if (isset($_POST['nazwisko']) && isset($_POST['nazwisko']))
			{
				$wyn = admin_edytuj_student($db);				
				html_header('Edytuj studenta');
				html_menu();
				add_admin_box_edytuj_student($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Edytuj studenta');
				html_menu();
				add_admin_box_edytuj_student(-1,$db);
				html_footer();
			}			
			break;
		case 'admin_student_usun':
			if (isset($_POST['indeks']))
			{
				$wyn = admin_usun_student($db);				
				html_header('Usuń studenta');
				html_menu();
				add_admin_box_usun_student($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Usuń studenta');
				html_menu();
				add_admin_box_usun_student(-1,$db);
				html_footer();
			}			
			break;
		case 'admin_student_grupa':
			if (isset($_POST['indeks']))
			{
				$wyn = admin_dod_student_grupa($db);				
				html_header('Dodaj studenta do grupy');
				html_menu();
				add_admin_box_student_grupa($wyn,$db);
				html_footer();
			
			}
			else
			{
				html_header('Dodaj studenta do grupy');
				html_menu();
				add_admin_box_student_grupa(-1,$db);
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