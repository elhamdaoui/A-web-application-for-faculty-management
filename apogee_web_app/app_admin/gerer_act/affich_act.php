<?php
session_start();
include_once '../modele/connect_bdd.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>actualites</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <link rel="stylesheet" href="../files/styles/affich_act_style.css"/>
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
            include_once '../modele/classes/Actualite.class.php';
            if (isset($_POST['id_acc']) and isset($_POST['titre_acc']) and isset($_POST['contenu_acc'])) {
                $titre_acc = htmlspecialchars($_POST['titre_acc']);
                $id_acc = htmlspecialchars($_POST['id_acc']);
                $contenu_acc = htmlspecialchars($_POST['contenu_acc']);
                $image_acc = null;
                if (isset($_FILES['image_acc']) and $_FILES['image_acc']['error'] == 0) {
                    $image_acc = $_FILES['image_acc'];
                }
                $act = new Actualite();
                $act->setId($id_acc);
                $act->setTitre($titre_acc);
                $act->setContenu($contenu_acc);
                $act->setImage($image_acc);
                if ($act->modifierTCI()) {
                    echo '<script>alert("Actualite a ete modifiee"); </script>';
                }
                /* lorsque on a modifier  une actualite on affiche toutes les actualites */
                $acts = Actualite::tousActualites();
            } else if (isset($_GET['modif'])) {
                $id_acc = htmlspecialchars($_GET['modif']);
                $act = new Actualite();
                $act->recuperer($id_acc);
                ?>
                <form action="affich_act.php" method="post" enctype="multipart/form-data" class="modif_act">
                    <div><p class="description">Modifier une actualite</p></div>
                    <div><label >Titre</label><input type="text" name="titre_acc" id="titre_acc" value="<?php echo $act->getTitre(); ?>" autofocus="" required=""/></div>
                    <div> <label for="contenu_acc">Contenu</label><textarea name="contenu_acc" id="contenu_acc" placeholder="contenu d'actualite" NORESIZE="" required="" ><?php echo $act->getContenu(); ?></textarea></div>
                    <div><label for="image_acc">image "optionnel"</label><input type="file" name="image_acc" id="image_acc"/></div>
                    <div><input type="submit" name="modifier" id="modifier_a" value="Modifier" class="btns"/><input type="button" name="annuler" id="annuler" value="annuler"  class="btns" onclick="gererBtnAnul();"/></div>            
                    <input type="hidden" value="<?php echo $act->getId(); ?>" name="id_acc"/>
                </form>
                <script>
                        var modif = document.getElementById('modifier_a');
                        modif.onclick = function() {
                            if (!confirm('vous etes sur de modifier cet actualite !')) {
                                return false;
                            }
                        };
                        function gererBtnAnul() {
                            if (confirm('vous etes sur d\'annuler la modification !')) {
                                location = "affich_act.php";
                            }
                        }
                </script>
                <?php
                $acts = '-pour eviter l\'affichage du message "aucune actalite trouve" (-_^) -';
            } else if (isset($_GET['sup'])) {
                $id_acc = htmlspecialchars($_GET['sup']);
                $act = new Actualite();
                $act->setId($id_acc);
                if ($act->supprimer()) {
                    echo '<script>alert("actualite supprimer avec succes");</script>';
                } else {
                    echo '<script>alert("actualite non suprimer");</script>';
                }
                /* lorsque on a supprimer ou non une actualite on affiche toutes les actualites */
                $acts = Actualite::tousActualites();
            } else if (isset($_GET['act'])) {
                $ac = htmlspecialchars($_GET['act']);
                if (strcmp($ac, 'my') == 0) {
                    $acts = Actualite::actualitesAdmin($cin);
                } else if (strcmp($ac, 'res') == 0) {
                    $acts = Actualite::actualitesResponsables();
                } else if (strcmp($ac, 'prof') == 0) {
                    $acts = Actualite::actualitesProfesseurs();
                } else {
                    echo '<p>adresse introuvable</p>';
                }
            } else {
                $acts = Actualite::tousActualites();
            }
            ?>
            <body>
                <?php
                if (empty($acts)) {
                    echo '<p>aucune actualite trouvee</p>';
                } else {
                    if (is_array($acts)) {
                        foreach ($acts as $val) {
                            /* le chemin du moteur d'images données ici par ce que c'est l'emplacement actuelle.
                             * et si nous l'a ecrit dans la methode afficher de la classe Actualite.
                             * donc s'ammarche pas.
                             *
                             * $cheminMoteurImgAct = './moteur_img_act.php';
                             * $val->afficherAvecMoteurImg($cheminMoteurImgAct);
                             */
                            //$val->afficher();
                             $cheminMoteurImgAct = './moteur_img_act.php';
                             $val->afficherAvecMoteurImg($cheminMoteurImgAct);
                        }
                    }
                }
                ?>

                <script>
                    function btnModifier(id) {
                        location = 'affich_act.php?modif=' + id;
                    }
                    function btnSupprimer(id) {
                        if (confirm('vous voulez vraiment supprimer cette actualite')) {
                            location = 'affich_act.php?sup=' + id;
                        }
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

