<?php 
	//header( 'content-type: text/html; charset=utf-8' ); // Demande au serveur d'envoyer uniquement de l'UTF-8
	
	//extract($_POST);
	 
	if(isset($_POST["deConnexion"])){session_unset(); session_destroy();}
	else{session_start();}
	
	//include_once("../ascii/cake.html"); // It's something
	
	include_once("connexion.php"); // Connexion à la base de données
	//include_once("requetes.php"); // Liste des requetes à la BDD

	include_once("fonctions.php"); // Fonctions auxiliaires
?>
