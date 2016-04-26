<?php

	require_once("utils.php");
	require_once("fonctions.php");


	/** Affiche les technotes selon un mode de $tri et la $valeur correspondante
	*
	* $tri : Tri les technotes par date/auteur/mot clef
	* $valeur : Valeur spécifique pour le tri
	*/
	function requeteParTri($tri, $valeur){
		$query = "SELECT DISTINCT id, titre, auteur, date FROM technote";
		
		switch($tri){
			case "date" :
				$query .= (strlen($valeur) == 0 ? " ORDER BY date DESC" 
															: " WHERE date ='".$valeur."'");
				break;
			case "auteur" :
				$query .= (strlen($valeur) == 0 ? " ORDER BY auteur" 
															: " WHERE auteur='".$valeur."'");
				break;
			case "cle" :
				// Si une valeur est définie, renvoie les technotes dont l'un des mots clés correspond à cette valeur ou a une sous catégorie 
				$query .= (strlen($valeur) == 0 ? " INNER JOIN indexation ON technote.id = indexation.technote GROUP BY mot_cle" 
															: " WHERE id IN (SELECT technote FROM indexation WHERE mot_cle='".$valeur."' OR mot_cle IN
																							(SELECT fils FROM arbre_cle WHERE pere='".$valeur."')
																					)");
				break;
				
			default :
				break;
		}
		return $query;
	}

	/** Affiche les technotes contenues dans la BDD $dbh selon un mode de $tri et une $valeur donnée
	*
	* $dbh : BDD
	* $tri : mode de tri (date/auteur/mot clef)
	* $valeur : restreint le tri à une valeur donnée
	*/
	function afficheTechnote($dbh, $tri, $valeur){
		$html = recupererHTML("../html/technote.html");
	
		$technotes = $dbh->query(requeteParTri($tri, $valeur));
		
		$listeTech = "";
		foreach($technotes as $technote){
			$listeTech .= "<p><a href='consulter_technote.php?id=".$technote["id"]."'>"
									.$technote["titre"]." publiée par ".$technote["auteur"].", le ".$technote["date"].
								"</a><p>";
		}
		
		return str_replace("%listeTech%", $listeTech, $html);
	}
	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("technote");
	$pageCentrale 	= afficheTechnote($dbh, $_GET["tri"], $_GET["valeur"]);
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
