<?php
function add_admin_box_ogloszenie($err)
{
	echo "<form action=\"admin.php?action=admin_ogloszenie\" method=\"POST\" accept-charset=\"UTF-8\">";
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
								<option value=\"3\">Normalny</option>
								<option value=\"2\">Wysoki</option>
								<option value=\"1\">Bardzo wysoki</option>
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
	$data['ogloszenie'] = iconv(mb_detect_encoding($data['ogloszenie']),"UTF-8",$data['ogloszenie']);
	$query = 'SELECT ogloszenie FROM ogloszenia WHERE ogloszenie = \''.$data['ogloszenie'].'\' and data_wygasniecia= \''.$data['data_wygasniecia'].'\'';
	$wynik = $db->query($query);
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
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
function add_admin_box_ogloszenie_grupa($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_ogloszenie_grupa\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Ogłoszenie zostało dodane!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma studentów w grupie!</span></td></tr>";
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
		$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
		$wynik = $db->query($query);
		$wiersz = $wynik->fetch_assoc();
		$id=$wiersz['id_osoby'];
		$query='SELECT kod_grupy FROM grupa WHERE id_osoby = '.$id;
		$wynik = $db->query($query);
		if ($wynik->num_rows > 0)
		{
			echo "<tr><th>Kod grupy</th><td><select name=\"kod_grupy\">";
			for($i=0;$i<$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['kod_grupy']."\">".$wiersz['kod_grupy']."</option>";
			}
			echo "</select></td></tr>";
		}
		else
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma grup do których można by było przypisać ogłoszenie!</span></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodogloszenie_grupa(&$db)
{
	$data = array(	'ogloszenie' => vs($_POST['ogloszenie']),
					'data' => vs($_POST['data']),
					'data_wygasniecia' => vs($_POST['data_wygasniecia']),
					'kod_grupy' => vs($_POST['kod_grupy']),
					'id_osoby' => vs($_POST['id_osoby']),
					'id_ogl',
					'id_grupy',
					'indeks'
					);
	$data['ogloszenie'] = iconv(mb_detect_encoding($data['ogloszenie']),"UTF-8",$data['ogloszenie']);
	$query = 'SELECT ogloszenie FROM ogloszenia_stud WHERE ogloszenie = \''.$data['ogloszenie'].'\' and data_wygasniecia= \''.$data['data_wygasniecia'].'\'';
	$wynik = $db->query($query);
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	$query = 'INSERT INTO ogloszenia_stud (ogloszenie, ID_osoby, data, data_wygasniecia)
				VALUES (\''.$data['ogloszenie'].'\',
						\''.$data['id_osoby'].'\',
						NOW(),
						\''.$data['data_wygasniecia'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	$query = 'SELECT id_ogl FROM ogloszenia_stud WHERE ogloszenie = \''.$data['ogloszenie'].'\' and data_wygasniecia= \''.$data['data_wygasniecia'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_ogl'] = $wiersz['id_ogl'];
	$query = 'SELECT id_grupy FROM grupa WHERE kod_grupy = \''.$data['kod_grupy'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_grupy'] = $wiersz['id_grupy'];
	$query = 'SELECT indeks FROM asoc_stud_grupa WHERE id_grupy = '.$data['id_grupy'].'';
	$wynik = $db->query($query);
	if($wynik->num_rows == 0)
		return 1;
	for($i=0;$i<$wynik->num_rows;$i++)
	{
		$wiersz = $wynik->fetch_assoc();
		$data['indeks'] = $wiersz['indeks'];
		$query = 'INSERT INTO asoc_ogl_stud (indeks, id_ogl)
							VALUES (\''.$data['indeks'].'\',
									\''.$data['id_ogl'].'\')';
		$wyn = $db->query($query);
		if ($db->affected_rows==0) return 3;
	}
	return 0;
}

function add_admin_box_ogloszenie_student($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_ogloszenie_student\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Ogłoszenie zostało dodane!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma studentów w grupie!</span></td></tr>";
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
		$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
		$wynik = $db->query($query);
		$wiersz = $wynik->fetch_assoc();
		$id=$wiersz['id_osoby'];
		$query='SELECT indeks, imie, nazwisko FROM student natural join asoc_stud_grupa natural join grupa WHERE id_osoby = '.$id;
		$wynik = $db->query($query);
		if ($wynik->num_rows > 0)
		{
			echo "<tr><th>Student</th><td><select name=\"indeks\">";
			for($i=0;$i<$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['indeks']."\">".$wiersz['indeks']." ".$wiersz['nazwisko']." ".$wiersz['imie']."</option>";
			}
			echo "</select></td></tr>"; 
		}
		else
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma studentów do których można by było przypisać ogłoszenie!</span></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodogloszenie_student(&$db)
{
	$data = array(	'ogloszenie' => vs($_POST['ogloszenie']),
					'data' => vs($_POST['data']),
					'data_wygasniecia' => vs($_POST['data_wygasniecia']),
					'id_osoby',
					'id_ogl',
					'indeks' => vs($_POST['indeks'])
					);
	$data['ogloszenie'] = iconv(mb_detect_encoding($data['ogloszenie']),"UTF-8",$data['ogloszenie']);
	$query = 'SELECT ogloszenie FROM ogloszenia_stud WHERE ogloszenie = \''.$data['ogloszenie'].'\' and data_wygasniecia= \''.$data['data_wygasniecia'].'\'';
	$wynik = $db->query($query);
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	$query = 'INSERT INTO ogloszenia_stud (ogloszenie, ID_osoby, data, data_wygasniecia)
				VALUES (\''.$data['ogloszenie'].'\',
						\''.$data['id_osoby'].'\',
						NOW(),
						\''.$data['data_wygasniecia'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	$query = 'SELECT id_ogl FROM ogloszenia_stud WHERE ogloszenie = \''.$data['ogloszenie'].'\' and data_wygasniecia= \''.$data['data_wygasniecia'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_ogl'] = $wiersz['id_ogl'];
	$query = 'INSERT INTO asoc_ogl_stud (indeks, id_ogl)
						VALUES (\''.$data['indeks'].'\',
								\''.$data['id_ogl'].'\')';
	$wyn = $db->query($query);
	if ($db->affected_rows==0) return 3;
	return 0;
}
function ogloszenia(&$db)
{
	echo "<table>";
	echo "<tr><th>Ogłoszenie</th><th>Data</th><th>Data wygaśnięcia</th><th>Priorytet (3-min, 1-max)</th></tr>";
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$id=$wiersz['id_osoby'];
	$query = 'SELECT id_ogloszenia, ogloszenie, data, data_wygasniecia, priorytet FROM ogloszenia WHERE id_osoby = \''.$id.'\' order by id_ogloszenia';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		for($i=0;$i<=$wynik->num_rows;$i++)
		{
			$wiersz = $wynik->fetch_assoc();
			echo "<tr><th>".$wiersz['ogloszenie']."</th><td>".$wiersz['data']."</td><td>".$wiersz['data_wygasniecia']."</td><td>".$wiersz['priorytet']."</td></tr>";
		}
	}
	echo "</table>";
}
?>