<?php
	 $nom = isset($_POST["nom"])? $_POST["nom"] : ""; //if then else
	 $age = isset($_POST["age"])? $_POST["age"] : "";
	 $phone = isset($_POST["telephone"])? $_POST["telephone"] : "";
	 $birthday = isset($_POST["naissance"])? $_POST["naissance"] : "";
	 $erreur = "";

	 if ($nom == "") 
	 {  
	 	$erreur .= "Nom est vide. <br>"; 
	 }
	 if ($age == "") 
	 {
	 	$erreur .= "Age est vide. <br>"; 
	 }
	 if ($phone == "") 
	 {
	 	$erreur .= "Téléphone est vide. <br>"; 
	 }
	 if ($birthday == "") 
	 {
	 	$erreur .= "Date de naissance est vide. <br>"; 
	 }
	 if ($erreur == "") 
	 {
	 	echo "Formulaire valide";
	 }
	 else 
	 {
	 	echo "Erreur : $erreur";
	 }
?>
