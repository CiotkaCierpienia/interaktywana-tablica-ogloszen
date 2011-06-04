<?php
function add_admin_box_przedmiot($err)
{
	echo "<form action=\"admin.php?action=admin_przedmiot\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Kurs został dodany!</span></td></tr>";
			break;
		case 2:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Kurs juz istnieje!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		echo "<tr><th>Kod kursu</th><td><input type=\"text\" name=\"kod_kursu\" value=\""; echo stripslashes($wiersz['kod_kursu']); echo "\" /></td></tr>";
		echo "<tr><th>Nazwa kursu</th><td><input type=\"text\" name=\"przedmiot\" value=\""; echo stripslashes($wiersz['przedmiot']); echo "\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Wyślij\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_dodprzedmiot(&$db)
{
	$data = array(	'kod_kursu' => vs($_POST['kod_kursu']),
					'przedmiot' => vs($_POST['przedmiot']),
					'forma'
					);
	$data['przedmiot'] = iconv(mb_detect_encoding($data['przedmiot']),"UTF-8",$data['przedmiot']);
	$query = 'SELECT kod_kursu FROM przedmioty WHERE kod_kursu = \''.$data['kod_kursu'].'\'';
	$wynik = $db->query($query);
	if ($wynik->num_rows > 0 ) 
	{
		$wynik->free();
		return 2;
	}
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
	else return 0;
}
function add_admin_box_edytuj_przedmiot($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_przedmiot_edytuj\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Kurs został edytowany!</span></td></tr>";
			break;
		case 3:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Błąd aktualizacji bazy danych!</span></td></tr>";
			break;
		default:
			break;
	}
		$query = 'SELECT kod_kursu, przedmiot FROM przedmioty';
		$wynik = $db->query($query);
		if ($wynik->num_rows > 0 ) 
		{
			echo "<tr><th>Kurs do zmiany</th><td><select name=\"kod_kursu\">";
			for($i=0;$i<=$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['kod_kursu']."\">".$wiersz['kod_kursu'].", ".$wiersz['przedmiot']."</option>";
			}
			echo "</select></td></tr>";
		}
		else
			echo "<tr><td colspan=\"2\"><span class=\"err\">Nie ma kursów do edycji!</span></td></tr>";
		echo "<tr><th>Nazwa kursu</th><td><input type=\"text\" name=\"przedmiot\" value=\""; echo stripslashes($wiersz['przedmiot']); echo "\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Edytuj\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_edytujprzedmiot(&$db)
{
	$data = array(	'kod_kursu' => vs($_POST['kod_kursu']),
					'przedmiot' => vs($_POST['przedmiot'])
					);
	$data['przedmiot'] = iconv(mb_detect_encoding($data['przedmiot']),"UTF-8",$data['przedmiot']);
	$query = 'UPDATE przedmioty SET przedmiot = \''.$data['przedmiot'].'\' WHERE kod_kursu = \''.$data['kod_kursu'].'\'';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function przedmioty(&$db)
{
	echo "<table>";
	echo "<tr><th>Kod kursu</th><th>Nazwa kursu</th><th>Forma kursu</th></tr>";
	$query = 'SELECT kod_kursu, przedmiot, forma FROM przedmioty';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		for($i=0;$i<=$wynik->num_rows;$i++)
		{
			$wiersz = $wynik->fetch_assoc();
			echo "<tr><th>".$wiersz['kod_kursu']."</th><td>".$wiersz['przedmiot']."</td><td>".$wiersz['forma']."</td></tr>";
		}
	}
	echo "</table>";
}
?>