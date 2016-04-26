<?php

	/**
	* Renvoie le contenu de la $page html
	*
	* $page : vue html
	*/
	function recupererHTML($page){
		ob_start();
		include($page);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	/**
	* Remplace le contenu de la $vue par le $nouveauContenu et retourne le résultat
	*
	* $vue : vue html
	* $nouveauContenu : contenu à insérer dans la vue
	*/
	function remplacerContenu($vue, $nouveauContenu){
		return str_replace(array_keys($nouveauContenu), array_values($nouveauContenu), $vue);
	}
	
	function afficheOnglets($pageCourante){
		$html = ' <ul id="menuNav"> ';
		
		/* Onglets des visiteurs */
		$html .= ($pageCourante == "accueil" ? 
					'<li><a class="selected" href="accueil.php">Accueil</a></li>' 
					: '<li><a href="accueil.php">Accueil</a></li>');
		$html .= ($pageCourante == "technote" ? 
					'<li><a class="selected" href="technote.php">Technotes</a></li>' 
					: '<li><a href="technote.php">Technotes</a></li>');
		$html .= ($pageCourante == "question" ? 
					'<li><a class="selected" href="question.php">Questions</a></li>' 
					: '<li><a href="question.php">Questions</a></li>');
		/* Onglets des membres */
		if(isset($_SESSION["estConnecte"]) && $_SESSION["estConnecte"]){
		$html .= ($pageCourante == "depot" ? 
					'<li><a class="selected" href="depot_technote.php">Ajouter une technote</a></li>' 
					: '<li><a href="depot_technote.php">Ajouter une technote</a></li>');
		$html .= ($pageCourante == "ajout" ? 
					'<li><a class="selected" href="depot_question.php">Ajouter une question</a></li>' 
					: '<li><a href="depot_question.php">Ajouter une question</a></li>');
		$html .= ($pageCourante == "profil" ? 
					'<li><a class="selected" href="profil.php">Edition de profil</a></li>' 
					: '<li><a href="profil.php">Edition de profil</a></li>');
		
			/* Onglets des admins */
			if(isset($_SESSION["role"]) && $_SESSION["role"] == "admin"){
			$html .= ($pageCourante == "gestion" ? 
						'<li><a class="selected" href="gestion_compte.php">Gestion de compte</a></li>' 
						: '<li><a href="gestion_compte.php">Gestion de compte</a></li>');
			}
		}
		$html .= '</ul>';
		return $html;
	}
	
	/**
	*	Envoi un mail contenant le mot de passe $mdp au $destinataire
	*/
	function envoiMail($destinataire, $mdp){
		$sujet = "Profitez de votre compte 1000K !";
		$entete = "From: tendrement_web@1000K.com";
		
		$message = '
		Bienvenue sur 1000K,
		
		Votre mot de passe est désormais : '.urlencode($mdp).'
		 
		---------------
		Ceci est un mail automatique, Merci de ne pas y répondre.';
		
		return mail($destinataire, $sujet, $message, $entete);
	}
	
	/**
	*	Crée le compte utilisateur avec l'adresse $destinataire et le pseudo $login dans la BDD $dbh
	*	Vérifie si les données sont valides et disponibles
	*	Crée un mot de passe aléatoire
	*	Envoi un mail contenant le mot de passe à l'adresse donnée
	*/
	function creationCompte($destinataire, $login, $dbh){
		$adr_valide = preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $destinataire);  
		if($adr_valide != 1){return "L'adresse est invalide !";}
		
		$adr_dispo = $dbh->query("SELECT adresse_mail FROM utilisateur WHERE adresse_mail='".$destinataire."'");
		$adr_dispo = $adr_dispo->fetch(PDO::FETCH_OBJ);
		if($adr_dispo != false){return "Adresse existante !";}

		$pseudo_dispo = $dbh->query("SELECT pseudo FROM utilisateur WHERE pseudo='".$login."'");
		$pseudo_dispo = $pseudo_dispo->fetch(PDO::FETCH_OBJ);
		if($pseudo_dispo != false){return "Pseudo existant";}
		
		$mdp = uniqid();
		
		$ok = $dbh->query("INSERT INTO utilisateur(adresse_mail, pseudo, mot_de_passe, role, actif) VALUES 
								('".$destinataire."', '".$login."', '".$mdp."', 'membre', '1')");
		/*
		if($ok == false){"Problème dans la création du compte";}
		$ok = envoiMail($destinataire, $mdp);
		
		if(!$ok){$dbh->query("DELETE FROM utilisateur WHERE adresse_mail='".$destinataire."'");}*/
		return "Votre mot de passe est desormais : ".$mdp." !";
	}
	
	/**
	* Renvoie vrai si les identifiants sont corrects, faux sinon
	*/
	function authentification($dbh, $login, $password){
		$connexionAccepte = $dbh->query("SELECT pseudo 
													FROM utilisateur 
													WHERE pseudo='".$login."' 
													AND mot_de_passe='".$password."'
													AND actif=1
		");
		$connexionAccepte = $connexionAccepte->fetch(PDO::FETCH_OBJ);
		return $connexionAccepte != false;
	}

	/**
	* 	Affiche le header du site
	*	Affiche les boutons de connexion/inscription pour les visiteurs
	*	Affiche un message personnalisé et l'option de déconnexion pour les membres authentifiés
	*/
	function afficheHeader($dbh){
		$html = recupererHTML("../html/bandeau.html");
		//$estConnecte = true;
		$espaceConnexion		= "";
		$modalInscription		= "";
		$modalConnexion		= "";
		$modalConfirmation	= "";
		
		if(isset($_POST["login"]) && $_POST["password"]){
			$_SESSION["estConnecte"] = authentification($dbh, $_POST["login"], $_POST["password"]);
			$_SESSION["role"] = $dbh->query("SELECT role FROM utilisateur WHERE pseudo='".$_POST["login"]."'");
			$_SESSION["role"] = $_SESSION["role"]->fetch(PDO::FETCH_OBJ);
			$_SESSION["role"] = $_SESSION["role"]->role;
			
			$_SESSION["pseudo"] = $_POST["login"];
		}
			
		if(isset($_SESSION["estConnecte"]) && $_SESSION["estConnecte"]){
			
			
			
			$espaceConnexion = '
			<div class="account" id="logged">
				
					Bienvenue, '.$_SESSION["pseudo"].' !
					<form method="post">
						<button type="submit" id="deConnexion" value="Deconnexion" name="deConnexion">Deconnexion</button>
					</form>
				
			</div>
			';
		}else{
			$espaceConnexion = '
			<nav class="main-nav">
				<ul>
					<li><a class="connexion lien" href="#conn">Se connecter</a></li>
					<li><a class="inscription lien" href="#inscr">S\'inscrire</a></li>
				</ul>
			</nav>
			';
			$modalInscription = '
			<div id="inscr" class="modal">
				<div>
					<a href="#fermer" title="Fermer" class="fermer">X</a>
					<h2>INSCRIPTION</h2>
					<form action="'.$_SERVER['PHP_SELF'].'#conf" method="post">
						<label for="inscrPseudo">Pseudo</label>
						<input type="text" name="inscrPseudo" id="inscrPseudo" placeholder="Pseudo" required><br>
						<label for="inscrMail">Mail</label>
						<input type="text" name="inscrMail" id="inscrMail" placeholder="mail@exemple.com" required><br>
						<button type="submit" class="submit">Je m\'inscris !</button>
					</form>
				</div>
			</div>
			';
			
			$pageActuelle = ($_SERVER['PHP_SELF'] == "/1000k_web/php/consulter_technote.php" ? 
								"/1000k_web/php/consulter_technote.php?id=".$_GET["id"] : ($_SERVER['PHP_SELF'] == "/1000k_web/php/consulter_question.php" ? "/1000k_web/php/consulter_question.php?id=".$_GET["id"] : $_SERVER['PHP_SELF']));
			
			$modalConnexion = '
			<div id="conn" class="modal">
				<div>
					<a href="#fermer" title="Fermer" class="fermer">X</a>
					<h2>CONNEXION</h2>
					<form action="'.$pageActuelle.'" method="post">
						<label for="connPseudo">Pseudo</label>
						<input type="text" name="login" id="connPseudo" placeholder="Pseudo" required><br>
						<label for="connMDP">Mail</label>
						<input type="password" name="password" id="connMDP" placeholder="*****" required><br>
						<button type="submit" class="submit">Je me connecte !</button>
					</form>
				</div>
			</div>
			';
			
			if(isset($_POST["inscrPseudo"]) && isset($_POST["inscrMail"])){
				$message = creationCompte($_POST["inscrMail"], $_POST["inscrPseudo"], $dbh);
									
				$modalConfirmation = '
				<div id="conf" class="modal">
					<div>
						<a href="#fermer" title="Fermer" class="fermer">X</a>
						<h2>CONFIRMATION</h2>
						<span>'.$message.'</span>
					</div>
				</div>
				';	
			} 
		}
		
		$remplacement = array(
			'%espaceConnexion%' 		=> $espaceConnexion,
			'%modalInscription%'		=> $modalInscription,
			'%modalConnexion%'		=> $modalConnexion,
			'%modalConfirmation%'	=> $modalConfirmation
		);
		
		return remplacerContenu($html, $remplacement);
	}

?>
