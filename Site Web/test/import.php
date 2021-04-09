<!DOCTYPE html>
<html>
<head>
	<title>Test cryptage</title>
</head>
<body>

<?php
	try
	{
		$bdd = new PDO('mysql:host=localhost;dbname=base_distante;charset=utf8','Snir','Snir2020*');
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(Exception $e)
	{
		die('Erreur : ' .$e->getMessage());
	}
    //l'accée du fichier sur le serveur 
	$fichier = fopen("donnee.csv", "r");	


    while (!feof($fichier)) {
		//on récupére toute la ligne
		$uneLigne = fgets($fichier);
		//on met dans un tableau les différentes valeur trouver (séparer par un ;)
		$tableauValeurs = explode(';', $uneLigne);
		
        //Encryptage des données 
		//Clé d'encryptage:
		$encryption_key = 's5u8x/A?D(G+KbPeShVmYq3t6w9z$B&E';
	    $iv = 'RfUjWnZr4u7x!A%D';
        $cypher = 'AES-256-CBC';

	    //Fonction d'encryption: 
	    $encrytnom = openssl_encrypt($tableauValeurs[2], $cypher, $encryption_key, 0, $iv);
	    $encryptprenom = openssl_encrypt($tableauValeurs[1], $cypher, $encryption_key, 0, $iv);
	    $encrytplaque = openssl_encrypt($tableauValeurs[0], $cypher, $encryption_key, 0, $iv);

	    echo $encrytnom;
        echo '<br>';
        echo $encryptprenom;
        echo '<br>';
	    echo $encrytplaque;
        echo '<br>';   
        
	    //on crée la requete SQL pour inserer les données 
		$req=$bdd->prepare("INSERT INTO Information(plaque, prenom, nom) VALUES(:plaque, :prenom, :nom)");
		$req->execute(array('plaque'=> $encrytplaque, 'prenom'=>$encryptprenom, 'nom'=>$encrytnom));
		//echo $sql; //ligne de debug 
		
	}
?>

</body>
</html>
