<?php

session_start();

if (isset($_POST['nom_admin']) and isset($_POST['cin_admin']) and isset($_POST['pwd_admin'])) {
    include_once './connect_bdd.php';
    include_once './classes/Personne.class.php';
    include_once './classes/Admin.class.php';
    $nom = htmlspecialchars($_POST['nom_admin']);
    $cin = htmlspecialchars($_POST['cin_admin']);
    $pwd = htmlspecialchars($_POST['pwd_admin']);
    if (($admin = Admin::existePourConnecter($nom, $cin, $pwd))) {
        $cin = $admin->getCin();
        $pwd = $admin->getPwd();
        $nom = $admin->getNom();
        $pnom = $admin->getPrenom();
        $fct = $admin->getFonction();
        $id_f = $admin->getId_fil();
        $_SESSION['ps'] = $cin;
        $_SESSION['mp'] = $pwd;
        $_SESSION['nom'] = $nom;
        $_SESSION['pnom'] = $pnom;
        $_SESSION['fct'] = $fct;
        $_SESSION['id_f'] = $id_f;

        if (isset($_POST['cnx_auto'])) {
            setcookie('ps', $cin, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('mp', $pwd, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('nom', $nom, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('pnom', $pnom, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('fct', $fct, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('id_f', $id_f, time() + 30 * 24 * 3600, null, null, false, true);
            /* créer des cookies avec 30 jours comme duré de vie.
             * le dernier parametre permet d'activer le mode httpOnly
             *  sur le cookie, et donc de le rendre en quelque sorte plus securise.
             */
        }
        header("Location: ../accueil_admin.php");
    } else {
        header("Location: ../accueil_admin.php?cnt_ad=false");
    }
} else {
    header("Location: ../accueil_admin.php?cnt_ad=false");
}
?>
