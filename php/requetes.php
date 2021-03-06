<?php
/**
* Liste des requêtes pour accéder aux données de la BDD
* Rend le code plus fluide par la suite
* 
* Toutes les variables utilisées viennent de $_POST
* qui a été extract dans main.php
*/

// Vérification de l'authentification d'un utilisateur
$check_authentification = "
SELECT pseudo 
FROM utilisateur 
WHERE pseudo='".$login."' 
	AND mot_de_passe='".$password."'
";

// Grandes catégories des mots clés
$pere_clefs = "
SELECT DISTINCT pere 
FROM arbre_cle
";
$id = 2;
// Récupération d'une technote par son ID
$technote_ID = "
SELECT *
FROM technote
WHERE id='".$id."'
";

// Liste des commentaires d'une technote
$commentaires = "
SELECT *
FROM commentaire
WHERE technote='".$id."'
ORDER BY ordre
";

// Technote la plus récente
$Trecente = "
SELECT * 
FROM technote 
WHERE id = (SELECT MAX(id) 
				FROM technote 
				WHERE date = (SELECT MAX(date) 
									FROM technote))
";

$Tcom = "
SELECT * 
FROM commentaire
WHERE";

?>
