<!DOCTYPE html>

<?php 
	//header( 'content-type: text/html; charset=utf-8' ); // Demande au serveur d'envoyer uniquement de l'UTF-8
	require_once("utils.php");
?>

<html>
	<head>
	<title>1000K, tendrement Wysiwyg</title>
		<!--<meta http-equiv="content-type" content="text/html; charset=utf-8">-->
		<meta name="description" content="WELCOME TO THE LEAGUE OOOOOOOOF TECHNOTES">
		<meta name="keywords" content="HTML,CSS,XML,JavaScript,Technote,Pompidor,Meynard">
		<meta name="author" content="Edouard BREUILLE, Celia ROUQUAIROL">
		<link rel="stylesheet" type="text/css" href="../css/1000k_sheet.css" />
		
		<script type="text/javascript" src="../js/header.js"></script>
	</head>

	<body>	
		
		<div id="wrap">
			
				<?php
					echo afficheHeader($dbh); ?>
			
			<div id="bodyContainer">
				
				<?php include_once("menu.php"); ?>
				
				<div id="leftSide">
					<?php include("technote.php");
							include("commentaires.php");
					?>
				</div>
				
				<div id="rightSide">
					<?php include("infoBarre.php"); ?>
				</div>
				<div style="clear:both; font-size:1px;"></div> <!-- regle le soucis de debordement -->
			</div>
			
			<div id="footer">
				<?php include("footer.php"); ?>
			</div>
		</div>
	
	</body>
</html> 

<?php 
	$dbh = null; // Pas besoin d'une connexion persistante pour ce site 
?>

