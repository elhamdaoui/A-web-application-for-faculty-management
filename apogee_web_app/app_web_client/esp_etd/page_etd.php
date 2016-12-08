<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @param type $sexe
 * @return string
 */
function showSexe($sexe) {
    if (strcmp(strtolower($sexe), 'h') == 0) {
        echo 'Homme ';
    } else {
        echo 'Femme';
    }
}

/**
 * 
 * @param type $note
 */
function echoNote($note) {
    if (is_numeric($note)) {
        if ($note == -1) {
            echo 'ABS';
        } else {
            echo $note;
        }
    } else {
        echo $note;
    }
}

/**
 * 
 */
if (isset($_SESSION['psetd']) and isset($_SESSION['pwdetd']) and isset($_SESSION['nometd']) and isset($_SESSION['pnometd'])) {
    $pse = htmlspecialchars($_SESSION['psetd']);
    $nome = htmlspecialchars($_SESSION['nometd']);
    $pwde = htmlspecialchars($_SESSION['pwdetd']);
    $pnome = htmlspecialchars($_SESSION['pnometd']);
    include_once '../modele/verify_connexion.php';
    if (verify_connexion('etudiant', array('cne_e' => $pse, 'nom_e' => $nome, 'prenom_e' => $pnome, 'pwd_e' => $pwde))) {
        include_once '../modele/classes/Personne.class.php';
        include_once '../modele/classes/Etudiant.class.php';
        $etd = new Etudiant();
        $etd->recuperer_etudiant($pse);
        ?>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>FPS: <?php echo $etd->getNom() . ' ' . $etd->getPrenom(); ?></title>
                <link rel="shortcut icon" type="image/x-icon" href="../files/icns/fps.png" />
                <link rel="stylesheet" href="../files/styles/page_etd_style.css"/>
            </head>
            <body>
                <?php
                if (isset($_POST['modifier_compte'])) {
                    if (isset($_POST['pwd']) and strcmp(sha1($_POST['pwd']), $etd->getPwd()) == 0) {
                        if (isset($_POST['email']) and isset($_POST['ntel']) and isset($_POST['adresse'])) {
                            $email = htmlspecialchars($_POST['email']);
                            $ntel = htmlspecialchars($_POST['ntel']);
                            $adresse = htmlspecialchars($_POST['adresse']);
                            $etd->pret_a_stocker = TRUE;
                            $etd->setEmail($email);
                            $etd->setNtel($ntel);
                            $etd->setAdresse($adresse);
                            $message = '';
                            if (isset($_POST['nvpwd'])) {
                                if (strlen($_POST['nvpwd']) > 1) {
                                    $etd->setPwd(sha1($_POST['nvpwd']));
                                    $message.='mot de passe, ';
                                }
                            }
                            if ($etd->pret_a_stocker) {
                                $message.='email, N telephone, adresse ';
                                $message = 'Modifiacation avec succes \n ' . $message;
                                $etd->modifENAP();
                                $_SESSION['pwdetd'] = $etd->getPwd();
                            } else {
                                $message = 'Erreur: non modification email,ntel ou adresse non valides';
                            }
                        } else {
                            $message = 'Erreur: non modification email,ntel ou adresse non envoyer';
                        }
                        echo '<script>alert("' . $message . '");</script>';
                    } else {
                        echo '<script>alert("Erreur: mot de passe incorrect");</script>';
                    }
                    echo '<script>location="page_etd.php?choix=mninfs";</script>';
                } elseif (isset($_POST['poster_reclm'])) {
                    if (isset($_POST['type_rec']) and strlen($_POST['type_rec']) > 1) {
                        $type = htmlspecialchars($_POST['type_rec']);
                        $contenu = '';
                        if (isset($_POST['contenu_rec'])) {
                            $contenu = htmlspecialchars($_POST['contenu_rec']);
                        }
                        $etd->envoyerReclammation($type, $contenu);
                        echo '<script>alert("reclammation a bien envoye"); </script>';
                    } else {
                        echo '<script>alert("Erreur:type reclammation non trouve");</script>';
                    }
                    echo '<script>location="page_etd.php?choix=mesrecs"; </script>';
                } elseif (isset($_GET['choix'])) {
                    $choix = htmlspecialchars($_GET['choix']);
                    if (strcmp($choix, 'mninfs') == 0) {
                        ?>
                        <div class="mninfs">
                            <p><em>Nom complet</em><strong><?php echo $etd->getNom() . ' ' . $etd->getPrenom(); ?></strong></p>
                            <p><em>Sexe</em><strong><?php showSexe($etd->getSexe()); ?></strong></p>
                            <p><em>CIN</em><strong><?php echo $etd->getCin(); ?></strong></p>
                            <p><em>Nationnalite</em><strong><?php echo $etd->getNationalite(); ?></strong></p>
                            <p><em>Date Naissance</em><strong><?php echo $etd->getDateN(); ?></strong></p>
                            <p><em>Lieu du naissance</em><strong><?php echo $etd->getLieun(); ?></strong></p>
                            <p><em>Adresse</em><strong><?php echo $etd->getAdresse(); ?></strong></p>
                            <p><em>N telephone</em><strong><?php echo $etd->getNtel(); ?></strong></p>
                            <p><em>email</em><strong><?php echo $etd->getEmail(); ?></strong></p>
                            <p><em>Filiere</em><strong><?php echo $etd->getNomFil(); ?></strong></p>
                            <p><em>Date inscription</em><strong><?php echo $etd->getDateins(); ?></strong></p>
                            <p><em>N inscription</em><strong><?php echo $etd->getNumins(); ?></strong></p>
                            <p><em>CNE</em><strong><?php echo $etd->getCne(); ?></strong></p>
                            <p><em>Bacclaureat <?php echo $etd->getNatBac(); ?></em><strong><?php echo $etd->getNomBac(); ?></strong></p>
                            <p><em>Type Bac</em><strong><?php echo $etd->getTypeBac(); ?></strong></p>
                            <p><em>Moyenne Bac</em><strong><?php echo $etd->getMoyenneBac(); ?></strong></p>
                            <p><em>Anne Bac</em><strong><?php echo $etd->getAnneeDeDateDabe(); ?></strong></p>
                        </div>
                        <?php
                    } elseif (strcmp($choix, 'prmcmpte') == 0) {
                        ?>

                        <div class="param_cmpte">
                            <form action="page_etd.php" method="post">
                                <div id="mod_com_tit">modification du compte</div>
                                <div><label>Email</label><input type="email" id="email" name="email" value="<?php echo $etd->getEmail(); ?>"/></div>
                                <div><label>N telephone</label><input type="text" id="ntel" name="ntel" value="<?php echo $etd->getNtel(); ?>"/></div>
                                <div><label>Adresse</label><input type="text" id="adresse" name="adresse" value="<?php echo $etd->getAdresse(); ?>"/></div>
                                <div id="nv_pwd"><input type="button" id="mdpwdbtn" value="modifier mot de passe" /></div>
                                <div><label>Mot de passe</label><input type="password" id="pwd" name="pwd" value=""/></div>
                                <div><input type="submit" id="modifier_compte" value="modifier" name="modifier_compte"/></div>
                            </form>
                        </div>
                        <script>
                            var btnmdpass = document.getElementById('mdpwdbtn');
                            btnmdpass.onclick = function() {
                                var div = document.getElementById('nv_pwd');
                                div.innerHTML = '<p><label>Nouveau mot de passe</label><input type="password" name="nvpwd" id="nvpwd1" required=""/></p><p><label>Confirmer mot de passe</label><input type="password" id="nvpwd2" required=""/></p>';
                            };
                            var btnsubMod = document.getElementById('modifier_compte');
                            btnsubMod.onclick = function() {
                                var em = document.getElementById('email'),
                                        ntel = document.getElementById('ntel'),
                                        adres = document.getElementById('adresse'),
                                        pwd = document.getElementById('pwd');
                                if (em.value.length < 1 || ntel.value.length < 1 || adres.value.length < 1 || pwd.value.length < 1) {
                                    alert("remplis tous les champs !!");
                                    return  false;
                                }
                                if (document.getElementById('nvpwd1')) {
                                    var pwd1 = document.getElementById('nvpwd1'), pwd2 = document.getElementById('nvpwd2');
                                    if (pwd1.value.length < 1 || (pwd1.value !== pwd2.value)) {
                                        alert("mots de passe non identiques");
                                        return false;
                                    }
                                }
                                if (!confirm("etes-sur de cette modification ?")) {
                                    return false;
                                }
                            };
                        </script>
                        <?php
                    } elseif (strcmp($choix, 'mnesp') == 0) {
                        include_once '../modele/classes/Actualite.class.php';
                        $acts = Actualite::getActualitesIntersserEtudiant($etd->getCne());
                        if (!empty($acts)) {
                            foreach ($acts as $act) {
                                ?>
                                <div class="act">
                                    <div class="infs">
                                        <div class="tit"><?php echo $act['titre_acc']; ?></div>
                                        <div class="cont"><?php echo $act['contenu_acc']; ?></div>
                                        <div class="date"><?php echo 'poster par <font color="blue">M.' . $act['nom_p'] . ' ' . $act['prenom_p'] . '</font> professeur de <font color="green">' . $act['nom_s'] . '_' . $act['id_m'] . '_' . $act['nom_mod'] . '</font>  <g> le ' . $act['date_acc'] . '</g>'; ?></div>
                                    </div>
                                    <img src="../modele/moteur_img.php?tab=actualite&attr=image_acc&colid=id_acc&id=<?php echo $act['id_acc']; ?>"/>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p> aucune actualite existe qui vous interssez</p>';
                        }
                    } elseif (strcmp($choix, 'masitu') == 0) {
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
                                                <tr>
                                                    <th>id module</th><th colspan="2">Nom module</th><th>Note Normale</th><th>Note Rattrapage</th><th>Validation</th><th>Nb inscription</th><th>Date inscr</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($mods as $mod) {
                                                    ?>

                                                    <tr>
                                                        <td><?php echo $mod['id_m']; ?></td>
                                                        <td colspan="2"><?php echo $mod['nom_mod']; ?></td>
                                                        <td align="center"><?php echoNote($mod['note_n']); ?></td>
                                                        <td align="center"><?php echoNote($mod['note_r']); ?></td>
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
                                                <tr>
                                                    <th>id module</th><th colspan="2">Nom module</th><th>Note Normale</th><th>Note Rattrapage</th><th>Validation</th><th>Nb inscription</th><th>Date inscr</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i=0;
                                                foreach ($mods as $mod) {
                                                    $style='';
                                                    if($i%2==0)
                                                    $style='style="background-color: #dedfbe;"';
                                                    $i++;
                                                    ?>
                                                    <tr <?php echo $style;?>>
                                                        <td><?php echo $mod['id_m']; ?></td>
                                                        <td colspan="2"><?php echo $mod['nom_mod']; ?></td>
                                                        <td align="center"><?php echoNote($mod['note_n']); ?></td>
                                                        <td align="center"><?php echoNote($mod['note_r']); ?></td>
                                                        <td align="center"><?php echoNote($mod['etat_v']); ?></td>
                                                        <td align="center"><?php echoNote($mod['nb_ins']); ?></td>
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
                    } elseif (strcmp($choix, 'mesdip') == 0) {
                        $dips = $etd->getDiplomes();
                        if (!empty($dips)) {
                            foreach ($dips as $dip) {
                                ?>
                                <div class="dips">
                                    <div><g>Diplome </g><strong><?php echo $dip['nom_dip']; ?></strong></div>
                                    <div><g>Type</g><strong><?php echo $dip['type_dip']; ?></strong></div>
                                    <div><g>Date d'obtentir</g><strong><?php echo $dip['date_obt']; ?></strong></div>
                                    <div><g>Moyenne</g><strong><?php echo $dip['moyenne']; ?></strong></div>
                                    <div><g>Mention</g><strong><?php echo $dip['mention_dip']; ?></strong></div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="dips"><br/><center><h2><font color="orange">Aucun Diplomes obtenu</font></h2></center></div>';
                        }
                    } elseif (strcmp($choix, 'mesprfs') == 0) {
                        include_once '../modele/classes/Personne.class.php';
                        include_once '../modele/classes/Professeur.class.php';
                        $profs = Professeur::professeursActuellesPourEtudiant($etd->getCne());
                        if (!empty($profs)) {
                            echo '<center>';
                            foreach ($profs as $prof) {
                                ?>
                                <div class="monprof">
                                    <img src="../modele/moteur_img.php?tab=professeur&attr=photo_p&colid=cin_p&id=<?php echo $prof['cin_p']; ?>"/>
                                    <div><?php echo 'M. ' . $prof['nom_p'] . ' ' . $prof['prenom_p']; ?></div>
                                    <div><strong>Email:</strong><?php echo $prof['email_p']; ?></div>
                                    <div><strong>N Tel:</strong><?php echo $prof['ntel_p']; ?></div>
                                    <div><strong>Modul:</strong><?php echo $prof['nom_s'] . '_' . $prof['id_m'] . '_' . $prof['nom_mod']; ?></div>
                                </div>
                                <?php
                            }
                            echo '</center>';
                        } else {
                            echo '<p>aucun prof trouver, peut les modules non encore donner aux professeurs</p>';
                        }
                    } elseif (strcmp($choix, 'mesrecs') == 0) {
                        $reclms = $etd->getReclamations();
                        ?>
                        <div class="poster_reclms">
                            <form method="post" action="page_etd.php">
                                <div>
                                    <label>type reclamation</label><select name="type_rec">
                                        <option value="Atestation scolaire">Atestation scolaire</option>
                                        <option value="Faute inscription">Faute inscription</option>
                                    </select>
                                </div>
                                <div>
                                    <label>Contenu 'optionnel'</label>
                                    <textarea placeholder="contenu" name="contenu_rec"></textarea>
                                </div>
                                <div><input type="submit" name="poster_reclm" value="Envoyer" onclick="if (!confirm('etes-sur d\'envoyer cette reclammation ?'))
                                    return false;"/></div>
                            </form>
                        </div>
                        <?php
                        if (!empty($reclms)) {
                            ?>
                            <table class="reclms" CELLPADDING="5" >
                                <caption> Toutes les reclammations envoyer</caption>
                                <thead>
                                    <tr><th>type</th><th>Contenu</th><th>Date d'envoie</th><th>Etat de traitement</th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($reclms as $rec) {
                                        ?>
                                        <tr>
                                            <td><?php echo $rec['type_r']; ?></td>
                                            <td><?php echo $rec['contenu_r']; ?></td>
                                            <td><?php echo $rec['date_r']; ?></td>
                                            <td><?php echo $rec['etat_r']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        } else {
                            echo '<div class="dips"><br/><center><h2><font color="orange">Aucun Reclamation trouve</font></h2></center></div>';
                        }
                    } else {
                        echo '<p><strong>Erreur 404 : adresse non trouve dans ce serveur/strong></p>';
                        echo '<script>
                        alert("choix non trouve");parent.location="./esp_etd.php";                        
                        </script>';
                    }
                } else {
                    echo '<p><strong>Erreur 404 : adresse non trouve dans ce serveur/strong></p>';
                    echo '<script>
                        alert("choix non trouve");parent.location="./esp_etd.php";                        
                        </script>';
                }
                ?>
                <script>
                </script>
            </body>
        </html>
        <?php
    } else {
        echo '<script>parent.location="../modele/deconnect.php";</script>';
    }
} else {
    echo '<script>parent.location="../modele/deconnect.php";</script>';
}
?>
