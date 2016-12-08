<?php
session_start();
include_once '../modele/connect_bdd.php';
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
            if (isset($_GET['ajouter']) and strcmp($_GET['ajouter'], 'true') == 0) {
                include_once '../modele/classes/Actualite.class.php';
                /* if (strcmp($fct, 'admin') == 0) { */

                if ($_POST['titre_acc'] and isset($_POST['contenu_acc'])) {
                    $titre = htmlspecialchars($_POST['titre_acc']);
                    $contenu = $_POST['contenu_acc'];
                    $image = null;
                    if (isset($_FILES['image_acc']) and $_FILES['image_acc']['error']==0) {
                        $image = $_FILES['image_acc'];
                    }
                    $ac = new Actualite();
                    $ac->remplir($titre, $contenu, NULL, $image, $cin, NULL, NULL, NULL, NULL);
                    $ac->stocker();
                }
                /* } */
            } else {
                ?>
                <head>
                    <title>ajouter actualite</title>
                    <meta http-equiv="Content-Type" charset="UTF-8"/>
                    <link rel="stylesheet" href="../files/styles/ajout_act_style.css"/>
                </head>
                <body>
                    <form action="ajout_act.php?ajouter=true" method="post" enctype="multipart/form-data" >
                        <div><p class="description">Ajouter une actualite</p></div>
                        <div><label >Titre</label><input type="text" name="titre_acc" id="titre_acc" autofocus="" required=""/></div>
                        <div> <label for="contenu_acc">Contenu</label><textarea name="contenu_acc" id="contenu_acc" placeholder="contenu d'actualite" NORESIZE="" required="" ></textarea></div>
                        <div><label for="image_acc">image</label><input type="file" name="image_acc" id="image_acc"/></div>
                        <div><input type="submit" name="poster" id="poster" value="Poster" class="btns"/><input type="reset" name="annuler" id="annuler" value="annuler"  class="btns" /></div>            
                    </form>
                    <script>
                        function gererBtns(){
                        var titre=document.getElementById('titre_acc'),
                                contenu=document.getElementById('contenu_acc'),
                                poster=document.getElementById('poster'),
                                annuler=document.getElementById('annuler');
                        poster.onclick=function(){
                            if(titre.value.length<=0 || contenu.value.length<=0){
                                alert('titre ou contenu est vide!!');
                                return false;
                            }
                        };
                        annuler.onclick=function(){
                            parent.location='../gerer_act.php';
                        };
                        }
                        
                        (function (){
                            gererBtns();
                        })();
                    </script>
                </body>
                <?php
            }
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

