<?php
session_start();
include_once './modele/connect_bdd.php';
include_once './modele/classes/Personne.class.php';
include_once './modele/classes/Admin.class.php';
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
        include_once ('./modele/verify_connexion.php');
            if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {
            $fct_ad='';
            if(strcmp($fct,'responsable')==0){
                $ad=new Admin();
                $ad->recuperer_Admin($cin);
                $fct_ad=$ad->getNomFil();
            }else if(strcmp($fct,'admin')==0){
               $fct_ad='Admin'; 
            }
            ?>
            <head>
                <meta charset="utf-8" />
                <title>apogee espace admin</title>
                <link rel="shortcut icon" type="image/x-icon" href="files/icns/fps.png" />
                <link rel="stylesheet" href="files/styles/esp_admin_style.css"/>

            </head>

            <body>
                <header>
                    <p class="libele_fct"><?php echo $fct_ad;?></p>
                    <p id="accueil_p">
                        <a href="accueil_admin.php">Accueil</a>
                    </p>
                    <p id="profile">
                        <img class='imgProfile' src='./modele/moteur_img.php?id=<?php echo $cin; ?>'/>
                        <span id="nom_profile"> <?php echo strtoupper($nom) . ' ' . strtolower($pnom); ?></span>
                        <span id="optionsProfile">
                            <a href="./modele/deconnect_admin.php" id="deconect"><label style="cursor: pointer;">Deconnexion</label></a>
                            <a href="./modele/parametres_profile.php" id="param"><label style="cursor: pointer;">Parametres</label></a>
                        </span>
                        <script>
                            var op = document.getElementById('optionsProfile'), np = document.getElementById('nom_profile');
                            np.onmouseover = function() {
                                op.style.display = 'inline-block';
                            };
                            np.onmouseout = function() {
                                op.style.display = 'none';
                            };
                            op.onmouseover = function() {
                                this.style.display = 'inline-block';
                            };
                            op.onmouseout = function() {
                                this.style.display = 'none';
                            };
                        </script>
                    </p> </header>
                <iframe src="" id="corps"></iframe>
                <footer><p>Copie Right FPS 2014</p></footer>
                <script>
                    function gererFrame() {
                        var frame = document.getElementById('corps');
                        var loc = document.location;
                        var lie = new String(loc), sp = lie.split('gr=');
                        var poss = ['etd', 'prof', 'res', 'act','sys'], choix = sp[1];
                        if (poss.indexOf(choix) !== -1) {
                            frame.src = 'gerer_' + choix + '.php';
                        } else {
                            document.location = 'accueil_admin.php';
                        }
                    }
                    (function() {
                        gererFrame();
                    })();
                </script>
            </body>
            <?php
        } else {
            include_once './modele/deconnect_admin.php';
            header("Location: accueil_admin.php");
        }
    } else {
        include_once './modele/deconnect_admin.php';
        header("Location: accueil_admin.php");
    }
    ?>
</html>
