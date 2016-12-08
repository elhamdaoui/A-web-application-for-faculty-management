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
                <title></title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                <link rel="stylesheet" href="files/styles/gerer_act_style.css"/>

            </head>
            <body>
                <nav>
                    <a class="menu" id="a_aff" href="gerer_act/affich_act.php" target="fenetre" onmouseover="afficheMenu('u_aff', 'inline-block');" onmouseout="afficheMenu('u_aff', 'none');">Afficher les actualites</a>
                    <ul id="u_aff" onmouseover="afficheMenu('u_aff', 'inline-block');" onmouseout="afficheMenu('u_aff', 'none');">
                        <li><a href="gerer_act/affich_act.php?act=my" target="fenetre">mes actualites</a></li>
                        <li><a href="gerer_act/affich_act.php?act=res" target="fenetre">actualites responsables</a></li>
                        <li><a href="gerer_act/affich_act.php?act=prof" target="fenetre">actualites professeurs</a></li>
                    </ul>
                    <a class="menu" id="a_ajt" href="gerer_act/ajout_act.php" target="fenetre" onmouseover="afficheMenu('u_ajt', 'inline-block');" onmouseout="afficheMenu('u_ajt', 'none');">Ajouter une actualite</a>
                    <ul id="u_ajt" onmouseover="afficheMenu('u_ajt', 'inline-block');" onmouseout="afficheMenu('u_ajt', 'none');">
                        <li><a href="gerer_act/ajout_act.php?act=fac" target="fenetre">actualite pour faculte</a></li>
                        <li><a href="gerer_act/ajout_act.php?act=fil" target="fenetre">actualite pour filiere</a></li>
                        <li><a href="gerer_act/ajout_act.php?act=mod" target="fenetre">actualite pour module</a></li>
                    </ul>
                </nav>
                <iframe name="fenetre" id="fenetre" src="gerer_act/default_gerer_act.html"></iframe>
                <script>
                        function afficheMenu(id, dis) {
                            document.getElementById(id).style.display = dis;
                        }
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