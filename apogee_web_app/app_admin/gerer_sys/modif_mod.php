<?php
session_start();
include_once '../modele/connect_bdd.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>page departement</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="../files/styles/modif_mod_style.css"/>
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
                if (isset($_GET['idd']) and isset($_GET['idf']) and isset($_GET['idm'])) {
                    include_once '../modele/classes/Personne.class.php';
                    include '../modele/classes/Professeur.class.php';
                    include '../modele/classes/Departement.class.php';
                    $idd = htmlspecialchars($_GET['idd']);
                    $idf = htmlspecialchars($_GET['idf']);
                    $idm = htmlspecialchars($_GET['idm']);
                    $idprf = "";
                    if (isset($_GET['prof'])) {
                        $idprf = htmlspecialchars($_GET['prof']);
                    }
                    $dep = new Departement();
                    if ($dep->recuperer_departement($idd)) {
                        if ($dep->recupererFiliere($idf) != FALSE) {
                            if (($mod = $dep->getUnModuleFiliere($idf, $idm))) {
                                if (isset($_POST['prf_res']) and isset($_POST['nom_m'])) {
                                    $newNom = htmlspecialchars($_POST['nom_m']);
                                    $cinp = htmlspecialchars($_POST['prf_res']);
                                    $mes = '';
                                    if ($dep->updateNomModule($idf, $idm, $newNom)) {
                                        $mes = 'nom module modifier\n';
                                    }
                                    if (strcmp($cinp, '0') != 0) {
                                        if ($dep->updateResponsableModule($idf, $idm, $cinp)) {
                                            $mes.=',professeur responsable de ce module modifier\n';
                                        }
                                    }

                                    echo '<script>alert("' . $mes . '");location="modif_mod.php?idf=' . $idf . '&idd=' . $idd . '&idm=' . $idm . '&prof=' . $cinp . '";</script>';
                                } else {
                                    $profs = Professeur::tousProfesseursParFilieres();
                                    ?>
                                    <form method="post" action="modif_mod.php?idf=<?php echo $idf; ?>&idd=<?php echo $idd; ?>&idm=<?php echo $idm; ?>">
                                        <p><em>Filiere</em><strong><?php echo $mod['nom_f']; ?></strong></p>
                                        <p><em>Semestre</em><strong><?php echo $mod['nom_s']; ?></strong></p>
                                        <p><em>Module</em><strong><?php echo $mod['id_m']; ?></strong></p>
                                        <p><em>Nom Module</em><strong><input type="text" name="nom_m" value="<?php echo $mod['nom_mod']; ?>" required=""/></strong></p>
                                        <p><em>Module equivalant</em><strong>&nbsp;<?php echo $mod['id_eq']; ?></strong></p>
                                        <p><em>Prof responsable</em><strong>
                                                <select name="prf_res">
                                                    <option value="0">Aucun</option>
                                                    <?php
                                                    if (!empty($profs)) {
                                                        foreach ($profs as $fil => $prfs) {
                                                            echo '<optgroup label="' . $fil . '">';
                                                            foreach ($prfs as $pr) {
                                                                $sel = "";
                                                                if (strcmp($pr->getCin(), $idprf) == 0) {
                                                                    $sel = ' selected="" ';
                                                                }
                                                                echo '<option value="' . $pr->getCin() . '" ' . $sel . '>' . $pr->getNom() . ' ' . $pr->getPrenom() . '</option>';
                                                            }
                                                            echo '</optgroup>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </strong></p>
                                        <p><input type="submit" value="enregistrer" name="md_md" id="md_md" onclick="if (!confirm('confirmation'))
                                                                                return  false;"/></p>
                                    </form>
                                    <?php
                                }
                            }
                        }
                    }
                    /**/
                } else {
                    echo '<p>Module introuvable</p>';
                }
            } else {
                echo '<script>alert("vous etes deconnecte");parent.parent.location="../modele/deconnect_admin.php";</script>';
            }
        } else {
            echo '<script>alert("vous etes deconnecte");parent.parent.location="../modele/deconnect_admin.php";</script>';
        }
        ?>
    </body>
</html>