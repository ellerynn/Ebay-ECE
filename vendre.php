<?php
	include("const.php");
	// On prolonge la session
	session_start();

	// On teste si la variable de session existe et contient une valeur
	if(isset($_SESSION['login']))
	{
		$login = $_SESSION['login'];
		$psw = $_SESSION['psw'];
		$statut = $_SESSION['Statut'];
		$id = $_SESSION['ID'];
	}
	else 
	{
	  	// Si inexistante ou nulle, on redirige vers le formulaire de login
	  	header('Location: connexion.php');
	  	exit();
	}
	    //identifier votre BDD
    $database = "ebay ece paris";
    $db_handle = mysqli_connect('localhost', 'root', '');
    $db_found = mysqli_select_db($db_handle, $database);
	$erreur ="";

	$table_item = array();
	$table_photo = array();
	$nom_item = array();
	$ID_item = array();

	//Récuperation des ID_item du vendeur (dont admins) connecté
	$sql = "SELECT * FROM item WHERE ID_vendeur LIKE '$id' ";
	$result = mysqli_query($db_handle, $sql);

	if (mysqli_num_rows($result) != 0) 
	{
		$i=0;
		while ($data = mysqli_fetch_assoc($result)) 
		{
			$ID_item[$i] = $data['ID_item'];
			$i++;
		}	
	}

	//recuperation pour mes ventes
	$sql1 = "SELECT * FROM item WHERE ID_vendeur LIKE '$id' "; //retrouver les ID_items issu de ID_item[]
	$result1 = mysqli_query($db_handle, $sql1);

	if (mysqli_num_rows($result1) != 0) 
	{
		$i=0;
		$temp = array();
		while ($data = mysqli_fetch_assoc($result1) ) 
		{
			$i_temp = 0;
			$temp[$i_temp] = $ID_item[$i]; //on garde en mémoire d'ID du item qu'on traite i_temp = 0
			$i_temp++;
			$temp[$i_temp] = $data['Nom_item']; // i_temp = 1

			$table_item["$ID_item[$i]"] = $temp; // Tableau associatif
			$i++;
		}
	}	

	for($a=0; $a < count($ID_item); $a++)
	{
		$sql1 = "SELECT * FROM photo WHERE ID_item LIKE '$ID_item[$a]' ";
		$result1 = mysqli_query($db_handle, $sql1);
		
		if (mysqli_num_rows($result1) != 0) 
		{
			$v = 0;
			$temp =array();
			while ($data = mysqli_fetch_assoc($result1) )  //extraction de toute les photos d'un item donnée ($u)
			{
				$temp[$v] = $data['Nom_photo'];
				$v++;
			}
			$table_photo["$ID_item[$a]"]= $temp; //array de photo dans tableau associatif
		}
	}

	if (isset($_POST["boutonajoutproduit"])) 
	{
	  	$datetime = date('Y-m-d');
		echo $datetime;
		echo date('H:i');

		$nom = isset($_POST["nom"])? $_POST["nom"] : "";
		$filephoto = isset($_POST["filephoto"])? $_POST["filephoto"] : "";
		$filevideo = isset($_POST["fileVideo"])? $_POST["fileVideo"] : "";
		$description = isset($_POST["description"])? $_POST["description"]: "";
		$categorie = isset($_POST["categorie"])? $_POST["categorie"] : "";
		$prix = isset($_POST["prix"])? $_POST["prix"] : "";
		$vente1 = isset($_POST["vente1"])? $_POST["vente1"] : ""; //achat immediat
		$vente2 = isset($_POST["vente2"])? $_POST["vente2"] : ""; //enchere ou offre
		$datedebut = isset($_POST["datedebut"])? $_POST["datedebut"] : "";
		$heuredebut = isset($_POST["heuredebut"])? $_POST["heuredebut"] : "";
		$datefin = isset($_POST["datefin"])? $_POST["datefin"] : "";
		$heurefin = isset($_POST["heurefin"])? $_POST["heurefin"] : "";
		$prixdepart = isset($_POST["prixdepart"])? $_POST["prixdepart"] : "";
		
		if ($nom == "")  
			$erreur .= "Nom est vide. <br>"; 
		
		if ($_FILES['filephoto']['name'][0] =="") 
	 		$erreur .= "Aucune photo n'a été ajouté. <br>";
	 	
	 	//verification de type des photos :
		$countfiles = count($_FILES['filephoto']['name']);

		for($i=0;$i<$countfiles;$i++)
		{
			if ($_FILES['filephoto']['type'][$i] != "image/jpeg" && $_FILES['filephoto']['name'][0] !="")
				$erreur .= "Une ou plusieurs images ne sont pas en .jpg. <br>";	
		}

	 	//vérification du type de la vidéo
	 	if (count($_FILES['filevideo']['name']) == 1 && $_FILES['filevideo']['type'][0] != "video/mp4" && $_FILES['filevideo']['name'][0] != "")
	 	 	$erreur .="La vidéo choisi doit être en .mp4. <br>";

		if ($description == "")  
		 	$erreur .= "La description est vide. <br>"; 
		
		if ($categorie == "")
		 	$erreur .= "La catégorie est vide. <br>"; 
		 
		if (($prix == "" && $vente1 != "") || ($prix == "" && $vente1 != "" && $vente2 == "offre") || ($prix == "" && $vente2 == "offre")) 
		 	$erreur .= "Le prix est vide. <br>"; 
		
		if ($prix != "" && $vente2 == "enchere" && $prixdepart != "" && $prix<$prixdepart)
		 	$erreur .= "Le prix normal doit être supérieur au prix de départ pour l'enchere. <br>";
		
		if ($vente1 == "" && $vente2 =="")
		 	$erreur .= "Le choix de vente est vide. <br>";
		
		if ($vente2 == "enchere")
		{
		 	if ($datedebut == "")
		 		$erreur .= "Vous n'avez pas choisi une date de début pour l'enchère du produit. <br>";
		 	if ($heuredebut == "")
		 		$erreur .= "Vous n'avez pas choisi une heure de début pour l'enchère du produit. <br>";
		 	if ($datefin == "")
		 		$erreur .= "Vous n'avez pas choisi une date de fin pour l'enchère du produit. <br>";
		 	if ($heurefin == "")
		 		$erreur .= "Vous n'avez pas choisi une heure de fin pour l'enchère du produit. <br>";
		}
		
		if ($prixdepart == "" && $vente2 == "enchere") 
	 	 	$erreur .= "Le prix de départ pour l'enchère est vide. <br>";
	 	 
	 	if ($prixdepart >= $prix && $prixdepart != "" && $prix != "" && $vente2 == "enchere")
	 	 	$erreur .= "le prix pour l'enchère doit être inférieur au prix normal du produit. <br>";
	 	
	 	//Si tous les champs sont rempli correctement
		if ($erreur == "") 
		{
		 	$type_vente_choisi = $vente1." ". $vente2;

		    ///BDD
	        if ($db_found) 
	        {
	        	$filenamevideo = "";
				if (isset($fileVideo))
				{
				    $countfiles = count($_FILES['filevideo']['name']);
					for($i=0;$i<$countfiles;$i++)
					{
						$filenamevideo = $_FILES['filevideo']['name'][$i];
						move_uploaded_file($_FILES['filevideo']['tmp_name'][$i],'videos_web/'.$filenamevideo);
					}
				}
				
				//s'il ne s'agit que d'un enchère Sinon le prix reste en prix par défaut: 
				if (strlen($type_vente_choisi) == 8)
					$prix = $prixdepart;

		        $sql = "INSERT INTO item(ID_vendeur, ID_type_vente, Nom_item, Description, Categorie, Prix, Video) VALUES ($id,'$type_vente_choisi','$nom','$description','$categorie','$prix','$filenamevideo');";
		        $result = mysqli_query($db_handle, $sql);
		        //Normalement c'est ajouté , mtn vérifions et extraction de l'ID: 
		        $sql = "SELECT LAST_INSERT_ID(ID_item) FROM item ";
		        $result = mysqli_query($db_handle, $sql);

	        	$last_id_item = "";
	           	if (mysqli_num_rows($result) != 0)
	            {
	                echo "Votre item a été ajouté avec succes";
	                while ($data = mysqli_fetch_assoc($result)) 
                    {
                        $last_id_item = $data['LAST_INSERT_ID(ID_item)'];
                    }
		        } 

		        //Ajout dans la table photo
				$countfiles = count($_FILES['filephoto']['name']);
				for($i=0;$i<$countfiles;$i++)
				{
					$filenamephoto = $_FILES['filephoto']['name'][$i];
					move_uploaded_file($_FILES['filephoto']['tmp_name'][$i],'images_web/'.$filenamephoto);
					$sql = "INSERT INTO photo(Nom_photo, ID_item) VALUES ('$filenamephoto','$last_id_item');";
			        $result = mysqli_query($db_handle, $sql);
				}
				
				//Ajout dans la liste d'enchere si enchere.
				if (strlen($type_vente_choisi) == 8 || strlen($type_vente_choisi) == 22)
				{
					echo "je suis dedans";
					$heuredebut .=":00";
					$heurefin .=":00";
					echo "$last_id_item"."<br>";
					echo "$datedebut"."<br>";
					echo "$heuredebut"."<br>";
					echo "$datefin"."<br>";
					echo "$heurefin"."<br>";
					echo "$prixdepart"."<br>";
					$sql = "INSERT INTO liste_enchere(ID_item, Date_debut, Heure_debut, Date_fin, Heure_fin, Prix_premier, Prix) VALUES ('$last_id_item', '$datedebut', '$heuredebut', '$datefin', '$heurefin', '$prixdepart','$prixdepart');";
					$result = mysqli_query($db_handle, $sql);
				}
	        }
	        else 
	            echo "Database not found";
	        
	        //fermer la connexion
	    	mysqli_close($db_handle); 
	    }   
	    else 
	    	echo "Erreur : <br>$erreur";
	}  
?>

<!DOCTYPE html> 
<html> 
	<head>  
		<title>Vendre</title>  
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

			<form class="navbar-form inline-form">
				<div class="form-group">
				  	<span style="color:white;"><i class="fas fa-search"></i></span>
				   	<input type="search" class="input-sm form-control-sm" placeholder="Rechercher sur eBay ECE">
				   	<button class="btn btn-outline-secondary btn-sm">Chercher</button>
				</div>
			</form>

			<div class="collapse navbar-collapse">     
				<ul class="navbar-nav">       
					<li class="nav-item">
						<a class="nav-link" href="accueil.php">Accueil</a>
					</li>
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" id="ades">Mon eBay</button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="vendre.php">Vendre</a>
					  	</div>
					</li>  
					<li class="nav-item">
						<a style="display: none; transform: translateY(7px); color: white;" href="admin.php" id="ad">Admin.</a>
					</li> 
					<li class="nav-item dropdown">
						<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user"></i></button>
					  	<div class="dropdown-menu" id="menu-deroulant">
						    <a class="nav-link dropdown-item" href="votre_compte.php">Mon compte</a>
						    <a class="nav-link dropdown-item" href="deconnexion.php">Se déconnecter</a>
					  	</div>
					</li> 
					<li class="nav-item">
						<i class="nav-link fas fa-shopping-cart" style="margin-top: 5px;"></i>
					</li>    
				</ul>      
			</div> 
		</nav>

		<br><br><br>
		<div class="container features">
			<div class="row"> 
				<div class="col-lg-3 col-md-3 col-sm-12" style="position: relative;">
					<h3 class="text-center">eBay ECE</h3>
					<p></p>
					
			        <div class="list-group">
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv1">Vendre</button>
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv2">Supprimer une vente</button>
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv3">Messages</button>
			        	<button type="button" class="list-group-item btn" style="width: 100%;" id="bv4">Offres</button>
			        </div>	
			    </div>
			    <div class="col-lg-9 col-md-9 col-sm-12" style="position: relative; height: 800px;">
			    	<div class="panel border" style="padding: 1em; border-radius: 5px;" id="panel_vendre">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Vendre</h2><br>
					    </div>
					    <div class="panel-body">
							<form method="post" action="" enctype="multipart/form-data">
						       	<div class="form-group">
						          	<div class="row">
						          		<div class="col-lg-6 col-md-6 col-sm-12">
						           			<p class="font-weight-bold">Nom du produit</p>
						               		<input class="form-control" style="width: 100%" type="text" name="nom" placeholder="Nom du produit" required>
						               	</div>
							            <div class="col-lg-6 col-md-6 col-sm-12">
							                <p class="font-weight-bold">Prix</p>
							                <input class="form-control" type="number" style="width: 100%" name="prix" placeholder="Prix">
							            </div>
							        </div>
						        </div>
						        <div class="form-group">
						          	<div class="row">
						           		<div class="col-lg-6 col-md-6 col-sm-12">
						                    <p class="font-weight-bold">Photo(s)</p>
						                    <input type="file" name="filephoto[]" id="file" multiple required>
						                </div>
										<div class="col-lg-6 col-md-6 col-sm-12">
							                <p class="font-weight-bold">Vidéo (facultative)</p>
						                    <input type="file" name="filevideo[]" id="file" multiple>
							            </div>
							        </div>
				                </div>
						        <div class="form-group">
						            <textarea name="description" rows="5" cols="100" placeholder="Description" id="description" required></textarea>
						        </div>
						        <div class="form-group">
						            <p class="font-weight-bold">Catégorie(s)</p>
						            <input type="radio" name="categorie" value="Farraille_tresor" id="cb">Ferraille ou Trésor
									<input type="radio" name="categorie" value="Musee" id="cb">Bon pour le Musée
									<input type="radio" name="categorie" value="VIP" id="cb">Accessoire VIP
						        </div>
						        <!--A BLINDER-->
						        <div class="form-group">
						          	<p class="font-weight-bold">Type de vente</p>
						           	<input type="checkbox" name="vente1" value="achat_immediat" id="cb">Achat immédiat 
									<input type="checkbox" name="vente2" value="enchere" style="margin-right: 5px;margin-left: 10px;" id="ench" onclick="montrer()">Enchère
									<input type="checkbox" name="vente2" value="offre" id="cb">Meilleur offre
								</div>
								<div class="form-group" style="display: none;" id="jpp">
									<div class="row">       
										<div class="col-lg-6 col-md-6 col-sm-12">
											Date de début : <input class="form-control" type="Date" name="datedebut"> 
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											Date de fin : <input class="form-control"  type="Date" name="datefin"> 
										</div>
									</div>
									<div class="row">       
										<div class="col-lg-4 col-md-4 col-sm-12">
											Heure de début : <input class="form-control" type="time" name="heuredebut">
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12">
											Heure de fin : <input class="form-control" type="time" name="heurefin">
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12">
											Prix de départ : <input class="form-control" type="number" name="prixdepart">
										</div>
									</div>
								</div>
								<div class="form-group">
						           	<input class="form-control" style="width:200px; margin: 0 auto" name="boutonajoutproduit" type="submit" value="Ajouter le produit">
								</div>

								<script>
									function montrer() 
									{
										// Get the checkbox
										var checkBox = document.getElementById("ench");
										// Get the output text
										var text = document.getElementById("jpp");

										// If the checkbox is checked, display the output text
										if (checkBox.checked == true)
											text.style.display = "block";
										else 
											text.style.display = "none";
									}
								</script> 
		               		</form>
		                </div>
		            </div>
				    <div class="panel border" style="display: none; padding: 1em; padding-bottom: 2em; border-radius: 5px;" id="panel_supp_vendeur">
					    <div class="panel-body row">
					    	<div class="col-lg-6 col-md-6 col-sm-12" style="position: relative; min-height: 400px;">
					    		<br><h2 class="text-center">Supprimer une vente</h2><br>
							    <form action="suppression_produit.php" method="post">
									<div class="form-group">
				                        <input class="form-control" style="width: 50%; margin: 0 auto" type="number" name="id" placeholder="ID de l'article" required>
				                    </div>
				                    <div class="form-group">
				                    	<input class="form-control" style="width: 30%; margin: 0 auto" name="buttonsupprimer" type="submit" value="Supprimer">
									</div>
								</form>	
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12" style="position: relative; min-height: 400px;">
								<br><h2 class="text-center">Mes ventes</h2><br>
								<?php
									if(count($ID_item)==0)	
										echo '<p>Vous ne vendez rien ! <br> Commencez à vendre <a href="vendre.php">ici</a></p>';

									else
									{						
										for ($i = 0 ; $i<count($ID_item); $i++)
										{ 
											for ($u = 0 ; $u < count($table_photo["$ID_item[$i]"]); $u++)
											{
												echo '<img src = "images_web/'.$table_photo["$ID_item[$i]"][$u].'" height=100 width =100 ><br>';
											}
											//traitement de la table item:
											echo "L'ID de l'item :".$table_item["$ID_item[$i]"][0]."<br>";
											echo "Le nom de l'item :".$table_item["$ID_item[$i]"][1]."<br>";
										}
										echo "valeur dans table <br> ";
										echo $table_item["75"][1]."<br>";
										echo $table_item["76"][1]."<br>";
										echo $table_item["77"][1]."<br>";
										echo $table_item["78"][1]."<br>";
										echo "valeur dans ID_tiem[] <br>";
										echo $ID_item[0]."<br>";
										echo $ID_item[1]."<br>";
										echo $ID_item[2]."<br>";
										echo $ID_item[3]."<br>";

									}
								?>
							</div>				
				        </div>
				    </div>
					<div class="panel" style="display: none;" id="panel_mes_vendeur">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Messages</h2><br>
					    </div>
					    <div class="panel-body">					
				        </div>
				    </div>
				    <div class="panel" style="display: none;" id="panel_o_vendeur">
					    <div class="panel-heading">
					    	<br><h2 class="text-center">Offres</h2><br>
					    </div>
					    <div class="panel-body">					
				        </div>
				    </div>
			    </div>
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
						<h5 class="text-uppercase font-weight-bold">Achat</h5>
						<ul>  
							<li>
								Enchères
							</li>    
							<li>
								Achetez-le maintenant
							</li> 
							<li>
								Meilleure offre
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
								<h5 class="text-uppercase font-weight-bold"> <a href="admin.php" id="admin">Admin</a> </h5>
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

		<?php
			if($_SESSION['Statut'] == VENDEUR)
			{?>
				<script>		
					document.getElementById("admin").onclick = function() {return false;}
				</script> <?php
			}

			else
			{?>
				<script type="text/javascript">
					var x = document.getElementById("ad");
					x.style.display = "block";
				</script> <?php
			}?>

		<script type="text/javascript">
			var panel_supp = document.getElementById("panel_supp_vendeur");
			var panel_vendre = document.getElementById("panel_vendre");
			var panel_mes = document.getElementById("panel_mes_vendeur");
			var panel_offres = document.getElementById("panel_o_vendeur");

			document.getElementById("bv1").onclick = function() {
				panel_vendre.style.display ="block";
				panel_supp.style.display ="none";
				panel_mes.style.display ="none";
				panel_offres.style.display ="none";
			}

			document.getElementById("bv2").onclick = function() {
				panel_vendre.style.display ="none";
				panel_supp.style.display ="block";
				panel_mes.style.display ="none";
				panel_offres.style.display ="none";
			}

			document.getElementById("bv3").onclick = function() {
				panel_supp.style.display ="none";
				panel_vendre.style.display ="none";
				panel_mes.style.display ="block";
				panel_offres.style.display ="none";
			}

			document.getElementById("bv4").onclick = function() {
				panel_supp.style.display ="none";
				panel_vendre.style.display ="none";
				panel_mes.style.display ="none";
				panel_offres.style.display ="block";
			}
		</script>
	</body> 
</html> 