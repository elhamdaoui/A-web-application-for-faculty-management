<?php

session_start();
include_once '../modele/connect_bdd.php';
if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
    $cin = $_SESSION['ps'];
    $nom = $_SESSION['nom'];
    $pnom = $_SESSION['pnom'];
    $fct = $_SESSION['fct'];
    include_once ('../modele/verify_connexion.php');
    if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'fonction_ad' => $fct))) {

        if (isset($_GET['id'])) {
            global $bdd;
            $result = $bdd->prepare('select image_acc from actualite where id_acc=:id');
            $result->execute(array('id' => $_GET['id']));
            $donnes = $result->fetch();
            if (!empty($donnes)) {
                echo $donnes[0];
            } else {
                echo "";
                /* image par defaut */
            }
        } else {
            echo "";
            /* image par defaut */
        }
    } else {
        header("Location: ../modele/deconnect_admin.php");
    }
} else {
    header("Location: ../modele/deconnect_admin.php");
}
?>
