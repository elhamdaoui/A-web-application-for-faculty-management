<?php

session_start();
include_once './connect_bdd.php';
if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
    $cin = $_SESSION['ps'];
    $nom = $_SESSION['nom'];
    $pnom = $_SESSION['pnom'];
    $fct = $_SESSION['fct'];
    include_once ('./verify_connexion.php');
    if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'fonction_ad' => $fct))) {

        if (isset($_GET['id'])) {
            global $bdd;
            $result = $bdd->prepare('select PHOTO_AD from administrateur where CIN_AD=:id');
            $result->execute(array('id' => $_GET['id']));
            $donnes = $result->fetch();
            if (!empty($donnes)) {
                echo $donnes[0];
            } else {  
                echo "../files/imgs/admin.jpg";
            }
        } else {
            echo "../files/imgs/admin.jpg";
        }
    } else {
        header("Location: ./deconnect_admin.php ");
    }
} else {
    header("Location: ./deconnect_admin.php ");
}
?>
