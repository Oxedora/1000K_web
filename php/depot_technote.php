<?php

	require_once("utils.php");
	require_once("fonctions.php");

	if(!isset($_SESSION["role"])){
		echo recupererHTML("../html/interdit.html");
		return 1;
	}
	
	/** Ajoute une technote avec un $titre, un $contenu et un $mot_cle dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	* $titre : titre de la technote
	* $contenu : contenu de la technote
	* mots_cles : mots clés pour l'indexation de la technote
	*/
	function ajoutTechnote($dbh, $titre, $contenu, $mots_cles){
		$insertMot = 1;
	
		$id = $dbh->query("SELECT MAX(id) as id FROM technote");
		$id = $id->fetch(PDO::FETCH_OBJ);
		$id = $id->id+1;
		
		$requete = $dbh->prepare("INSERT INTO technote(id, titre, contenu, auteur, date) 
											VALUES (:id, :titre, :contenu, :auteur, :date)");
		
		$requete->bindValue(':id', $id);
		$requete->bindValue(':titre', $titre);
		$requete->bindValue(':contenu', $contenu);
		$requete->bindValue(':auteur', $_SESSION["pseudo"]);
		$requete->bindValue(':date', date("o-m-d"));
		$insertTechnote = $requete->execute();
		
		if($insertTechnote != false){
			foreach($mots_cles as $mot_cle){
				$requete = $dbh->prepare("INSERT INTO indexation(technote, mot_cle) VALUES (:id, :mc)");
				$requete->bindValue(':id', $id);
				$requete->bindValue(':mc', $mot_cle);
				$insertMot = $insertMot && $requete->execute();
			}
		}else{
			$dbh->query("DELETE FROM technote WHERE id='".$id."'");		
			return 1;
		}
		
		return ($insertMot ? 0 : 2);
	}
	
	/** Renvoie une liste des mots clés disponibles compatible avec un select
	*
	* $dbh : BDD utilisée
	*/
	function recupererMotsCles($dbh){
		$mots = $dbh->query("SELECT designation FROM mot_cle");
		foreach($mots as $mot){
			$motscles .= "<option selected value='".utf8_encode($mot["designation"])."'>".utf8_encode($mot["designation"])."</option>";
		}
		return $motscles;
	}
	
	/** Renvoie la page html du dépôt de technotes une fois la liste des mots clés ajoutée
	*
	* $dbh : BDD utilisée
	*/
	function depotTechnote($dbh){
		$html = recupererHTML("../html/depot_technote.html");
		return str_replace("%motscles%", recupererMotsCles($dbh), $html);
		
	}
	
	function information($code){
		switch($code){
			case 0:
				$message = "Votre technote a bien été publiée !";
				break;
			case 1:
				$message = "Votre technote n'a pas pu être prise en compte.";
				break;
			case 2:
				$message = "Certains mots clés n'ont pas pu être indexé à votre technote.";
				break;
			default:
				$message = "Evènement non reconnu.";
				break;
		}
		return $message;
	}
	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("depot");
	$pageCentrale 	= depotTechnote($dbh);
	$message			= "";
	$menu 			= "";
	$script			= "";
	
	if(isset($_POST["titre"]) && isset($_POST["contenu"])){
		$message = information(ajoutTechnote($dbh, utf8_decode($_POST["titre"]), utf8_decode($_POST["contenu"]), $_POST["cle"]));
	}
	
	
	/* Stockage de la vue à charger dans un buffer */
	$html = recupererHTML("../html/index.html");
	 
	/* Initialisation du tableau pour le remplacement */
	$remplacement = array(
	  '%header%' 			=> $header,
	  '%onglets%' 			=> $onglets,
	  '%pageCentrale%' 	=> $pageCentrale,
	  '%message%'			=> $message,
	  '%menu%' 				=> $menu,
	  '%script%'			=> $script
	);
	
	/* Remplacement des variables de la vue par les données de la page */ 
	$html = str_replace(array_keys($remplacement), array_values($remplacement), $html);
	 
	echo $html;
?>
