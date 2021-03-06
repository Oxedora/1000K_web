<?php

	require_once("utils.php");
	require_once("fonctions.php");
	
	function afficherRep($dbh, $id){
		$reponses = $dbh->query("SELECT * FROM reponse WHERE question='".$id."' ORDER BY ordre");
	
		$liste_rep = "";
		foreach($reponses as $reponse){
			// Permet de colorer le fond d'un reponse sur deux pour la lisibilité
			$codeCouleur = fmod($reponse["ordre"], 2) == 0 ? "pair" : "impair";
			$liste_rep .= '<div class="com '.$codeCouleur.'">
									<div class="auteurCom">'.$reponse["auteur"].'</div>
									<div class="contenuCom">
										<div class="dateCom"> Ecrit le '.$reponse["date"].'</div>
											'.utf8_encode($reponse["contenu"]).'
									</div>
								</div>
			';
		}
		return $liste_rep;
	}
	
	function consulterQuestion($dbh, $id){
		$html = recupererHTML("../html/consulter_question.html");
	
		$requete = $dbh->prepare("SELECT titre, contenu, auteur, date FROM question WHERE id = :id");
		$requete->bindValue(':id', $id);
		$requete->execute();
		
		$question;
		while($donnee = $requete->fetch(PDO::FETCH_ASSOC)){$question = $donnee;}
		
		$requete = $dbh->prepare("SELECT mot_cle FROM association WHERE question = :id");
		$requete->execute(array(':id' => $id));
		$requete->execute();
		
		$motscles = array();
		while($donnee = $requete->fetch(PDO::FETCH_ASSOC)){array_push($motscles, $donnee["mot_cle"]);}
		

		$remplacement = array(
			'%titre%'		=> utf8_encode($question["titre"]),
			'%contenu%'		=> utf8_encode($question["contenu"]),
			'%auteur%'		=> $question["auteur"],
			'%date%'			=> $question["date"],
			'%mc%'			=> utf8_encode(implode(", ", $motscles)),
			'%listeRep%'	=> afficherRep($dbh, $id)
		);
		
		return str_replace(array_keys($remplacement), array_values($remplacement), $html);
	}
	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("question");
	$pageCentrale 	= consulterQuestion($dbh, $_GET["id"]);
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
