<?php

	include_once("utils.php");
	include_once("fonctions.php");

/*
	if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
		echo recupererHTML("../html/interdit.html");
		return 1;
	}
*/
	function afficheGestion($dbh){
		$html = recupererHTML("../html/gestion_compte.html");
		$pseudos = $dbh->query("SELECT pseudo, actif FROM utilisateur WHERE role<>'admin'");
		$gestion = "";
		foreach($pseudos as $pseudo){
			$gestion .= "<tr>
								<td>".$pseudo['pseudo']."</td>
								<td align='center'>
									<input type='radio' name='".$pseudo['pseudo']."' value='1' ".($pseudo['actif'] == 1 ? 'checked' : '').">
								<td align='center'>
									<input type='radio' name='".$pseudo['pseudo']."' value='0' ".($pseudo['actif'] == 0 ? 'checked' : '').">
								<td align='center'>
									<input type='radio' name='".$pseudo['pseudo']."' value='2'>
							</tr>";
		}
		return str_replace("%champs%", $gestion, $html);
	}
	
	if(isset($_GET)){
		$taille	= sizeof($_GET);
		$pseudos = array_keys($_GET);
		$etats 	= array_values($_GET);
		for($i = 0; $i < $taille; $i++){
			if((int)$etats[$i] <= 1){
				$dbh->query("UPDATE utilisateur SET actif=".(int)$etats[$i]." WHERE pseudo='".$pseudos[$i]."'");
			}
		}
		var_dump($dbh->query("DELETE FROM utilisateur WHERE actif=2"));
	}
	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("gestion");
	$pageCentrale 	= afficheGestion($dbh);
	$menu 			= "";
	
	//var_dump($pageCentrale);
	
	/* Stockage de la vue à charger dans un buffer */
	$html = recupererHTML("../html/index.html");
	 
	/* Initialisation du tableau pour le remplacement */
	$remplacement = array(
	  '%header%' 			=> $header,
	  '%onglets%' 			=> $onglets,
	  '%pageCentrale%' 	=> $pageCentrale,
	  '%menu%' 				=> $menu
	);
	
	/* Remplacement des variables de la vue par les données de la page */ 
	$html = str_replace(array_keys($remplacement), array_values($remplacement), $html);
	 
	echo $html;

?>
