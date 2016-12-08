<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @global type $bdd
 * @return array
 */
function allsBacs() {
    global $bdd;
    $bacs = array();
    $res = $bdd->query('select nom_bac from baccalaureat');
    while ($bc = $res->fetch()) {
        array_push($bacs, $bc['nom_bac']);
    }
    $res->closeCursor();
    return $bacs;
}

/**
 * 
 * @global type $bdd
 * @param type $idf
 */
function libererFiliereBacs($idf) {
    global $bdd;
    $res = $bdd->prepare('delete from fil_bac where id_f=:idf');
    $res->execute(array('idf' => $idf));
    $res->closeCursor();
}

/**
 * 
 */
function corresBacFil($idf, $tabBac) {
    global $bdd;
    if (!empty($tabBac)) {
        foreach ($tabBac as $bac) {
            $res = $bdd->prepare('insert into fil_bac(id_f,nom_bac) values(:idf,:nmbc)');
            $res->execute(array('idf' => $idf, 'nmbc' => $bac));
        }
    }
}
/**
 * 
 * @global type $bdd
 * @param type $idf
 * @param type $tabBac
 */
function suppBacFil($idf, $tabBac){
    global $bdd;
    if (!empty($tabBac)) {
        foreach ($tabBac as $bac) {
            $res = $bdd->prepare('delete from fil_bac where (id_f,nom_bac)=(:idf,:nmbc)');
            $res->execute(array('idf' => $idf, 'nmbc' => $bac));
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>page departement</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="../files/styles/page_dept_style.css"/>
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

                if (isset($_GET['id'])) {
                    $id_d = htmlspecialchars($_GET['id']);
                    include_once '../modele/classes/Personne.class.php';
                    include_once '../modele/classes/Professeur.class.php';
                    include_once '../modele/classes/Departement.class.php';
                    $dept = new Departement();
                    if ($dept->recuperer_departement($id_d)) {
                        ?>
                        <nav class="nav_dep">
                            <a  id="titre_d_f" href="page_dept.php?id=<?php echo $dept->getId(); ?>"><em>departement :: &nbsp;</em><?php echo $dept->getNom(); ?></a>
                            <input type="button" name="options_dep" id="options_dep" value="options"/>
                            <ul id="lu_options_dep">
                                <form  action="page_dept.php?id=<?php echo $dept->getId(); ?>" method="post">
                                    <li><input type="submit" id="sup_dep" name="sup_dep" value="supprimer departement"/></li>
                                    <li><input type="submit" id="mod_dep" name="mod_dep" value="modifier departement"/></li>
                                    <li><input type="submit" id="toutes_fils" name="toutes_fils" value="toutes filieres"/></li>
                                    <li><input type="submit" id="ajt_fils" name="ajt_fils" value="ajouter filiere"/></li>
                                    <li><input type="hidden" name="name_fil_ajt" id="name_fil_ajt"/></li>

                                    <div id="filiere_de" style="display: none;">
                                        <li><input type="submit" id="sup_filiere" name="sup_filiere"  value="supprimer filiere" onclick="if (!confirm('confirmation du supprimer filiere'))
                                                    return false;"/></li>
                                        <li><input type="submit" id="modif_filiere" name="modif_filiere"  value="modifier filiere"/></li>
                                        <li><input type="submit" id="modif_bacs_filiere" name="modif_bacs_filiere"  value="correspondances Bac"/></li>
                                        <li><input type="hidden" value="-1" name="id_filiere" id="id_filiere"/></li>
                                    </div>
                                </form>
                            </ul>
                        </nav>
                        <div class="corps_dep">
                            <?php
                            if (isset($_GET['idf'])) {
                                $idf = htmlspecialchars($_GET['idf']);
                                $fil = $dept->recupererFiliere($idf);
                                if ($fil != FALSE) {
                                    if (isset($_GET['bacs'])) {
                                        $bacsGet = htmlspecialchars($_GET['bacs']);
                                        $bacsA = str_split($bacsGet);
                                        $bc = '';
                                        $bacs = array();
                                        /* on fait le traitement de spilit() car elle ne marche pas */
                                        for ($i = 0; $i < count($bacsA); $i++) {
                                            if ($i == 0) {
                                                array_push($bacs, '0');
                                                $i++;
                                                continue;
                                            }
                                            if (strcmp($bacsA[$i], ',') == 0) {
                                                array_push($bacs, $bc);
                                                $bc = '';
                                            } else {
                                                $bc.=$bacsA[$i];
                                            }
                                        }
                                        if($i>1 and count($bacs)>1){
                                          array_push($bacs, $bc);  
                                        }
                                        if (empty($bacs)) {
                                            echo '<script>alert("Erreur: aucun choix du bac envoyer");</script>';
                                        } elseif (count($bacs) == 1 and strcmp($bacs[0], '0') == 0) {
                                            libererFiliereBacs($idf);
                                            echo '<script>alert("modification terminee. \naucun bac pour cette filiere\n tous etudiants de cette filiere sont supprimes");</script>';
                                        } elseif (count($bacs) > 1) {
                                            $bacs = array_splice($bacs, 1);
                                            $bacsBdd = allsBacs();
                                            $diff = array_diff($bacs, $bacsBdd);
                                            if (empty($diff)) {
                                            $bacsFil = $dept->getBacsFiliere($idf);
                                            $bacsSup=  array_diff($bacsFil, $bacs);
                                            $bacsAjt=array_diff($bacs,$bacsFil);
                                             corresBacFil($idf, $bacsAjt);
                                             suppBacFil($idf, $bacsSup);
                                             $bacsajt=  join($bacsAjt, '\n');
                                             $bacssup=  join($bacsSup, '\n');
                                                echo '<script>alert("modification terminer avec :\n les bacs supprimer ->\n'.$bacssup.'\nles bacs ajouter ->\n'.$bacsajt.'");</script>';
                                            } else {
                                                echo '<script>alert("Erreur: Bac envoyer non trouve dans les types validants!");</script>';
                                            }
                                        }
                                        echo '<script>location="page_dept.php?id='.$id_d.'&idf='.$idf.'";</script>';
                                    } elseif (isset($_POST['modfi_nom_cor_fil'])) {
                                        /**/
                                        try {
                                            $nom_ff = strtoupper(htmlspecialchars($_POST['nom_ff']));
                                            if (!(strlen($nom_ff) > 0)) {
                                                throw new Exception('Erreur: nom du filiere vide');
                                            }
                                            if (!$dept->updateNomFilBDD($fil['id_f'], $nom_ff)) {
                                                throw new Exception('Erreur: pas de modification');
                                            }
                                            echo '<script>alert("modification filiere terminee avec succes");location="page_dept.php?id=' . $dept->getId() . '"</script>';
                                            if (isset($_POST['coor_ff'])) {
                                                $cin_coor_f = htmlspecialchars($_POST['coor_ff']);
                                                $pr = new Professeur();
                                                $pr->recuperer_Professeur($cin_coor_f);
                                                $pr->setCoordinateurFilBDD();
                                            }
                                        } catch (Exception $e) {
                                            echo '<script>alert("' . $e->getMessage() . '");</script>';
                                        }
                                        /**/
                                    } else {
                                        ?>

                                        <center><p class="tit_mds">modules</p></center>
                                        <?php
                                        $modules = $dept->getModulesFiliere($fil['id_f']);
                                        $i = 2;
                                        if (empty($modules)) {
                                            echo '<script>location="page_dept.php?id=' . $id_d . '";</script>';
                                        } else {
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
                                                            <fieldset><legend>Semestre <?php echo $nums; ?></legend>
                                                                <?php
                                                                $nbmd = count($semestre);
                                                                $cmp = 0;
                                                                foreach ($semestre as $modul) {
                                                                    $idMd = $modul['id_m'];
                                                                    $nomMd = $modul['nom_mod'];
                                                                    $eqMd = $modul['id_eq'];
                                                                    $idFl = $modul['id_f'];
                                                                    $cin_res = $modul['cin_p'];
                                                                    ?>
                                                                    <p onclick="modifierModuleFiliere('<?php echo $dept->getId(); ?>', '<?php echo $idFl; ?>', '<?php echo $idMd; ?>', '<?php echo $cin_res; ?>');"><em> <?php echo $idMd; ?> </em><strong title="<?php echo $nomMd; ?>"> <?php echo substr($nomMd, 0, 10); ?> </strong><i> -> <?php echo $eqMd; ?> </i></p>
                                                                    <!--on peut ajouter le nom du prof responsable-->
                                                                    <?php
                                                                    if ($cmp == $nbmd - 1) {
                                                                        while ($cmp < 6) {
                                                                            echo '<p>&nbsp;</p>';
                                                                            $cmp++;
                                                                        }
                                                                    }
                                                                    $cmp++;
                                                                }
                                                                ?>
                                                            </fieldset>
                                                        </div>
                                                        <?php if ($test != 0) { ?>
                                                        </fieldset>
                                                    </div>
                                                    <?php
                                                }
                                                $i++;
                                            }
                                        }
                                        ?>
                                        <div id="page_update_module">
                                            <iframe src="" id="fen_mdl"></iframe>
                                            <input type="button" value="Annuler" id="btn_dis"/>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <!--lancer le script qui permet d'afficher les options pour une filiere -->
                                    <script>
                                                    document.getElementById('filiere_de').style.display = "block";
                                                    document.getElementById('titre_d_f').innerHTML += "<em> &nbsp;::Filiere :: &nbsp;<?php echo $fil['nom_f']; ?></em>";
                                                    document.getElementById('id_filiere').value = "<?php echo $fil['id_f']; ?>";

                                                    function modifierModuleFiliere(id_d, id_f, id_m, cin_p) {
                                                        var div = document.getElementById('page_update_module'), fen = document.getElementById('fen_mdl');
                                                        div.style.display = "inline-block";
                                                        fen.src = "modif_mod.php?idd=" + id_d + "&idf=" + id_f + "&idm=" + id_m + "&prof=" + cin_p;
                                                    }
                                                    var btn = document.getElementById('btn_dis');
                                                    btn.onclick = function() {
                                                        var div = document.getElementById('page_update_module'), fen = document.getElementById('fen_mdl');
                                                        fen.src = "";
                                                        div.style.display = "none";
                                                    }
                                    </script>
                                    <?php
                                } else {
                                    echo '<p>Erreur : Filiere Non Exite !!! </p>';
                                }
                            } elseif (isset($_POST['sup_filiere'])) {
                                /**/
                                $id_f = htmlspecialchars($_POST['id_filiere']);
                                $filiere = $dept->recupererFiliere($id_f);
                                if ($filiere != FALSE) {
                                    if ($dept->supprimerFiliere($id_f)) {
                                        $msg = $filiere['nom_f'];
                                        echo '<script>alert("Filiere ' . $msg . ' a ete suprimee");location="page_dept.php?id=' . $dept->getId() . '"</script>';
                                    }
                                }
                                /**/
                            } elseif (isset($_POST['modif_filiere'])) {
                                /**/
                                $id_f = htmlspecialchars($_POST['id_filiere']);
                                $filiere = $dept->recupererFiliere($id_f);
                                if ($filiere != FALSE) {
                                    $profs = $dept->tousProfesseurs();
                                    ?>
                                    <form  id="form_modif_dept" action="page_dept.php?id=<?php echo $dept->getId(); ?>&idf=<?php echo $filiere['id_f']; ?>" method="post" style="background-color: #009999;">
                                        <p><em>Nom Filiere</em><input type="text" name="nom_ff" id="nom_ff" value="<?php echo $filiere['nom_f'] ?>"/></p>
                                        <p><em>Coordonateur Filiere</em>
                                            <select name="coor_ff">
                                                <?php
                                                if (!empty($profs)) {
                                                    foreach ($profs as $prf) {
                                                        if (strcmp($prf->get_id_f(), $filiere['id_f']) == 0) {
                                                            $sel = '';
                                                            if ($prf->corActuelFiliere() != FALSE) {
                                                                $sel = 'selected=""';
                                                            }
                                                            echo '<option value="' . $prf->getCin() . '" ' . $sel . '>' . $prf->getNom() . ' ' . $prf->getPrenom() . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </p>
                                        <p><input type="submit" value="Modifier" name="modfi_nom_cor_fil" onclick="if (!confirm('confirmer'))
                                                            return false;"/></p>
                                    </form> 
                                    <?php
                                }
                                /**/
                            } elseif (isset($_POST['modif_bacs_filiere'])) {
                                $id_f = htmlspecialchars($_POST['id_filiere']);
                                $filiere = $dept->recupererFiliere($id_f);
                                if ($filiere != FALSE) {
                                    $nmFil = $filiere['nom_f'];
                                    $allBacs = allsBacs();
                                    if (!empty($allBacs)) {
                                        $bacsFil = $dept->getBacsFiliere($id_f);
                                        $bacs = array_diff($allBacs, $bacsFil);
                                        ?>
                                        <div class="bacs">Bacs</div>
                                        <div class="dropper">
                                            &nbsp;
                                            <?php
                                            foreach ($bacs as $bc) {
                                                ?>
                                                <div class="draggable"><?php echo $bc; ?></div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="bacs_fils"> <?php echo $filiere['nom_f']; ?> Bacs</div>
                                        <div class="dropper" id="bacs_fils">
                                            &nbsp;
                                            <?php
                                            foreach ($bacsFil as $bc) {
                                                ?>
                                                <div class="draggable"><?php echo $bc; ?></div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="btn_mod_bacs">
                                            <center><input type="button" id="vald_bacs_fils" value="valider"/></center>
                                        </div>
                                        <script>
                                                        (function() {
                                                            var dndHandler = {
                                                                draggedElement: null, // Propriete pointant vers l'element en cours de deplacement
                                                                applyDragEvents: function(element) {
                                                                    element.draggable = true;
                                                                    var dndHandler = this; // Cette variable est necessaire pour que l'evenement 'dragstart' accede facilement au namespace 'dndHandler' 
                                                                    element.addEventListener('dragstart', function(e) {
                                                                        dndHandler.draggedElement = e.target; // On sauvegarde l'element en cours de deplacement
                                                                        e.dataTransfer.setData('text/plain', ''); // Necessaire pour Firefox
                                                                    }, false);
                                                                    element.addEventListener('drop', function(e) {
                                                                        e.stopPropagation();
                                                                        /* On stoppe la propagation de l'evenement pour empecher la zone de drop d'agir,pour eviter le probleme 
                                                                         * << un element dragable recoit un element dropable; un Bac devient comme une filiere>>
                                                                         * """si on drop une Bac sur un autre bac2 ,don bac2 sa sera une zone de droppe """.
                                                                         */
                                                                    }, false);
                                                                },
                                                                applyDropEvents: function(dropper) {
                                                                    dropper.addEventListener('dragover', function(e) {
                                                                        e.preventDefault(); // On autorise le drop d'elements
                                                                        this.className = 'dropper drop_hover';
                                                                        // Et on applique le style adequat a notre zone de drop "Filiere" quand un element la survole
                                                                    }, false);

                                                                    dropper.addEventListener('dragleave', function() {
                                                                        this.className = 'dropper'; // On revient au style de base lorsque l'element quitte la zone de drop
                                                                    });

                                                                    dropper.addEventListener('drop', function(e) {
                                                                        var target = e.target,
                                                                                draggedElement = dndHandler.draggedElement, // Recuperation de l'element concerne
                                                                                clonedElement = draggedElement.cloneNode(true); // On cree immediatement le clone de cet element
                                                                        /*while(target.className.indexOf('dropper') == -1) { // Cette boucle permet de remonter jusqu'a la zone de drop parente
                                                                         target = target.parentNode;
                                                                         }*/// c'est la sulotion 2.du probleme addDrop au elment qui est en parametres du applyDragEvents.
                                                                        target.className = 'dropper'; // Application du style par defaut
                                                                        clonedElement = target.appendChild(clonedElement); // Ajout de l'element clone a la zone de drop actuelle
                                                                        dndHandler.applyDragEvents(clonedElement); // Nouvelle application des evenements qui ont ete perdus lors du cloneNode()
                                                                        draggedElement.parentNode.removeChild(draggedElement); //suppression de l'element d'origine (draguee)
                                                                    });
                                                                }
                                                            };
                                                            var elements = document.querySelectorAll('.draggable'),
                                                                    elementsLen = elements.length;
                                                            for (var i = 0; i < elementsLen; i++) {
                                                                dndHandler.applyDragEvents(elements[i]); // Application des parametres necessaires aux elements deplacables.
                                                            }
                                                            var droppers = document.querySelectorAll('.dropper'),
                                                                    droppersLen = droppers.length;
                                                            for (var i = 0; i < droppersLen; i++) {
                                                                dndHandler.applyDropEvents(droppers[i]); // Application des evenements necessaires aux zones de drop
                                                            }
                                                            /*gerer l'evenemet click du button valider*/
                                                            var btnValid = document.getElementById('vald_bacs_fils');
                                                            btnValid.onclick = function() {
                                                                if (!confirm('etes-vous sur de modifier la correspondance filiere-bac ?')) {
                                                                    return false;
                                                                }
                                                                var divBacsFil = document.getElementById('bacs_fils'),
                                                                        bacsFil = divBacsFil.getElementsByTagName('div'),
                                                                        n = bacsFil.length;
                                                                var bacs = '';
                                                                for (var cmp = 0; cmp < n; cmp++) {
                                                                    bacs += ',' + bacsFil[cmp].textContent;
                                                                }
                                                                if (confirm('les types de Bacs selection pour la filiere "<?php echo $nmFil; ?>" sont:\n' + bacs.substring(1))) {
                                                                    bacs = 'bacs=0' + bacs;
                                                                    location = "page_dept.php?id=<?php echo $dept->getId(); ?>&idf=<?php echo$id_f; ?>&" + bacs;
                                                                }
                                                                return false;
                                                            };
                                                        })();
                                        </script>

                                        <?php
                                    } else {
                                        echo '<p>aucun type de bac trouve dans cette Fac</p>';
                                    }
                                } else {
                                    echo '<p>Filiere non trouve</p>';
                                }
                            } elseif (isset($_POST['sup_dep'])) {
                                /**/
                                if ($dept->supprimer()) {
                                    echo '<script>alert("departement supprimee");location="../gerer_sys.php";</script>';
                                } else {
                                    echo '<script>alert("Erreur: departement non supprimee !!");location="../gerer_sys.php";</script>';
                                }
                                /**/
                            } elseif (isset($_POST['mod_dep'])) {
                                /**/
                                $profs = $dept->tousProfesseurs();
                                ?>
                                <form  id="form_modif_dept" action="page_dept.php?id=<?php echo $dept->getId(); ?>" method="post">
                                    <p><em>Nom departement</em><input type="text" name="nom_d" id="nom_d" value="<?php echo $dept->getNom(); ?>"/></p>
                                    <p><em>Cheuf departement</em>
                                        <select name="cheuf_d">
                                            <?php
                                            if (!empty($profs)) {
                                                foreach ($profs as $prf) {
                                                    $sel = '';
                                                    if ($prf->chefActuelDepartemet() != FALSE) {
                                                        $sel = 'selected=""';
                                                    }
                                                    echo '<option value="' . $prf->getCin() . '" ' . $sel . '>' . $prf->getNom() . ' ' . $prf->getPrenom() . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </p>
                                    <p><input type="submit" value="Modifier" name="modfi_nom_chef_dept" onclick="if (!confirm('confirmer'))
                                                        return false;"/></p>
                                </form>
                                <?php
                                /**/
                            } elseif (isset($_POST['ajt_fils'])) {
                                /**/
                                try {
                                    if (isset($_POST['name_fil_ajt']) and strlen($_POST['name_fil_ajt']) > 1) {
                                        $nom_ff = htmlspecialchars($_POST['name_fil_ajt']);
                                        $dept->ajouteFiliere($nom_ff);
                                    } else {
                                        throw new Exception('nom du filiere no valide');
                                    }
                                } catch (Exception $e) {
                                    echo '<script>alert("Erreur:' . $e->getMessage() . '");location="page_dept.php?id=' . $dept->getId() . '";</script>';
                                }
                                echo '<script>alert("filiere a bien ajoutee");location="page_dept.php?id=' . $dept->getId() . '";</script>';
                                /**/
                            } elseif (isset($_POST['modfi_nom_chef_dept'])) {
                                /**/
                                try {
                                    $nom_dp = strtoupper(htmlspecialchars($_POST['nom_d']));
                                    if (!(strlen($nom_dp) > 0)) {
                                        throw new Exception('Erreur: nom du departement vide');
                                    }
                                    if (!$dept->updateNomBDD($nom_dp)) {
                                        throw new Exception('Erreur: pas de modification');
                                    }
                                    echo '<script>alert("modification terminee avec succes");location="page_dept.php?id=' . $dept->getId() . '"</script>';
                                    if (isset($_POST['cheuf_d'])) {
                                        $cin_cheuf_dp = htmlspecialchars($_POST['cheuf_d']);
                                        $pr = new Professeur();
                                        $pr->recuperer_Professeur($cin_cheuf_dp);
                                        $pr->setCheufDeptBDD();
                                    }
                                } catch (Exception $e) {
                                    echo '<script>alert("' . $e->getMessage() . '");</script>';
                                }
                                /**/
                            } else {
                                /**/
                                $fils = $dept->toutesFilieres();
                                if (empty($fils)) {
                                    echo '<p> departement vide</p>';
                                } else {
                                    ?>
                                    <p class="fils_departement">les filieres du departement</p>
                                    <center>
                                        <?php
                                        foreach ($fils as $fil) {
                                            ?>
                                            <a class="nms_fils" href="page_dept.php?id=<?php echo $dept->getId() . '&idf=' . $fil['id_f']; ?>"><?php echo $fil['nom_f'] ?></a>
                                            <?php
                                        }
                                        ?>
                                    </center>
                                    <?php
                                }
                                /**/
                            }
                            ?>
                        </div>
                        <script>
                                            function gererNav() {
                                                var lu_op_dep = document.getElementById('lu_options_dep'),
                                                        op_dep_btn = document.getElementById('options_dep'),
                                                        sup_dep = document.getElementById('sup_dep'),
                                                        name_fil_ajt = document.getElementById('name_fil_ajt'),
                                                        ajt_fils = document.getElementById('ajt_fils');
                                                op_dep_btn.onmouseover = function() {
                                                    lu_op_dep.style.display = "inline-block";
                                                };
                                                op_dep_btn.onmouseout = function() {
                                                    lu_op_dep.style.display = "none";
                                                };
                                                lu_op_dep.onmouseover = function() {
                                                    this.style.display = "inline-block";
                                                };
                                                lu_op_dep.onmouseout = function() {
                                                    this.style.display = "none";
                                                };
                                                ajt_fils.onclick = function() {
                                                    var nom_f = '';
                                                    while (nom_f.length < 1) {
                                                        nom_f = prompt('entrer le nom du filiere');
                                                        if (nom_f === null) {
                                                            return false;
                                                        }
                                                    }
                                                    name_fil_ajt.value = nom_f;
                                                };
                                                sup_dep.onclick = function() {
                                                    if (!confirm("attention la suppression du departement\n supprime aussi ses filiers et ses professeurs!!\n vous etes sur de votre choix?")) {
                                                        return  false;
                                                    }
                                                };
                                            }
                                            (function() {
                                                gererNav();
                                            })();
                        </script>

                        <?php
                    } else {
                        echo '<p>departement non existe</p>';
                    }
                } else {
                    echo '<p>Erreur: adresse invalide</p>';
                }
                /**
                 * 
                 */
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