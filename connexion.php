<?php
    //Constantes
    include("const.php");

    //Variables de connexion
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
    if (isset($_POST["buttonconnexion"])) 
    {
        //message erreur pour connexion acheteur si mail et mot de passe non rempli
        if ($login == "") 
            $erreur .= "Identifiant est vide. <br>"; 

        echo "<br>";

        if ($psw == "")
            $erreur .= "Mot de passe est vide. <br>";
        
        echo "$erreur";
        echo "<br>";

        if ($erreur == "") 
        {
            if ($db_found) 
            {
                $sql = "SELECT * FROM personne WHERE Email LIKE '$login'";
                $result = mysqli_query($db_handle, $sql);
                $sqlv = "SELECT * FROM personne WHERE Email LIKE '$psw'";
                $resultv = mysqli_query($db_handle, $sqlv);

                //Si aucun résultat, c'est peut être un vendeur ! son email lui sert de mot de passe
                if (mysqli_num_rows($result) == 0 && mysqli_num_rows($resultv) == 0) 
                {
                    $erreur.= "Le compte n'existe pas, veuillez vous inscrire";
                    header('Location: inscription.php');
                }
                
                else
                {
                    //On choppe id et statut (1) admin, (2) vendeur ou (3) acheteur
                    $id = "";
                    $statut = "";
                    if(mysqli_num_rows($result) != 0)
                        while ($data = mysqli_fetch_assoc($result)) 
                        {
                            $id =$data['ID'];
                            $statut =$data['Statut'];
                        }
                    else
                        while ($data = mysqli_fetch_assoc($resultv)) 
                        {
                            $id =$data['ID'];
                            $statut =$data['Statut'];
                        }

                    //Si c'est un acheteur ou un admin
                    if($statut == ACHETEUR || $statut == ADMIN)
                    {
                        $sql = "SELECT ID , Statut FROM personne WHERE Email LIKE '$login' AND Mot_de_passe LIKE '$psw'";
                        $result = mysqli_query($db_handle, $sql);
                        
                        if (mysqli_num_rows($result) != 0)
                        {
                            // On ouvre la session
                            session_start();
                            // On enregistre le login en session
                            $_SESSION['login'] = $login;
                            $_SESSION['psw'] = $psw;
                            $_SESSION['Statut'] = $statut;
                            $_SESSION['ID'] = $id;
                            // On redirige vers le fichier accueil.php
                            header('Location: accueil.php');
                            exit();
                        }
                        elseif (mysqli_num_rows($result) == 0)
                            $erreur.= "Erreur de connexion admin/acheteur, veuillez reessayer.";
                    }

                    //Si c'est un vendeur
                    elseif($statut == VENDEUR)
                    {
                        $sql = "SELECT P.Email, P.ID, P.Statut FROM vendeur V, personne P WHERE P.ID = V.ID AND V.Pseudo LIKE '$login' AND P.Mot_de_passe LIKE '$psw'";
                        $result = mysqli_query($db_handle, $sql);
                        
                        if (mysqli_num_rows($result) != 0)
                        {
                            // On ouvre la session
                            session_start();
                            // On enregistre le login en session
                            $_SESSION['login'] = $login;
                            $_SESSION['psw'] = $psw;
                            $_SESSION['Statut'] = $statut;
                            $_SESSION['ID'] = $id;
                            // On redirige vers le fichier acuueil.php
                            header('Location: accueil.php');
                            exit();
                        }
                        elseif (mysqli_num_rows($result) == 0)
                            $erreur .="Erreur de connexion vendeur, veuillez reessayer.";
                    }
                }
            }
            else
                echo "Database not found";
        }
    }
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
            <a class="navbar-brand" href="accueil.php"><img src="logo.png" style="width: 100px; transform: translateY(-4px);"></a>
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
                            <a class="nav-link dropdown-item" href="achat.php" id="l3">Achat</a>
                            <a class="nav-link dropdown-item" href="vendre.php" id="l2">Vendre</a>
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
            <div class="panel border" style="margin: 0 auto; height: 350px; width: 700px; padding: 50px; margin-bottom: 1em;">
                <h1 class="text-center">Connexion</h1>
                <form style="margin: 0 auto; padding: 50px; padding-top: 10px;" action="" method="post">
                    <table>
                        <tr>
                            <td><input class="form-control" style="margin-bottom: 10px; width:500px; border-top: 0; border-right: 0; border-left: 0;" type="text" name="login" placeholder="Identifiant" required="true"></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" style="margin-bottom: 1em; width:500px; border-top: 0; border-right: 0; border-left: 0;" type="password" name="psw" placeholder="Mot de passe" required="true"></td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                            <input class="form-control btn border btn-outline-secondary rounded-lg" name="buttonconnexion" type="submit" value="Connexion">
                            <p class="text-center"><small>Pas encore de <a href="inscription.php">compte</a> ?</small></p>
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
                            <li>Ferraille ou Trésor                            </li>    
                            <li>Bon pour le Musée</li> 
                            <li>Accessoires VIP</li>               
                        </ul> 
                    </div> 
                    <div class="col-lg-3 col-md-3 col-sm-12">   
                        <a href="achat.php" id="l3"><h5 class="text-uppercase font-weight-bold">Achat</h5></a>
                        <ul>  
                            <li>Enchères </li>    
                            <li>Achetez-le maintenant</li> 
                            <li>Meilleure offre</li>               
                        </ul> 
                    </div>   
                    <!--On a laissé les liens actifs ici parce que de toute facon, si vous cliquez, vous serez redirigé vers connexion-->
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