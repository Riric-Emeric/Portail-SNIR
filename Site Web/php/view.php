<!--====================================================================================================
	fichier: view.php
	bdd: base_distante / Information
	auteur: Seguin Emeric
	date: 24/02/2021
	dernière mise à jour: 12/03/2021
	rôle: scipt php pour visualiser et gérer le contenu de la bdd avec les plaques
	projet: PORTAIL
	résultat: page de visualisation dynamique
==================================================================================================-->

<?php 
    //Création d'un nouveau gabarit et import de code HTML dedans
	$tpl = new hyla_tpl( 'tpl' );
	$tpl->importFile( 'index.tpl' );

    session_start();
    if(empty($_SESSION['login'])) 
    {
        // Si inexistante ou nulle, on redirige vers le formulaire de login
        require './php/login.php';
    }

    // Instanciation des variables
	$cypher = 'AES-256-CBC';
    $encryption_key = 's5u8x/A?D(G+KbPeShVmYq3t6w9z$B&E';
    $iv = 'RfUjWnZr4u7x!A%D'; 
    $data = array();
    $msgDecrypte = array();

    //Implémentation du texte dans le gabarit à partir du ficher message
	$tpl->setVar( 'titrePage', $titrePageMsg );
	$tpl->setVar( 'titre', $titreMsg );
    $tpl->setVar( 'tabId', $tabIdMsg );
	$tpl->setVar( 'tabPrenom', $tabPrenomMsg );
	$tpl->setVar( 'tabNom', $tabNomMsg );
	$tpl->setVar( 'tabPlaque', $tabPlaqueMsg );
    $tpl->setVar( 'debutPhrase', $phraseMsg );
    $tpl->setVar( 'titre2' , $titre2Msg );

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

    if(isset($_POST['deconnect'])){// Quand on clique sur le boutons déconnexion     
         $_SESSION = array();
        // Destruction de la session
        session_destroy();
        // Destruction du tableau de session
        unset($_SESSION);
        header("refresh:0");
    }
    
    if( @$_GET['cmde'] ) // On vérifie si on a reçut une commande 
    {    
        $id = (int) @$_GET['id'];  //On récupère l'id      
        switch ( $_GET['cmde'] ) //En fonction de la commande on réalise la bonne opération 
        {
            case "del": //On supprime l'entrée de la bdd
                $req = $bdd->prepare('DELETE FROM Information WHERE ID = :id');
                $req->execute(array('id' => $id));
                $req->closeCursor();

                break;
            case "upd": //On récupère les données que l'on veut modifier
                $update = $bdd->prepare('SELECT * FROM Information WHERE ID = :id');
                $update->execute(array('id' => $id));
                while( $donnees = $update->fetch())
                {
                    $plaqueDecrypte = openssl_decrypt($donnees[plaque], $cypher, $encryption_key, 0, $iv);
                    $prenomDecrypte = openssl_decrypt($donnees[prenom], $cypher, $encryption_key, 0, $iv);
                    $nomDecrypte = openssl_decrypt($donnees[nom], $cypher, $encryption_key, 0, $iv);
                    $tpl->setVar('nom', $nomDecrypte);
                    $tpl->setVar('prenom', $prenomDecrypte);
                    $tpl->setVar('plaque', $plaqueDecrypte); 
                } 
                $update->closeCursor();            
                break;
            case 'add':
                $tpl->render('add_user');
                break;
            default:
                break;
        }
        if(isset($_POST['modif'])){
            $prenomCrypte = openssl_encrypt($_POST['first-name'], $cypher, $encryption_key, 0, $iv);
            $nomCrypte = openssl_encrypt($_POST['last-name'], $cypher, $encryption_key, 0, $iv);      
            $plaqueCrypte = openssl_encrypt($_POST['plaque'], $cypher, $encryption_key, 0, $iv); 

            // Mise à jour de l'utilisateur de la bdd
            $req = $bdd->prepare('UPDATE Information SET plaque = :plaque, prenom = :prenom, nom = :nom WHERE ID = :id');
            $req->execute(array('id' => $id, 'plaque'=> $plaqueCrypte, 'prenom'=>$prenomCrypte, 'nom'=>$nomCrypte));
            $req->closeCursor;       
        }
        if(isset($_POST['ajout'])){    
            // Récupération des données entrées en encryption de celle-ci      
            $prenomCrypte = openssl_encrypt($_POST['first-name'], $cypher, $encryption_key, 0, $iv);
            $nomCrypte = openssl_encrypt($_POST['last-name'], $cypher, $encryption_key, 0, $iv);      
            $plaqueCrypte = openssl_encrypt($_POST['plaque'], $cypher, $encryption_key, 0, $iv);   
            
            // Test si la plque est déja présent et si non ajout de la nouvelle dans la bdd
            $plaqueBdd = $bdd->prepare('SELECT * FROM Information WHERE plaque = :plaque');
            $plaqueBdd->execute(array('plaque' => $plaqueCrypte));
            $donnees = $plaqueBdd->fetch();
            $plaqueBdd->closeCursor();
            if(empty($donnees)){  
                $req = $bdd->prepare('INSERT INTO Information(plaque, prenom, nom) VALUES(:plaque, :prenom, :nom)');
                $req->execute(array('plaque'=>$plaqueCrypte, 'prenom'=>$prenomCrypte, 'nom'=>$nomCrypte));
                $req->closeCursor;
            }    
        }
    } 
        
    // Récupération des données de la base de donnée
	$req = $bdd->query('SELECT * FROM Information');
    while($msgCrypte = $req->fetch())
    { 
        // On décrypte les données que l'on à extrait de la bdd      
        $msgDecrypte[plaque] = openssl_decrypt($msgCrypte[plaque], $cypher, $encryption_key, 0, $iv);
        $msgDecrypte[prenom] = openssl_decrypt($msgCrypte[prenom], $cypher, $encryption_key, 0, $iv);
        $msgDecrypte[nom] = openssl_decrypt($msgCrypte[nom], $cypher, $encryption_key, 0, $iv);
        
        // Connecte le bouton à l'id correspondant
        $msgDecrypte[supp] = "index.php?id=".$msgCrypte[ID]."&cmde=del";  
        $msgDecrypte[mod] = "index.php?id=".$msgCrypte[ID]."&cmde=upd";
		
        array_push($data, $msgDecrypte); // Ajout de la donnée décrypté dans un tableau
	}
	$req->closeCursor();
	
    // Affichage du contenu du tableau avec les données décrypté
    foreach ($data as $info){
		$tpl->setVar('info', $info);
		$tpl->render('line');
	}

    $tpl->setVar('nbr_entre', count($data));// On compte le nombre d'entrée dans la bdd

    //echo $tpl->render();

?>
