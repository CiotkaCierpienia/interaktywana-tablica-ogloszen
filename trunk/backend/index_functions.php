<?php
function verify_login($user, $pwd, &$db)
{
	$query = sprintf("SELECT p.email, p.haslo FROM prowadzacy p WHERE p.email = '%s' AND p.haslo = '%s'", $user, $pwd);
	$wynik = $db->query($query);
	if ($wynik->num_rows==1)
	{
		$wynik->free();
		$query = sprintf("SELECT p.id_osoby as idp, p.email, p.haslo, p.potwierdzony as pot FROM prowadzacy p WHERE p.email = '%s' AND p.haslo = '%s'", $user, $pwd);
		$wynik = $db->query($query);
		if ($wynik->num_rows==1)
		{
			$wiersz = $wynik->fetch_assoc();
			if($wiersz['pot']==1)
			{
				$_SESSION['user'] = $user;
				$_SESSION['user_type']='admin';
				$_SESSION['user_id']=$wiersz['idp'];
				$wynik ->free();
				return 'ok';
			}
			else
				return 'loc';
		}
		else
			return 'err';
		
	}
	else
		return 'err';
}
function logout()
{
	if (!isset($_SESSION['user']))
	{
		echo "NARUSZENIE SYSTEMU!\n";
	}
	else
	{
		session_unset();
		session_destroy();
		echo "<p>Wylogowano użytkownika </p>";
	}
}
?>