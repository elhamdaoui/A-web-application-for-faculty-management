<?php
session_start();
include_once './modele/connect_bdd.php';
include_once './modele/classes/Personne.class.php';
include_once './modele/classes/Admin.class.php';
foreach ($_COOKIE as $cle => $val){
    echo "<script>$cle => $val</script>";
}
if (isset($_COOKIE['ps']) and isset($_COOKIE['mp']) and isset($_COOKIE['nom']) and isset($_COOKIE['pnom']) and isset($_COOKIE['fct'])) {
    $cin = htmlspecialchars($_COOKIE['ps']);
    $nom = htmlspecialchars($_COOKIE['nom']);
    $pnom = htmlspecialchars($_COOKIE['pnom']);
    $fct = htmlspecialchars($_COOKIE['fct']);
    $pwd = htmlspecialchars($_COOKIE['mp']);
    $_SESSION['ps'] = $cin;
    $_SESSION['nom'] = $nom;
    $_SESSION['pnom'] = $pnom;
    $_SESSION['fct'] = $fct;
    $_SESSION['pwd'] = $pwd;
    echo "<script>alert('$nom $pnom $cin $fct $pwd');</script>";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>apogee</title>
        <link rel="shortcut icon" type="image/x-icon" href="files/icns/fps.png" />
        <link rel="stylesheet" href="files/styles/accueil_admin_style.css"/>

    </head>

    <body>
        <?php
        if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
            $cin = $_SESSION['ps'];
            $nom = $_SESSION['nom'];
            $pnom = $_SESSION['pnom'];
            $fct = $_SESSION['fct'];
             $pwd = $_SESSION['mp'];
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
                <header>
                    <p class="libele_fct"><?php echo $fct_ad;?></p>
                    <p id="profile">
                        <img class='imgProfile' src='./modele/moteur_img.php?id=<?php echo $cin; ?>'/>  
                        <span id="nom_profile"> <?php echo strtoupper($nom) . ' ' . strtolower($pnom); ?></span>
                        <span id="optionsProfile">
                            <a href="modele/deconnect_admin.php" id="deconect"><label style="cursor: pointer;">Deconnexion</label></a>
                            <a href="modele/parametres_profile.php" id="param"><label style="cursor: pointer;">Parametres</label></a>
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
                <section>
                    <p>
                        <a class='a_ad' id='a_ad_a' href="esp_admin.php?gr=etd"><label>Gerer les etudiants</label></a>
                        <a class='a_ad' id='a_ad_b' href="esp_admin.php?gr=prof"><label>Gerer les professeurs</label></a>
                        <a class='a_ad' id='a_ad_c' href="esp_admin.php?gr=res"><label>Gerer les responsables<br/> (admins)</label></a>
                        <a class='a_ad' id='a_ad_d' href="esp_admin.php?gr=act"><label>Gerer les actualites</label></a>
                        <a class='a_ad' id='a_ad_e' href="esp_admin.php?gr=sys"><label>Gerer SYSTEME</label></a>
                    </p>
                </section>
                <?php
            } else {
                header('Location: ./modele/deconnect_admin.php');
                /* decnnexion d'admin actuel quand 
                 * la verification est fausse.
                 */
            }
        } else {
            ?>
            <header>
            </header> 
            <section>
                <form method="post" action="modele/connect_admin.php">
                    <div><p>connectez vous</p></div>
                    <div><label for="nom_admin">Nom</label><input type="text" name="nom_admin" id="nom_admin" required autofocus/></div>
                    <div><label for="cin_admin">CIN</label><input type="text" name="cin_admin" id="cin_admin" required /></div>
                    <div><label for="pwd_admin">Mot de passe</label><input type="password" name="pwd_admin" id="pwd_admin" required/></div>
                    <div><label for="cnx_auto" class="cnx_auto_lab">Connextion automatique</label><input type="checkbox" name="cnx_auto" id="cnx_auto" class="cnx_auto_in" /></div>
                    <div><input type="submit" value="Connexion" id="connect_admin" /></div>
                    <?php
                    if (isset($_GET['cnt_ad']) and strcmp($_GET['cnt_ad'], 'false') == 0) {
                        ?>
                        <p id="cnt_false">s'il vous plait entrer<br> des informations correctes</p>
                            <?php
                        }
                        ?>    
                </form>
            </section>
            <?php
        }
        ?>
        <footer><p>Copie Right FPS 2015</p></footer>
    </body>
    <script>
    </script>
</html>

