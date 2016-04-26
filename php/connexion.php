<?php
	/**
	* Connexion à la BDD du site via PDO
	*/	
	$host = "localhost";
	$dbname = "1000k_web";
	$user = "root";
	$pass = "projet_web";
	try {$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);}
	catch(PDOException $e){die('Erreur : ' . $e->getMessage());}
?>
