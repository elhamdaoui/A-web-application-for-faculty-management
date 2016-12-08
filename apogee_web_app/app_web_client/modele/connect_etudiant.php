<?php

session_start();

if (isset($_POST['nom_e']) and isset($_POST['cne_e']) and isset($_POST['cin_e']) and isset($_POST['pwd_e'])) {
    include_once './connect_bdd.php';
    include_once './classes/Personne.class.php';
    include_once './classes/Etudiant.class.php';
    $nom = htmlspecialchars($_POST['nom_e']);
    $cne=htmlspecialchars($_POST['cne_e']);
    $cin = htmlspecialchars($_POST['cin_e']);
    $pwd = htmlspecialchars($_POST['pwd_e']);
    if (Etudiant::existePourConnecter($nom, $cin,$cne, $pwd)) {
        $etd=new Etudiant();
        $etd->recuperer_etudiant($cne);
        $cin = $etd->getCne();
        $pwd = $etd->getPwd();
        $nom = $etd->getNom();
        $pnom = $etd->getPrenom();
        $_SESSION['psetd'] = $cin;
        $_SESSION['pwdetd'] = $pwd;
        $_SESSION['nometd'] = $nom;
        $_SESSION['pnometd'] = $pnom;

        if (isset($_POST['cnx_auto'])) {
            setcookie('psetd', $cin, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('pwdetd', $pwd, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('nometd', $nom, time() + 30 * 24 * 3600, null, null, false, true);
            setcookie('pnometd', $pnom, time() + 30 * 24 * 3600, null, null, false, true);
            /* créer des cookies avec 30 jours comme duré de vie.
             * le dernier parametre permet d'activer le mode httpOnly
             *  sur le cookie, et donc de le rendre en quelque sorte plus securise.
             */
        }
        header("Location: ../esp_etd/esp_etd.php");
    } else {
        header("Location: ../accueil.php?esp=etudiant&cnt_etd=informations incorrectes");
    }
} else {
    header("Location: ../accueil.php?esp=etudiant&cnt_etd=informations incorrectes");
}
?>

