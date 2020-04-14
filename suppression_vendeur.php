<?php
 $id = isset($_POST["id"])? $_POST["id"] : "";
 $pseudo = isset($_POST["pseudo"])? $_POST["pseudo"] : "";

 
 $erreur = "";

 if ($id == "") { 
  $erreur .= "ID est vide. <br>"; }
   echo "<br>";
 if ($pseudo == "") { 
  $erreur .= "Le pseudo est vide. <br>"; }
   echo "<br>";

 if ($erreur == "") 
 {
     //identifier votre BDD
     $database = "ebay ece paris";
     //connectez-vous dans votre BDD
     //Rappel: votre serveur = localhost | votre login = root |votre password = <rien>
     $db_handle = mysqli_connect('localhost', 'root', '');
     $db_found = mysqli_select_db($db_handle, $database);
           if (isset($_POST["buttonsupprimervendeur"])) 
           {
                if ($db_found) 
                {
                      //Vérification si le nom est déjà existant:
                      $sql = "SELECT * FROM personne P, vendeur V WHERE P.ID = '$id' AND V.ID = '$id' AND V.Pseudo LIKE '$pseudo'";
                      $result = mysqli_query($db_handle, $sql);
                      /*
                      if ($id != "") 
                      {
                        $sql .= " WHERE ID_item LIKE '%$id%'";
                      }*/
                      
                      if (mysqli_num_rows($result) == 0 ) {
                        //Livre inexistant
                        echo "Erreur, ce vendeur n'existe pas. <br>";
                      }
                      else {
                              $sql = "DELETE FROM personne";
                              $sql .= " WHERE ID = $id";
                              $result = mysqli_query($db_handle, $sql);
                              echo "Suppression réussi dans personne! . <br>";
                              $sql = "DELETE FROM vendeur";
                              $sql .= " WHERE ID = $id AND Pseudo LIKE '$pseudo'";
                              $result = mysqli_query($db_handle, $sql);
                              echo "Suppression réussi dans vendeur! . <br>";
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
 




