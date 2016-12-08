<?php

session_start();

if (isset($_POST['nom_prof']) and isset($_POST['cin_prof']) and isset($_POST['pwd_prof'])) {
    include_once './connect_bdd.php';
    include_once './classes/Personne.class.php';
    include_once './classes/Professeur.class.php';
    $nom = htmlspecialchars($_POST['nom_prof']);
    $cin = htmlspecialchars($_POST['cin_prof']);
    $pwd = htmlspecialchars($_POST['pwd_prof']);
    if (($pr = Professeur::existePourConnecter($nom, $cin, $pwd))) {
        $cin = $pr->getCin();
        $pwd = $pr->getPwd();
        $nom = $pr->getNom();
        $pnom = $pr->getPrenom();
        $id_f = $pr->get_id_f();
        $_SESSION['psprof'] = $cin;
        $_SESSION['pwdprof'] = $pwd;
        $_SESSION['nomprof'] = $nom;
        $_SESSION['pnomprof'] = $pnom;
        $_SESSION['idfprof'] = $id_f;

        if (isset($_POST['cnx_auto'])) {
            setcookie('psprof', $cin, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('pwdprof', $pwd, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('nomprof', $nom, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('pnomprof', $pnom, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('idfprof', $id_f, time() + 30 * 24 * 3600, null, null, false, true);
            /* créer des cookies avec 30 jours comme duré de vie.
             * le dernier parametre permet d'activer le mode httpOnly
             *  sur le cookie, et donc de le rendre en quelque sorte plus securise.
             */
        }
        header("Location: ../esp_prof/esp_prof.php");
    } else {
        header("Location: ../accueil.php?esp=professeur&cnt_prof=false");
    }
} else {
    header("Location: ../accueil.php?esp=professeur&cnt_prof=false");
}
?>
