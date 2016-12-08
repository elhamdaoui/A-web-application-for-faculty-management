<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @global type $bdd
 */
function ttFiliere() {
    global $bdd;
    $result = $bdd->query('select nom_f from filiere');
    while ($don = $result->fetch()) {
        $fil = $don['nom_f'];
        echo '<option value="' . $fil . '">' . $fil . '</option>';
    }
    $result->closeCursor();
}

/**
 * 
 * @global type $bdd
 * @param type $filCh
 */
function Filieres($filCh) {
    global $bdd;
    $result = $bdd->query('select nom_f from filiere');
    while ($don = $result->fetch()) {
        $fil = $don['nom_f'];
        $sel = '';
        if (strcmp($fil, $filCh) == 0)
            $sel = 'selected=""';
        echo '<option value="' . $fil . '" ' . $sel . '>' . $fil . '</option>';
    }
    $result->closeCursor();
}

/**
 * 
 * @param type $sexe
 */
function afficherSexe($sexe) {
    if (strcmp(strtolower($sexe), 'h') == 0) {
        echo 'Homme';
    } else {
        echo 'Femme';
    }
}

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
 * @global type $bdd
 * @return type
 */
function filsDepts() {
    global $bdd;
    $fil_dept = array();
    $result = $bdd->query('select nom_f,nom_d from filiere f, departement d where f.id_d=d.id_d');
    while ($don = $result->fetch()) {
        $fil_dept[$don['nom_f']] = $don['nom_d'];
    }
    $result->closeCursor();
    return $fil_dept;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>page professeur</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="../files/styles/page_professeur_style.css"/>
    </head>
    <body>
        <?php
        if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
            $cin = $_SESSION['ps'];
            $pwd = $_SESSION['mp'];
            $nom = $_SESSION['nom'];
            $pnom = $_SESSION['pnom'];
            $fct = $_SESSION['fct'];
            include_once ('../modele/verify_connexion.php');
            if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {
                include_once '../modele/classes/Personne.class.php';
                include_once '../modele/classes/Professeur.class.php';
                if (isset($_GET['id'])) {
                    $cin_p = htmlspecialchars($_GET['id']);
                    $pr = new Professeur();
                    if ($pr->recuperer_Professeur($cin_p)) {
                        ?>
                        <nav class="nav_prof">
                            <form action="page_professeur.php?id=<?php echo $pr->getCin(); ?>" method="post">
                                <input type="submit" value="<?php echo returnMdOrMr($pr->getSexe()) . $pr->getNom() . ' ' . $pr->getPrenom(); ?>" name="infos_i_prof" id="infos_i_prof"/>
                                <input type="submit" value="modules " name="mod_i_prof" id="mod_i_prof"/>
                                <input type="submit" value="fonctions " name="fcts_i_etd" id="fcts_i_prof"/>
                            </form>
                        </nav>
                        <?php
                        if (isset($_POST['mod_i_prof'])) {
                            $mods = $pr->getModulesProfesseur();
                            if (empty($mods)) {
                                echo '<p style="margin-top:7%;">aucune module etudie par ce professeur</p>';
                            } else {
                                ?>
                                <div class="div_prof">
                                    <center>
                                        <?php
                                        foreach ($mods as $mod) {
                                            ?>
                                            <p class="modules_prof" onclick="verPageModifModule('<?php echo $mod['id_m'] ?>', '<?php echo $mod['id_f'] ?>');">
                                                <?php echo $mod['nom_f'] . '_' . $mod['nom_s'] . '_' . $mod['id_m'] . '_' . $mod['nom_mod']; ?>
                                            </p>
                                            <?php
                                        }
                                        ?>
                                    </center>
                                </div> 
                                <script>
                                            function verPageModifModule(id_m, id_f) {
                                                alert('on peut rediriger l\'utilisateur vers \n la page de mdification de ce module\n id_m=' + id_m + ',id_f=' + id_f);
                                            }
                                </script>
                                <?php
                            }
                        } elseif (isset($_POST['fcts_i_etd'])) {
                            $nom_f_c = $pr->corActuelFiliere();
                            $nom_dept_c = $pr->chefActuelDepartemet();
                            $message = '';
                            if ($nom_f_c != FALSE) {
                                $nom_f_c = 'Coordonateur de sa filiere ' . $nom_f_c;
                            }
                            if ($nom_dept_c != FALSE) {
                                $nom_dept_c = 'Cheuf de son departemet ' . $nom_dept_c;
                            }
                            if (!$nom_f_c and !$nom_dept_c) {
                                $message = 'ni cheuf de son departement ni coordinateur de sa filiere';
                            }
                            ?>
                            <div class="div_prof">
                                <center>
                                    <p id="chef_p"><?php echo $nom_dept_c; ?></p>
                                    <p id="cor_p"><?php echo $nom_f_c; ?></p>
                                    <p id="mes_p"><?php echo $message; ?></p>
                                </center>
                            </div>
                            <?php
                        } elseif (isset($_POST['supp_prof'])) {
                            if ($pr->supprimer()) {
                                echo '<script>alert("professeur supprime");
                                    parent.location="../gerer_prof.php";</script>';
                            } else {
                                echo '<script>alert("!!! vous pouvez pas suupprimer de professeur !!!!");
                                    parent.location="../gerer_prof.php";</script>';
                            }
                        } elseif (isset($_POST['modif_prof'])) {
                            ?>

                            <div class="div_prof" id="div_prof">
                                <form action="page_professeur.php?id=<?php echo $pr->getCin(); ?>" method="post">
                                    <p>
                                        <label>Filiere</label><select name="fil_prof" id="filp_prof">
                                            <?php Filieres($pr->get_nom_f()); ?>
                                        </select>
                                    </p>
                                    <p>
                                        <label>Coordonateur du filiere <strong id="fil" style="color:yellow;"><?php echo $pr->get_nom_f(); ?></strong></label><input type="checkbox" name="coor"/>
                                    </p>
                                    <p>
                                        <label>Chef du departement <strong id="dept" style="color:yellow;"><?php echo $pr->getDepartement(); ?></strong></label><input type="checkbox" name="cheuf" />
                                    </p>
                                    <p id="modif_anul_p"><input type="submit" name="modification_pr" id="modification_pr"value="Modifier"/>
                                        <input type="button" name="annuler_modif_prf" value="Annuler" onclick="anulModPr('<?php echo $pr->getCin(); ?>');"/>
                                    </p>
                                </form>
                            </div>
                            <script>
                                        var fil_dept = {};
                                        /*un objet 'tableau associatif , fil:DEPT*/
                    <?php
                    $fil_dep = filsDepts();
                    foreach ($fil_dep as $fil => $dept) {
                        echo 'fil_dept["' . $fil . '"]="' . $dept . '";';
                    }
                    ?>
                                        var filp_prof = document.getElementById('filp_prof'),
                                                fil_s = document.getElementById('fil'),
                                                dept_s = document.getElementById('dept'),
                                                modification_pr = document.getElementById('modification_pr');
                                        modification_pr.onclick = function() {
                                            if (!confirm('vous etes sur de cette modification ?')) {
                                                return false;
                                            }
                                        };
                                        filp_prof.onchange = function() {
                                            var f = this.value;
                                            fil_s.innerHTML = f;
                                            dept_s.innerHTML = fil_dept[f];
                                        };
                                        /**/
                                        function anulModPr(cin) {
                                            if (confirm('confirmez l\'annulation')) {
                                                location = 'page_professeur.php?id=' + cin;
                                            }
                                        }
                            </script>
                            <?php
                        } elseif (isset($_POST['modification_pr'])) {
                            if (isset($_POST['fil_prof'])) {
                                $nom_f = htmlspecialchars($_POST['fil_prof']);
                                $pr->setNomFil($nom_f);
                                $pr->setIdF_par_Nom_F($nom_f);
                            }
                            if (isset($_POST['coor'])) {
                                $pr->setCoordinateurFilBDD();
                            }
                            if (isset($_POST['cheuf'])) {
                                $pr->setCheufDeptBDD();
                            }
                            echo '<script>alert("modification terminee");location="page_professeur.php?id=' . $pr->getCin() . '"</script>';
                        } else {
                            ?>
                            <div class="div_prof">
                                <div class="div_1_prof">
                                    <p class="inf_prof">
                                        <em>Nom</em><strong><?php echo $pr->getNom(); ?></strong>
                                        <em>Prenom</em><strong><?php echo $pr->getPrenom(); ?></strong>
                                        <em>CIN</em><strong><?php echo $pr->getCin(); ?></strong>
                                        <em>Sexe</em><strong><?php afficherSexe($pr->getSexe()); ?></strong>
                                        <em>Date naissance</em><strong><?php echo $pr->getDateN(); ?></strong>
                                        <em>Nationnalite</em><strong><?php echo $pr->getNationalite(); ?></strong>
                                        <em>Filiere</em><strong><?php echo $pr->get_nom_f(); ?></strong>
                                        <em>Date d'ajoute</em><strong><?php echo $pr->getDateAjout(); ?></strong>
                                    </p>
                                    <p class="img_prof" >
                                        <?php
                                        $cheminMoteurImgAct = './moteur_img_prof.php';
                                        $pr->afficherAvecMoteurImg($cheminMoteurImgAct);
                                        ?></p>
                                </div>
                                <div class="div_2_prof">
                                    <em>Email</em><strong><?php echo $pr->getEmail(); ?></strong>
                                    <em>N telephone</em><strong><?php echo $pr->getNtel(); ?></strong>
                                    <em>Adresse</em><strong><?php echo $pr->getAdresse(); ?></strong>
                                </div>
                                <div class="btn">
                                    <form action="page_professeur.php?id=<?php echo $pr->getCin(); ?>" method="post">
                                        <input type="submit" value="Supprimer" name="supp_prof" id="supp_prof" />
                                        <input type="submit" value="Modifier" name="modif_prof" id="modif_prof" />
                                    </form>
                                </div>
                            </div>
                            <script>
                                function btnModifSuppProf() {
                                    var sup = document.getElementById('supp_prof'),
                                            modif = document.getElementById('modif_prof');
                                    sup.onclick = function() {
                                        if (!confirm('vous voulez supprimer ce professeur ?')) {
                                            return false;
                                        }
                                    };
                                    modif.onclick = function() {
                                        if (!confirm('vous voulez modifier ce professeur ?')) {
                                            return false;
                                        }
                                    };
                                }
                                (function() {
                                    btnModifSuppProf();
                                })();
                            </script>
                            <?php
                        }
                        //fin du else pour les isset des btns clicke
                    } else {
                        //si le id receptée n'est pas un cin donc ...
                        echo '<p>professeur non trouve</p>';
                    }
                }

            } else {
                include
                        header("Location: ../modele/deconnect_admin.php");
            }
        } else {
            header("Location: ../modele/deconnect_admin.php");
        }
        ?>
    </body>
</html>