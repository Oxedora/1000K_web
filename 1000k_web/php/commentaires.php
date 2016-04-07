<div id="commentaires">

	<?php
	
	/**
	* Récupère la liste ordonnée des commentaires associés à la technote demandée
	* et les affiche
	*/
	
	$liste_com = $dbh->query($commentaires);
	echo '<hr size="3px"/>';
	
	foreach($liste_com as $commentaire){
		// Permet de colorer le fond d'un commentaire sur deux pour la lisibilité
		$codeCouleur = fmod($commentaire["ordre"], 2) == 0 ? "pair" : "impair";
		echo '
			<div class="com '.$codeCouleur.'">
				<div class="auteurCom">'.$commentaire["auteur"].'</div>
				<div class="contenuCom">
					<div class="dateCom"> Ecrit le '.$commentaire["date"].'</div>
					'.$commentaire["contenu"].'
				</div>
			</div>
		';
	}
	?>

</div>
