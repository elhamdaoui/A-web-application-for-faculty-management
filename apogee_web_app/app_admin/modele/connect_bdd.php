<?php
try {
    $bdd = new PDO("mysql:host=localhost;dbname=apogee", "root", "", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	//$bdd= new PDO('pgsql:host=localhost;dbname=example;','nom_utilisateur','mot_de_passe', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>