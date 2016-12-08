<?php
session_start();
include_once './modele/connect_bdd.php';
?>
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
                <title>gerer professeurs</title>
                <link rel="stylesheet" href="files/styles/gerer_prof_style.css"/>
            </head>
             <body>
                <nav>
                    <a href="gerer_prof/affich_prof.php" target="fen"><label>Afficher les professeurs</label></a>
                    <a href="gerer_prof/ajout_prof.php" target="fen"><label>Ajouter un professeur</label></a>
                    <article>
                        <h1 >Rechercher</h1>
                        <p>
                            <label>Nom</label><input type="search" id="nom_prof"/>
                        </p>
                    </article>
                </nav>
                <iframe name="fen" src="" id="fen"></iframe>
                <script>
                    var search = document.getElementById('nom_prof'),
                            frame = document.getElementById('fen');
                    search.onkeyup = function() {
                        var nom = this.value;
                        if(nom.length>0)
                        frame.src = 'gerer_prof/affich_prof.php?nom=' + nom;
                    };
                </script>
            </body>
            <?php
        } else {
            include_once './modele/deconnect_admin.php';
        }
    } else {
        include_once './modele/deconnect_admin.php';
    }
    ?>
</html>