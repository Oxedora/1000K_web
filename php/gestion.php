<?php

include_once("utils.php");
include_once("fonctions.php");

if(!isset($_SESSION["role"]) && $_SESSION["role"] != "admin"){
	echo recupererHTML("../html/interdit.html");
	return 1;
}

shablagoo

?>
