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
        $pr = new Professeur();
        $pr->recuperer_Professeur($psp);

        $mods = $pr->getModulesProfesseur();
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <title>FPS: modules professeur</title>
                <link rel="stylesheet" href="../files/styles/modules_style.css"/>
            </head>

            <body>
                <div class="div_up">
                    <form action="modules.php" method="post" id='form_nav'>
                        <?php
                        if (empty($mods)) {
                            echo '<p>aucun module</p>';
                        } else {
                            ?>
                            <table class="table_nav">
                                <tr>
                                    <td colspan="2">
                                        <select name="module" id="module_sel" onchange="document.getElementById('ts_etds').onclick();
                                                            document.getElementById('form_nav').submit();">
                                            <!--
                                            l'envenement on change , on click automatiquement le btn ts_etds pour recuperer la filiere et module
                                            et on envoi le formulaire automatiquement pour tranferer vers le module selectionne
                                            -->
                                            <?php
                                            if (isset($_POST['idm']) and isset($_POST['idf'])) {
                                                $idmpost = htmlspecialchars($_POST['idm']);
                                                $idfpost = htmlspecialchars($_POST['idf']);
                                            }
                                            foreach ($mods as $mod) {
                                                $selected = '';
                                                if (isset($idfpost) and isset($idmpost)) {
                                                    if (strcmp($idfpost, $mod['id_f']) == 0 and strcmp($idmpost, $mod['id_m']) == 0) {
                                                        $selected = 'selected=""';
                                                        $moduleName = array($mod['nom_f'], $mod['nom_s'], $mod['id_m'], $mod['nom_mod'], $pr->getNom(), $pr->getPrenom());
                                                    }
                                                }
                                                ?>
                                                <option value="<?php echo $mod['id_m'] . '_' . $mod['id_f'] ?>" <?php echo $selected ?> onclick="">
                                                    <?php
                                                    echo $mod['nom_f'] . '_' . $mod['nom_s'] . '_' . $mod['id_m'] . '_' . $mod['nom_mod'];
                                                    ?>
                                                </option>
                                                <?php
                                            }
                                            ?>

                                        </select></td>
                                    <td>
                                        <input type="hidden" id="idm_hid" name="idm" value=""/>
                                        <input type="hidden" id="idf_hid" name="idf" value=""/>
                                        <input type="submit" id="saisir_notes" name="saisir_notes" value="saisir notes" onclick="getIdmIdF();"/>
                                    </td><td>
                                        <input type="submit" id="ts_etds" name="ts_etds" value="tous etudiants" onclick="getIdmIdF();"/>
                                    </td><td>
                                        <input type="submit" id="ratt_etds" name="ratt_etds" value="etudiants ratte" onclick="getIdmIdF();"/>
                                    </td><td>
                                        <input type="submit" id="vld_etds" name="vld_etds" value="etudiants valide" onclick="getIdmIdF();"/>
                                    </td><td>
                                        <input type="submit" id="poster_act" name="poster_act" value="poster actualite" onclick="getIdmIdF();"/>
                                    </td>
                                    <?php
                                }
                                ?>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="corps" id="corps">
                    <?php
                    if (isset($_POST['saisir_notes'])) {
                        if (!isset($_POST['idm']) or !isset($_POST['idf']) or strlen($_POST['idm']) < 1 or strlen($_POST['idf']) < 1) {
                            echo '<script>alert("module ou filiere non trouve");location="modules.php";</script>';
                        }
                        $idmn = htmlspecialchars($_POST['idm']);
                        $idfn = htmlspecialchars($_POST['idf']);
                        $modlsn = $pr->getInscriptionModule($idmn, $idfn);
                        $sessRt = false;
                        if (!empty($modlsn)) {
                            foreach ($modlsn as $mdl) {
                                if ($mdl['note_N'] < 10 and $mdl['note_N'] >= 0 and $mdl['note_R'] == NULL) {
                                    $sessRt = true;
                                    break;
                                }
                            }
                        }
                        if (!empty($modlsn)) {

                            if ($modlsn[0]['note_N'] == NULL) {
                                ?>
                                <form action="modules.php" method="post" id="form_note_n">
                                    <table class="tab_etds" CELLPADDING="6px" style="border-radius: 5px;">
                                        <caption>Session Normale</caption>
                                        <thead>
                                            <tr class="infos_md">
                                                <th colspan="1" class="libele">Filiere</th><th colspan="2"><?php echo $moduleName[0]; ?></th>
                                                <th colspan="1" class="libele">Semestre</th><th colspan="2"><?php echo $moduleName[1]; ?></th>
                                            </tr>
                                            <tr class="infos_md">
                                                <th colspan="1" class="libele">Module</th><th colspan="2"><?php echo $moduleName[2] . '_' . $moduleName[3]; ?></th>
                                                <th colspan="1" class="libele">Responsable</th><th colspan="2"><?php echo 'M. ' . $moduleName[4] . ' ' . $moduleName[5]; ?></th>
                                            </tr>
                                            <tr><th colspan="7"><hr></th></tr>
                                        <tr>
                                            <th style="background-color: #007d78;border:1px solid #007d78;">N inscription</th>
                                            <th style="background-color: #4fef54;border:1px solid #4fef54;">Nom</th>
                                            <th style="background-color: #d87887;border:1px solid #d87887;">Prenom</th>
                                            <th style="background-color: #7c6fd6;border:1px solid #7c6fd6;">nombre inscription</th>
                                            <th style="background-color: #108743;border:1px solid #108743;">Note normale</th>
                                            <th style="background-color: #ffcc00;border:1px solid #ffcc00;">Absent</th>
                                        </tr>
                                        </thead>
                                        <tfoot><tr>
                                        <input type="hidden" name="idm" value="<?php echo $idmn; ?> " />
                                        <input type="hidden" name="idf" value="<?php echo $idfn; ?>" />
                                        <th></th>
                                        <th colspan="2"><input type="submit" value="Valider" id="valid_not_nor" name="valid_not_nor" /></th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($modlsn as $mdl) {
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
                                                    <td align="center"><input type="text" id="<?php echo $mdl['cne_e']; ?>" name="<?php echo $mdl['cne_e']; ?>" value="<?php echo $mdl['note_N']; ?>"/></td>
                                                    <td align="center"><input type="checkbox"  onchange="gererAbsence('<?php echo $mdl['cne_e']; ?>', this);"/></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </form>
                                <!--fin partie insertion notes du session normale-->
                                <?php
                            } elseif ($sessRt) {
                                ?>
                                <form action="modules.php" method="post" id="form_note_r">
                                    <table class="tab_etds" CELLPADDING="6px" style="border-radius: 5px;">
                                        <caption> Session Rattrapage</caption>
                                        <thead>
                                            <tr class="infos_md">
                                                <th colspan="1" class="libele">Filiere</th><th colspan="2"><?php echo $moduleName[0]; ?></th>
                                                <th colspan="1" class="libele">Semestre</th><th colspan="3"><?php echo $moduleName[1]; ?></th>
                                            </tr>
                                            <tr class="infos_md">
                                                <th colspan="1" class="libele">Module</th><th colspan="2"><?php echo $moduleName[2] . '_' . $moduleName[3]; ?></th>
                                                <th colspan="1" class="libele">Responsable</th><th colspan="3"><?php echo 'M. ' . $moduleName[4] . ' ' . $moduleName[5]; ?></th>
                                            </tr>
                                            <tr><th colspan="7"><hr></th></tr>
                                        <tr>
                                            <th style="background-color: #007d78;border:1px solid #007d78;">N inscription</th>
                                            <th style="background-color: #4fef54;border:1px solid #4fef54;">Nom</th>
                                            <th style="background-color: #d87887;border:1px solid #d87887;">Prenom</th>
                                            <th style="background-color: #7c6fd6;border:1px solid #7c6fd6;">nbr inscription</th>
                                            <th style="background-color: #108743;border:1px solid #108743;">Note normale</th>
                                            <th style="background-color: #00cc99;border:1px solid #00cc99;">Note Ratt</th>
                                            <th style="background-color: #ffcc00;border:1px solid #ffcc00;">Absent</th>
                                        </tr>
                                        </thead>
                                        <tfoot><tr>
                                        <input type="hidden" name="idm" value="<?php echo $idmn; ?> " />
                                        <input type="hidden" name="idf" value="<?php echo $idfn; ?>" />
                                        <th></th>
                                        <th colspan="2"><input type="submit" value="Valider" id="valid_not_rat" name="valid_not_rat" /></th>
                                        </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($modlsn as $mdl) {
                                                if ($i % 2) {
                                                    // $style='style="background-color:#c090eb;"';
                                                    $style = 'style=" background-color: #ccccff;"';
                                                } else {
                                                    // $style='style="background-color:#c090eb;"';
                                                    $style = 'style=" background-color: #ffff99;"';
                                                }
                                                if ($mdl['note_N'] < 10) {
                                                    $i++;
                                                    ?>

                                                    <tr <?php echo $style; ?>>
                                                        <td><?php echo $mdl['numins_e']; ?></td>
                                                        <td><?php echo $mdl['nom_e']; ?></td>
                                                        <td><?php echo $mdl['prenom_e']; ?></td>
                                                        <td align="center"><?php echo $mdl['nb_ins']; ?></td>
                                                        <td align="center"><?php echo $mdl['note_N']; ?></td>
                                                        <td align="center"><input type="text" id="<?php echo $mdl['cne_e']; ?>" name="<?php echo $mdl['cne_e']; ?>" value="<?php echo $mdl['note_R']; ?>"/></td>
                                                        <td align="center"><input type="checkbox"  onchange="gererAbsence('<?php echo $mdl['cne_e']; ?>', this);"/></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </form>
                                <!--fin partie insertion notes du session normale-->
                                <?php
                            } else {
                                echo '</p>session normale et session rattrapage sont faites<br/>
                                    ou bien  tous les etudiants sont valide le module au session normle</p>';
                            }
                        } else {
                            echo '<p>aucun etudiant trouve</p>';
                        }
                    } elseif (isset($_POST['ts_etds'])) {
                        if (isset($_POST['module'])) {
                            $idmidf = htmlspecialchars($_POST['module']);
                            $idmidft = str_getcsv($idmidf, '_');
                            $idmm = $idmidft[0];
                            $idmf = $idmidft[1];
                            $modls = $pr->getInscriptionModule($idmm, $idmf);
                            if (!empty($modls)) {
                                ?>
                                <table class="tab_etds" CELLPADDING="6px" style="border-radius: 5px;">
                                    <caption>&nbsp;</caption>
                                    <thead>
                                        <tr class="infos_md">
                                            <th colspan="1" class="libele">Filiere</th><th colspan="2"><?php echo $moduleName[0]; ?></th>
                                            <th colspan="1" class="libele">Semestre</th><th colspan="3"><?php echo $moduleName[1]; ?></th>
                                        </tr>
                                        <tr class="infos_md">
                                            <th colspan="1" class="libele">Module</th><th colspan="2"><?php echo $moduleName[2] . '_' . $moduleName[3]; ?></th>
                                            <th colspan="1" class="libele">Responsable</th><th colspan="3"><?php echo 'M. ' . $moduleName[4] . ' ' . $moduleName[5]; ?></th>
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
                                    <tfoot><tr>
                                            <th></th>
                                            <th colspan="2"><input type="button" value="imprimer" id="imprim_rslts" onclick="print();"/></th>
                                        </tr></tfoot>
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
                                echo '<p>aucun etudiant inscrit dans ce module</p>';
                            }
                        }
                    } elseif (isset($_POST['ratt_etds'])) {
                        $idmn = htmlspecialchars($_POST['idm']);
                        $idfn = htmlspecialchars($_POST['idf']);
                        $modlsn = $pr->getInscriptionModule($idmn, $idfn);
                        if (!empty($modlsn)) {
                            ?>
                            <table class="tab_etds" CELLPADDING="6px" style="border-radius: 5px;">
                                <caption> etudiant passeront ou sont passes la session du rattrapage</caption>
                                <thead>
                                    <tr class="infos_md">
                                        <th colspan="1" class="libele">Filiere</th><th colspan="1"><?php echo $moduleName[0]; ?></th>
                                        <th colspan="1" class="libele">Semestre</th><th colspan="2"><?php echo $moduleName[1]; ?></th>
                                    </tr>
                                    <tr class="infos_md">
                                        <th colspan="1" class="libele">Module</th><th colspan="1"><?php echo $moduleName[2] . '_' . $moduleName[3]; ?></th>
                                        <th colspan="1" class="libele">Responsable</th><th colspan="2"><?php echo 'M. ' . $moduleName[4] . ' ' . $moduleName[5]; ?></th>
                                    </tr>
                                    <tr><th colspan="5"><hr></th></tr>
                                <tr>
                                    <th style="background-color: #007d78;border:1px solid #007d78;">N inscription</th>
                                    <th style="background-color: #4fef54;border:1px solid #4fef54;">Nom</th>
                                    <th style="background-color: #d87887;border:1px solid #d87887;">Prenom</th>
                                    <th style="background-color: #7c6fd6;border:1px solid #7c6fd6;">nombre inscription</th>
                                    <th style="background-color: #108743;border:1px solid #108743;">Note normale</th>
                                </tr>
                                </thead>
                                <tfoot>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($modlsn as $mdl) {
                                        if ($mdl['note_N'] < 10 and $mdl['note_N'] != NULL) {
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
                                            </tr>
                                            <?php
                                        }
                                    }
                                    if ($i == 0) {
                                        echo '<tr><td colspan="5" style="background-color:yellow;">aucun etudiant ratte</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <?php
                        } else {
                            echo '<p>aucun etudiant trouve</p>';
                        }
                        /* --fin partie  d'affichage des etudiants ratte */
                    } elseif (isset($_POST['vld_etds'])) {
                        $idmn = htmlspecialchars($_POST['idm']);
                        $idfn = htmlspecialchars($_POST['idf']);
                        $modlsn = $pr->getInscriptionModule($idmn, $idfn);
                        if (!empty($modlsn)) {
                            ?>
                            <table class="tab_etds" CELLPADDING="6px" style="border-radius: 5px;">
                                <caption>etudiant qui sont valides le module</caption>
                                <caption>module <?php if (isset($moduleName)) echo join($moduleName, '_'); ?></caption>
                                <thead>
                                    <tr class="infos_md">
                                        <th colspan="1" class="libele">Filiere</th><th colspan="2"><?php echo $moduleName[0]; ?></th>
                                        <th colspan="1" class="libele">Semestre</th><th colspan="3"><?php echo $moduleName[1]; ?></th>
                                    </tr>
                                    <tr class="infos_md">
                                        <th colspan="1" class="libele">Module</th><th colspan="2"><?php echo $moduleName[2] . '_' . $moduleName[3]; ?></th>
                                        <th colspan="1" class="libele">Responsable</th><th colspan="3"><?php echo 'M. ' . $moduleName[4] . ' ' . $moduleName[5]; ?></th>
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
                                <tfoot>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($modlsn as $mdl) {
                                        if ($mdl['note_N'] >= 10 or $mdl['note_R'] >= 10) {
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
                                    }
                                    if ($i == 0) {
                                        echo '<tr><td colspan="7" style="background-color:yellow;">aucun etudiant valide</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <?php
                        } else {
                            echo '<p>aucun etudiant trouve</p>';
                        }
                    } elseif (isset($_POST['poster_act'])) {
                        ?>
                        <form class="form_act" action="modules.php" method="post" enctype="multipart/form-data" >
                            <div><p class="description">Ajouter une actualite</p></div>
                            <div><label >Titre</label><input type="text" name="titre_acc" id="titre_acc" autofocus="" required=""/></div>
                            <div> <label for="contenu_acc">Contenu</label><textarea name="contenu_acc" id="contenu_acc" placeholder="contenu d'actualite" NORESIZE="" required="" ></textarea></div>
                            <div><label for="image_acc">image</label><input type="file" name="image_acc" id="image_acc"/></div>
                            <div><input type="submit" name="poster_act_pres" id="poster_act_pres" value="Poster" class="btns"/><input type="reset" name="annuler" id="annuler" value="annuler"  class="btns" onclick="location = 'modules.php';"/></div>            
                            <input type="hidden" id="idm_act" name="idm_act" value="<?php echo $_POST['idm']; ?>"/>
                            <input type="hidden" id="idf_act" name="idf_act" value="<?php echo $_POST['idf']; ?>"/>
                        </form>
                        <?php
                    } elseif (isset($_POST['poster_act_pres'])) {
                        $cinp_act = $pr->getCin();
                        if (isset($_POST['idm_act']) and isset($_POST['idf_act'])) {
                            $idm_act = htmlspecialchars($_POST['idm_act']);
                            $idf_act = htmlspecialchars($_POST['idf_act']);
                            if (isset($_POST['titre_acc']) and isset($_POST['contenu_acc']) and strlen($_POST['titre_acc']) > 1 and strlen($_POST['contenu_acc']) > 1) {
                                $titre_act = htmlspecialchars($_POST['titre_acc']);
                                $contenu_act = htmlspecialchars($_POST['contenu_acc']);
                                include_once '../modele/classes/Actualite.class.php';
                                $act = new Actualite();
                                $act->remplir($titre_act, $contenu_act, date('Y-m-d'), NULL, NULL, $cinp_act, $idm_act, $idf_act, NULL);
                                if (isset($_FILES['image_acc']) and $_FILES['image_acc']['error'] == 0) {
                                    $act->setImage($_FILES['image_acc']);
                                }
                                if ($act->stocker()) {
                                    echo '<script>alert("Actualite a bien ajoutee");location="modules.php";</script>';
                                } else {
                                    echo '<script>alert("Erreur: actualite non ajouter");location="modules.php";</script>';
                                }
                            } else {
                                echo '<script>alert("Erreur: contenu ou titre non valide");location="modules.php";</script>';
                            }
                        } else {
                            echo '<script>alert("Erreur: module et filiere non specifies");location="modules.php";</script>';
                        }
                    } elseif (isset($_POST['valid_not_nor'])) {
                        if (!isset($_POST['idm']) or !isset($_POST['idf']) or strlen($_POST['idm']) < 1 or strlen($_POST['idf']) < 1) {
                            echo '<script>alert("module ou filiere non trouve");location="modules.php";</script>';
                        }
                        $idmn = htmlspecialchars($_POST['idm']);
                        $idfn = htmlspecialchars($_POST['idf']);
                        $modlsn = $pr->getInscriptionModule($idmn, $idfn);
                        $cnees = array();
                        $cneesPpst = array();
                        $cnesNotes = array();
                        $mes = '';
                        if (empty($modlsn)) {
                            echo '<script>alert("Erreur: probleme du module filiere,aucun etudiant trouve");location="modules.php";</script>';
                        }
                        foreach ($modlsn as $mdl) {
                            array_push($cnees, $mdl['cne_e']);
                        }
                        foreach ($_POST as $cle => $val) {
                            if (is_numeric($cle)) {
                                array_push($cneesPpst, $cle);
                                $cnesNotes[$cle] = $val;
                                if (!is_numeric($val)) {
                                    $mes = 'des notes ne sont pas valides .\n';
                                }
                            }
                        }
                        $difs1 = array_diff($cneesPpst, $cnees);
                        $difs2 = array_diff($cnees, $cneesPpst);
                        if (!empty($difs1)) {
                            $mes .= 'les etudiants qui ont les CNE suivants \nne sont pas inscrit dans ce mmodules:\n' . join($difs1, '\n');
                        }
                        if (!empty($difs2)) {
                            $mes.='il faut noter les etudiant qui ont les CNE suivants:\n' . join($difs2, '\n');
                        }
                        if (strlen($mes) > 1) {
                            echo '<script>alert("Erreur:\n' . $mes . '");location="modules.php";</script>';
                        } else {
                            if ($pr->setNotesPourModule($idmn, $idfn, 'note_N', $cnesNotes)) {
                                echo '<script>alert("Notes inserer avec succes");location="modules.php";</script>';
                            } else {
                                echo '<script>alert("Erreur: nautes non completement inserer");location="modules.php";</script>';
                            }
                        }
                    } elseif (isset($_POST['valid_not_rat'])) {
                        if (!isset($_POST['idm']) or !isset($_POST['idf']) or strlen($_POST['idm']) < 1 or strlen($_POST['idf']) < 1) {
                            echo '<script>alert("module ou filiere non trouve");location="modules.php";</script>';
                        }
                        $idmn = htmlspecialchars($_POST['idm']);
                        $idfn = htmlspecialchars($_POST['idf']);
                        $modlsn = $pr->getInscriptionModule($idmn, $idfn);
                        $cnees = array();
                        $cneesPpst = array();
                        $cnesNotes = array();
                        $mes = '';
                        if (empty($modlsn)) {
                            echo '<script>alert("Erreur: probleme du module filiere,aucun etudiant trouve");location="modules.php";</script>';
                        }
                        foreach ($modlsn as $mdl) {
                            if ($mdl['note_N'] < 10)
                                array_push($cnees, $mdl['cne_e']);
                        }
                        foreach ($_POST as $cle => $val) {
                            if (is_int($cle)) {
                                array_push($cneesPpst, $cle);
                                $cnesNotes[$cle] = $val;
                                if (!is_numeric($val)) {
                                    $mes = 'des notes ne sont pas valides .\n';
                                }
                            }
                        }
                        $difs1 = array_diff($cneesPpst, $cnees);
                        $difs2 = array_diff($cnees, $cneesPpst);
                        if (!empty($difs1)) {
                            $mes .= 'les etudiants qui ont les CNE suivants \nne sont pas inscrit dans ce mmodules:\n' . join($difs1, '\n');
                        }
                        if (!empty($difs2)) {
                            $mes.='il faut noter les etudiant qui ont les CNE suivants:\n' . join($difs2, '\n');
                        }
                        if (strlen($mes) > 1) {
                            echo '<script>alert("Erreur:\n' . $mes . '");location="modules.php";</script>';
                        } else {
                            if ($pr->setNotesPourModule($idmn, $idfn, 'note_R', $cnesNotes)) {
                                echo '<script>alert("Notes inserer avec succes");location="modules.php";</script>';
                            } else {
                                echo '<script>alert("Erreur: nautes non completement inserer");location="modules.php";</script>';
                            }
                        }
                    }
                    ?>
                </div>
                <script>
                                            function getIdmIdF() {
                                                var sel = document.getElementById('module_sel');
                                                var str = sel.value;
                                                var ssstr = str.split('_'), idm = ssstr[0], idf = ssstr[1];
                                                document.getElementById('idm_hid').value = idm;
                                                document.getElementById('idf_hid').value = idf;
                                            }
                                            function gererAbsence(id, elm) {
                                                var note = document.getElementById(id);
                                                if (elm.checked) {
                                                    note.value = "ABS";
                                                    note.disabled = true;

                                                } else {
                                                    note.value = "";
                                                    note.disabled = false;
                                                    note.focus();
                                                }
                                            }
                                            function gererBtnValiderNN(idForm, idBtnValider) {
                                                if (document.getElementById(idForm)) {
                                                    var formi = document.getElementById(idForm),
                                                            btn = document.getElementById(idBtnValider);
                                                    btn.onclick = function() {
                                                        var inpts = formi.getElementsByTagName('input');
                                                        var j = 0;
                                                        for (var i = 0; i < inpts.length; i++) {
                                                            var tex = inpts[i];
                                                            if (tex.type.toLowerCase() === 'text' && isNaN(tex.name) === false) {
                                                                if (tex.disabled === true) {
                                                                    tex.disabled = false;
                                                                    tex.value = -1;
                                                                    j++;
                                                                }
                                                                if(tex.value.length<1||isNaN(tex.value)===true||tex.value>20){
                                                                    alert(tex.value+' n\'est pas une  note valide !!');
                                                                    tex.focus();
                                                                    return false;
                                                                }
                                                            }
                                                        }
                                                        if (j > 0) {
                                                            if (!confirm('Monsieur vous sur que ' + j + ' etudiant(s) sont absents')) {
                                                                for (var i = 0; i < inpts.length; i++) {
                                                                    var tex = inpts[i];
                                                                    if (tex.type.toLowerCase() === 'text' && isNaN(tex.name) === false && tex.value === '-1') {
                                                                        tex.disabled = true;
                                                                        tex.value = 'ABS';
                                                                    }
                                                                }
                                                                return false;
                                                            }
                                                        }
                                                        if (!confirm('confirmer l\'ajoute des notes')) {
                                                            for (var i = 0; i < inpts.length; i++) {
                                                                var tex = inpts[i];
                                                                if (tex.type.toLowerCase() === 'text' && isNaN(tex.name) === false && tex.value === '-1') {
                                                                    tex.disabled = true;
                                                                    tex.value = 'ABS';
                                                                }
                                                            }
                                                            return false;
                                                        }
                                                    };
                                                }
                                            }
                                            (function() {
                                                gererBtnValiderNN('form_note_n', 'valid_not_nor');
                                                gererBtnValiderNN('form_note_r', 'valid_not_rat');
                                            })();
                </script>
            </body>
        </html>
        <?php
    } else {
        echo '<script> parent.location="../modele/deconnect.php";</script>';
    }
} else {
    echo '<script> parent.location="../modele/deconnect.php";</script>';
}
?>
