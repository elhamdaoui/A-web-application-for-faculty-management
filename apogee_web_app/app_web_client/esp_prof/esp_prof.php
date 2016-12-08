<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @param type $sexe
 * @return string
 */
function returnMdOrMr($sexe) {
    if (strcmp(strtolower($sexe), 'h') == 0) {
        return 'MR. ';
    } else {
        return 'Md. ';
    }
}

/**
 * 
 */
if (isset($_SESSION['psprof']) and isset($_SESSION['pwdprof']) and isset($_SESSION['nomprof']) and isset($_SESSION['pnomprof']) and isset($_SESSION['idfprof'])) {
    $psp = htmlspecialchars($_SESSION['psprof']);
    $nomp = htmlspecialchars($_SESSION['nomprof']);
    $pwdp = htmlspecialchars($_SESSION['pwdprof']);
    $pnomp = htmlspecialchars($_SESSION['pnomprof']);
    $idfp = htmlspecialchars($_SESSION['idfprof']);
    include_once '../modele/verify_connexion.php';
    if (verify_connexion('professeur', array('cin_p' => $psp, 'nom_p' => $nomp, 'prenom_p' => $pnomp, 'pwd_p' => $pwdp, 'id_f' => $idfp))) {
        include_once '../modele/classes/Personne.class.php';
        include_once '../modele/classes/Professeur.class.php';
        $pr = new Professeur();
        $pr->recuperer_Professeur($psp);
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>FPS: espace professeur</title>
                <link rel="shortcut icon" type="image/x-icon" href="../files/icns/fps.png" />
                <link rel="stylesheet" href="../files/styles/esp_prof_style.css"/>
            </head>

            <body>
                <iframe src="actualites_prof.php" id="fen" name="fen"></iframe>
                <nav class="nav_prof">
                    <span><a target="fen" href="page_prof.php" id="pr_pf" title="<?php echo returnMdOrMr($pr->getSexe()) . $pr->getNom() . ' ' . $pr->getPrenom(); ?>">
                            <img src="../modele/moteur_img.php?tab=professeur&attr=photo_p&colid=cin_p&id=<?php echo $psp ?>"/>
                        </a>
                    </span>
                    <span><a target="" href="../accueil.php" id="acc_pf">&nbsp;</a></span>
                    <span><a target="fen" href="modules.php" id="mod_pf">&nbsp;</a></span>
                    <?php
                    if ($pr->corActuelFiliere()) {
                        ?>
                        <span><a target="fen" href="coordoner.php" id="sem_pf">&nbsp;</a></span>
                        <?php
                    }
                    ?>
                    <span><a target="fen" href="actualites_prof.php" id="acts_pf">&nbsp;</a></span>
                    <span><a target="" href="../modele/deconnect.php" id="decnx_pf">&nbsp;</a></span>
                </nav>
                <div class="des_nav_prof">
                    <span ><strong id="des_pr_pf">Profile</strong></span>
                    <span ><strong id="des_acc_pf">Acceuil</strong></span>
                    <span ><strong id="des_mod_pf">Modules</strong></span>
                    <?php
                    if ($pr->corActuelFiliere()) {
                        ?>
                        <span ><strong id="des_sem_pf">Semestres</strong></span>
                        <?php
                    }
                    ?>
                    <span ><strong id="des_acts_pf">mes actualites</strong></span>
                    <span ><strong id="des_decnx_pf">Deconnexion</strong></span>
                </div>
                <footer>copie right FPS 2015</footer>
                <script>
                    function gereOccOptio(id) {
                        if (document.getElementById(id)) {
                            document.getElementById(id).onmouseover = function() {
                                setTimeout(function() {
                                    document.getElementById('des_' + id).style.display = 'inline-block';
                                }, 200);
                            };
                            document.getElementById(id).onmouseout = function() {
                                setTimeout(function() {
                                    document.getElementById('des_' + id).style.display = 'none';
                                }, 200);
                            };
                        }
                    }
                    function afficheDesNav() {
                        gereOccOptio('pr_pf');
                        gereOccOptio('acc_pf');
                        gereOccOptio('mod_pf');
                        gereOccOptio('decnx_pf');
                        gereOccOptio('acts_pf');
                        gereOccOptio('sem_pf');/*poser la dernier car peut n'existe pas,mais je traité ça
                         ,le jeu est fait sans erreurs (-_°)*/
                    }
                    /**/
                    (function() {
                        afficheDesNav();
                    })();
                </script>
        </html>
        <?php
    } else {
        include_once '../modele/deconnect.php';
    }
} else {
    include_once '../modele/deconnect.php';
}
?>