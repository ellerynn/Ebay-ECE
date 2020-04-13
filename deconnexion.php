<?php

  if (isset($_POST["buttontemporaire"])){
      $monfichier = fopen('connexion.txt', 'r+');
      $infoconnexion = fgets($monfichier);
        if (stripos($infoconnexion,'true') !== FALSE){
          ftruncate($monfichier,0);
          fseek($monfichier, 0);
          fputs($monfichier,"false ");
        }
          fclose($monfichier);
    }
?>





