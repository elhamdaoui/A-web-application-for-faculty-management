<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @param type $validation
 */
function afficheColorValidation($validation) {
    if (strcmp(strtolower($validation), 'vm') == 0) {
        echo 'green';
    } elseif (strcmp(strtolower($validation), 'ratt') == 0) {
        echo 'wheat';
    } elseif (strcmp(strtolower($validation), 'var') == 0) {
        echo 'lightgreen';
    } elseif (strcmp(strtolower($validation), 'abs') == 0) {
        echo 'yellow';
    } elseif (strcmp(strtolower($validation), 'nv') == 0) {
        echo 'red';
    } else {
        echo 'none';
    }
}

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
        include_once '../modele/classes/Departement.class.php';
        $pr = new Professeur();
        $pr->recuperer_Professeur($psp);
        if (!$pr->corActuelFiliere()) {
            header("Location: ../acceuil.php");
        } else {
            ?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <title>FPS: coordonner filiere</title>
                    <link rel="stylesheet" href="../files/styles/coordoner_style.css"/>
                </head>

                <body>
                    <?php
                    if (isset($_GET['infmod']) and strcmp($_GET['infmod'], 'true') == 0) {
                        include_once '../modele/classes/Departement.class.php';
                        $idmm = htmlspecialchars($_GET['idm']);
                        $idmf = htmlspecialchars($_GET['idf']);
                        $semmod = htmlspecialchars($_GET['sem']);
                        $modls = Departement::getInscriptionsModuleFiliere($idmm, $idmf);
                        if (!empty($modls)) {
                            $nom_res = $modls[0]['nom_p'] . ' ' . $modls[0]['prenom_p'];
                            $nomod = $modls[0]['nom_mod'];
                            ?>
                            <table class="tab_etds" CELLPADDING="6px" style="border-radius: 5px;">
                                <caption>&nbsp;</caption>
                                <thead>
                                    <tr class="infos_md">
                                        <th colspan="1" class="libele">Filiere</th><th colspan="2"><?php echo $pr->get_nom_f(); ?></th>
                                        <th colspan="1" class="libele">Semestre</th><th colspan="3"><?php echo $semmod; ?></th>
                                    </tr>
                                    <tr class="infos_md">
                                        <th colspan="1" class="libele">Module</th><th colspan="2"><?php echo $idmm . '_' . $nomod; ?></th>
                                        <th colspan="1" class="libele">Responsable</th><th colspan="3"><?php echo 'M. ' . $nom_res; ?></th>
                                    </tr>
                                    <tr><th colspan="7"><hr></th></tr>
                                <tr>
                                    <th style="background-color: #007d78;border:1px solid #007d78;">N inscription</th>
                                    <th style="background-color: #4fef54;border:1px solid #4fef54;">Nom</th>
                                    <th style="background-color: #d87887;border:1px solid #d87887;">Prenom</th>
                                    <th style="background-color: #7c6fd6;border:1px solid #7c6fd6;">nombre inscription</th>
                                    <th style="background-color: #108743;border:1px solid #108743;">Note normale</th>
                                    <th style="background-color: #8d0033;border:1px solid #8d0033;">Note Ratt</th>
                                    <th style="background-color: #003e85;border:1px solid #003e85;">Validation</th>
                                </tr>
                            </thead>
                            <tfoot></tfoot>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($modls as $mdl) {
                                    if ($i % 2) {
                                        // $style='style="background-color:#c090eb;"';
                                        $style = 'style=" background-color: #ccccff;"';
                                    } else {
                                        // $style='style="background-color:#c090eb;"';
                                        $style = 'style=" background-color: #ffff99;"';
                                    }
                                    $i++;
                                    ?>

                                    <tr <?php echo $style; ?>>
                                        <td><?php echo $mdl['numins_e']; ?></td>
                                        <td><?php echo $mdl['nom_e']; ?></td>
                                        <td><?php echo $mdl['prenom_e']; ?></td>
                                        <td align="center"><?php echo $mdl['nb_ins']; ?></td>
                                        <td align="center"><?php echo $mdl['note_N']; ?></td>
                                        <td align="center"><?php echo $mdl['note_R']; ?></td>
                                        <td align="center" style="background-color:<?php echo afficheColorValidation($mdl['etat_V']); ?>;"><?php echo $mdl['etat_V']; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    } else {
                        echo '<script>location="coordoner.php";</script>';
                    }
                    /**/
                } elseif (isset($_GET['etdsem']) and strcmp($_GET['etdsem'], 'true') == 0) {
                    if (isset($_GET['idsem'])) {
                        $idss = htmlspecialchars($_GET['idsem']);
                        $idff = $pr->get_id_f();
                        $etds = Departement::getInscriptionsSemstreFiliere($idss, $idff);
                        if ($etds == FALSE) {
                            echo '<script>location="coordoner.php"</script>';
                        }
                        if (!empty($etds)) {
                            $inf = $etds[0];
                            ?>
                            <table class="tab_etds" CELLPADDING="6px" style="border-radius: 5px;">
                                <caption>Filiere <?php echo $pr->get_nom_f() ?> Semestre <?php echo $idss ?></caption>
                                <thead>
                                    <tr class="mdls">
                                        <th style="background-color: #007d78;border:1px solid #007d78;">N inscription</th>
                                        <th style="background-color: #4fef54;border:1px solid #4fef54;">Nom</th>
                                        <th style="background-color: #d87887;border:1px solid #d87887;">Prenom</th>
                                        <?php
                                        foreach ($inf as $nom_mod) {
                                            ?>
                                            <th><?php echo $nom_mod; ?></th>
                                            <?php
                                        }
                                        ?>
                                            <th style="background-color: #ffcc00;border:1px solid #ffcc00;">Moyenne</th>
                                            <th style="background-color: #009999;border:1px solid #009999;">Validation</th>
                                    </tr>
                                </thead>
                                <tfoot><tr style="background-color: #44413e;color:white;"><th></th><th colspan="11"><?php echo count($etds) - 1; ?> Etudiants</th></tr></tfoot>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($etds as $etd) {
                                        if ($i % 2) {
                                            $style = 'style=" background-color: #ccccff;"';
                                        } else {
                                            $style = 'style=" background-color: #ffff99;"';
                                        }
                                        if ($i > 0) {
                                            ?>
                                            <tr <?php echo $style; ?>>
                                                <?php
                                                foreach ($etd as $val) {
                                                    if($val==-1) $val='ABS';
                                                    ?>
                                                    <td><?php echo $val; ?></td>
                                                    <?php
                                                }
                                                ?>
                                                <!--td>moyenne</td-->
                                                <!--td>validation</td-->
                                            </tr>
                                            <?php
                                        }
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>                   
                            <?php
                        }
                    } else {
                        echo '<script>alert("semestre non existe");location="coordoner.php"</script>';
                    }
                } elseif (isset($_GET['valanne']) and strcmp($_GET['valanne'], 'true') == 0) {
                    include_once '../modele/classes/DelibirationEtudiants.class.php';
                    $del=new DelibirationEtudiants($pr->get_id_f(),$_GET['ids2'],$_GET['ids1']);
                    if(!$del->getNonDelibrer()){
                        echo '<script>alert("Delibiration faite avec succes");location="coordoner.php";</script>';
                    }
                } else {
                    $dept = new Departement();
                    $dept->recuperer_departement($pr->getIdDepartement());
                    $modules = $dept->getModulesFiliere($pr->get_id_f());
                    $i = 2;
                    if (!empty($modules)) {
                        foreach ($modules as $nums => $semestre) {
                            $anne = (int) ($i / 2);
                            $test = $i % 2;
                            if ($test == 0) {
                                ?>
                                <div class="anne">
                                    <fieldset><legend>Annee <?php echo $anne; ?></legend>
                                        <?php
                                    }
                                    ?>
                                    <div class="semestre">
                                        <p class="btn_sem" onclick="afficheDisparaitre('<?php echo 'S' . $nums; ?>');">Semestre <?php echo $nums; ?></p>
                                        <fieldset id="<?php echo 'S' . $nums; ?>">
                                            <?php
                                            foreach ($semestre as $modul) {
                                                $idMd = $modul['id_m'];
                                                $nomMd = $modul['nom_mod'];
                                                $eqMd = $modul['id_eq'];
                                                $idFl = $modul['id_f'];
                                                $cin_res = $modul['cin_p'];
                                                $semeq = $modul['nom_s_eq'];
                                                ?>
                                                <p onclick="document.location = 'coordoner.php?infmod=true&iddep=<?php echo $dept->getId() . '&idf=' . $idFl . '&idm=' . $idMd . '&sem=' . $nums; ?>';"><em> <?php echo $idMd; ?> </em><strong title="<?php echo $nomMd; ?>">&nbsp;&nbsp; <?php echo substr($nomMd, 0, 18); ?> </strong></p>
                                                <!--on peut ajouter le nom du prof responsable-->
                                                <?php
                                            }
                                            ?>
                                        </fieldset>
                                        <p class="val_btn_sem" onclick="document.location = 'coordoner.php?etdsem=true&idsem=<?php echo $nums ?>';">Etudiants semestre <?php echo $nums; ?></p>
                                    </div>
                                    <?php if ($test != 0) { ?>
                                        <input type="button" name="valid_anne" class="valid_anne" value="Valider semestres d'annee" onclick="document.location = 'coordoner.php?valanne=true&ids1=<?php echo $nums . '&ids2=' . $semeq ; ?>';"/> 
                                    </fieldset>
                                </div>
                                <?php
                            }
                            $i++;
                        }
                    } else {
                        echo '<script>alert("aucun module trouve");location="modules.php";</script>';
                    }
                }
            }
            ?>
        </body>
        <script>
                                            function afficheDisparaitre(id) {
                                                if (document.getElementById(id)) {
                                                    var elm = document.getElementById(id);
                                                    if (elm.style.display === 'none' || elm.style.display === '') {
                                                        elm.style.display = 'block';
                                                        elm.style.transform = 'translateY(-100%)';
                                                        elm.style.transitionDuration = '2s';
                                                        setTimeout(function() {
                                                            elm.style.transform = 'translateY(0%)';
                                                        }, 100);
                                                    } else {
                                                        elm.style.transitionDuration = '2s';
                                                        setTimeout(function() {
                                                            elm.style.transform = 'translateY(-120%)';
                                                        }, 100);
                                                        setTimeout(function() {
                                                            elm.style.display = 'none';
                                                        }, 1800);
                                                    }
                                                }
                                            }
        </script>
        </html>
        <?php
    } else {
        echo '<script> parent.location="../modele/deconnect.php";</script>';
    }
} else {
    echo '<script> parent.location="../modele/deconnect.php";</script>';
}
