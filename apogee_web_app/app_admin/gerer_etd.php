<?php
session_start();
include_once './modele/connect_bdd.php';

function ttFiliere() {
    global $bdd;
    $result = $bdd->query('select nom_f from filiere');
    while ($don = $result->fetch()) {
        $fil = $don['nom_f'];
        echo '<option value="' . $fil . '">' . $fil . '</option>';
    }
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
        include_once ('./modele/verify_connexion.php');
        if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'fonction_ad' => $fct))) {
            ?>

            <head>
                <meta charset="utf-8"/>
                <title>gerer les etudiants</title>
                <link rel="stylesheet" href="files/styles/gerer_res_etd_style.css"/>
            </head>
            <body>
                <nav class="lien_etd">
                    <a href="gerer_etd/affich_etd.php" target="fen" id="aff_etd" onmouseover="afficheDisparaitre('options_aff', 'block');" onmouseout="afficheDisparaitre('options_aff', 'none');"><label>Afficher tous les etudiants</label></a>
                    <p id="options_aff" onmouseover="afficheDisparaitre('options_aff', 'block');" onmouseout="afficheDisparaitre('options_aff', 'none');">
                        <label>Filiere</label><select id="filiere_aff">
                            <option value="tous">toutes filieres</option>
                            <?php ttFiliere(); ?>
                        </select>
                        <label>Inscrire</label><input type="radio" name="ins_etd" id="ins_radio" value="ins" checked=""/><br/>
                        <label>PreInscrire</label><input type="radio" name="ins_etd" id="preins_radio" value="non_ins"/>
                    </p>
                    <a href="gerer_etd/ajout_etd.php" target="fen" ><label>Ajouter un etudiant</label></a>
                    <article>
                        <h1 >Rechercher</h1>
                        <p>
                            <label>Nom</label><input type="search" id="nom_etd"/>
                            <label>CNE</label><input type="search" id="cne_etd"/>
                            <label>Filiere</label><select name="fil_etd" id="fil_etd">
                                <option value="tous">toutes filieres</option>
                                <?php ttFiliere(); ?>
                            </select>
                        </p>
                    </article>
                    <a href="gerer_etd/gerer_insr.php" target="fen" ><label>Gerer Inscription</label></a>

                </nav>
                <iframe name="fen" src="gerer_etd/affich_etd.php" id="fen"></iframe>
                <script>
                        var nom_etd = document.getElementById('nom_etd'),
                                cne_etd = document.getElementById('cne_etd'),
                                fil_etd = document.getElementById('fil_etd'),
                                frame = document.getElementById('fen');
                        var aff_etd = document.getElementById('aff_etd');
                        aff_etd.onclick = function() {
                            var ins_radio = document.getElementById('ins_radio'),
                                    filiere_aff = document.getElementById('filiere_aff');
                            if (ins_radio.checked) {
                                this.href = 'gerer_etd/affich_etd.php?nom_f=' + filiere_aff.value + '&ins=true';
                            } else {
                                this.href = 'gerer_etd/affich_etd.php?nom_f=' + filiere_aff.value + '&ins=false';
                            }
                        };
                        nom_etd.onkeyup = function() {
                            var nom = this.value;
                            var cne = cne_etd.value;
                            var fil = fil_etd.value;
                            var lien = getLien(nom, cne, fil);
                            if (lien.length > 0) {
                                frame.src = './gerer_etd/affich_etd.php' + lien;
                            }
                        };
                        cne_etd.onkeyup = function() {
                            var nom = nom_etd.value;
                            var cne = this.value;
                            var fil = fil_etd.value;
                            var lien = getLien(nom, cne, fil);
                            if (lien.length > 0) {
                                frame.src = './gerer_etd/affich_etd.php' + lien;
                            }
                        };
                        fil_etd.onchange = function() {
                            var nom = nom_etd.value;
                            var cne = cne_etd.value;
                            var fil = this.value;
                            var lien = getLien(nom, cne, fil);
                            if (lien.length > 0) {
                                frame.src = './gerer_etd/affich_etd.php' + lien;
                            }
                        };

                        /**/
                        function getLien(nom, cne, fil) {
                            var lien = '?fil=' + fil;
                            if (nom.length > 0) {
                                lien += '&nom=' + nom;
                            }
                            if (cne.length > 0) {
                                lien += '&cne=' + cne;
                            }
                            return lien;
                        }

                        /**/
                        function afficheDisparaitre(id, prop) {
                            var elm = document.getElementById(id);
                            setTimeout(function() {
                                elm.style.display = prop;
                            }, 700);
                        }
                </script>
            </body>



            <?php
        } else {
            include_once './modele/deconnect_admin.php';
            header("Location: ./accueil_admin.php");
        }
    } else {
        include_once './modele/deconnect_admin.php';
        header("Location: ./accueil_admin.php");
    }
    ?>
</html>