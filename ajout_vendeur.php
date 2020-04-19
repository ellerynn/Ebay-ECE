<?php
 $nom = isset($_POST["nom"])? $_POST["nom"] : "";
 $prenom = isset($_POST["prenom"])? $_POST["prenom"] : ""; //if then else
 $pseudo = isset($_POST["pseudo"])? $_POST["pseudo"] : "";
 $email = isset($_POST["email"])? $_POST["email"] : "";

 
 $erreur = "";

 if ($nom == "") { 
 	$erreur .= "Nom est vide. <br>"; }
 	 echo "<br>";
 if ($prenom == "") { 
 	$erreur .= "Prenom est vide. <br>"; }
 	 echo "<br>";
 if ($pseudo == "") {
  $erreur .= "Pseudo est vide. <br>"; }
   echo "<br>";
 if ($email == "") {
 $erreur .= "Email est vide. <br>"; }
  echo "<br>";



 if ($erreur == "") 
 {
     //identifier votre BDD
     $database = "ebay ece paris";
     //connectez-vous dans votre BDD
     //Rappel: votre serveur = localhost | votre login = root |votre password = <rien>
     $db_handle = mysqli_connect('localhost', 'root', '');
     $db_found = mysqli_select_db($db_handle, $database);
           if (isset($_POST["buttonajoutervendeur"])) 
           {
                if ($db_found) 
                {
                     $sql = "SELECT * FROM personne P, vendeur V";

                     if ($email != "") 
                     {
                        
                        $sql .= " WHERE P.Email LIKE '$email' OR V.Pseudo LIKE '$pseudo';";
                        $result = mysqli_query($db_handle, $sql);
                        //regarder s'il y a de résultat
                              if (mysqli_num_rows($result) != 0) 
                              {
                                    echo "Soit Le compte existe déjà, soit le pseudo existe déjà";
                                    //a mettre le lien de la page de connexion en php
                              } 
                              else 
                              {
                                    $sql = "INSERT INTO personne( Nom, Prenom, Email, Statut, Mot_de_passe)
                                    VALUES('$nom', '$prenom','$email', '2', '$email');";
                                    $result = mysqli_query($db_handle, $sql);
                                    //création dans vendeur
                                     $sql = "SELECT * FROM personne WHERE Email LIKE '$email'";
                                     $result = mysqli_query($db_handle, $sql);

                                    $dat = "";
                                     while ($data = mysqli_fetch_assoc($result)) 
                                    {
                                      $dat =$data['ID'];
                                    }
                                    $sql = "INSERT INTO vendeur(ID,Pseudo, ID_photo, ID_image_fond) VALUES ('$dat','$pseudo', 'photo_defaut.jpg', 'fond.jpg' );";
                                    $result = mysqli_query($db_handle, $sql);
                                   
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
 




