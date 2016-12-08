<?php

session_start();
include_once './connect_bdd.php';
if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
    $cin = $_SESSION['ps'];
    $pwd = $_SESSION['mp'];
    $nom = $_SESSION['nom'];
    $pnom = $_SESSION['pnom'];
    $fct = $_SESSION['fct'];
    include_once ('./verify_connexion.php');
    if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {

        if(isset($_GET['id'])){
            $cin=$_GET['id'];
            include_once './classes/Personne.class.php';
            include_once './classes/Admin.class.php';
            $ad=new Admin();
            $ad->setCin($cin);
            if($ad->supprimer()){
                echo '<script>alert("responsable ou admin supprime");location="../gerer_res/affich_resp.php";</script>';
            }else{
             echo '<script>alert("!! Erreur !!! \n responsable ou admin n\'est pas supprime");location="../gerer_res/affich_resp.php";</script>';
            }
            
        }
    } else {
        header("Location: ./deconnect_admin.php ");
    }
} else {
    header("Location: ./deconnect_admin.php ");
}
?>
