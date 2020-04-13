<?php
 $email = isset($_POST["email"])? $_POST["email"] : "";
 $motdepasse = isset($_POST["motdepasse"])? $_POST["motdepasse"] : "";
 $pseudo = isset($_POST["pseudo"])? $_POST["pseudo"] : "";
 $erreur = "";




     //identifier votre BDD
     $database = "ebay ece paris";
     //connectez-vous dans votre BDD
     //Rappel: votre serveur = localhost | votre login = root |votre password = <rien>
     $db_handle = mysqli_connect('localhost', 'root', '');
     $db_found = mysqli_select_db($db_handle, $database);
           //Partie pour connexion acheteur
           if (isset($_POST["buttonconnexion"])) 
           {
            //message erreur pour connexion acheteur si mail et mot de passe non rempli
              if ($email == "") {
              $erreur .= "Email est vide. <br>"; }
              echo "<br>";
              if ($motdepasse == "") {
              $erreur .= "Mot de passe est vide. <br>"; }
              echo "$erreur";
              echo "<br>";

              if ($erreur == "") 
              {
                  if ($db_found) 
                  {
                       $sql = "SELECT * FROM personne";

                       if ($email != "") 
                       {
                          
                          $sql .= " WHERE Email LIKE '$email'";
                          $result = mysqli_query($db_handle, $sql);
                          //regarder s'il y a de résultat
                                if (mysqli_num_rows($result) == 0) 
                                {
                                      echo "Le compte n'existe pas, veuillez vous inscrire";
                                      //redirection vers la page html
                                } 
                                else 
                                {
                                     
                                      //création dans acheteur
                                      $sql = "SELECT ID , Statut FROM personne WHERE Email LIKE '$email' AND Mot_de_passe LIKE '$motdepasse'";
                                      $result = mysqli_query($db_handle, $sql);
                                      $dat = "";
                                      $statut = "";
                                       while ($data = mysqli_fetch_assoc($result)) 
                                      {
                                        $dat =$data['ID'];
                                        $statut =$data['Statut'];
                                      }

                                      if (mysqli_num_rows($result) != 0 && $statut == 3){
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
                                      }
                                      if (mysqli_num_rows($result) == 0){
                                        echo "Erreur de connexion, veuillez reessayer.";
                                      }
                                      if ($statut != 3){
                                        echo "Erreur, ce formulaire est dédié au acheteur. Redirigez vous vers votre page atitré.";
                                      }
                                }
                       }

                   }
                   else 
                   {
                        echo "Database not found";
                   }
              } 
         }

           //Partie pour connexion vendeur
      if (isset($_POST["buttontemporairevendeur"])) 
           {
            //message erreur pour connexion vendeur si pseudo et mail non rempli
             if ($pseudo == "") {
             $erreur .= "Pseudo est vide. <br>"; }
             echo "<br>";
             if ($motdepasse == "") {
             $erreur .= "Mot de passe est vide. <br>"; }
             echo "$erreur";
             echo "<br>";
             if ($erreur == "") 
              {
                if ($db_found) 
                {
                   // $sql = "SELECT V.Pseudo,P.Email FROM vendeur V , personne P WHERE P.ID = V.ID";
                    $sql = "SELECT * FROM vendeur";
                     if ($pseudo != "") 
                     {
                        
                        $sql .= " WHERE Pseudo LIKE '$pseudo'";
                        $result = mysqli_query($db_handle, $sql);
                        //regarder s'il y a de résultat
                              if (mysqli_num_rows($result) == 0) 
                              {
                                    echo "Votre compte n'existe pas, veuillez contactez l'adminisatrateur du site";
                                    //redirection vers la page html
                              } 
                              else 
                              {
                                   
                                    $sql = "SELECT P.Email, P.ID, P.Statut FROM vendeur V , personne P WHERE P.ID = V.ID AND V.Pseudo LIKE '$pseudo'";
                                    $result = mysqli_query($db_handle, $sql);
                                    $dat = "";
                                    $monId="";
                                    $statut ="";
                                     while ($data = mysqli_fetch_assoc($result)) 
                                    {
                                      $dat =$data['Email'];
                                      $monId=$data['ID'];
                                      $statut= $data['Statut'];
                                    }

                                    if (mysqli_num_rows($result) != 0 && $statut == 2){
                                      echo "Connexion réussi<br>";
                                      $monfichier = fopen('connexion.txt', 'r+');
                                      $infoconnexion = fgets($monfichier);
                                      if (stripos($infoconnexion,'false') !== FALSE){
                                        ftruncate($monfichier,0);
                                        fseek($monfichier, 0);
                                        fputs($monfichier,"true ");
                                        fputs($monfichier,$monId);
                                      }
                                      fclose($monfichier);
                                    }
                                    if (mysqli_num_rows($result) == 0){
                                      echo "Erreur de connexion, veuillez reessayer.";
                                    }
                                    if ($statut != 2){
                                      echo "Erreur, ce formulaire est dédié au vendeur. Redirigez vous vers votre page atitré.";
                                    }
                              }
                     }

                 }
                 else 
                 {
                      echo "Database not found";
                 }
              }
           } 

           //PARTIE connexion admin
           if (isset($_POST["buttontemporaireadmin"])) 
           {
            //message erreur pour connexion admin si mail et mot de passe non rempli
             if ($email == "") {
             $erreur .= "Email est vide. <br>"; }
             echo "<br>";
             if ($motdepasse == "") {
             $erreur .= "Mot de passe est vide. <br>"; }
             echo "$erreur";
             echo "<br>";
             if ($erreur == "") 
            {
                if ($db_found) 
                {
                     $sql = "SELECT * FROM personne";

                     if ($email != "") 
                     {
                        
                        $sql .= " WHERE Email LIKE '$email'";
                        $result = mysqli_query($db_handle, $sql);
                        //regarder s'il y a de résultat
                              if (mysqli_num_rows($result) == 0) 
                              {
                                    echo "Le compte n'existe pas, veuillez vous inscrire";
                                    //redirection vers la page html
                              } 
                              else 
                              {
                                   
                                    //création dans acheteur
                                    $sql = "SELECT ID , Statut FROM personne WHERE Email LIKE '$email' AND Mot_de_passe LIKE '$motdepasse'";
                                    $result = mysqli_query($db_handle, $sql);
                                    $dat = "";
                                    $statut = "";
                                     while ($data = mysqli_fetch_assoc($result)) 
                                    {
                                      $dat =$data['ID'];
                                      $statut =$data['Statut'];
                                    }

                                    if (mysqli_num_rows($result) != 0 && $statut == 1){
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
                                    }
                                    if (mysqli_num_rows($result) == 0){
                                      echo "Erreur de connexion, veuillez reessayer.";
                                    }
                                    if ($statut != 1){
                                      echo "Erreur, ce formulaire est dédié au administrateur. Redirigez vous vers votre page atitré.";
                                    }
                              }
                     }

                 }
                 else 
                 {
                      echo "Database not found";
                 }
           } 
         }
  mysqli_close($db_handle);

?>
 




