<?php
function add_admin_box_grupa($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_grupa\" method=\"POST\" accept-charset=\"UTF-8\">";
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
		$query='SELECT kod_kursu, przedmiot FROM przedmioty';
		$wynik = $db->query($query);
		if ($wynik->num_rows > 0)
		{
			echo "<tr><th>Nazwa kursu</th><td><select name=\"kod_kursu\">";
			for($i=0;$i<$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['kod_kursu']."\">".$wiersz['kod_kursu'].", ".$wiersz['przedmiot']."</option>";
			}
			echo "</select></td></tr>";
		}
		else
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma kursów do których można by było przypisać grupę!</span></td></tr>";
		echo "<tr><th>Termin</th><td><input type=\"text\" name=\"termin\" value=\""; echo stripslashes($wiersz['termin']); echo "\" />(np. pn/TP 09:15-11:00)</td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodgrupa(&$db)
{
	$data = array(	'kod_kursu' => vs($_POST['kod_kursu']),
					'kod_grupy' => vs($_POST['kod_grupy']),
					'termin' => vs($_POST['termin']),
					'id_osoby'
					);
	$query = 'SELECT kod_grupy FROM grupa WHERE kod_grupy = \''.$data['kod_grupy'].'\'';
	$wynik = $db->query($query);
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$data['termin'] = iconv(mb_detect_encoding($data['termin']),"UTF-8",$data['termin']);
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	$query = 'INSERT INTO grupa (kod_grupy, kod_kursu, termin, id_osoby)
				VALUES (\''.$data['kod_grupy'].'\',
						\''.$data['kod_kursu'].'\',
						\''.$data['termin'].'\',
						\''.$data['id_osoby'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_grupacsv($err)
{
	echo "<form action=\"admin.php?action=admin_grupacsv\" method=\"POST\" accept-charset=\"UTF-8\" enctype=\"multipart/form-data\">";
	echo "<table class=\"formularz\">";
	
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Grupa została dodana!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie udało się otworzyć pliku!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie udało się przesłać pliku!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
	echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"30000\">";
	echo "<tr><th>Plik csv z danymi grupy</th><td><input type=\"file\" name=\"pliczek\"></td></tr>";
	echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodgrupacsv(&$db)
{
	$data = array(	'id_osoby',
					'kod_kursu',
					'kod_grupy',
					'przedmiot',
					'indeks',
					'imie',
					'nazwisko',
					'id_grupy',
					'termin'
					);
	$plik_tmp = $_FILES['pliczek']['tmp_name'];
	$plik_nazwa = $_FILES['pliczek']['name'];
	$plik_rozmiar = $_FILES['pliczek']['size'];

	if(is_uploaded_file($plik_tmp)) {
		move_uploaded_file($plik_tmp,"./".$plik_nazwa);
		$handle = fopen($plik_nazwa,rt);
		if(!$handle) return 1;
	}
	else 
	{
		return 2;
	}
	while(($dane = fgetcsv($handle,0,';',' ')) != FALSE)
	{
		if(iconv(mb_detect_encoding($dane[0]),"UTF-8",$dane[0]) == "Politechnika Wrocławska" || $dane[0] == "Rok akademicki" || $dane[0] == "Typ kalendarza" || $dane[0] == "Lp." || $dane[0] == "Semestr" || iconv(mb_detect_encoding($dane[0]),"UTF-8",$dane[0]) == "Prowadzący")
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
		else if($dane[0] == "Termin")
		{
			$data['termin'] = $dane[1];
			$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
			$wynik = $db->query($query);
			$wiersz = $wynik->fetch_assoc();
			$data['id_osoby']=$wiersz['id_osoby'];
			$query = 'SELECT kod_kursu FROM przedmioty WHERE kod_kursu = \''.$data['kod_kursu'].'\'';
			$wynik = $db->query($query);
			if ($wynik->num_rows == 0 ) 
			{
				if(substr($data['kod_kursu'],-1)=='w' or substr($data['kod_kursu'],-1)=='W') $data['forma']='wyklad';
				else if(substr($data['kod_kursu'],-1)=='c' or substr($data['kod_kursu'],-1)=='C') $data['forma']='cwiczenia';
				else if(substr($data['kod_kursu'],-1)=='l' or substr($data['kod_kursu'],-1)=='L') $data['forma']='laboratorium';
				else if(substr($data['kod_kursu'],-1)=='p' or substr($data['kod_kursu'],-1)=='P') $data['forma']='projekt';
				else if(substr($data['kod_kursu'],-1)=='s' or substr($data['kod_kursu'],-1)=='S') $data['forma']='seminarium';
				$query = 'INSERT INTO przedmioty (kod_kursu, przedmiot, forma)
							VALUES (\''.$data['kod_kursu'].'\',
									\''.$data['przedmiot'].'\',
									\''.$data['forma'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
			}
			$query = 'SELECT kod_grupy FROM grupa WHERE kod_grupy = \''.$data['kod_grupy'].'\'';
			$wynik = $db->query($query);
			if ($wynik->num_rows == 0 ) 
			{
				$query = 'INSERT INTO grupa (kod_kursu, ID_osoby, termin, kod_grupy)
							VALUES (\''.$data['kod_kursu'].'\',
									\''.$data['id_osoby'].'\',
									\''.$data['termin'].'\',
									\''.$data['kod_grupy'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
			}
		}
		else if($dane[0] == "Nazwa kursu")
		{
			$data['przedmiot']=iconv(mb_detect_encoding($dane[1]),"UTF-8",$dane[1]);
		}
		else 
		{
			$data['indeks']=substr($dane[1],4,10);
			$data['imie']=iconv(mb_detect_encoding($dane[3]),"UTF-8",$dane[3]);
			$data['imie']=substr($data['imie'],0,strpos($data['imie']," "));
			$data['nazwisko']=iconv(mb_detect_encoding($dane[2]),"UTF-8",$dane[2]);
			$query = 'SELECT indeks FROM student WHERE indeks = \''.$data['indeks'].'\'';
			$wynik = $db->query($query);
			if ($wynik->num_rows == 0 ) 
			{
				$query = 'INSERT INTO student (indeks, imie, nazwisko)
							VALUES (\''.$data['indeks'].'\',
									\''.$data['imie'].'\',
									\''.$data['nazwisko'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
			}
			$query = 'SELECT indeks, id_grupy FROM asoc_stud_grupa natural join grupa WHERE indeks = \''.$data['indeks'].'\' and kod_grupy = \''.$data['kod_grupy'].'\'';
			$wynik = $db->query($query);
			if ($wynik->num_rows == 0 ) 
			{
				$query = 'SELECT id_grupy FROM grupa WHERE kod_grupy = \''.$data['kod_grupy'].'\'';
				$wynik = $db->query($query);
				$wiersz = $wynik->fetch_assoc();
				$data['id_grupy']=$wiersz['id_grupy'];
				$query = 'INSERT INTO asoc_stud_grupa (indeks, id_grupy)
							VALUES (\''.$data['indeks'].'\',
									\''.$data['id_grupy'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
			}
		}
			
	}
	fclose($handle);
  //unlink($plik_nazwa);
	return 0;
}
function add_admin_box_edytuj_grupa($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_grupa_edytuj\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Grupa została edytowana!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
		$wynik = $db->query($query);
		$wiersz = $wynik->fetch_assoc();
		$id=$wiersz['id_osoby'];
		$query='SELECT id_grupy, kod_grupy, termin, kod_kursu, przedmiot FROM grupa natural join przedmioty WHERE id_osoby = \''.$id.'\'';
		$wynik = $db->query($query);
		if ($wynik->num_rows > 0)
		{
			echo "<tr><th>Grupa do edycji</th><td><select name=\"id_grupy\">";
			for($i=0;$i<=$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['id_grupy']."\">".$wiersz['kod_grupy'].", ".$wiersz['termin'].", ".$wiersz['kod_kursu'].", ".$wiersz['przedmiot']."</option>";
			}
			echo "</select></td></tr>";
		}
		else
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma grup do które można by było edytować!</span></td></tr>";
		echo "<tr><th>Termin</th><td><input type=\"text\" name=\"termin\" value=\""; echo stripslashes($wiersz['termin']); echo "\" />(np. pn/TP 09:15-11:00)</td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Edytuj\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_edytujgrupa(&$db)
{
	$data = array(	'id_grupy' => vs($_POST['id_grupy']),
					'termin' => vs($_POST['termin'])
					);
	$data['termin'] = iconv(mb_detect_encoding($data['termin']),"UTF-8",$data['termin']);
	$query = 'UPDATE grupa SET termin = \''.$data['termin'].'\' WHERE id_grupy = \''.$data['id_grupy'].'\'';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_usun_grupa($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_grupa_usun\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Grupa została usunięta!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
		$wynik = $db->query($query);
		$wiersz = $wynik->fetch_assoc();
		$id=$wiersz['id_osoby'];
		$query='SELECT id_grupy, kod_grupy, termin, kod_kursu, przedmiot FROM grupa natural join przedmioty WHERE id_osoby = \''.$id.'\'';
		$wynik = $db->query($query);
		if ($wynik->num_rows > 0)
		{
			echo "<tr><th>Grupa do usunięcia</th><td><select name=\"id_grupy\">";
			for($i=0;$i<=$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['id_grupy']."\">".$wiersz['kod_grupy'].", ".$wiersz['termin'].", ".$wiersz['kod_kursu'].", ".$wiersz['przedmiot']."</option>";
			}
			echo "</select></td></tr>";
		}
		else
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma grup do które można by było edytować!</span></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Usuń\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_usungrupa(&$db)
{
	$data = array(	'id_grupy' => vs($_POST['id_grupy'])
					);
	$query = 'SELECT * FROM oceny WHERE id_asoc_stud_grupa IN (SELECT id_asoc_stud_grupa FROM asoc_stud_grupa WHERE id_grupy = '.$data['id_grupy'].')';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		$query = 'DELETE FROM oceny WHERE id_asoc_stud_grupa IN (SELECT id_asoc_stud_grupa FROM asoc_stud_grupa WHERE id_grupy = '.$data['id_grupy'].')';
		$wynik = $db->query($query);
		if ($db->affected_rows==0) return 3;
	}
	$query = 'SELECT * FROM asoc_stud_grupa WHERE id_grupy = '.$data['id_grupy'].'';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		$query = 'DELETE FROM asoc_stud_grupa WHERE id_grupy = '.$data['id_grupy'].'';
		$wynik = $db->query($query);
		if ($db->affected_rows==0) return 3;
	}
	$query = 'DELETE FROM grupa WHERE id_grupy = '.$data['id_grupy'].'';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function grupy(&$db)
{
	echo "<table>";
	echo "<tr><th>Kod grupy</th><th>Termin</th><th>Kurs</th><th>Forma</th></tr>";
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$id=$wiersz['id_osoby'];
	$query = 'SELECT id_grupy, kod_grupy, termin, przedmiot, forma FROM grupa natural join przedmioty WHERE id_osoby = \''.$id.'\' order by id_grupy';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		for($i=0;$i<=$wynik->num_rows;$i++)
		{
			$wiersz = $wynik->fetch_assoc();
			echo "<tr><th>".$wiersz['kod_grupy']."</th><td>".$wiersz['termin']."</td><td>".$wiersz['przedmiot']."</td><td>".$wiersz['forma']."</td></tr>";
		}
	}
	echo "</table>";
}
?>