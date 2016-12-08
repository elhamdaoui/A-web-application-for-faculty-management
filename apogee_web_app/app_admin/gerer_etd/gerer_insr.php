<?php
session_start();
include_once '../modele/connect_bdd.php';

function ttFiliere() {
    global $bdd;
    $result = $bdd->query('select nom_f,id_f,nb_ins_anne from filiere');
    $ret = $result->fetchAll();
    $result->closeCursor();
    return $ret;
}

function afficherEtatIns($etat) {
    if (isset($etat) and !empty($etat) and $etat != NULL) {
        echo 'active';
    } else {
        echo 'desactive';
    }
}

function modifierNbInsFiliere($id_fil, $active = FALSE) {
    global $bdd;
    $result = $bdd->prepare('update filiere set nb_ins_anne=:nb where id_f=:id');

    $nb = NULL;
    if ($active == TRUE) {
        /* recuperer l'annee actuel ,et la multiplier par 100000 pour activer l'inscri */
        $an = date('Y');
        $nb = $an * 100000;
    }
    $result->execute(array('nb' => $nb, 'id' => $id_fil));
    $result->closeCursor();
}
?>
<html>
    <?php
    if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
        $cin = $_SESSION['ps'];
        $nom = $_SESSION['nom'];
        $pnom = $_SESSION['pnom'];
        $fct = $_SESSION['fct'];
        include_once ('../modele/verify_connexion.php');
        if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'fonction_ad' => $fct))) {
            $filieres = ttFiliere();
            if (isset($_GET['c']) and (strcmp($_GET['c'], 'lan') == 0 or strcmp($_GET['c'], 'des') == 0)) {
                $c = htmlspecialchars($_GET['c']);
                if (strcmp($c, 'lan') == 0) {
                    foreach ($_POST as $nom_fil => $id_fil) {
                        modifierNbInsFiliere($id_fil, TRUE);
                    }
                    echo '<p style="align:center;height:20%;color:green;font-size:x-large;background-color:orange;border-radius:10px;">
                            Le service d\'inscription et Active<br/>
                            pour les filirees selectiones...</p>';
                    echo '<script>setTimeout(function(){
                            document.location="gerer_insr.php";
                            },2500);</script>';
                    /* pour le redirection on peut mis la page par defaut  pour gerer etd en corps */
                } else {
                    foreach ($_POST as $nom_fil => $id_fil) {
                        modifierNbInsFiliere($id_fil);
                    }
                    echo '<p style="align:center;height:20%;color:yellow;font-size:x-large;background-color:#8888dd;border-radius:10px;">
                            Le service d\'inscription est desactive<br/>
                            pour les filirees selectiones...</p>';
                    echo '<script>setTimeout(function(){
                            document.location="gerer_insr.php";
                            },2500);</script>';
                    /* pour le redirection on peut mis la page par defaut  pour gerer etd en corps */
                }
            } else {
                ?>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                    <title>lancer/desactive inscription</title>
                    <link rel="stylesheet" href="../files/styles/gerer_insr_style.css"/>
                </head>
                <body>
                    <form action="" method="post" id="form_ins">
                        <p id="titre">lancer service d'inscription</p>
                        <p><label>Filiere</label><label>etat inscription</label><label>Modifier</label> </p>
                        <div id="fils_insc">
                            <?php
                            $name = 0;
                            foreach ($filieres as $valArray) {
                                /* for($i=0;$i<3;$i++){pour tester , overflow:scroll; */
                                $name++;
                                ?>
                                <p><label for="<?php echo $valArray['nom_f']; ?>"><?php echo $valArray['nom_f']; ?></label><em><?php afficherEtatIns($valArray['nb_ins_anne']); ?></em><input type="checkbox" value="<?php echo $valArray['id_f']; ?>" name="<?php echo $valArray['nom_f']; ?>" id="<?php echo $valArray['nom_f']; ?>"/></p>

                                <?php
                                //}
                            }
                            ?>
                        </div>
                        <p><label for="tt_fils">toutes filieres</label><input type="checkbox" value="tt_fils" id="tt_fils"/></p>
                        <p id="btns"><input type="submit" value="lancer" id="lancer"/><input type="submit" value="desactiver" id="desactiver"/><input type="reset" value="annuler" id="annuler"/></p>
                    </form>
                    <script>
                        var ttfil = document.getElementById('tt_fils'), fil = '';

                        /*gerer l'evenement change pour le bouton radio tous filieres*/
                        ttfil.onchange = function() {
                            var div = document.getElementById('fils_insc');
                            var radios = div.getElementsByTagName('input');
                            for (var i = 0; i < radios.length; i++) {
                                radios[i].checked = this.checked;
                            }
                        };

                        /*remplir la variable 'fil' par les noms des filiers*/
                        function filieresSelectiones() {
                            var div = document.getElementById('fils_insc');
                            var radios = div.getElementsByTagName('input');
                            fil = '';
                            for (var i = 0; i < radios.length; i++) {
                                if (radios[i].checked) {
                                    fil += '"' + radios[i].name + '", ';
                                }
                            }
                        }
                        /*gere l'evenements click de tous les boutons 'annuler', 'lancer', et 'desactiver'.*/
                        function gererBtnsIns() {
                            var form_ins = document.getElementById('form_ins'),
                                    lancer = document.getElementById('lancer'),
                                    desactive = document.getElementById('desactiver'),
                                    annuler = document.getElementById('annuler');
                            /*bouton lancer*/
                            lancer.onclick = function() {
                                filieresSelectiones();
                                if (fil.length > 0) {
                                    fil = 'le nb d\'inscription  pour les fileres:\n '
                                            + fil + ' va etre initialiser a 0 ,\n'
                                            + 'et le service d\inscription va etre active!';
                                    if (confirm(fil)) {
                                        form_ins.action = "gerer_insr.php?c=lan";
                                    } else {
                                        return false;
                                    }
                                } else {
                                    alert('aucune modification');
                                    return false;
                                }
                            };
                            /*bouton desactiver*/
                            desactive.onclick = function() {
                                filieresSelectiones();
                                if (fil.length > 0) {
                                    fil = 'le service  d\'inscription  pour les fileres:\n '
                                            + fil + ' va etre desactiver 0 !';
                                    if (confirm(fil)) {
                                        form_ins.action = "gerer_insr.php?c=des";
                                    } else {
                                        return false;
                                    }
                                } else {
                                    alert('aucune modification');
                                    return false;
                                }
                            };
                            /*bouton annuler*/
                            annuler.onclick = function() {
                                document.location='../gerer_etd/affich_etd.php';
                            };
                        }
                        /*fonction anonyme: ce que va execute*/
                        (function() {
                            gererBtnsIns();
                        })();
                    </script>
                </body>
                <?php 
            }
        } else {
            include_once '../modele/deconnect_admin.php';
            header("Location: ../accueil_admin.php");
        }
    } else {
        include_once '../modele/deconnect_admin.php';
        header("Location: ../accueil_admin.php");
    }
    ?>
</html>
