<!--====================================================================================================
	fichier: login.php
	bdd: base_distante / Identification
	auteur: Seguin Emeric
	date: 10/03/2021
	dernière mise à jour: 11/03/2021
	rôle: scipt php pour gérer la connection avec la création d'une session et redirection vers la page acceuil
	projet: PORTAIL
	résultat: page de login dynamique 
==================================================================================================-->

<?php
    //Création d'un nouveau gabarit et import de code HTML dedans
	$tpl = new hyla_tpl( 'tpl' );
	$tpl->importFile( 'login.tpl' );

    // Instanciation des variables
	$cypher = 'AES-256-CBC';
    $encryption_key = 's5u8x/A?D(G+KbPeShVmYq3t6w9z$B&E';
    $iv = 'RfUjWnZr4u7x!A%D'; 

    //Implémentation du texte dans le gabarit à partir du ficher message
	$tpl->setVar( 'titrePageLogin', $titrePageLoginMsg );
	$tpl->setVar( 'titreLogin', $titreLoginMsg );
    $tpl->setVar( 'iD', $idMsg );
	$tpl->setVar( 'mDP', $mdpMsg );
	$tpl->setVar( 'loginConnect', $loginMsg );

    // Connections à la base de données
    try 
	{
		$bdd = new PDO( 'mysql:host=localhost;dbname=base_distante;charset=utf8','root','Snir2020*' );
		$bdd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	}
	catch( Exception $e ) // En cas d'échec de connection renvoie l'erreur
	{
		die( 'Erreur : '.$e->getMessage() );
	}

    // Récupération des données de la base de donnée
	$req = $bdd->query('SELECT * FROM Identification');
	while($msgCrypte = $req->fetch())
    {
        $idCrypte = $msgCrypte[ID];
        $mdpCrypte = $msgCrypte[MDP];
	}
	$req->closeCursor();
    
    // Quand on clique sur le boutons connexion
    if(isset($_POST['connect'])){
        
        // Récupération des données entrées en encryption de celle-ci      
        $username = openssl_encrypt($_POST['username'], $cypher, $encryption_key, 0, $iv);
        $mdp = openssl_encrypt($_POST['password'], $cypher, $encryption_key, 0, $iv);     
        
        if($username !== $idCrypte){ //test entre le mdp entrées et le bon
            //echo "Mauvais nom d'utilisateur </br>"; 
        }elseif($mdp !== $mdpCrypte){ //test entre le mdp entrées et le bon
            //echo "Mauvais mot de passe </br>";
        }else{// ouverture de session 
            session_start();
            $user = openssl_decrypt($username, $cypher, $encryption_key, 0, $iv);;
            $_SESSION['login'] = $user;
            require './php/view.php';            
        }
    }
    

    /*$boutonConnect = "index.php?user=".$_POST['username']."&mdp=".$_POST['password']."&cmde=connect";
    $tpl->setVar('bouton', $boutonConnect);*/

?>
