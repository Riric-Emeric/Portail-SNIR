<!--====================================================================================================
	fichier: index.php
	bdd: base_distante
	auteur: Seguin Emeric
	date: 05/03/2021
	dernière mise à jour: 11/03/2021
	rôle: importation des librairies et lancement des page php en foction de la présence d'une session
	projet: PORTAIL
	résultat: Affichage du site web
=====================================================================================================-->

<?php 
    //Récupération ddu dossier avec les dossiers avec les messages, le php et la librairie Hyla.tpl
	require './msg/msgFr.php';
	require './lib/hyla_tpl.class.php';
    require './php/login.php';
       
    session_start();
    if(empty($_SESSION['login'])) 
    {
        // Si inexistante ou nulle, on redirige vers le formulaire de login
        require './php/login.php';
    }else{
        require './php/view.php';
    }
    echo $tpl->render();    

?>
