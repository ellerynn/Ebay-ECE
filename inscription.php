<?php
 $nom = isset($_POST["nom"])? $_POST["nom"] : "";
 $prenom = isset($_POST["prenom"])? $_POST["prenom"] : ""; //if then else
 $email = isset($_POST["email"])? $_POST["email"] : "";
 $motdepasse = isset($_POST["motdepasse"])? $_POST["motdepasse"] : "";
 
 $erreur = "";

 if ($nom == "") { 
 	$erreur .= "Nom est vide. <br>"; }
 	 echo "<br>";
 if ($prenom == "") { 
 	$erreur .= "Prenom est vide. <br>"; }
 	 echo "<br>";
 if ($email == "") {
 $erreur .= "Email est vide. <br>"; }
  echo "<br>";
 if ($motdepasse == "") {
 $erreur .= "Mot de passe est vide. <br>"; }
  echo "<br>";


 if ($erreur == "") 
 {
     //identifier votre BDD
     $database = "ebay ece paris";
     //connectez-vous dans votre BDD
     //Rappel: votre serveur = localhost | votre login = root |votre password = <rien>
     $db_handle = mysqli_connect('localhost', 'root', '');
     $db_found = mysqli_select_db($db_handle, $database);
           if (isset($_POST["buttoninscription"])) 
           {
                if ($db_found) 
                {
                     $sql = "SELECT * FROM personne";

                     if ($email != "") 
                     {
                        //on cherche le livre avec les paramètres titre et auteur
                        $sql .= " WHERE email LIKE '%$email%'";
                        $result = mysqli_query($db_handle, $sql);
                        //regarder s'il y a de résultat
                              if (mysqli_num_rows($result) != 0) 
                              {
                                    echo "Le compte existe déjà. Veuillez vous connecter";
                                    //a mettre le lien de la page de connexion en php
                              } 
                              else 
                              {
                                    $sql = "INSERT INTO personne( Nom, Prenom, Email, ID_statut, Mot_de_passe)
                                    VALUES('$nom', '$prenom','$email', '3', '$motdepasse');";
                                    $result = mysqli_query($db_handle, $sql);
                                    //création dans acheteur
                                     $sql = "SELECT * FROM personne WHERE Email LIKE '%$email%'";
                                     $result = mysqli_query($db_handle, $sql);

                                    $dat = "";
                                     while ($data = mysqli_fetch_assoc($result)) 
                                    {
                                      $dat =$data['ID'];
                                    }
                                    $sql = "INSERT INTO acheteur(ID) VALUES ('$dat');";
                                    $result = mysqli_query($db_handle, $sql);
                                   
                              }
                     }

                 }
                 else 
                 {
                      echo "Database not found";
                 }
           }   
   }  
   else 
   {
      echo "Erreur : $erreur";
   }

//fermer la connexion
mysqli_close($db_handle);
?>
 




