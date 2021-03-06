<?php

	require_once("utils.php");
	require_once("fonctions.php");
	
	function afficherCom($dbh, $id){
		$commentaires = $dbh->query("SELECT * FROM commentaire WHERE technote='".$id."' ORDER BY ordre");
	
		$liste_com = "";
		foreach($commentaires as $commentaire){
			// Permet de colorer le fond d'un commentaire sur deux pour la lisibilité
			$codeCouleur = fmod($commentaire["ordre"], 2) == 0 ? "pair" : "impair";
			$liste_com .= '<div class="com '.$codeCouleur.'">
									<div class="auteurCom">'.$commentaire["auteur"].'</div>
									<div class="contenuCom">
										<div class="dateCom"> Ecrit le '.$commentaire["date"].'</div>
											'.utf8_encode($commentaire["contenu"]).'
									</div>
								</div>
			';
		}
		return $liste_com;
	}
	
	function consulterTechnote($dbh, $id){
		$html = recupererHTML("../html/consulter_technote.html");
	
		$message = "";
		$idTech 	= "";
		
		if(isset($_SESSION["role"])){
			$html .= recupererHTML("../html/commentaire_technote.html");
			if(isset($_POST["commentaire"])){
				$requete = $dbh->prepare("SELECT MAX(ordre) FROM commentaire WHERE technote = :id");
				$requete->bindValue(':id', $id);
				$ordre = $requete->execute();
		
				$ordre = ($ordre == NULL ? 1 : $ordre + 1);
		
				$requete = $dbh->prepare("INSERT INTO commentaire VALUES (:ordre, :technote, :contenu, :auteur, :date)");
				$requete->bindValue(':ordre', $ordre+1);
				$requete->bindValue(':technote', $id);
				$requete->bindValue(':contenu', $_POST["commentaire"]);
				$requete->bindValue(':auteur', $_SESSION["pseudo"]);
				$requete->bindValue(':date', date("o-m-d"));
		
				$ajout = $requete->execute();
		
				$message = ($ajout ? "Votre commentaire a bien été publié." : "Une erreur est survenue.");
				$idTech = $id;
			}
		}
	
		$requete = $dbh->prepare("SELECT titre, contenu, auteur, date FROM technote WHERE id = :id");
		$requete->bindValue(':id', $id);
		$requete->execute();
		
		$technote;
		while($donnee = $requete->fetch(PDO::FETCH_ASSOC)){$technote = $donnee;}
		
		$requete = $dbh->prepare("SELECT mot_cle FROM indexation WHERE technote = :id");
		$requete->execute(array(':id' => $id));
		$requete->execute();
		
		$motscles = array();
		while($donnee = $requete->fetch(PDO::FETCH_ASSOC)){array_push($motscles, $donnee["mot_cle"]);}
		
		$remplacement = array(
			'%titre%'		=> $technote["titre"],
			'%contenu%'		=> utf8_encode($technote["contenu"]),
			'%auteur%'		=> $technote["auteur"],
			'%date%'			=> $technote["date"],
			'%mc%'			=> utf8_encode(implode(", ", $motscles)), 
			'%listeCom%'	=> afficherCom($dbh, $id),
			'%message%'		=> $message,
			'%id%'			=> $idTech
		);
		
		return str_replace(array_keys($remplacement), array_values($remplacement), $html);
	}
	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("technote");
	$pageCentrale 	= consulterTechnote($dbh, $_GET["id"]);
	$menu 			= "";
	$script			= "";
	
	//var_dump($pageCentrale);
	
	/* Stockage de la vue à charger dans un buffer */
	$html = recupererHTML("../html/index.html");
	 
	/* Initialisation du tableau pour le remplacement */
	$remplacement = array(
	  '%header%' 			=> $header,
	  '%onglets%' 			=> $onglets,
	  '%pageCentrale%' 	=> $pageCentrale,
	  '%menu%' 				=> $menu,
	  '%script%'			=> $script
	);
	
	/* Remplacement des variables de la vue par les données de la page */ 
	$html = str_replace(array_keys($remplacement), array_values($remplacement), $html);
	 
	echo $html;

?>
