<?php

session_start();

include_once './connect_bdd.php';

    //include_once ('./verify_connexion.php');
    /* if (verify_connexion('professeur', array('cin_p' => $cin, 'nom_p' => $nom, 'prenom_p' => $pnom))) {
     */
    if (isset($_GET['tab']) and isset($_GET['attr']) and isset($_GET['colid']) and isset($_GET['id'])) {
        $tab = htmlspecialchars($_GET['tab']);
        $attr = htmlspecialchars($_GET['attr']);
        $colid = htmlspecialchars($_GET['colid']);
        $id = htmlspecialchars($_GET['id']);
        global $bdd;
        $result = $bdd->prepare("select $attr from $tab where $colid=:id");
        $result->execute(array('id' => $id));
        $donnes = $result->fetch();
        if (!empty($donnes)) {
            echo $donnes[0];
        } else {
            echo "";
        }
    } else {
        echo "no infos";
    }
    /* } else {
      header("");
      } */

?>
