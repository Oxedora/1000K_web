<?php

	require_once("utils.php");
	require_once("fonctions.php");

	if(!isset($_SESSION["role"])){
		echo recupererHTML("../html/interdit.html");
		return 1;
	}

	function mdpValide($dbh, $mdp){
		$ancienMDP = $dbh->query("SELECT mot_de_passe FROM utilisateur WHERE pseudo='".$_SESSION["pseudo"]."'");
		$ancienMDP = $ancienMDP->fetch(PDO::FETCH_OBJ);
		return $ancienMDP->mot_de_passe === $mdp;
	}

	function information($code){
		switch($code){
			case 0:
				$message = "La modification de votre e-mail a bien été prise en compte.";
				break;
			case 1:
				$message = "Votre e-mail n'a pas pu être modifié.";
				break;
			case 2:
				$message = "La modification de votre mot de passe a bien été prise en compte.";
				break;
			case 3:
				$message = "Votre mot de passe n'a pas pu être modifié.";
				break;
			case 4:
				$message = "Votre e-mail n'est pas valide.";
				break;
			case 5:
				$message = "Votre mot de passe n'est pas valide.";
				break;
			case 6:
				$message = "Les nouveaux mots de passe ne sont pas identiques.";
				break;
			default:
				$message = "Evènement non reconnu.";
				break;
		}
		return $message;
	}

	function changerEmail($dbh, $email){
		$res = $dbh->query("UPDATE utilisateur SET adresse_mail='".$email."' WHERE pseudo='".$_SESSION["pseudo"]."'");
		return ($res === false ? 1 : 0);
	}
	
	function changerMDP($dbh, $mdp){
		$res = $dbh->query("UPDATE utilisateur SET mot_de_passe='".$mdp."' WHERE pseudo='".$_SESSION["pseudo"]."'");
		return ($res === false ? 3 : 2);
	}

	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("profil");
	$pageCentrale 	= recupererHTML("../html/profil.html");
	$message			= "";
	$menu 			= "";
	$script			= "";
	
	if(isset($_POST["mdp"])){
		if(mdpValide($dbh, $_POST["mdp"])){
			$valide = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
			$message = ($valide == false ? information(4) : information(changerEmail($dbh, $_POST["email"])));
		}else{$message = information(5);}
	}
	
	if(isset($_POST["ancien"])){
		if($_POST["nouv1"] === $_POST["nouv2"]){
			//$message = (mdpValide($dbh, $_POST["ancien"]) ? information(changerMDP($dbh, $_POST["nouv1"])) : information(5));
		}else{$message = information(6);}
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
