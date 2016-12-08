<?php
session_start();
include_once './connect_bdd.php';
if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
    $cin = $_SESSION['ps'];
    $pwd = $_SESSION['mp'];
    $nom = $_SESSION['nom'];
    $pnom = $_SESSION['pnom'];
    $fct = $_SESSION['fct'];
    include_once ('./verify_connexion.php');
    if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {
        if (isset($_POST['pwd'])) {
            include_once './classes/Personne.class.php';
            include_once './classes/Admin.class.php';
            $pass = $_POST['pwd'];
            $ad = new Admin();
            $ad->recuperer_Admin($cin);
            if (strcmp(sha1($pass), $ad->getPwd()) == 0 and strcmp(sha1($pass), $pwd) == 0) {
                $tabAssoc = array();
                if (isset($_POST['adresse']) and !empty($_POST['adresse'])) {
                    $tabAssoc['adresse_ad'] = $_POST['adresse'];
                }
                if (isset($_POST['email']) and !empty($_POST['email'])) {
                    $tabAssoc['email_ad'] = $_POST['email'];
                }
                if (isset($_POST['ntel']) and !empty($_POST['ntel'])) {
                    $tabAssoc['ntel_ad'] = $_POST['ntel'];
                }
                if (isset($_POST['nv_pwd']) and !empty($_POST['nv_pwd'])) {
                    if(strcmp($_POST['nv_pwd'],$_POST['nv_c_pwd'])==0){
                    $tabAssoc['pwd_ad'] = sha1($_POST['nv_pwd']);
                    $_SESSION['mp'] = sha1($_POST['nv_pwd']);
                    }else{
                        header("Location: ./parametres_profile.php?fx=true");
                    }
                    // $_COOKIE['mp'] = sha1($_POST['nv_pwd']);
                    //on sait pas si l'admin connecté avec activation du cookie (vnt automatique)
                }
                $ad->modifier($tabAssoc);

                echo '<script>
                    alert("votre informations sont mis a jours");
                    document.location="../accueil_admin.php";
                    </script>';
            } else {
                header("Location: ./parametres_profile.php?fx=true");
            }
        } else {
            ?> 
            <html>
                <head>
                    <meta charset="utf-8" />
                    <title>Parametres compte</title>
                    <link rel="shortcut icon" type="image/x-icon" href="../files/icns/fps.png" />
                    <link rel="stylesheet" href="../files/styles/parametres_profile_style.css"/>
                </head>
                <body>
                    <form method="post" action="parametres_profile.php">
                        <div><label for="adresse">Adresse </label><input type="texte" name="adresse"/></div>
                        <div><label for="email">Email </label><input type="email" name="email"/></div>
                        <div><label for="ntel">N° telephone </label><input type="tel" name="ntel"/></div>
                        <div><label for="nv_pwd">Nouveau mot de passe </label><input type="password" name="nv_pwd" id="nv_pwd"/></div>
                        <div><label for="nv_c_pwd">Confirmer mot de passe </label><input type="password" name="nv_c_pwd" id="nv_c_pwd"/></div>
                        <div><label for="pwd">Aucien mot de passe </label><input type="password" name="pwd"/></div>
                        <div><input type="submit" value="Valider" id="valider"/><input type="button" value="Annuler" id="annuler"/> </div>
                        <?php
                        if (isset($_GET['fx']) and strcmp($_GET['fx'], 'true') == 0) {
                            ?>
                            <div><p style="color: red;"> des informations sont incorrectes</p></div>
                            <?php
                        }
                        ?>
                    </form>
                    <script>
                        var mdp = document.getElementById('nv_pwd'),
                                conf = document.getElementById('nv_c_pwd'),
                                valider = document.getElementById('valider'),
                                annuler=document.getElementById('annuler');
                        valider.onclick = function() {
                            if (!(mdp.value === conf.value)) {
                                alert('mots de passe non identiques');
                                return false;
                            }
                        };
                        annuler.onclick=function(){
                            document.location='../accueil_admin.php';
                        };

                    </script>
                </body>
            </html>
            <?php
        }
    } else {
        header("Location: ./deconnect_admin.php ");
    }
} else {
    header("Location: ./deconnect_admin.php ");
}
?>