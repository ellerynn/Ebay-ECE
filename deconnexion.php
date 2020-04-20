<?php
    session_start();
    //On détruit la session en cours, et on redirige vers l'accueil
    session_destroy();
    header('Location: accueil.php');
    exit();  
?>





