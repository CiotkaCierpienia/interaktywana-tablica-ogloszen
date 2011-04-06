<?php
function add_admin_box($err)
{
	echo "<form action=\"admin.php?action=admin_mgmt\" method=\"POST\">";
	echo "<table>";
	
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Administrator został dodany!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Podane hasła są różne!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Podany login jest już zajęty!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Email</th><td><input type=\"text\" name=\"email\" value=\""; echo stripslashes($wiersz['email']); echo "\" /></td></tr>";
		echo "<tr><th>Imie</th><td><input type=\"text\" name=\"imie\" value=\""; echo stripslashes($wiersz['imie']); echo "\" /></td></tr>";
		echo "<tr><th>Nazwisko</th><td><input type=\"text\" name=\"nazwisko\" value=\""; echo stripslashes($wiersz['nazwisko']); echo "\" /></td></tr>";
		echo "<tr><th>Tytuł naukowy</th><td><input type=\"text\" name=\"stopien_naukowy\" value=\""; echo stripslashes($wiersz['stopien_naukowy']); echo "\" /></td></tr>";
		echo "<tr><th>Numer telefonu</th><td><input type=\"text\" name=\"telefon\" value=\""; echo stripslashes($wiersz['nr_telefonu']); echo "\" /></td></tr>";
		echo "<tr><th>Hasło</th><td><input type=\"password\" name=\"pwd\" /></td></tr>";
		echo "<tr><th>Potwierdź</th><td><input type=\"password\" name=\"pwd_conf\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_add(&$db)
{
	$data = array(	'email' => vs($_POST['email']),
					'imie' => vs($_POST['imie']),
					'nazwisko' => vs($_POST['nazwisko']),
					'stopien_naukowy' => vs($_POST['stopien_naukowy']),
					'telefon' => vs($_POST['telefon']),
					'pwd' => vs($_POST['pwd']),
					'pwd_conf' => vs($_POST['pwd_conf'])
					);
	if ($data['pwd']!=$data['pwd_conf']) return 1;
	$query = 'SELECT email FROM prowadzacy WHERE email = \''.$data['email'].'\'';
	echo $query;
	$wynik = $db->query($query);
	echo $wynik->num_rows;
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'INSERT INTO prowadzacy (imie, nazwisko, stopien_naukowy, email, nr_telefonu, haslo, potwierdzony)
				VALUES (\''.$data['imie'].'\',
						\''.$data['nazwisko'].'\',
						\''.$data['stopien_naukowy'].'\',
						\''.$data['email'].'\',
						\''.$data['telefon'].'\',
						\''.$data['pwd'].'\',
						\'1\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_haslo($err)
{
	echo "<form action=\"admin.php?action=admin_haslo\" method=\"POST\">";
	echo "<table>";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Hasło zostało zmienione!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Podane hasła są różne!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Email jest błędny!</span></td></tr>";
			break;
		case 4:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Stare hasło jest błędne!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Email</th><td><input type=\"text\" name=\"email\" value=\""; echo stripslashes($wiersz['email']); echo "\" /></td></tr>";
		echo "<tr><th>Stare hasło</th><td><input type=\"password\" name=\"pwd\" /></td></tr>";
		echo "<tr><th>Nowe hasło</th><td><input type=\"password\" name=\"pwd_n\" /></td></tr>";
		echo "<tr><th>Potwierdź</th><td><input type=\"password\" name=\"pwd_conf\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_zmhaslo(&$db)
{
	$data = array(	'email' => vs($_POST['email']),
					'pwd' => vs($_POST['pwd']),
					'pwd_n' => vs($_POST['pwd_n']),
					'pwd_conf' => vs($_POST['pwd_conf'])
					);
	$query = sprintf("SELECT email, haslo FROM prowadzacy WHERE email = '%s'",$data['email']);
	$wynik = $db->query($query);
	if ($wynik->num_rows != 1 ) 
	{
		$wynik->free();
		return 2;
	}
	$wiersz = $wynik->fetch_assoc();
	if ($wiersz['haslo'] != $data['pwd']) return 4;
	if ($data['pwd_n']!=$data['pwd_conf']) return 1;
	$query = sprintf("UPDATE prowadzacy SET haslo='%s' WHERE email='%s'",$data['pwd_n'], $data['email']);
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
?>