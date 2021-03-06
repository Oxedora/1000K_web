<?php

function afficheHeader($pseudo, $mdp, $estConnecte){

	if(isset($pseudo) && isset($mdp)){
		$connexionAccepte = $dbh->query($check_authentification);
		$connexionAccepte = $connexionAccepte->fetch(PDO::FETCH_OBJ);
		$estConnecte = $connexionAccepte != false;
	}
	
	$afficheHeader = (isset($estConnecte) && $estConnecte ?
			'<div class="account" id="logged">
				<span> Bienvenue, '.$login.' !</span>
					<form method="post">
						<input type="submit" value="deconnexion" name="deConnexion">
					</form>
				</div>'
		:
			'<div id="logPanel" class="account">
				<button onclick="connexion()">Se connecter</button>
				<button onclick="inscription()">S\'inscrire</button>
			</div>'
		);

	return $afficheHeader;
}
?>
