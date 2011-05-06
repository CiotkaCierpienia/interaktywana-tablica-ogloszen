<?php
function add_admin_box($err)
{
	echo "<form action=\"admin.php?action=admin_mgmt\" method=\"POST\">";
	echo "<table class=\"formularz\">";
	
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
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
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
	echo "<table class=\"formularz\">";
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
function add_admin_box_konsultacje($err)
{
	echo "<form action=\"admin.php?action=admin_konsultacje\" method=\"POST\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Konsultacje zostały dodane!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Któreś pole nie jest wypełnione!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Konsultacje już istnieją!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Dzień</th><td><select name=\"dzien\">
								<option value=\"poniedzialek\">Poniedziałek</option>
								<option value=\"wtorek\">Wtorek</option>
								<option value=\"sroda\">Środa</option>
								<option value=\"czwartek\">Czwartek</option>
								<option value=\"piatek\">Piatek</option>
								<option value=\"sobota\">Sobota</option>
								<option value=\"niedziela\">Niedziela</option>
					</select></td></tr>"; echo stripslashes($wiersz['dzien']); 
					//echo "</td></tr>";
		echo "<tr><th>Od</th><td><input type=\"text\" name=\"od\" value=\""; echo stripslashes($wiersz['od']); echo "\" /></td></tr>";
		echo "<tr><th>Do</th><td><input type=\"text\" name=\"do\" value=\""; echo stripslashes($wiersz['do']); echo "\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodkonsultacje(&$db)
{
	$data = array(	'dzien' => vs($_POST['dzien']),
					'od' => vs($_POST['od']),
					'do' => vs($_POST['do']),
					'id_osoby' => vs($_POST['id_osoby'])
					);
	if ($data['od'] == NULL || $data['do'] == NULL) return 1;
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
	//$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	$query = 'SELECT id_osoby, dzien, DATE_FORMAT(od_,\'%H:%m\'), DATE_FORMAT(do_,\'%H:%m\') FROM konsultacje WHERE id_osoby = \''.$data['id_osoby'].'\' and dzien = \''.$data['dzien'].'\' and od_ = \''.$data['od'].'\' and do_ = \''.$data['do'].'\'';
	echo '$query $data[\'od\'] $data[\'do\']';
	echo "<br>";
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	
	$query = 'INSERT INTO konsultacje (dzien, od_, do_, ID_osoby)
				VALUES (\''.$data['dzien'].'\',
						\''.$data['od'].'\',
						\''.$data['do'].'\',
						\''.$data['id_osoby'].'\')';
	//echo "$query<br>";
	$wynik = $db->query($query);
	//echo $db->affected_rows;
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_przedmiot($err)
{
	echo "<form action=\"admin.php?action=admin_przedmiot\" method=\"POST\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Przedmiot został dodany!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Przedmiot juz istnieje!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Kod kursu</th><td><input type=\"text\" name=\"kod_kursu\" value=\""; echo stripslashes($wiersz['kod_kursu']); echo "\" /></td></tr>";
		echo "<tr><th>Nazwa przedmiotu</th><td><input type=\"text\" name=\"przedmiot\" value=\""; echo stripslashes($wiersz['przedmiot']); echo "\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodprzedmiot(&$db)
{
	$data = array(	'kod_kursu' => vs($_POST['kod_kursu']),
					'przedmiot' => vs($_POST['przedmiot'])
					);
	$query = 'SELECT kod_kursu FROM przedmioty WHERE kod_kursu = \''.$data['kod_kursu'].'\'';
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'INSERT INTO przedmioty (kod_kursu, przedmiot)
				VALUES (\''.$data['kod_kursu'].'\',
						\''.$data['przedmiot'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_grupa($err)
{
	echo "<form action=\"admin.php?action=admin_grupa\" method=\"POST\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Grupa została dodana!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Grupa juz istnieje!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Kod grupy</th><td><input type=\"text\" name=\"kod_grupy\" value=\""; echo stripslashes($wiersz['kod_grupy']); echo "\" /></td></tr>";
		$query='SELECT kod_kursu, przedmiot FROM przedmioty WHERE id_osoby=SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
		$wynik = $db->query($query);
		if ($wynik->num_rows() > 0)
		{
			echo "<tr><th>Nazwa przedmiotu</th><td><select name=\"przedmiot\">";
			for($i=0;$i<=$wynik->num_rows();$i++)
			{
				$wiersz=mysql_fetch_row($wynik);
				echo "<option value=\"kod_kursu\">$wiersz[2]</option>";
			}
			echo "</select></td></tr>"; echo stripslashes($wiersz['kod_kursu']); 
		}
		else
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma kursów do których można by było przypisać grupę!</span></td></tr>";

		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodgrupa(&$db)
{
	$data = array(	'kod_kursu' => vs($_POST['kod_kursu']),
					'kod_grupy' => vs($_POST['kod_grupy']),
					'id_osoby'
					);
	$query = 'SELECT kod_kursu FROM przedmioty WHERE kod_kursu = \''.$data['kod_kursu'].'\'';
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	$query = 'INSERT INTO grupy (kod_grupy, kod_kursu, id_osoby)
				VALUES (\''.$data['kod_grupy'].'\',
						\''.$data['kod_kursu'].'\',
						\''.$data['id_osoby'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_ogloszenie($err)
{
	echo "<form action=\"admin.php?action=admin_ogloszenie\" method=\"POST\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Ogłoszenie zostało dodane!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Ogłoszenie juz istnieje!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Ogłoszenie</th><td><textarea rows=10 cols=90 name=\"ogloszenie\" value=\"\">"; echo stripslashes($wiersz['ogloszenie']); echo "</textarea></td></tr>";
		echo "<tr><th>Data wygaśnięcia</th><td><input type=\"text\" name=\"data_wygasniecia\" value=\""; echo stripslashes($wiersz['data_wygasniecia']); echo "\" />(RRRR.MM.DD)</td></tr>";
		echo "<tr><th>Priorytet</th><td><select name=\"priorytet\">
								<option value=\"1\">Bardzo wysoki</option>
								<option value=\"2\">Wysoki</option>
								<option value=\"3\">Normalny</option>
					</select></td></tr>"; echo stripslashes($wiersz['priorytet']); 
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodogloszenie(&$db)
{
	$data = array(	'ogloszenie' => vs($_POST['ogloszenie']),
					'data' => vs($_POST['data']),
					'data_wygasniecia' => vs($_POST['data_wygasniecia']),
					'priorytet' => vs($_POST['priorytet']),
					'id_osoby' => vs($_POST['id_osoby'])
					);
	$query = 'SELECT ogloszenie FROM ogloszenia WHERE ogloszenie = \''.$data['ogloszenie'].'\' and data_wygasniecia= \''.$data['data_wygasniecia'].'\'';
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	//echo $query;
	$wynik = $db->query($query);
	//echo $wynik->num_rows;
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	//$data['data'] = DATE(NOW());
	$query = 'INSERT INTO ogloszenia (ogloszenie, ID_osoby, data, data_wygasniecia, priorytet)
				VALUES (\''.$data['ogloszenie'].'\',
						\''.$data['id_osoby'].'\',
						NOW(),
						\''.$data['data_wygasniecia'].'\',
						\''.$data['priorytet'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_grupacsv($err)
{
	echo "<form action=\"admin.php?action=admin_grupacsv\" method=\"POST\">";
	echo "<table class=\"formularz\">";
	
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Ogłoszenie zostało dodane!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie udało się otworzyć pliku!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Ogłoszenie juz istnieje!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
	echo "<tr><th>Plik csv z danymi grupy</th><td><input type=\"file\" name=\"plik\"/></td></tr>";
	echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodgrupacsv(&$db)
{
	$data = array(	'plik' => vs($_POST['plik']),
					'id_osoby',
					'kod_kursu',
					'kod_grupy',
					'przedmiot',
					'indeks',
					'imie',
					'nazwisko'
					);
	echo $data['kod_kursu'];
	$handle = fopen($data['plik'],rt);
	if(!$handle) return 1;
	while(($dane = fgetcsv($handle,0,';',' ')) != FALSE)
	{
		if(iconv(mb_detect_encoding($dane[0]),"UTF-8",$dane[0]) == "Politechnika Wrocławska" || $dane[0] == "Rok akademicki" || $dane[0] == "Typ kalendarza" || $dane[0] == "Termin" || $dane[0] == "Lp." || $dane[0] == "Semestr" || iconv(mb_detect_encoding($dane[0]),"UTF-8",$dane[0]) == "Prowadzący")
		{
			continue;
		}
		else if($dane[0] == "Kod grupy")
		{
			$data['kod_grupy']=$dane[1];
		}
		else if($dane[0] == "Kod kursu")
		{
			$data['kod_kursu']=$dane[1];
		}
		else if($dane[0] == "Nazwa kursu")
		{
			$data['przedmiot']=$dane[1];
			$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
			$wynik = $db->query($query);
			$wiersz = $wynik->fetch_assoc();
			$data['id_osoby']=$wiersz['id_osoby'];
			$query = 'SELECT kod_kursu FROM przedmioty WHERE kod_kursu = \''.$data['kod_kursu'].'\'';
			//echo $query;
			$wynik = $db->query($query);
			//echo $wynik->num_rows;
			if ($wynik->num_rows == 0 ) 
			{
				$query = 'INSERT INTO przedmioty (kod_kursu, przedmiot)
							VALUES (\''.$data['kod_kursu'].'\',
									\''.$data['przedmiot'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
			}
			$query = 'SELECT kod_grupy FROM grupa WHERE kod_grupy = \''.$data['kod_grupy'].'\'';
			//echo $query;
			$wynik = $db->query($query);
			//echo $wynik->num_rows;
			if ($wynik->num_rows == 0 ) 
			{
				$query = 'INSERT INTO grupa (kod_kursu, ID_osoby, kod_grupy)
							VALUES (\''.$data['kod_kursu'].'\',
									\''.$data['id_osoby'].'\',
									\''.$data['kod_grupy'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
			}
		}
		else 
		{
			$data['indeks']=substr($dane[1],4,10);
			$data['imie']=iconv(mb_detect_encoding($dane[3]),"UTF-8",$dane[3]);
			$data['imie']=substr($data['imie'],0,strpos($data['imie']," "));
			$data['nazwisko']=iconv(mb_detect_encoding($dane[2]),"UTF-8",$dane[2]);
			//echo $dane[0];
			//echo "<br />";
			//echo $data['indeks'];
			//echo "<br />";
			//echo "blablablabla";
			$query = 'SELECT indeks FROM student WHERE indeks = \''.$data['indeks'].'\'';
			//echo $query;
			$wynik = $db->query($query);
			//echo $wynik->num_rows;
			if ($wynik->num_rows == 0 ) 
			{
				$query = 'INSERT INTO student (indeks, imie, nazwisko)
							VALUES (\''.$data['indeks'].'\',
									\''.$data['imie'].'\',
									\''.$data['nazwisko'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
			}
		}
			
	}
	fclose($handle);
	return 0;
}

?>