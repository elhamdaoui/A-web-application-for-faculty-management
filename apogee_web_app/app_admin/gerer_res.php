<?php
session_start();
include_once './modele/connect_bdd.php';
?>
<!DOCTYPE html>
<html>
    <?php
    if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
        $cin = $_SESSION['ps'];
        $nom = $_SESSION['nom'];
        $pnom = $_SESSION['pnom'];
        $fct = $_SESSION['fct'];
        include_once ('./modele/verify_connexion.php');
        if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'fonction_ad' => $fct))) {
            ?>


            <head>
                <meta charset="utf-8"/>
                <link rel="stylesheet" href="files/styles/gerer_res_etd_style.css"/>
                <title>gerer les responsables</title>
            </head>
            <body>
                <nav>
                    <a href="gerer_res/affich_resp.php" target="fen"><label>Afficher les responsables</label></a>
                    <a href="gerer_res/ajout_resp.php" target="fen"><label>Ajouter un responsable</label></a>
                    <article>
                        <h1 >Rechercher</h1>
                        <p>
                            <label>Nom</label><input type="search" id="nom_res"/>
                        </p>
                    </article>
                </nav>
                <iframe name="fen" src="" id="fen"></iframe>
                <script>
                    var search = document.getElementById('nom_res'),
                            frame = document.getElementById('fen');
                    search.onkeyup = function() {
                        var nom = this.value;
                        if(nom.length>0)
                        frame.src = 'gerer_res/affich_resp.php?nom=' + nom;
                    };
                </script>
            </body>


            <?php
        } else {
            include_once './modele/deconnect_admin.php';
            header("Location: ./accueil_admin.php");
        }
    } else {
        include_once './modele/deconnect_admin.php';
        header("Location: ./accueil_admin.php");
    }
    ?>
</html>

