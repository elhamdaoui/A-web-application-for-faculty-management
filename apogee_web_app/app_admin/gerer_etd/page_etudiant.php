<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @param type $num
 */
function afficheNumIns($num) {
    if (isset($num) and !empty($num) and $num != NULL) {
        echo $num;
    } else {
        echo '<em class="non_ins">Non inscrit</em>';
    }
}

/**
 * 
 * @param type $sexe
 */
function affiche_Sexe($sexe) {
    if (strcmp($sexe, 'H') == 0) {
        echo 'Homme';
    } elseif (strcmp($sexe, 'F') == 0) {
        echo 'Femme';
    }
}

/**
 * 
 * @param type $etat
 */
function etatTraiteRec($etat) {
    if (strcmp($etat, 'non traite') == 0) {
        echo 'Traiter';
    } else {
        echo 'Annuler traitement';
    }
}

/**
 * 
 * @param type $etat
 */
function styleEtatTraiteRec($etat) {
    if (strcmp($etat, 'non traite') == 0) {
        echo 'color:brown;';
    } else {
        echo 'color:green;font-weight:bold;';
    }
}

/**
 * 
 * @param type $str
 * @return string
 */
function returnEtatTraaitement($str) {
    if (strcmp(strtolower($str), 'traiter') == 0) {
        return 'traite';
    }
    return 'non traite';
}

/**
 * 
 * @param type $note
 */
function echoNote($note, $idm, $cne, $sess) {
    if (is_numeric($note)) {
        if ($note == -1) {
            echo 'ABS ';
            ?>
            <input type="checkbox" onchange="if (this.checked === true) {
                        if (!confirm('etes-vous sur d\'annuler l\'absence pour cet etudiant en : <?php echo $idm . ' ' . $sess; ?>')) {
                            this.checked = false;
                            return false;
                        } else {
                            location = 'page_etudiant.php?id=<?php echo $cne; ?>&idm=<?php echo $idm; ?>&sess=<?php echo $sess; ?>';
                        }
                    }"/>
                   <?php
               } else {
                   echo $note;
               }
           } else {
               echo $note;
           }
       }
       ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv = "Content-Type" content = "text/html; charset=UTF-8">
        <title>gerer inscription etudiant</title>
        <link rel="stylesheet" href="../files/styles/page_etudiant_style.css"/>
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
                include_once '../modele/classes/Etudiant.class.php';
                if (isset($_GET['id'])) {
                    /* la variable $_GET['id']
                     * designe que la page est appele par la page
                     * affich etd , lorsque l'utilisateur
                     * qlick sur un etudiant.id est le cne d'un etudiant.
                     * et on fait le teste si cet etudiant est inscrit ou non càd 
                     * il est seulement preinscrit.
                     */
                    $cne_e = htmlspecialchars($_GET['id']);
                    $etd = new Etudiant();
                    if ($etd->recuperer_etudiant($cne_e) != FALSE) {
                        $numins = $etd->getNumins();
                        if (isset($numins) and $numins != NULL) {
                            ?>
                            <nav class="nav_etd"><form action="page_etudiant.php?id=<?php echo $cne_e; ?>" method="post">
                                    <input type="submit" value="informations personnelles" name="infos_i_etd" id="infos_i_etd"/>
                                    <input type="submit" value="situations pedagogique" name="stua_i_etd" id="stua_i_etd"/>
                                    <input type="submit" value="reclamations" name="recla_i_etd" id="recla_i_etd"/>
                                    <input type="submit" value="diplomes" name="diplomes_i_etd" id="diplomes_i_etd"/>
                                </form>
                            </nav>
                            <?php
                            if (isset($_GET['idm']) and isset($_GET['sess'])) {
                                if (strcmp($_GET['sess'], 'session normale') == 0) {
                                    if ($etd->annulerAbsenceModulSession($_GET['idm'], 'note_n')) {
                                        echo '<script>alert("la modification du note normale est faite avec succes");</script>';
                                    }
                                } elseif (strcmp($_GET['sess'], 'session rattrapage') == 0) {
                                    if ($etd->annulerAbsenceModulSession($_GET['idm'], 'note_r')) {
                                        echo '<script>alert("la modification du note rattrapage est faite avec succes");</script>';
                                    }
                                } else {
                                    echo '<script>alert("des infos sont incorrects");</script>';
                                }
                                echo '<script>location="page_etudiant.php?id=' . $cne_e . '";</script>';
                            } elseif (isset($_POST['stua_i_etd'])) {
                                /* lorsque l'user click sur le boutton situation pedagogique du navigation */
                                echo '<p class="p_etd_menu"><em>situation pedagogique de </em><strong>' . $etd->getNom() . '_' . $etd->getPrenom() . '_' . $etd->getNumins() . '</strong></p>';
                                /* on affiche les situations pedagogique */
                                $situa = $etd->situationPedagogique();
                                $situaAuc = array();
                                $situaActuell = array();
                                if (!empty($situa)) {
                                    foreach ($situa as $sem => $mods) {
                                        $situaActuell[$sem] = array();
                                        $situaAuc[$sem] = array();
                                        foreach ($mods as $mod) {
                                            if (is_null($mod['etat_v'])) {
                                                array_push($situaActuell[$sem], $mod);
                                            } else {
                                                array_push($situaAuc[$sem], $mod);
                                            }
                                        }
                                    }
                                } else {
                                    echo '<p>vous avez 0 modules .donc vous etes pas encore inscrit</p>';
                                }
                                if (!empty($situaAuc)) {
                                    ?>
                                    <fieldset><legend class="md_vlds">Les modules valides</legend>
                                        <?php
                                        foreach ($situaAuc as $sem => $mods) {
                                            if (!empty($mods)) {
                                                ?>
                                                <table class="situ_mod">
                                                    <caption> Semestre <?php echo $sem; ?></caption>
                                                    <thead>
                                                        <tr style="background-color: #ffff47;">
                                                            <th>id module</th><th>Nom module</th><th>Note Normale</th><th>Note Rattrapage</th><th>Validation</th><th>Nb inscription</th><th>Date inscr</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 0;
                                                        foreach ($mods as $mod) {
                                                            $style = '';
                                                            if ($i % 2 == 0)
                                                                $style = 'style="background-color: #c4eaec;"';
                                                            $i++;
                                                            ?>

                                                            <tr <?php echo $style; ?>>
                                                                <td><?php echo $mod['id_m']; ?></td>
                                                                <td><?php echo $mod['nom_mod']; ?></td>
                                                                <td align="center"><?php echoNote($mod['note_n'], $mod['id_m'], $mod['cne_e'], 'session normale'); ?></td>
                                                                <td align="center"><?php echoNote($mod['note_r'], $mod['id_m'], $mod['cne_e'], 'session rattrapage'); ?></td>
                                                                <td align="center"><?php echo $mod['etat_v']; ?></td>
                                                                <td align="center"><?php echo $mod['nb_ins']; ?></td>
                                                                <td><?php echo $mod['date_ins']; ?></td>
                                                            </tr>

                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </fieldset>
                                    <?php
                                }
                                /* les modules actuels */
                                if (!empty($situaActuell)) {
                                    ?>
                                    <fieldset><legend class="md_act">Les modules Actuels</legend>
                                        <?php
                                        foreach ($situaActuell as $sem => $mods) {
                                            if (!empty($mods)) {
                                                ?>
                                                <table class="situ_act_mod">
                                                    <caption> Semestre <?php echo $sem; ?></caption>
                                                    <thead>
                                                        <tr style="background-color: #ffff47;">
                                                            <th>id module</th><th>Nom module</th><th>Note Normale</th><th>Note Rattrapage</th><th>Validation</th><th>Nb inscription</th><th>Date inscr</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 0;
                                                        foreach ($mods as $mod) {
                                                            $style = '';
                                                            if ($i % 2 == 0)
                                                                $style = 'style="background-color: #dedfbe;"';
                                                            $i++;
                                                            ?>

                                                            <tr <?php echo $style; ?>>
                                                                <td><?php echo $mod['id_m']; ?></td>
                                                                <td><?php echo $mod['nom_mod']; ?></td>
                                                                <td align="center"><?php echoNote($mod['note_n'], $mod['id_m'], $mod['cne_e'], 'session normale'); ?></td>
                                                                <td align="center"><?php echoNote($mod['note_r'], $mod['id_m'], $mod['cne_e'], 'session rattrapage'); ?></td>
                                                                <td align="center"><?php echo $mod['etat_v']; ?></td>
                                                                <td align="center"><?php echo $mod['nb_ins']; ?></td>
                                                                <td><?php echo $mod['date_ins']; ?></td>
                                                            </tr>

                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </fieldset>
                                    <?php
                                }
                            } elseif (isset($_POST['recla_i_etd'])) {
                                $reclms = $etd->getReclamations();
                                echo '<p class="p_etd_menu"><em>reclamations de </em><strong>' . $etd->getNom() . '_' . $etd->getPrenom() . '_' . $etd->getNumins() . '</strong></p>';
                                if (empty($reclms)) {
                                    echo '<p>aucune reclamtions</p>';
                                } else {
                                    foreach ($reclms as $rec) {
                                        $id_r = $rec['id_r'];
                                        $type_r = $rec['type_r'];
                                        $contenu_r = $rec['contenu_r'];
                                        $etat_r = $rec['etat_r'];
                                        $date_r = $rec['date_r'];
                                        ?>

                                        <div class="rec_etd" >
                                            <span class="type_contenu_rec">
                                                <span class="type_rec"><?php echo $type_r; ?></span>
                                                <span class="contenu_rec"><?php echo $contenu_r; ?></span>

                                            </span>
                                            <span class="date_etat_rec">
                                                <span class="date_rec"><?php echo $date_r; ?></span>
                                                <span class="etat_rec" style="<?php styleEtatTraiteRec($etat_r); ?>"><?php echo $etat_r; ?></span>
                                            </span>
                                            <span class="btn_rec">
                                                <form action="page_etudiant.php?id=<?php echo $etd->getCne(); ?>" method="post">
                                                    <input type="hidden" name="id_rec" value="<?php echo $id_r; ?>"/>
                                                    <input type="submit" name="trait_rec_etd" id="trait_rec_etd" value="<?php etatTraiteRec($etat_r); ?>"/>
                                                    <input type="submit" name="supprimer_rec_etd" id="supprimer_rec_etd" value="Supprimer"/>
                                                </form>
                                            </span>
                                        </div>
                                        <?php
                                    }
                                }
                            } elseif (isset($_POST['diplomes_i_etd'])) {
                                /* lorsque l'user click sur le boutton diplomes du navigation */
                                echo '<p class="p_etd_menu"><em>Diplomes de </em><strong>' . $etd->getNom() . '_' . $etd->getPrenom() . '_' . $etd->getNumins() . '</strong></p>';
                                $dips = $etd->getDiplomes();
                                if (empty($dips)) {
                                    echo '<p>aucun diplome obtenu</p>';
                                } else {
                                    foreach ($dips as $dip) {
                                        $nom_dip = $dip['nom_dip'];
                                        $type_dip = $dip['type_dip'];
                                        $date_dip = $dip['date_obt'];
                                        $moyenne_dip = $dip['moyenne'];
                                        $mention_dip = $dip['mention_dip'];
                                        ?>
                                        <div class="dip_etd">
                                            <p>
                                                <em>Diplome</em><strong><?php echo $nom_dip; ?></strong>
                                            </p>
                                            <p>
                                                <em>Type diplome</em><strong><?php echo $type_dip; ?></strong>
                                            </p>
                                            <p>
                                                <em>obtenu le </em><strong><?php echo $date_dip; ?></strong>
                                            </p>
                                            <p>
                                                <em>Moyenne</em><strong><?php echo $moyenne_dip; ?></strong>
                                            </p>
                                            <p>
                                                <em>Mention</em><strong><?php echo $mention_dip; ?></strong>
                                            </p>
                                        </div>
                                        <?php
                                    }
                                }
                            } elseif (isset($_POST['trait_rec_etd'])) {
                                /* lorsque l'user click sur le boutton modifier etat traitement d'une reclamtion */
                                $etat_r_m = returnEtatTraaitement($_POST['trait_rec_etd']);
                                $id_r_m = $_POST['id_rec'];
                                $etd->modifierEtatReclamation($id_r_m, $etat_r_m);
                                echo '<script>alert("reclamtion modifiee");location="page_etudiant.php?id=' . $etd->getCne() . '"</script>';
                            } elseif (isset($_POST['supprimer_rec_etd'])) {
                                /* lorsque l'user click sur le boutton supprimer d'une reclamtion */
                                $id_r_m = $_POST['id_rec'];
                                $etd->supprimerReclamation($id_r_m);
                                echo '<script>alert("reclamtion supprimee");location="page_etudiant.php?id=' . $etd->getCne() . '"</script>';
                            } elseif (isset($_POST['supprimer_etd'])) {
                                if ($etd->supprimerEtudiant()) {
                                    echo '<script>alert("etdudiant supprimer avec succes");</script>';
                                } else {
                                    echo '<script>alert("Erreur:etdudiant non supprimer !!");</script>';
                                }
                                echo '<script>location="./affich_etd.php";</script>';
                            } elseif (isset($_POST['modifier_etd'])) {
                                echo '<p style="margin-top:5%;">on rien a  modifier etd </p><pre>';
                                print_r($etd);
                                echo '</pre>';
                            } else {
                                ?>
                                <div class="div_etd">
                                    <div class="div_1_etd">
                                        <p class="image_p_etd">
                                            <!--affichage d'image d'etudiant <img class="img_etd" src=".."/>-->
                                            <?php $etd->afficherAvecMoteurImg('./moteur_img_etd.php'); ?>
                                        </p>
                                        <p class="inf_etd">
                                            <em>Nom complet</em><strong><?php echo $etd->getNom() . ' ' . $etd->getPrenom(); ?></strong>
                                            <em>CIN</em><strong><?php echo $etd->getCin(); ?></strong>
                                            <em>Sexe</em><strong><?php affiche_Sexe($etd->getSexe()); ?></strong>
                                            <em>Nationnalite</em><strong><?php echo $etd->getNationalite(); ?></strong>
                                        </p>
                                        <p class="nais_etd">
                                            <em>Date du naissance</em><strong><?php echo $etd->getDateN(); ?></strong>
                                            <em>Lieu du naissance</em><strong><?php echo $etd->getLieun(); ?></strong></p>
                                    </div>
                                    <div class="div_2_etd">
                                        <em>Adresse</em><strong><?php echo $etd->getAdresse(); ?></strong>
                                        <em>N telphone</em><strong><?php echo $etd->getNtel(); ?></strong>
                                        <em>Email</em><strong><?php echo $etd->getEmail(); ?></strong>
                                    </div>
                                    <div class="div_3_etd">
                                        <em>Filiere</em><strong><?php echo $etd->getNomFil(); ?></strong>
                                        <em>N inscription</em><strong><?php echo $etd->getNumins(); ?></strong>
                                        <em>Baccalaureat</em><strong><?php echo $etd->getNomBac(); ?></strong>
                                        <em>CNE</em><strong><?php echo $etd->getCne(); ?></strong>
                                        <em>Moyenne Bac</em><strong><?php echo $etd->getMoyenneBac(); ?></strong>
                                        <em>Type Bac</em><strong><?php echo $etd->getTypeBac(); ?></strong>
                                        <em>Annee bac</em><strong><?php echo $etd->getAnneeDeDateDabe(); ?></strong>
                                        <em>Nationnalite Bac</em><strong><?php echo $etd->getNatBac(); ?></strong>
                                    </div>
                                    <div class="div_4_etd">
                                        <form action="page_etudiant.php?id=<?php echo $etd->getCne(); ?>" method="post">
                                            <input type="submit" value="Supprimer" name="supprimer_etd" onclick="if (!confirm('etes-vous sur de supprimer cet etudiant ?'))
                                    return false;"/>
                                            <input type="submit" value="Modifier" name="modifier_etd" onclick="if (!confirm('etes-vous sur de modifier cet etudiant ?'))
                                    return false;"/>
                                        </form>
                                    </div>
                                </div>


                                <?php
                            }
                        } else {
                            ?>
                            <div class="info_p_etd">
                                <fieldset><legend>Informations personnelles</legend>
                                    <div><em>Nom complet</em><strong name="nom_complet_p_etd"><?php echo $etd->getNom() . ' ' . $etd->getPrenom(); ?></strong></div>
                                    <div><em>Sexe</em><strong name="sexe_p_etd"><?php affiche_Sexe($etd->getSexe()); ?></strong></div>
                                    <div><em>Date de naissance</em><strong name="ddn_p_etd"><?php echo $etd->getDateN(); ?></strong></div>
                                    <div><em>Nationalite</em><strong name="nat_p_etd"><?php echo $etd->getNationalite(); ?></strong></div>
                                    <div><em>CIN</em><strong name="cin_p_etd"><?php echo $etd->getCin(); ?></strong></div>
                                </fieldset>
                                <fieldset><legend>Inforamtions scolaires</legend>
                                    <div><em>Filiere choisi </em><strong name="fil_p_etd"><?php echo $etd->getNomFil(); ?></strong></div>
                                    <div><em>CNE </em><strong name="cne_p_etd"><?php echo $etd->getCne(); ?></strong></div>
                                    <div><em>Baccalaureat </em><strong name="bac_p_etd"><?php echo $etd->getNomBac(); ?></strong></div>
                                    <div><em>type bac</em><strong name="type_bac_p_etd"><?php echo $etd->getTypeBac(); ?></strong></div>
                                    <div><em>nationalite bac</em><strong name="type_bac_p_etd"><?php echo $etd->getNatBac(); ?></strong></div>
                                    <div><em>Moyenne bac</em><strong name="moy_p_etd"><?php echo $etd->getMoyenneBac(); ?></strong></div>
                                </fieldset>
                            </div>
                            <form class="gerer_ins_etd" action="page_etudiant.php" method="post">
                                <!-- 
                                le champ hidden ce-dessous designe lors l'envoi
                                du formulaire que l'atudiant va supprimer ou valider
                                leur inscription.de plus il fait la diference entre 
                                que la page est appelle par la page page_etudiant.php
                                lorsque l'etudiant est non encore inscrit (preinscription). 
                                ,et que cet page  php affiche le code html
                                coresspond au page page_etudiant meme.
                                blabla (-_^). 
                                -->
                                <input type="hidden" name="cne_etd_ins" id="cne_etd_ins" value="<?php echo $etd->getCne(); ?>" />
                                <input type="submit" name="valider_ins" id="valider_ins" value="Valider inscription" />
                                <input type="submit" name="supprimer_ins" id="supprimer_ins" value="Supprimer"/>
                                <input type="button" name="annuler_ins" id="annuler_ins" value="Anuller"/>
                            </form>
                            <script>
                        function gererBtnsB() {
                            var annuler = document.getElementById('annuler_ins'),
                                    supprimer = document.getElementById('supprimer_ins'),
                                    valider = document.getElementById('valider_ins');
                            valider.onclick = function() {
                                if (!confirm('vous vouler valider l\'inscription \n pour cet etudiant!')) {
                                    return false;
                                }
                            };
                            supprimer.onclick = function() {
                                if (!confirm('vous vouler vraiment supprimer cet etudiant !')) {
                                    return false;
                                }
                            };
                            annuler.onclick = function() {
                                if (!confirm('annulation !')) {
                                    return false;
                                } else {
                                    location = './affich_etd.php';
                                }
                            };
                        }
                        (function() {
                            gererBtnsB();
                        })();
                            </script>
                            <?php
                        }
                    } else {
                        echo '<p>etudiant introuvable<p>';
                    }
                } else if (isset($_POST['cne_etd_ins'])) {

                    /* cette condition designe que la page appeler par 
                     * page_etudiant.php (meme page) ,mais lorsque l'user click
                     * sur le boutton 'valider inscription' ou 'supprimer' d'un etudiant
                     * preinscrit
                     */
                    $cne_etd_ins = htmlspecialchars($_POST['cne_etd_ins']);
                    if (isset($_POST['valider_ins'])) {
                        /* lorsque l'user veut valider l'inscription d'un etudiant preinscrit */
                        $etd = new Etudiant();
                        $etd->recuperer_etudiant($cne_etd_ins);
                        if ($etd->validerInscription()) {
                            echo '<script>alert("inscription est bien faite");</script>';
                        }
                        echo '<script>location="page_etudiant.php?id=' . $etd->getCne() . '";</script>';
                    } else if (isset($_POST['supprimer_ins'])) {

                        /* lorsque l'user veut supprimer d'un etudiant preinscrit */
                        $etd = new Etudiant();
                        $etd->recuperer_etudiant($cne_etd_ins);
                        if ($etd->supprimerEtudiant()) {
                            echo '<script> alert("etudiant supprime");location="affich_etd.php"; </script>';
                        } else {
                            echo '<script>alert("un probleme est rencontre"); location="affich_etd.php";</script>';
                        }
                    }
                } else {
                    echo '<p>adresse introuvable<p>';
                }
            } else {
                header("Location: ../modele/deconnect_admin.php");
            }
        } else {
            header("Location: ../modele/deconnect_admin.php");
        }
        ?>

    </body>
</html>
