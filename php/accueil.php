<?php

	include_once("utils.php"); // $_SESSION, connexion BDD & requetes associées
	require_once("fonctions.php");

	/** Retourne une clé aléatoire contenue dans la $table donnée de la BDD $dbh
	*
	* $dbh : BDD
	* $table : table contenant la liste des mots clés
	*/
	function aleaCle($dbh, $table){
		$cle = $dbh->query("SELECT mot_cle
									FROM ".$table."
									ORDER BY RAND()
									LIMIT 1
		");
		$cle = $cle->fetch(PDO::FETCH_OBJ);
		
		return $cle->mot_cle;
	}
	
	/** Retourne un auteur aléatoire contenu dans la $table donnée de la BDD $dbh
	*
	* $dbh : BDD
	* $table table contenant la liste des auteurs
	*/
	function aleaAuteur($dbh, $table){
		$auteur = $dbh->query("SELECT auteur
										FROM ".$table."
										ORDER BY RAND()
										LIMIT 1
		");
		$auteur = $auteur->fetch(PDO::FETCH_OBJ);
		return utf8_encode($auteur->auteur);
	}
	
	/**
	* Retourne la technote la plus récente dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	*/
	function TechParDate($dbh){
		$qd = $dbh->query("SELECT * 
								FROM technote 
								WHERE id = (SELECT MAX(id) 
												FROM technote 
												WHERE date = (SELECT MAX(date) 
																	FROM technote)
												)
		"); // La dernière publiée (ID croissant) parmi les plus récentes
		return $qd->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	* Retourne la technote dont le commentaire est le plus récent dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	*/
	function TechParCom($dbh){
		$tc = $dbh->query("SELECT *
								FROM technote
								WHERE id = (SELECT technote
												FROM commentaire 
												WHERE ordre = (SELECT MAX(ordre) 
																	FROM commentaire 
																	WHERE date = (SELECT MAX(date) 
																						FROM commentaire)
																	)
												LIMIT 1	
												)
		"); // Le dernier publié (ID croissant) parmi les plus récents
		return $tc->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	* Retourne une technote aléatoire contenant le mot clé $cle dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	* $cle : mot clé contenu dans la technote
	*/
	function TechParCle($dbh, $cle){
		$tc = $dbh->query("SELECT *
								FROM technote
								WHERE id = (SELECT technote
												FROM indexation
												WHERE mot_cle ='".$cle."'
												ORDER BY RAND()
												LIMIT 1
												)
		");
		return $tc->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	* Retourne une technote aléatoire parmi celles rédigées par $auteur dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	* $auteur : auteur de la technote
	*/
	function TechParAuteur($dbh, $auteur){
		$ta = $dbh->query("SELECT *
								FROM technote
								WHERE auteur ='".$auteur."'
								ORDER BY RAND()
								LIMIT 1
		");
		return $ta->fetch(PDO::FETCH_OBJ);
	}
	
	/** Retourne l'affichage de la $technote selon son mode de $tri
	*
	* $technote : la technote à afficher
	* $tri : tri par date/commentaire/clé/auteur
	*/
	function resumeTechnote($technote, $tri){
		return '<a href="consulter_technote.php?id='.$technote->id.'">'.
						$tri.' '.utf8_encode($technote->titre).' par '.$technote->auteur.
					'</a>';
	}
	
	/**
	* Retourne la question la plus récente dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	*/
	function QuestParDate($dbh){
		$qd = $dbh->query("SELECT * 
								FROM question 
								WHERE id = (SELECT MAX(id) 
												FROM question 
												WHERE date = (SELECT MAX(date) 
																	FROM question)
												)
		");
		return $qd->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	* Retourne la question dont la réponse est la plus récente dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	*/
	function QuestParRep($dbh){
		$qr = $dbh->query("SELECT *
								FROM question
								WHERE id = (SELECT question
												FROM reponse 
												WHERE ordre = (SELECT MAX(ordre) 
																	FROM reponse 
																	WHERE date = (SELECT MAX(date) 
																						FROM reponse)
																	)
												LIMIT 1
												)
		");
		return $qr->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	* Retourne une question aléatoire contenant le mot clé $cle dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	* $cle : mot clé contenu dans la question
	*/
	function QuestParCle($dbh, $cle){
		$qc = $dbh->query("SELECT *
								FROM question
								WHERE id = (SELECT question
												FROM association
												WHERE mot_cle ='".$cle."'
												ORDER BY RAND()
												LIMIT 1
												)
		");
		return $qc->fetch(PDO::FETCH_OBJ);
	}
	
	/**
	* Retourne une question aléatoire parmi celles rédigées par $auteur dans la BDD $dbh
	*
	* $dbh : BDD utilisée
	* $auteur : auteur de la question
	*/
	function QuestParAuteur($dbh, $auteur){
		$ta = $dbh->query("SELECT *
								FROM question
								WHERE id = (SELECT id
												FROM question
												WHERE auteur ='".$auteur."'
												ORDER BY RAND()
												LIMIT 1
												)
		");
		return $ta->fetch(PDO::FETCH_OBJ);
	}
	
	/** Retourne l'affichage de la $question
	*
	* $question : la question à afficher
	* $tri : tri par date/commentaire/clé/auteur
	*/
	function resumeQuestion($question, $tri){
		return '<a href="consulter_question.php?id='.$question->id.'">'.
						$tri.' '.utf8_encode($question->titre).' par '.$question->auteur.
					'</a>';
	}
	
	function afficheAccueil($dbh){
		$html = recupererHTML("../html/accueil.html");
		
		$Tcle = aleaCle($dbh, "indexation");
		$Qcle = aleaCle($dbh, "association");
		
		$donneesAffichees = array(
			'%TparDate%'	=> resumeTechnote(TechParDate($dbh), "La plus récente :"),
			'%TparCom%'		=> resumeTechnote(TechParCom($dbh), "On a récemment commenté cette technote :"),
			'%TparCle%'		=> resumeTechnote(TechParCle($dbh, $Tcle), "Dans la catégorie ".utf8_encode($Tcle)." :"),
			'%TparAuteur%'	=> resumeTechnote(TechParAuteur($dbh, aleaAuteur($dbh, "technote")), "Il a publié ça :"),
			'%QparDate%'	=> resumeQuestion(QuestParDate($dbh), "La plus récente :"),
			'%QparRep%'		=> resumeQuestion(QuestParRep($dbh), "On a récemment répondu à cette question :"),
			'%QparCle%'		=> resumeQuestion(QuestParCle($dbh, $Qcle), "Dans la catégorie ".utf8_encode($Qcle)." :"),
			'%QparAuteur%'	=> resumeQuestion(QuestParAuteur($dbh, aleaAuteur($dbh, "question")), "Il a besoin de votre aide :")	
		);
		return str_replace(array_keys($donneesAffichees), array_values($donneesAffichees), $html);
	}

	
	/* Déclaration des variables pour cette page */
	
	$header 			= afficheHeader($dbh);
	$onglets 		= afficheOnglets("accueil");
	$pageCentrale 	= afficheAccueil($dbh);
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
