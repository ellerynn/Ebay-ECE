<?php
 $email = isset($_POST["email"])? $_POST["email"] : "";
 $motdepasse = isset($_POST["motdepasse"])? $_POST["motdepasse"] : "";
 
 $erreur = "";

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
           if (isset($_POST["buttonconnexion"])) 
           {
                if ($db_found) 
                {
                     $sql = "SELECT * FROM personne";

                     if ($email != "") 
                     {
                        //on cherche le livre avec les paramètres titre et auteur
                        $sql .= " WHERE email LIKE '$email'";
                        $result = mysqli_query($db_handle, $sql);
                        //regarder s'il y a de résultat
                              if (mysqli_num_rows($result) == 0) 
                              {
                                    echo "Le compte n'existe pas, Veuillez vous inscrire";
                                    //redirection vers la page html
                              } 
                              else 
                              {
                                   
                                    //création dans acheteur
                                    $sql = "SELECT ID FROM personne WHERE Email LIKE '$email' AND Mot_de_passe LIKE '$motdepasse'";
                                    $result = mysqli_query($db_handle, $sql);
                                    $dat = "";
                                     while ($data = mysqli_fetch_assoc($result)) 
                                    {
                                      $dat =$data['ID'];
                                    }

                                    if (mysqli_num_rows($result) != 0){
                                      echo "Connexion réussi<br>";
                                      $monfichier = fopen('connexion.txt', 'r+');
                                      $infoconnexion = fgets($monfichier);
                                      if (stripos($infoconnexion,'false') !== FALSE){
                                        ftruncate($monfichier,0);
                                        fseek($monfichier, 0);
                                        fputs($monfichier,"true ");
                                        fputs($monfichier,$dat);
                                      }
                                      fclose($monfichier);
                                    }else{
                                      echo "Erreur de connexion, Veuillez reessayer";
                                    }
                              }
                     }

                 }
                 else 
                 {
                      echo "Database not found";
                 }
           } 
      //fermer la connexion
      mysqli_close($db_handle);  
   }  
   else 
   {
      echo "Erreur : $erreur";
   }

?>
 




