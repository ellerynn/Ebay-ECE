<?php
    include("const.php");

    $nom = isset($_POST["nom"])? $_POST["nom"] : "";
    $prenom = isset($_POST["prenom"])? $_POST["prenom"] : ""; //if then else
    $login = isset($_POST["login"])? $_POST["login"] : "";
    $psw = isset($_POST["psw"])? $_POST["psw"] : "";
    $erreur = "";

    //identifier votre BDD
    $database = "ebay ece paris";
    //connectez-vous dans votre BDD
    //Rappel: votre serveur = localhost | votre login = root |votre password = <rien>
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);

    //Connexion
    if (isset($_POST["buttoninscription"])) 
    {
        if ($nom == "") 
       	    $erreur .= "Nom est vide. <br>"; 

        echo "<br>";

        if ($prenom == "") 
         	  $erreur .= "Prenom est vide. <br>"; 
        
        echo "<br>";

        if ($login == "") 
            $erreur .= "Email est vide. <br>"; 
        
        echo "<br>";

        if ($psw == "")
            $erreur .= "Mot de passe est vide. <br>"; 
        
        echo "<br>";

        if ($erreur == "") 
        {
            if ($db_found) 
            {
                $sql = "SELECT * FROM personne";
                if ($login != "") 
                {
                    //on cherche le livre avec les paramètres titre et auteur
                    $sql .= " WHERE email LIKE '$login'";
                    $result = mysqli_query($db_handle, $sql);
                    //Si on trouve une correspondance : le compte existe deja
                    if (mysqli_num_rows($result) != 0) 
                    {
                        //echo "Le compte existe déjà. Veuillez vous connecter";
                        header('Location: connexion.php');
                    } 
                    else 
                    {
                        $sql = "INSERT INTO personne( Nom, Prenom, Email, Statut, Mot_de_passe)
                        VALUES('$nom', '$prenom','$login', '3', '$psw');";
                        $result = mysqli_query($db_handle, $sql);
                        //création dans acheteur
                        $sql = "SELECT * FROM personne WHERE Email LIKE '$login'";
                        $result = mysqli_query($db_handle, $sql);
                        $id = "";
                        while ($data = mysqli_fetch_assoc($result)) 
                            $id =$data['ID'];
                            
                        $sql = "INSERT INTO acheteur(ID) VALUES ('$id');";
                        $result = mysqli_query($db_handle, $sql); 

                        // On ouvre la session
                        session_start();
                        // On enregistre le login en session
                        $_SESSION['login'] = $login;
                        $_SESSION['psw'] = $psw;
                        $_SESSION['Statut'] = $statut;
                        $_SESSION['ID'] = $id;
                        // On redirige vers le fichier votre_compte.php
                        header('Location: accueil.php');
                    }
                }
            }
            else 
                echo "Database not found";
        } 
    }  
    //fermer la connexion
    mysqli_close($db_handle);
?>
 
<!DOCTYPE html> 
<html> 
    <head>  
        <title>Votre compte</title>  
        <meta charset="utf-8">  
        
        <meta name="viewport" content="width=device-width, initial-scale=1">     
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">            
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>  
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>  

        <link rel="stylesheet" type="text/css" href="style.css"> 
        
        <script type="text/javascript">      
            $(document).ready(function() {           
                $('.header').height($(window).height()); 
            }); 
        </script> 

        <script src="https://kit.fontawesome.com/58c71aba33.js" crossorigin="anonymous"></script>
    </head>     
    <body> 
        <nav class="navbar navbar-expand-md fixed-top"> 
            <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#main-navigation">    
                <span class="navbar-toggler-icon"></span>       
            </button>   

            <div class="collapse navbar-collapse">     
                <ul class="navbar-nav"> <!--navbar-nav — La classe de l'élément de liste <ul> qui contient les éléments de menu. Ces derniers sont notés avec nav-item et nav-link.-->          
                    <li class="nav-item">
                        <a class="nav-link" href="accueil.php">Accueil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">Mon eBay</button>
                        <div class="dropdown-menu" id="menu-deroulant">
                            <a class="nav-link dropdown-item" href="achat.php">Achat</a>
                            <a class="nav-link dropdown-item" href="vendre.php">Vendre</a>
                        </div>
                    </li>  
                    <li class="nav-item dropdown">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user"></i></button>
                        <div class="dropdown-menu" id="menu-deroulant">
                            <a class="nav-link dropdown-item" href="connexion.php">Se connecter</a>
                        </div>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" href="panier.php"><i class="fas fa-shopping-cart"></i></a>
                    </li>    
                </ul>      
            </div> 
        </nav>

        <br><br><br>
        <div class="container-fluid features">
            <div class="panel border" style="height: 500px; width: 700px; transform: translateX(40%); padding: 50px; margin-bottom: 1em;">
                <h1 class="text-center">Créer un compte</h1>
                <form style="transform: translateX(8%);" action="" method="post">
                    <table>
                        <tr>
                            <td><input class="form-control" style="margin-bottom: 10px; width:500px; border-top: 0; border-right: 0; border-left: 0;" type="text" name="nom" placeholder="Nom" required="true"></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" style="margin-bottom: 10px; width:500px; border-top: 0; border-right: 0; border-left: 0;" type="text" name="prenom" placeholder="Prénom" required="true"></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" style="margin-bottom: 10px; width:500px; border-top: 0; border-right: 0; border-left: 0;" type="email" name="login" placeholder="Mail" required="true"></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" style="margin-bottom: 10px; width:500px; border-top: 0; border-right: 0; border-left: 0;" type="password" name="psw" placeholder="Mot de passe" required="true"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <input class="btn border btn-outline-secondary rounded-lg" name="buttoninscription" type="submit" value="S'inscrire">
                                <p class="text-center"><small>Vous avez déjà un <a href="connexion.php">compte</a> ?
                                <br><br>Vous êtes vendeur professionnel ?
                                <br><a href="mailto:camille.bruant@edu.ece.fr">Contactez</a> l'administrateur</small></p>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>  
        </div>

        <footer class="page-footer">   
            <div class="container">    
                <div class="row">       
                    <div class="col-lg-3 col-md-3 col-sm-12">   
                        <h5 class="text-uppercase font-weight-bold">Catégories</h5>
                        <ul>  
                            <li>
                                <a href="#">Ferraille ou Trésor</a>
                            </li>    
                            <li>
                                <a href="#">Bon pour le Musée</a>
                            </li> 
                            <li>
                                <a href="#">Accessoires VIP</a>
                            </li>               
                        </ul> 
                    </div> 
                    <div class="col-lg-3 col-md-3 col-sm-12">   
                        <a href="achat.php" id="l3"><h5 class="text-uppercase font-weight-bold">Achat</h5></a>
                        <ul>  
                            <li>
                                <a href="#">Enchères</a>
                            </li>    
                            <li>
                                <a href="#">Achetez-le maintenant</a>
                            </li> 
                            <li>
                                <a href="#">Meilleure offre</a>
                            </li>               
                        </ul> 
                    </div>   
                    <div class="col-lg-3 col-md-3 col-sm-12">   
                        <ul>  
                            <li>
                                <h5 class="text-uppercase font-weight-bold"> <a href="vendre.php">Vendre</a> </h5>
                            </li>    
                            <li>
                                <h5 class="text-uppercase font-weight-bold"> <a href="votre_compte.php">Votre compte</a> </h5>
                            </li>    
                            <li>
                                <h5 class="text-uppercase font-weight-bold"> <a href="admin.php">Admin</a> </h5>
                            </li>            
                        </ul> 
                    </div> 

                    <div class="col-lg-3 col-md-3 col-sm-12">       
                        <h5 class="text-uppercase font-weight-bold">Contact</h5>       
                        <p> 37, quai de Grenelle, 75015 Paris, France <br>             
                            info@webDynamique.ece.fr <br>             
                            +33 01 02 03 04 05 <br>             
                            +33 01 03 02 05 04 </p>     
                    </div>   
                </div>   
                <div class="footer-copyright text-center"> 
                    <small>&copy; 2019 Copyright | Droit d'auteur: webDynamique.ece.fr</small>                  
                </div> 
            </div>
        </footer>
    </body> 
</html> 