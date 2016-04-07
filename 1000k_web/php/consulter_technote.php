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
	
		$technote = $dbh->query("SELECT titre, contenu, auteur, date FROM technote WHERE id=".$id);
		$technote = $technote->fetch(PDO::FETCH_OBJ);

		$remplacement = array(
			'%titre%'		=> $technote->titre,
			'%contenu%'		=> utf8_encode($technote->contenu),
			'%auteur%'		=> $technote->auteur,
			'%date%'			=> $technote->date,
			'%listeCom%'	=> afficherCom($dbh, $id)
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