<?php
function add_admin_box_ocenycsv($err)
{
	echo "<form action=\"admin.php?action=admin_ocenycsv\" method=\"POST\" accept-charset=\"UTF-8\" enctype=\"multipart/form-data\">";
	echo "<table>";
	
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Oceny zostały dodane!</span></td></tr>";
			break;
		case 1:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie udało się otworzyć pliku!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Niepoprawny kod grupy!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		case 4:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Niepoprawna ocena, wczytywanie zostało przerwane!</span></td></tr>";
			break;
		case 5:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie udało się przesłać pliku!</span></td></tr>";
			break;
		default:
			break;
	}
	echo "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"30000\">";
	echo "<tr></tr>";
	echo "<tr></tr>";
	echo "<tr><td  colspan=\"2\">Pliki pobrane z Edukacji.CL powinny być skonwertowane do kodowania UTF-8, inne będą odrzucane.</td></tr>";
	echo "<tr></tr>";
	echo "<tr><th>Plik csv z ocenami grupy</th><td><input type=\"file\" name=\"pliczek\" size=\"80\"></td></tr>";
	echo "<tr><th>Rodzaj oceny</th><td><input type=\"text\" name=\"typ_oceny\"/ size=\"80\"></td></tr>";
	echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodocenycsv(&$db)
{
	$data = array(	'id_osoby',
					'kod_grupy',
					'indeks',
					'imie',
					'nazwisko',
					'id_grupy',
					'ocena',
					'id_oceny',
					'typ_oceny' => vs($_POST['typ_oceny']),
					'id_soceny',
					'id_asoc_stud_grupa_',
					'typ',
					'id_typu',
					'info_dod',
					'data_w'
					);
	$plik_tmp = $_FILES['pliczek']['tmp_name'];
	$plik_nazwa = $_FILES['pliczek']['name'];
	$plik_rozmiar = $_FILES['pliczek']['size'];

	if(is_uploaded_file($plik_tmp)) {
		move_uploaded_file($plik_tmp,"./".$plik_nazwa);
		$handle = fopen($plik_nazwa,rt);
		if(!$handle) return 1;
		$test = file_get_contents($plik_nazwa);
		$data['kodowanie'] = mb_detect_encoding($test);
			if($data['kodowanie'] !="UTF-8")
			{
				fclose($handle);
				unlink($plik_nazwa);
				return 4;
			}
	}
	else 
	{
		return 5;
	}
	while(($dane = fgetcsv($handle,0,';',' ')) != FALSE)
	{
		if($dane[0] == "Nazwa kursu" || 
			$dane[0] == "Kod kursu" || 
			iconv(mb_detect_encoding($dane[0]),"UTF-8",$dane[0]) == "Politechnika Wrocławska" || 
			$dane[0] == "Rok akademicki" || 
			$dane[0] == "Typ kalendarza" || 
			$dane[0] == "Termin" || 
			$dane[0] == "Lp." || 
			$dane[0] == "Semestr" || 
			iconv(mb_detect_encoding($dane[0]),"UTF-8",$dane[0]) == "Prowadzący")
		{
			continue;
		}
		else if($dane[0] == "Kod grupy")
		{
			$data['kod_grupy']=$dane[1];
			$query = 'SELECT id_grupy FROM grupa WHERE kod_grupy = \''.$data['kod_grupy'].'\'';
			$wynik = $db->query($query);
			if ($wynik->num_rows == 0 )
			{
				return 2;
			}
			$wiersz = $wynik->fetch_assoc();
			$data['id_grupy'] = $wiersz['id_grupy'];
		}
		else 
		{
			$query = 'SELECT id_typu FROM typy_ocen WHERE nazwa_typu = \''.$data['typ_oceny'].'\'';
			$wynik = $db->query($query);
			if ($wynik->num_rows == 0 )
			{
				$query = 'INSERT INTO typy_ocen (nazwa_typu)
							VALUES (\''.$data['typ_oceny'].'\')';
				$wynik = $db->query($query);
				if ($db->affected_rows==0) return 3;
				$query = 'SELECT id_typu FROM typy_ocen WHERE nazwa_typu = \''.$data['typ_oceny'].'\'';
				$wynik = $db->query($query);
			}
			$wiersz = $wynik->fetch_assoc();
			$data['id_typu'] = $wiersz['id_typu'];
			$data['indeks']=substr($dane[1],4,10);
			$data['imie']=iconv(mb_detect_encoding($dane[3]),"UTF-8",$dane[3]);
			$data['imie']=substr($data['imie'],0,strpos($data['imie']," "));
			$data['nazwisko']=iconv(mb_detect_encoding($dane[2]),"UTF-8",$dane[2]);
			$data['ocena']=$dane[7];
			$data['data_w']=$dane[8];
			$data['info_dod']=$dane[9];
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
			$query = 'SELECT id_asoc_stud_grupa, indeks, id_grupy FROM asoc_stud_grupa natural join grupa WHERE indeks = \''.$data['indeks'].'\' and kod_grupy = \''.$data['kod_grupy'].'\'';
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
				$query = 'SELECT id_asoc_stud_grupa, indeks, id_grupy FROM asoc_stud_grupa natural join grupa WHERE indeks = \''.$data['indeks'].'\' and kod_grupy = \''.$data['kod_grupy'].'\'';
				$wynik = $db->query($query);
			}
			$wiersz = $wynik->fetch_assoc();
			$data['id_asoc_stud_grupa']=$wiersz['id_asoc_stud_grupa'];
			if($data['ocena']!=NULL)
			{
				$query = 'SELECT id_soceny, ocena FROM slownik_ocen WHERE ocena = \''.$data['ocena'].'\'';
				$wynik = $db->query($query);
				if ($wynik->num_rows == 0 )
				{
					return 4;
				}
				$wiersz = $wynik->fetch_assoc();
				$data['id_soceny']=$wiersz['id_soceny'];
				$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
				$wynik = $db->query($query);
				$wiersz = $wynik->fetch_assoc();
				$data['id_osoby']=$wiersz['id_osoby'];
				if($data['data_w'] == NULL)
				{
					$data['data_w']=date("Y-m-d");
				}
				$query = 'SELECT id_asoc_stud_grupa, id_typu FROM oceny 
							WHERE id_asoc_stud_grupa = \''.$data['id_asoc_stud_grupa'].'\' and id_typu = \''.$data['id_typu'].'\'';
				$wynik = $db->query($query);
				if ($wynik->num_rows == 0 ) 
				{
					$query = 'INSERT INTO oceny (id_asoc_stud_grupa, id_soceny, inf_dod, id_typu, data_wprowadzenia)
									VALUES (\''.$data['id_asoc_stud_grupa'].'\',
											\''.$data['id_soceny'].'\',
											\''.$data['info_dod'].'\',
											\''.$data['id_typu'].'\',
											\''.$data['data_w'].'\')';
					$wynik = $db->query($query);
					if ($db->affected_rows==0) return 3;
				} 
				else
				{
					$query = 'UPDATE oceny SET id_soceny = \''.$data['id_soceny'].'\', 
									inf_dod, id_typu = \''.$data['info_dod'].'\', 
									data_wprowadzenia = \''.$data['data_w'].'\' 
									WHERE id_asoc_stud_grupa = \''.$data['id_asoc_stud_grupa'].'\' and id_typu = \''.$data['id_typu'].'\'';
					$wynik = $db->query($query);
				}
			}
		}
			
	}
	fclose($handle);
	unlink($plik_nazwa);
	return 0;
}
function add_admin_box_student($err)
{
	echo "<form action=\"admin.php?action=admin_student\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Student został dodany!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Student juz istnieje!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Indeks</th><td><input type=\"text\" name=\"indeks\" value=\""; echo stripslashes($wiersz['indeks']); echo "\" /></td></tr>";
		echo "<tr><th>Nazwisko</th><td><input type=\"text\" name=\"nazwisko\" value=\""; echo stripslashes($wiersz['nazwisko']); echo "\" /></td></tr>";
		echo "<tr><th>Imię</th><td><input type=\"text\" name=\"imie\" value=\""; echo stripslashes($wiersz['imie']); echo "\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dod_student(&$db)
{
	$data = array(	'indeks' => vs($_POST['indeks']),
					'nazwisko' => vs($_POST['nazwisko']),
					'imie' => vs($_POST['imie'])
					);
	$data['imie'] = iconv(mb_detect_encoding($data['imie']),"UTF-8",$data['imie']);
	$data['nazwisko'] = iconv(mb_detect_encoding($data['nazwisko']),"UTF-8",$data['nazwisko']);
	$query = 'SELECT indeks FROM student WHERE indeks = \''.$data['indeks'].'\'';
	$wynik = $db->query($query);
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'INSERT INTO student (indeks, nazwisko, imie)
				VALUES (\''.$data['indeks'].'\',
						\''.$data['nazwisko'].'\',
						\''.$data['imie'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	return 0;
}
function add_admin_box_edytuj_student($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_student_edytuj\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Student został edytowany!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		$query = 'SELECT indeks, imie, nazwisko FROM student';
		$wynik = $db->query($query);
		if($wynik->num_rows > 0)
		{	
			echo "<tr><th>Student do edycji</th><td><select name=\"indeks\">";
			for($i=0;$i<=$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['indeks']."\">".$wiersz['indeks']." ".$wiersz['imie']." ".$wiersz['nazwisko']."</option>";
			}
			echo "</select></td></tr>";
		}
		echo "<tr><th>Nazwisko</th><td><input type=\"text\" name=\"nazwisko\" value=\""; echo stripslashes($wiersz['nazwisko']); echo "\" /></td></tr>";
		echo "<tr><th>Imię</th><td><input type=\"text\" name=\"imie\" value=\""; echo stripslashes($wiersz['imie']); echo "\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_edytuj_student(&$db)
{
	$data = array(	'indeks' => vs($_POST['indeks']),
					'nazwisko' => vs($_POST['nazwisko']),
					'imie' => vs($_POST['imie'])
					);
	$data['imie'] = iconv(mb_detect_encoding($data['imie']),"UTF-8",$data['imie']);
	$data['nazwisko'] = iconv(mb_detect_encoding($data['nazwisko']),"UTF-8",$data['nazwisko']);
	$query = 'UPDATE student SET nazwisko = \''.$data['nazwisko'].'\', imie = \''.$data['imie'].'\' WHERE indeks = \''.$data['indeks'].'\'';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	return 0;
}
function add_admin_box_usun_student($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_student_usun\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Student został usunięty!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		$query = 'SELECT indeks, imie, nazwisko FROM student ORDER BY indeks';
		$wynik = $db->query($query);
		if($wynik->num_rows > 0)
		{	
			echo "<tr><th>Student do usunięcia</th><td><select name=\"indeks\">";
			for($i=0;$i<=$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['indeks']."\">".$wiersz['indeks']." ".$wiersz['imie']." ".$wiersz['nazwisko']."</option>";
			}
			echo "</select></td></tr>";
		}
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Usuń\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_usun_student(&$db)
{
	$data = array(	'indeks' => vs($_POST['indeks'])
					);
	$query = 'SELECT * FROM oceny WHERE id_asoc_stud_grupa IN (SELECT id_asoc_stud_grupa FROM asoc_stud_grupa WHERE indeks = '.$data['indeks'].')';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		$query = 'DELETE FROM oceny WHERE id_asoc_stud_grupa IN (SELECT id_asoc_stud_grupa FROM asoc_stud_grupa WHERE indeks = '.$data['indeks'].')';
		$wynik = $db->query($query);
		if ($db->affected_rows==0) return 3;
	}
	$query = 'SELECT * FROM asoc_stud_grupa WHERE indeks = '.$data['indeks'].'';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		$query = 'DELETE FROM asoc_stud_grupa WHERE indeks = '.$data['indeks'].'';
		$wynik = $db->query($query);
		if ($db->affected_rows==0) return 3;
	}
	$query = 'SELECT * FROM asoc_ogl_stud WHERE indeks = '.$data['indeks'].'';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		$query = 'DELETE FROM asoc_ogl_stud WHERE indeks = '.$data['indeks'].'';
		$wynik = $db->query($query);
		if ($db->affected_rows==0) return 3;
	}
	$query = 'DELETE FROM student WHERE indeks = \''.$data['indeks'].'\'';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	return 0;
}
function add_admin_box_student_grupa($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_student_grupa\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Student został dodany!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Powiązanie już istnieje!</span></td></tr>";
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
		$query='SELECT indeks, imie, nazwisko FROM student';
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
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma studentów w bazie!</span></td></tr>";
		$query='SELECT id_grupy, kod_grupy FROM grupa WHERE id_osoby = '.$id;
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
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma grup do których można by było przypisać studenta!</span></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dod_student_grupa(&$db)
{
	$data = array(	'indeks' => vs($_POST['indeks']),
					'kod_grupy' => vs($_POST['kod_grupy']),
					'id_grupy'
					);
	$query='SELECT id_grupy FROM grupa WHERE kod_grupy = \''.$data['kod_grupy'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_grupy'] = $wiersz['id_grupy'];
	$query = 'SELECT indeks FROM asoc_stud_grupa WHERE indeks = '.$data['indeks'].' and id_grupy = '.$data['id_grupy'].'';
	$wynik = $db->query($query);
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
	$query = 'INSERT INTO asoc_stud_grupa (indeks, id_grupy)
				VALUES (\''.$data['indeks'].'\',
						\''.$data['id_grupy'].'\')';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	return 0;
}
?>