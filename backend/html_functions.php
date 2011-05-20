<?php
function html_header($title)
{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<title><?php echo stripslashes($title) ?></title>
	
</head>
<body>
	<div id="main">
	<div id="header">
		<?php
			if (isset($_SESSION['user']));
			else { ?>
		<a href="index.php?action=login">Zaloguj się</a>
		<?php } ?></div>
<?php
}
function html_menu()
{
	$param = $_SESSION['user_type'];
	
	if ($param == 'admin')
	{
		$mozliwosci_index = array(	'index' => 'Strona główna',
									'logout' => 'Wyloguj'
									);
		$mozliwosci_admin = array(	'admin_mgmt' => 'Dodaj nowego użytkownika',
									'admin_haslo' => 'Zmień hasło',
									'admin_konsultacje' => 'Dodaj konsultacje',
									'admin_przedmiot' => 'Dodaj przedmiot',
									'admin_grupa' => 'Dodaj grupę',
									'admin_student' => 'Dodaj studenta',
									'admin_student_grupa' => 'Dodaj studenta do grupy',
									'admin_grupacsv' => 'Wczytaj grupę',
									'admin_ocenycsv' => 'Wczytaj oceny',
									'admin_ogloszenie' => 'Dodaj ogłoszenie',
									'admin_ogloszenie_grupa' => 'Dodaj ogłoszenie dla grupy',
									'admin_ogloszenie_student' => 'Dodaj ogłoszenie dla studenta'
									);
	}
	else //niezalogowany
	{
		$mozliwosci_index = array(	'index' => 'Strona główna',
									'login' => 'Zaloguj');
	}
	?>
	<div id="menu">
			<ul>
				<li class="menu_nagl">Menu</li>
				<?php
				foreach($mozliwosci_index as $key => $var)
				{
					?><li class="menu_pozycja"><a href="index.php?action=<?php echo $key ?>"><?php echo $var ?></a></li>
					<?php
				}
				?>
			</ul>
			<?php
			if ($param=='admin')
			{
			?>
				<ul>
					<li class="menu_nagl"><br></li>
					<?php
					foreach($mozliwosci_admin as $key => $var)
					{
						?><li class="menu_pozycja"><a href="admin.php?action=<?php echo $key ?>"><?php echo $var ?></a></li>
						<?php
					}
					?>
				</ul>
			<?php
			}
			?>
	</div>
	<?php
}
function html_footer()
{
?> 	
		<div id="footer">Glapiński, Grzywocz, Knapik, Owoc &copy;2011</div>
	</div>
</body>
</html>
<?php 
}
function login_box($param1) { ?>
<div id="login_box">
	<form action="index.php?action=login" method="post" >
		<fieldset>
			<legend>Podaj swój email i hasło</legend>
			<p><?php if ($param1=='err') { ?><span class="err">Niepoprawny email lub hasło!</span><br />
			<?php } ?><?php if ($param1=='loc') { ?><span class="err">Twoje konto nie zostało aktywowane! Sprawdż pocztę, by je aktywować.</span><br />
			<?php } ?>Email:<input type="text" name="login_box" /><br />
			Hasło:<input type="password" name="pwd_box" /><br />
			<input type="submit" value="Zaloguj" /></p>
		</fieldset>
	</form>
</div>
<?php }
function error_action_unknown()
{
	?>
	<div id="content">
		<h1>Błąd!</h1>
		<p>Podana strona nie istnieje. W przypadku, gdy trafiłeś tu z zapisanego wcześniej odnośnika, jest on już nieaktualny!</p>
	</div>
	<?php
}
function error_access_denied()
{
	?>
	<div id="content">
		<h1>Błąd!</h1>
		<p>Nie masz wymaganych uprawnień, aby otrzymać dostęp do tej strony!</p>
	</div>
	<?php
}
function html_index_welcome()
{
?>
	<div id="content">
		<h1>Tablica ogłoszeń</h1>
		<p>Tu....</p>
	</div>
<?php
}
function html_welcome_user($user)
{
	?>
				<div id="content">
					<h1>Witaj!</h1>
					<p>Zapraszamy do skorzystania z menu po lewej!</p>
				</div>
				<?php
}
?>