<?php
session_start();
include_once '../modele/connect_bdd.php';

function ttFiliere() {
    global $bdd;
    $result = $bdd->query('select nom_f from filiere');
    while ($don = $result->fetch()) {
        $fil = $don['nom_f'];
        echo '<option value="' . $fil . '">' . $fil . '</option>';
    }
    $result->closeCursor();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>afficahge des professeurs</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="../files/styles/affich_prof_style.css"/>
    </head>
    <?php
    if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
        $cin = $_SESSION['ps'];
        $pwd = $_SESSION['mp'];
        $nom = $_SESSION['nom'];
        $pnom = $_SESSION['pnom'];
        $fct = $_SESSION['fct'];
        include_once ('../modele/verify_connexion.php');
        if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {
            include_once '../modele/classes/Personne.class.php';
            include_once '../modele/classes/Professeur.class.php';
            $prfs=array();
            if (isset($_GET['nom']) and strlen($_GET['nom']) > 0) {
                $nom_pr = htmlspecialchars($_GET['nom']);
                $prfs = Professeur::tousProfesseursLikeNom($nom_pr);
            } else {
                $prfs = Professeur::tousProfesseur();
            }
            if (empty($prfs)) {
                echo '<p>aucun professeur trouve</p>';
            } else {
                ?>
                <body>
                    <p class="prof_p_t"><strong>CIN</strong><strong>Nom</strong><strong>Prenom</strong><strong>Filiere</strong><strong>Coordinateur</strong><strong>Chef departement</strong></p>
                    <p class="prof_p" style="box-shadow: none;">&nbsp;</p>
                    <?php
                    foreach ($prfs as $pr) {
                        ?>
                        <p class="prof_p" onclick="clickSurProf('<?php echo $pr->getCin() ?>');">
                            <strong title="<?php echo $pr->getCin() ?>"><?php echo substr($pr->getCin(),0,12); ?></strong>
                            <strong title="<?php echo $pr->getNom() ?>"><?php echo substr($pr->getNom(),0,12); ?></strong>
                            <strong title="<?php echo $pr->getPrenom() ?>"><?php echo substr($pr->getPrenom(),0,12); ?></strong>
                            <strong title="<?php echo $pr->get_nom_f() ?>"><?php echo substr($pr->get_nom_f(),0,12); ?></strong>
                            <strong title="<?php echo $pr->corActuelFiliere() ?>">&nbsp;<?php echo substr($pr->corActuelFiliere(),0,12); ?></strong>
                            <strong title="<?php echo $pr->chefActuelDepartemet(); ?>">&nbsp;<?php echo substr($pr->chefActuelDepartemet(),0,12); ?></strong>
                        </p>
                        <?php
                    }
                    ?>
                    <script>
                        function clickSurProf(cin) {
                            /*rederiction vers page professeur avec le CIN=cin.*/
                            location = "page_professeur.php?id=" + cin;
                        }
                    </script>
                </body>
                <?php
            }
            ?>





            <?php
        } else {
            include
                    header("Location: ../modele/deconnect_admin.php");
        }
    } else {
        header("Location: ../modele/deconnect_admin.php");
    }
    ?>
</html>