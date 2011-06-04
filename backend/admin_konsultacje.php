<?php
function add_admin_box_konsultacje($err)
{
	echo "<form action=\"admin.php?action=admin_konsultacje\" method=\"POST\" accept-charset=\"UTF-8\">";
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
		echo "<tr><th>Od</th><td><input type=\"text\" name=\"od\" value=\""; echo stripslashes($wiersz['od']); echo "\" />(HH:MM)</td></tr>";
		echo "<tr><th>Do</th><td><input type=\"text\" name=\"do\" value=\""; echo stripslashes($wiersz['do']); echo "\" />(HH:MM)</td></tr>";
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
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	$query = 'SELECT id_osoby, dzien, DATE_FORMAT(od_,\'%H:%m\'), DATE_FORMAT(do_,\'%H:%m\') FROM konsultacje WHERE id_osoby = \''.$data['id_osoby'].'\' and dzien = \''.$data['dzien'].'\' and od_ = \''.$data['od'].'\' and do_ = \''.$data['do'].'\'';
	$wynik = $db->query($query);
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
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function konsultacje(&$db)
{
	echo "<table>";
	echo "<tr><th>Dzień</th><th>Od</th><th>Do</th></tr>";
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$id=$wiersz['id_osoby'];
	$query = 'SELECT dzien, od_, do_ FROM konsultacje WHERE id_osoby = \''.$id.'\'';
	$wynik = $db->query($query);
	if($wynik->num_rows > 0)
	{
		for($i=0;$i<=$wynik->num_rows;$i++)
		{
			$wiersz = $wynik->fetch_assoc();
			echo "<tr><th>".$wiersz['dzien']."</th><td>".$wiersz['od_']."</td><td>".$wiersz['do_']."</td></tr>";
		}
	}
	echo "</table>";
}
function add_admin_box_edytuj_konsultacje($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_konsultacje_edytuj\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Konsultacje zostały edytowane!</span></td></tr>";
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
		$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
		$wynik = $db->query($query);
		$wiersz = $wynik->fetch_assoc();
		$id=$wiersz['id_osoby'];
		$query = 'SELECT id_konsultacji, dzien, od_, do_ FROM konsultacje WHERE id_osoby = \''.$id.'\'';
		$wynik = $db->query($query);
		
		if($wynik->num_rows > 0)
		{	
			echo "<tr><th>Konsultacje do zmiany</th><td><select name=\"id_konsultacji\">";
			for($i=0;$i<=$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['id_konsultacji']."\">".$wiersz['dzien']." ".$wiersz['od_']." ".$wiersz['do_']."</option>";
			}
			echo "</select></td></tr>"; echo stripslashes($wiersz['id_konsultacji']); 
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
		echo "<tr><th>Od</th><td><input type=\"text\" name=\"od\" value=\""; echo stripslashes($wiersz['od']); echo "\" /></td></tr>";
		echo "<tr><th>Do</th><td><input type=\"text\" name=\"do\" value=\""; echo stripslashes($wiersz['do']); echo "\" /></td></tr>";
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Edytuj\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_edytujkonsultacje(&$db)
{
	$data = array(	'dzien' => vs($_POST['dzien']),
					'od' => vs($_POST['od']),
					'do' => vs($_POST['do']),
					'id_osoby' => vs($_POST['id_osoby']),
					'id_konsultacji' => vs($_POST['id_konsultacji'])
					);
	if ($data['od'] == NULL || $data['do'] == NULL) return 1;
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];	
	$query = 'UPDATE konsultacje SET 
				dzien = \''.$data['dzien'].'\', 
				od_ = \''.$data['od'].'\', 
				do_ = \''.$data['do'].'\', 
				ID_osoby = \''.$data['id_osoby'].'\' 
				WHERE id_konsultacji = \''.$data['id_konsultacji'].'\'';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}
function add_admin_box_usun_konsultacje($err,&$db)
{
	echo "<form action=\"admin.php?action=admin_konsultacje_usun\" method=\"POST\" accept-charset=\"UTF-8\">";
	echo "<table class=\"formularz\">";
	switch ($err)
	{
		case 0:
			echo "<tr><td colspan=\"2\"><span class=\"err\">Konsultacje zostały usunięte!</span></td></tr>";
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
		$query = 'SELECT id_konsultacji, dzien, od_, do_ FROM konsultacje WHERE id_osoby = \''.$id.'\'';
		$wynik = $db->query($query);
		
		if($wynik->num_rows > 0)
		{	
			echo "<tr><th>Konsultacje do usunięcia</th><td><select name=\"id_konsultacji\">";
			for($i=0;$i<=$wynik->num_rows;$i++)
			{
				$wiersz = $wynik->fetch_assoc();
				echo "<option value=\"".$wiersz['id_konsultacji']."\">".$wiersz['dzien']." ".$wiersz['od_']." ".$wiersz['do_']."</option>";
			}
			echo "</select></td></tr>"; echo stripslashes($wiersz['id_konsultacji']); 
		}
		echo "<tr><th colspan=\"2\"><input type=\"submit\" value=\"Usuń\" /></th></tr>";
	echo "</table>";
	echo "</form>";
}
function admin_usunkonsultacje(&$db)
{
	$data = array(	'id_osoby' => vs($_POST['id_osoby']),
					'id_konsultacji' => vs($_POST['id_konsultacji'])
					);
	$query = 'SELECT id_osoby FROM prowadzacy WHERE email = \''.$_SESSION['user'].'\'';
	$wynik = $db->query($query);
	$wiersz = $wynik->fetch_assoc();
	$data['id_osoby']=$wiersz['id_osoby'];
	$query = 'DELETE FROM konsultacje WHERE id_konsultacji = \''.$data['id_konsultacji'].'\'';
	$wynik = $db->query($query);
	if ($db->affected_rows==0) return 3;
	else return 0;
}

?>