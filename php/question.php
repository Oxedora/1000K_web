<?php

	require_once("utils.php");
	require_once("fonctions.php");


	/** Affiche les questions selon un mode de $tri et la $valeur correspondante
	*
	* $tri : Tri les questions par date/statut/mot clef
	* $valeur : Valeur spécifique pour le tri
	*/
	function requeteParTri($tri, $valeur){
		$query = "SELECT DISTINCT id, titre, auteur, date, statut FROM question";
		
		switch($tri){
			case "date" :
				$query .= (strlen($valeur) == 0 ? " ORDER BY date DESC" 
															: " WHERE date ='".$valeur."'");
				break;
				
			case 1 :
				$query .= " WHERE statut=1";
				break;
				
			case 0 :
				$query .= " WHERE statut=0";
				break;
				
			case "cle" :
				// Si une valeur est définie, renvoie les questions dont l'un des mots clés correspond à cette valeur ou a une sous catégorie 
				$query .= (strlen($valeur) == 0 ? " INNER JOIN association ON question.id = association.question GROUP BY mot_cle" 
															: " WHERE id IN (SELECT question FROM association WHERE mot_cle='".$valeur."' OR mot_cle IN
																							(SELECT fils FROM arbre_cle WHERE pere='".$valeur."')
																					)");
				break;
				
			default :
				break;
		}
		return $query;
	}

	/** Affiche les questions contenues dans la BDD $dbh selon un mode de $tri et une $valeur donnée
	*
	* $dbh : BDD
	* $tri : mode de tri (date/statut/mot clef)
	* $valeur : restreint le tri à une valeur donnée
	*/
	function afficheQuestion($dbh, $tri, $valeur){
		$html = recupererHTML("../html/question.html");
	
		$questions = $dbh->query(requeteParTri($tri, $valeur));
		
		$listeQues = "";
		foreach($questions as $question){
			$listeQues .= "<p><a href='consulter_question.php?id=".$question["id"]."'>"
									.utf8_encode($question["titre"])." publiée par ".$question["auteur"].", le ".$question["date"].
								"</a><p>";
		}
		
		return str_replace("%listeQues%", $listeQues, $html);
	}
	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("question");
	$pageCentrale 	= afficheQuestion($dbh, $_GET["tri"], $_GET["valeur"]);
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
