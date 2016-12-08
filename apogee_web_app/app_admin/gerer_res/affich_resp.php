<?php
session_start();
include_once '../modele/connect_bdd.php';
global $bdd;
?>
<!DOCTYPE html>
<html>
    <?php
    if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
        $cin = $_SESSION['ps'];
        $nom = $_SESSION['nom'];
        $pnom = $_SESSION['pnom'];
        $fct = $_SESSION['fct'];
        include_once ('../modele/verify_connexion.php');
        if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'fonction_ad' => $fct))) {
            ?>
            <head>
                <meta charset="utf-8"/>
                <title> afficher admins</title>
                <link rel="stylesheet" href="../files/styles/affich_resp_style.css"/>
            </head>
            <body>
                <?php
                include_once '../modele/classes/Personne.class.php';
                include_once '../modele/classes/Admin.class.php';
                if (isset($_GET['nom'])) {
                    $nom = strtoupper(htmlspecialchars($_GET['nom']));
                    $admins = Admin::adminsLikeNom($nom);
                } else {
                    $admins = Admin::tousAdmins();
                }
                if (!empty($admins)) {
                    for ($i = 0; $i < count($admins); $i++) {
                        $ad = $admins[$i];
                        ?>
                        <p class="admin">

                            <span><em class="lab">Nom: </em><strong class="val"><?php echo $ad->getNom(); ?></strong></span>
                            <span><em class="lab">Prenom:</em><strong class="val"><?php echo $ad->getPrenom(); ?></strong></span>
                            <span><em class="lab">CIN: </em><strong class="val"><?php echo $ad->getCin(); ?></strong></span>
                            <span><em class="lab">Sexe: </em><strong class="val"><?php
                                    if (strcmp($ad->getSexe(), 'H') == 0) {
                                        echo 'Homme';
                                    } else if (strcmp($ad->getSexe(), 'F') == 0) {
                                        echo 'Femme';
                                    }
                                    ?>
                                </strong></span>

                            <span><em class="lab">Email: </em><strong class="val"><?php echo $ad->getEmail(); ?></strong></span>
                            <span><em class="lab">N° telephn: </em><strong class="val"><?php echo $ad->getNtel(); ?></strong></span>
                            <span><em class="lab">Adresse: </em><strong class="val"><?php echo $ad->getAdresse(); ?></strong></span>
                            <span><em class="lab">Nationalite: </em><strong class="val"><?php echo $ad->getNationalite(); ?></strong></span>

                            <span><em class="lab">DateN:</em><strong class="val"><?php echo $ad->getDateN(); ?></strong></span>
                            <span><em class="lab">Fonction</em><strong class="val"><?php
                                    $f = $ad->getFonction();
                                    if (strcmp(strtolower($ad->getFonction()), 'responsable') == 0) {
                                        $f.='_' . $ad->getNomFil();
                                    }
                                    echo $f;
                                    ?></strong></span>
                            <span>
                                <input type="button" value="photo" id="pht_ad" onclick="gererButPhoto('<?php echo $ad->getCin(); ?>');"/>
                            </span>
                            <span>
                                <input type="button" value="modifier" id="mod_ad" onclick="gererButModifier('<?php echo $ad->getCin(); ?>');"/>
                                <input type="button" value="supprimer" id="sup_ad" onclick="gererButSupprimer('<?php echo $ad->getCin(); ?>', '<?php echo $ad->getNom() . ' ' . $ad->getPrenom(); ?>');"/>
                            </span>

                        </p>
                        <hr size="4%"/>
                        <?php
                    }
                } else {
                    echo '<p style="color:red;">aucun responsable trouve</p>';
                }
                ?>
                <div class="image" id="im"><img id="img" src=""/></div>
                <div class="image" id="divmod"> 
                    <img id="imgmod" src=""/>
                    <em id="em_fct">Fonction</em><select name="fct_select" id="fct_select" onchange="gererFct_Fil(this);">
                        <option value="admin" selected="">admin</option>
                        <option value="responsable">responsable filiere</option>
                    </select>
                    <em id="em_fil">Filiere</em><select name="fil_select" id="fil_select">
                        <?php
                        $result = $bdd->query('select nom_f from filiere');
                        while ($don = $result->fetch()) {
                            $fil = $don['nom_f'];
                            echo '<option value="' . $fil . '">' . $fil . '</option>';
                        }
                        $result->closeCursor();
                        ?>
                    </select>
                    <input type="button" id="modifier_resp" name="modifier_resp" value="modifier" onclick="modifier();"/>
                    <input type="button" id="anuuler_resp" name="anuuler_resp" value="annuler" onclick="annuler();"/>
                </div>
                <script>
                                    var im = document.getElementById('im');
                                    var lien = '../modele/modif_resp.php?';
                                    im.onclick = function() {
                                        im.style.display = 'none';
                                    };
                                    //afficher la photo de l'admin
                                    function gererButPhoto(id) {
                                        var img = document.getElementById('img');
                                        img.src = '../modele/moteur_img.php?id=' + id;
                                        im.style.display = 'inline-block';
                                    }
                                    //redirige vers la page du modification avec l'admin a modifier a le cin id
                                    function gererButModifier(id) {
                                        var divmod = document.getElementById('divmod'),
                                                imgmod = document.getElementById('imgmod');
                                        divmod.style.display = 'inline-block';
                                        imgmod.src = '../modele/moteur_img.php?id=' + id;
                                        lien += 'id=' + id;
                                    }
                                    //gerer le boutton supprimer un responsable(admin)
                                    function gererButSupprimer(id, nom) {
                                        if (confirm('vous voulez supprimer :\n' + nom)) {
                                            document.location = '../modele/supp_admin.php?id=' + id;
                                        }
                                    }
                                    //pour le button annuler du partie modification
                                    function annuler() {
                                        var divmod = document.getElementById('divmod');
                                        divmod.style.display = 'none';
                                    }
                                    //pour le button modifier du partie modification
                                    function modifier() {
                                        if (confirm('vous voulez modifier ')) {
                                            var elm = document.getElementById('fct_select'),
                                                    fil = document.getElementById('fil_select');
                                            if (elm.value === 'admin') {
                                                lien += '&fonct=' + elm.value;
                                            } else if (elm.value === 'responsable') {
                                                lien += '&fonct=' + elm.value + '&fil=' + fil.value;
                                            }
                                            document.location = lien;
                                        }
                                    }
                                    //gerer admin ou responsable filiere
                                    function gererFct_Fil(elm) {
                                        var em = document.getElementById('em_fil'),
                                                fil = document.getElementById('fil_select');
                                        if (elm.value === 'admin') {
                                            em.style.display = 'none';
                                            fil.style.display = 'none';
                                        } else if (elm.value === 'responsable') {
                                            em.style.display = 'inline-block';
                                            fil.style.display = 'inline-block';
                                        }
                                    }
                </script>
            </body>
            <?php
        } else {
            header("Location: ../modele/deconnect_admin.php");
        }
    } else {
        header("Location: ../modele/deconnect_admin.php");
    }
    ?>
</html>