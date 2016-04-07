<?php

include("fonctions.php");

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
	echo recupererHTML("../html/interdit.html");
	return 1;
}

?>
