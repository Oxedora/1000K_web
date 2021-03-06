<?php
	require_once("utils.php");
	require_once("fonctions.php");

	if(!isset($_SESSION["role"])){
		echo recupererHTML("../html/interdit.html");
		return 1;
	}
	
	function ajoutQuestion($dbh, $titre, $contenu, $mot_cle){
		$insertMot = 1;
		
		$id = $dbh->query("SELECT MAX(id) as id FROM question");
		$id = $id->fetch(PDO::FETCH_OBJ);
		$id = $id->id+1;
		
		$requete = $dbh->prepare("INSERT INTO question(id, titre, contenu, auteur, date) 
											VALUES (:id, :titre, :contenu, :auteur, :date)");
		
		$requete->bindValue(':id', $id);
		$requete->bindValue(':titre', $titre);
		$requete->bindValue(':contenu', $contenu);
		$requete->bindValue(':auteur', $_SESSION["pseudo"]);
		$requete->bindValue(':date', date("o-m-d"));
		$insertQuestion = $requete->execute();
		
		if($insertQuestion != false){
			foreach($mots_cles as $mot_cle){
				$requete = $dbh->prepare("INSERT INTO association(question, mot_cle) VALUES (:id, :mc)");
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
	
	function recupererMotsCles($dbh){
		$mots = $dbh->query("SELECT designation FROM mot_cle");
		
		$motscles = "";
		foreach($mots as $mot){
			$motscles .= "<option selected value='".$mot["designation"]."'>".utf8_encode($mot["designation"])."</option>";
		}
		return $motscles;
	}
	
	function depotQuestion($dbh){
		$html = recupererHTML("../html/depot_question.html");
		return str_replace("%motscles%", recupererMotsCles($dbh), $html);
		
	}
	
	function information($code){
		switch($code){
			case 0:
				$message = "Votre question a bien été publiée !";
				break;
			case 1:
				$message = "Votre question n'a pas pu être prise en compte.";
				break;
			case 2:
				$message = "Votre question n'a pas pu être associée à un mot clé.";
				break;
			default:
				$message = "Evènement non reconnu.";
				break;
		}
		return $message;
	}
	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("ajout");
	$pageCentrale 	= depotQuestion($dbh);
	$message 		= "";
	$menu 			= "";
	$script			= "";
	
	if(isset($_POST["titre"]) && isset($_POST["contenu"])){
		$message = information(ajoutQuestion($dbh, utf8_decode($_POST["titre"]), utf8_decode($_POST["contenu"]), $_POST["cle"]));
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
