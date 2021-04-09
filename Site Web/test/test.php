<!DOCTYPE html>
<html>
<head>
	<title>Test cryptage</title>
</head>
<body>
	<p>test if it's work well</p>
	<!-- Code php permetant de crypter les données quand on les entre dans la table-->
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
	
    $cypher = 'AES-256-CBC';
    $encryption_key = 's5u8x/A?D(G+KbPeShVmYq3t6w9z$B&E';
    $iv = 'RfUjWnZr4u7x!A%D'; 

    $req = $bdd->query('SELECT * FROM Information');
	while($MSG_PLAQUES = $req->fetch()){
        $MSG_CRYPTE_PLAQUE = openssl_decrypt($MSG_PLAQUES[1], $cypher, $encryption_key, 0, $iv);
		echo $MSG_CRYPTE_PLAQUE;
        echo '<br>';
	}
    /*
    $req = $bdd->prepare('INSERT INTO Information(plaque, prenom, nom) VALUES(:plaque , :prenom, :nom)');
    $req->execute(array('plaque'=>$encrytplaque,'prenom'=>$encryptprenom,'nom'=>$encrytnom ));
    $req->closeCursor();
    echo 'c insérer et c cool';
    */
	?>
</body>
</html>
