<?php
ob_start();
session_start();
$db = new mysqli('localhost', 'root', 'admin1', 'projekt');
include('common.php');
include('html_functions.php');
include('index_functions.php');
if (isset($_GET['action']))
{
	$_action = $_GET['action'];
}
else
{
	$_action = 'index';
}
switch ($_action)
{
	case 'index':
		html_header('Tablica informacyjna');
		html_menu();
		html_index_welcome();
		html_footer();
		break;
	case 'login':
		if (isset($_POST['login_box']) && isset($_POST['pwd_box']))
		{
			$user_chk = vs($_POST['login_box']);
			$pwd_chk = vs($_POST['pwd_box']);
			$zalogowano = verify_login($user_chk, $pwd_chk, $db);
			if ($zalogowano=='err') 
			{
				html_header('Tablica informacyjna - logowanie');
				html_menu();
				login_box('err');
				html_footer();
			}
			else if ($zalogowano=='loc')
			{
				html_header('Tablica informacyjna - logowanie');
				html_menu();
				login_box('loc');
				html_footer();
			}
			else
			{
				html_header('Tablica informacyjna - logowanie');
				html_menu();
				html_welcome_user($user_chk);
				html_footer();
			}
		}
		else
		{
			html_header('Tablica informacyjna - logowanie');
			html_menu();
			login_box('');
			html_footer();
		}		
		break;
	case 'logout':
		logout();
		header('Location: index.php?action=index');
		break;
	default:
		html_header('B³¹d ogólny - podana strona nie istnieje!');
		html_menu();
		error_action_unknown();
		html_footer();
}
ob_end_flush();
?>