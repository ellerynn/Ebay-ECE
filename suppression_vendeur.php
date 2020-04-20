<?php //debut du php, code qui permet de supprimer un vendeur lorsque lon est connecte en tant q'un admin
///on declare les variables quon va recuperer lors de la saisie 
 $id = isset($_POST["id"])? $_POST["id"] : ""; 
 $pseudo = isset($_POST["pseudo"])? $_POST["pseudo"] : "";
//declare variable null, pour les blindages
 $erreur = "";

//message d'erreur si des cases ne sont pas remplies
 if ($id == "") { 
  $erreur .= "ID est vide. <br>"; }
   echo "<br>";
 if ($pseudo == "") { 
  $erreur .= "Le pseudo est vide. <br>"; }
   echo "<br>";
//si tout est rempli on entre dans la condition
 if ($erreur == "") 
 {
     //identifier notre BDD
     $database = "ebay ece paris";
     //on se connecte à notre BDD
     //Rappel: notre serveur = localhost | notre login = root |notre password = <rien>
     $db_handle = mysqli_connect('localhost', 'root', '');
     $db_found = mysqli_select_db($db_handle, $database);
     //si clique sur le bouton pour supprmier un vendeur
           if (isset($_POST["buttonsupprimervendeur"])) 
           {
            //si bdd trouvé
                if ($db_found) 
                {
                      //on selectionne tous les veuleurs avec des conditions
                      $sql = "SELECT * FROM personne P, vendeur V WHERE P.ID = '$id' AND V.ID = '$id' AND V.Pseudo LIKE '$pseudo'";
                      $result = mysqli_query($db_handle, $sql);
                      
                      if (mysqli_num_rows($result) == 0 ) {
                        //Donnee non existant
                        $erreur.= "Erreur, ce vendeur n'existe pas. <br>";
                      }
                      else {
                        //si la donnee existe on fait la suppression du vendeur
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
                  //si on ne trouve rien
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
   header('Location: admin.php?idErreur='.$erreur);
?>
 




