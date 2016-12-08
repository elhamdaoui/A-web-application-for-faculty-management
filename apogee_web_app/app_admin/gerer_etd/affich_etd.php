<?php
session_start();
include_once '../modele/connect_bdd.php';

function afficheNumIns($num) {
    if (isset($num) and !empty($num) and $num != NULL) {
        echo $num;
    } else {
        echo '<em class="non_ins">Non inscrit</em>';
    }
}
?>
<!DOCTYPE html>
<html>

    <?php
    if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
        $cin = $_SESSION['ps'];
        $pwd = $_SESSION['mp'];
        $nom = $_SESSION['nom'];
        $pnom = $_SESSION['pnom'];
        $fct = $_SESSION['fct'];
        include_once ('../modele/verify_connexion.php');
        if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {
            ?>
            <head>
                <title>affichage des etudiants</title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <link rel="stylesheet" href="../files/styles/affich_etd_style.css"/>
            </head>
            <body>
                <?php
                include_once '../modele/classes/Personne.class.php';
                include_once '../modele/classes/Etudiant.class.php';
                if (!isset($_GET['fil'])) {
                    if (isset($_GET['nom_f'])) {
                        $nom_f = htmlspecialchars($_GET['nom_f']);
                        if (isset($_GET['ins'])) {
                            $inscription = htmlspecialchars($_GET['ins']);
                            if (strcmp($inscription, 'true') == 0) {
                                $inscription = TRUE;
                            } else {
                                $inscription = FALSE;
                            }
                        }
                        $etudiants = Etudiant::etudiantsInscrire($nom_f, $inscription);
                    } else {
                        $etudiants = Etudiant::tousEtudiants();
                    }
                } else {
                    $tabAssoc = array();
                    $nom_f = htmlspecialchars($_GET['fil']);
                    if (strcmp($nom_f, 'tous') != 0) {
                        $tabAssoc['nom_f'] = htmlspecialchars($nom_f);
                    }
                    if (isset($_GET['nom'])) {
                        $tabAssoc['nom_e'] = htmlspecialchars($_GET['nom']);
                    }
                    if (isset($_GET['cne'])) {
                        $tabAssoc['cne_e'] = htmlspecialchars($_GET['cne']);
                    }
                    $etudiants = Etudiant::trouverEtudiants($tabAssoc);
                }
                if (!empty($etudiants)) {
                    echo "<p class='titre'><strong>Nom</strong><strong>Prenom</strong><strong>CNE</strong><strong>Filiere</strong><strong>N° inscr</strong></p>";
                    foreach ($etudiants as $etd) {
                        ?>
                        <p class="etudiant" onclick="versPageEtudiant('<?php echo $etd->getCne(); ?>');">
                            <strong class="val"><?php echo $etd->getNom(); ?></strong>
                            <strong class="val"><?php echo $etd->getPrenom(); ?></strong>
                            <strong class="val"><?php echo $etd->getCne(); ?></strong>
                            <strong class="val"><?php echo $etd->getNomFil(); ?></strong>
                            <strong class="val"><?php afficheNumIns($etd->getNumins()); ?></strong>
                        </p>
                        <hr size="3%"/>
                        <?php
                    }
                } else {
                    echo '<p>aucun etudiant trouve</p>';
                }
                ?>
                <script>
                    function versPageEtudiant(id) {
                        document.location = "./page_etudiant.php?id=" + id;
                    }
                </script>
            </body>

            <?php
        } else {
            header("Location: ../modele/deconnect_admin.php");
        }
    } else {
        header("Location: ../modele/deconnect_admin.php");
    }
    ?>
</html>