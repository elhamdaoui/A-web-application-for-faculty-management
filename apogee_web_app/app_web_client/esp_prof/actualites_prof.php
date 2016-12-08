<?php
session_start();
include_once '../modele/connect_bdd.php';

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
        include_once '../modele/classes/Actualite.class.php';
        $pr = new Professeur();
        $pr->recuperer_Professeur($psp);
        $acts = $pr->getIdsActualites();
        
        if (!empty($acts)) {
            ?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <title>FPS: coordonner filiere</title>
                    <link rel="stylesheet" href="../files/styles/actualites_prof_style.css"/>
                </head>

                <body>

                    <?php
                    if (isset($_POST['valid_mod_acc'])) {
                        if (isset($_POST['idacc'])) {
                            $id_acc_m = htmlspecialchars($_POST['idacc']);
                            $actualite = new Actualite();
                            $actualite->recuperer($id_acc_m);
                            if (strcmp(strtolower($actualite->getCinP()), strtolower($_SESSION['psprof'])) == 0) {
                                if (isset($_POST['titre_acc'])) {
                                    $actualite->setTitre(htmlspecialchars($_POST['titre_acc']));
                                }
                                if (isset($_POST['contenu_acc'])) {
                                    $actualite->setContenu(htmlspecialchars($_POST['contenu_acc']));
                                }
                                if (isset($_FILES['img_acc']) and $_FILES['img_acc']['error'] == 0) {
                                    $actualite->setimage($_FILES['img_acc']);
                                }
                                if ($actualite->modifierTCI()) {
                                    echo '<script>location="actualites_prof.php";</script>';
                                } else {
                                    echo '<script>alert("actualite non supprime");</script>';
                                }
                            } else {
                                echo '<script>alert("erreur: vous etes pas l\'acces de modifier cet actualite");location="actualites_prof.php";</script>';
                            }
                        }
                    } elseif (isset($_POST['modifier'])) {
                        if (isset($_POST['id_acc_sup_mod'])) {
                            $id_acc_mod = htmlspecialchars($_POST['id_acc_sup_mod']);
                            $actualite = new Actualite();
                            $actualite->recuperer($id_acc_mod);
                            ?>
                            <div class="out_in_add">
                                <form method="post" action="actualites_prof.php" enctype="multipart/form-data">
                                    <div class="t_c_acc">
                                        <input type="text" id="titre_acc_mod" name="titre_acc" value="<?php echo $actualite->getTitre(); ?>"/>
                                        <textarea id="contenu_acc_mod" name="contenu_acc" ><?php echo $actualite->getContenu(); ?></textarea>
                                    </div>
                                    <div class="im_im_acc">
                                        <img id="image_acc_mod" src="../modele/moteur_img_act.php?id=<?php echo $actualite->getId(); ?>" id="img_acc" />
                                        <input id="img_acc_mod" type="file" name="img_acc" />
                                        <input type="hidden" name="idacc" value="<?php echo $actualite->getId(); ?>"/>
                                    </div>
                                    <div class="acc_submit">
                                        <input type="submit" name="valid_mod_acc" id="valid_mod_acc" value="Modifier" onclick="gererClickBouttons('confirmation du modification de cet actualite !');"/>
                                    </div>
                                </form>
                            </div>
                            <?php
                        } else {
                            echo '<script>alert("erreur: actualite non valide");location="actualites_prof.php";</script>';
                        }
                    } elseif (isset($_POST['supprimer'])) {
                        if (isset($_POST['id_acc_sup_mod'])) {
                            $id_acc_sup = htmlspecialchars($_POST['id_acc_sup_mod']);
                            $actualite = new Actualite();
                            $actualite->recuperer($id_acc_sup);
                            if (strcmp(strtolower($actualite->getCinP()), strtolower($_SESSION['psprof'])) == 0) {
                                if ($actualite->supprimer()) {
                                    echo '<script>alert("actualite supprimer avec succes");location="actualites_prof.php";</script>';
                                } else {
                                    echo '<script>alert("actualite non supprime");location="actualites_prof.php"</script>';
                                }
                            } else {
                                echo '<script>alert("erreur: vous etes pas l\'acces de supprimer cet actualite");location="actualites_prof.php";</script>';
                            }
                        } else {
                            echo '<script>alert("erreur: actualite non valide");location="actualites_prof.php";</script>';
                        }
                    } else {
                        foreach ($acts as $act) {
                            $actualite = new Actualite();
                            $actualite->recuperer($act['id_acc']);
                            $moteurImg = '../modele/moteur_img_act.php';
                            ?>
                            <form action="actualites_prof.php" method="post">
                                <?php
                                $actualite->afficherAvecMoteurImg($moteurImg);
                                ?>
                            </form>
                            <?php
                        }
                    }
                    ?>
                    <script src="../files/jscripts/script_photo.js"></script>
                    <script>  
                        function gererClickBouttons(message){
                        this.onclick=function(){
                            if(!confirm(message)){
                                return false;
                            }
                        };
                        }
                        function PhotoChange(){
                            if(document.getElementById('img_acc_mod')){
                                telechargerPhoto('img_acc_mod','image_acc_mod');
                            }
                        }
                        /*main*/
                        (function() {
                            PhotoChange();
                        })();
                    </script>
                </body>
            </html>
            <?php
        } else {
            echo '<p>aucun actualite trouve</p>';
        }
        ?>      



        <?php
    } else {
        echo '<script> parent.location="../modele/deconnect.php";</script>';
    }
} else {
    echo '<script> parent.location="../modele/deconnect.php";</script>';
}
