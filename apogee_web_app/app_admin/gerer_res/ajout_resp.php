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
            if (isset($_POST['cin_ad']) and isset($_POST['nom_ad']) and isset($_POST['prenom_ad']) and
                    isset($_POST['daten_jj_ad']) and isset($_POST['daten_mm_ad']) and
                    isset($_POST['daten_aa_ad']) and
                    isset($_POST['email_ad']) and isset($_POST['ntel_ad']) and isset($_POST['adresse_ad']) and
                    isset($_POST['sexe_ad']) and isset($_POST['nationalite_ad']) and isset($_FILES['photo_ad']) and isset($_POST['fonction_ad'])) {
                $cin_ad = htmlspecialchars($_POST['cin_ad']);
                $nom_ad = htmlspecialchars($_POST['nom_ad']);
                $prenom_ad = htmlspecialchars($_POST['prenom_ad']);
                $jj = htmlspecialchars($_POST['daten_jj_ad']);
                $mm = htmlspecialchars($_POST['daten_mm_ad']);
                $aa = htmlspecialchars($_POST['daten_aa_ad']);
                $dateN_ad = '';
                $email_ad = htmlspecialchars($_POST['email_ad']);
                $ntel_ad = htmlspecialchars($_POST['ntel_ad']);
                $adresse_ad = htmlspecialchars($_POST['adresse_ad']);
                $sexe_ad = htmlspecialchars($_POST['sexe_ad']);
                $nat_ad = htmlspecialchars($_POST['nationalite_ad']);
                $photo = $_FILES['photo_ad'];
                $fct = htmlspecialchars($_POST['fonction_ad']);
                $nom_f = null;
                if (strcmp(strtolower($fct), 'responsable') == 0) {
                    if (isset($_POST['filiere_ad'])) {
                        $nom_f = $_POST['filiere_ad'];
                    } else {
                        header('Location: ajout_resp.php?fx=cet admin est un responsable de quelle filiere !');
                    }
                }
                $allMonths = array('*', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'decembre');
                if (($mois = array_search(strtolower($mm), $allMonths)) <= 0)
                    header('Location: ajout_resp.php?fx=date non valides');
                if (intval($mois, 10) < 10)
                    $mois = '0' . intval($mois, 10);
                if (intval($jj, 10) < 10)
                    $jj = '0' . intval($jj, 10);
                $dateN_ad = $aa . '-' . $mois . '-' . $jj;

                include_once '../modele/classes/Personne.class.php';
                include_once '../modele/classes/Admin.class.php';
                $admin = new Admin();
                $admin->remplir_Admin($cin_ad, $nom_ad, $prenom_ad, $dateN_ad, $adresse_ad, $ntel_ad, $photo, $sexe_ad, $email_ad, $nat_ad, $fct, NULL, NULL, $nom_f);
                if (!$admin->stocker()) {
                    header('Location: ajout_resp.php?fx=' . $admin->getFautes());
                } else {
                    ?>
                    <head>
                        <meta charset = "UTF-8" />
                        <title> admin ajoute</title>
                        <link rel = "stylesheet" href = "../files/styles/stk_adm_style.css"/>
                    </head>
                    <body>
                        <div><p id = "cont"></p><label>admin ajouté avec succés</label></div>
                        <script>
                            var dv = document.getElementById('cont'), i = 0;
                            var interv = setInterval(function() {
                                if (i <= 100) {
                                    dv.style.width = i + '%';
                                    i += 1;
                                } else {
                                    //clearInterval(interv);
                                    i = 101;

                                    //document.location +='';
                                }
                            }, 15);

                            //execute la fonction interv;
                            (function() {
                                inetrv();
                            })();
                        </script>
                    </body>
                    <?php
                }
            } else {
                ?>
                <head>
                    <meta charset="utf-8"/>
                    <title> ajouter admins</title>
                    <link rel="stylesheet" href="../files/styles/ajout_resp_style.css"/>
                </head>
                <body>
                    <form method="post" action="ajout_resp.php" enctype="multipart/form-data">

                        <fieldset> <legend>Informations personnelles</legend>
                            <div><label for="cin_ad">CIN </label><input type="text" placeholder="cin" name="cin_ad" id="cin_ad" required autofocus/></div>
                            <div><label for="nom_ad">Nom  </label><input type="text" placeholder="nom" name="nom_ad" id="nom_ad" required/></div>
                            <div><label for="prenom_ad">Prenom  </label><input type="text" placeholder="prenom" name="prenom_ad" id="prenom_ad" required/></div>
                            <div><label for="daten_ad">Date de naissance </label>
                                <select name="daten_jj_ad" id="daten_jj_ad"  class="ddn" required></select>
                                <select name="daten_mm_ad" id="daten_mm_ad"  class="ddn" required></select>
                                <input type="number" name="daten_aa_ad" id="daten_aa_ad" value="1980" class="ddn" required/></div>
                            <div><label for="email_ad">Email </label><input type="email" name="email_ad" id="email_ad" placeholder="email" required/></div>
                            <div><label for="ntel_ad">N° de téléphone </label><input type="tel" name="ntel_ad" id="ntel_ad" maxlength="15" placeholder="N° de téléphone" required/></div>
                            <div><label for="adresse_ad">Adresse </label><input type="text" name="adresse_ad" id="adresse_ad" placeholder="adresse" required/></div>
                            <div><label for="homme">Sexe </label>
                                <input type="radio" value="H" name="sexe_ad" id="homme" class="radio"/><label for="homme" class="sx">Homme</label>
                                <input type="radio" value="F"  name="sexe_ad" id="femme" checked="true" class="radio"/><label for="femme" class="sx">Femme</label>
                            </div>
                            <div><label for="nationalite_ad">Nationalité </label><input type="text" name="nationalite_ad" id="nationalite_ad" value="Marocaine"/></div>
                            <div><label for="photo_ad">Photo </label><input type="file" name="photo_ad" id="photo_ad" required/></div>
                        </fieldset>
                        <fieldset > <legend>Informations fonctionnelles </legend>
                            <div><label for="fonction_ad">Fonction </label><select id="fonction_ad" name="fonction_ad">
                                    <option value="admin">admin</option>
                                    <option value="responsable">responsable filiere</option>
                                </select></div>
                            <div id="fil"><label for="filiere_ad" >Filiére </label><select id="filiere_ad" name="filiere_ad">
                                    <?php
                                    $result = $bdd->query('select nom_f from filiere');
                                    while ($don = $result->fetch()) {
                                        $fil = $don['nom_f'];
                                        echo '<option value="' . $fil . '">' . $fil . '</option>';
                                    }
                                    $result->closeCursor();
                                    ?>
                                </select></div>
                        </fieldset>
                        <fieldset class="btn">
                            <div>
                                <button type="submit" name="valider" id="valider" value="valider"  >valider</button>
                                <button type="reset" name="initialiser" id="initialiser" value="initialiser" >initialiser</button>
                            </div>
                        </fieldset>
                        <img id="image" name="image" />
                        <?php
                        if (isset($_GET['fx'])) {
                            ?>               
                            <p id="message" name="message">
                                <?php echo $_GET['fx']; ?>
                            </p>
                            <?php
                        }
                        ?>
                    </form>
                    <script>
                        function gererDate() {
                            var j = document.getElementById('daten_jj_ad'), m = document.getElementById('daten_mm_ad');
                            var moisJours = {
                                'Janvier': 31, 'Fevrier': 29, 'Mars': 31, 'Avril': 30, 'Mai': 31, 'Juin': 30, 'Juillet': 31, 'Aout': 31, 'Septembre': 30, 'Octobre': 31, 'Novembre': 30, 'Decembre': 31
                            };
                            ;
                            for (var mois in moisJours) {
                                m.innerHTML += '<option value="' + mois + '">' + mois + '</option>';
                            }
                            for (var i = 1; i <= moisJours[m.value]; i++) {
                                j.innerHTML += '<option value="' + i + '">' + i + '</option>';
                            }
                            m.onchange = function() {
                                var month = this.value;
                                j.innerHTML = '';
                                for (var i = 1; i <= moisJours[month]; i++) {
                                    j.innerHTML += '<option value="' + i + '">' + i + '</option>';
                                }
                            };
                        }

                        function gererFonction() {
                            var fonct = document.getElementById('fonction_ad'),
                                    fil = document.getElementById('fil');
                            fonct.onchange = function() {

                                if (this.value === 'admin') {
                                    fil.style.display = 'none';
                                } else if (this.value === 'responsable') {
                                    fil.style.display = 'inline-block';
                                }
                            };
                        }
                        /*animation quan on veut afficher une image*/
                        function animationPhoto(img, h, maxH) {
                            if (h === 0) {
                                img.style.display = 'inline-block';
                                if (document.getElementById('message')) {
                                    document.getElementById('message').style.display = 'none';
                                }
                            }
                            img.style.height = h + '%';
                            if (h < maxH) {
                                setTimeout(function() {
                                    animationPhoto(img, h + 1, maxH);
                                }, 30);
                            } else {
                                img.style.height = maxH + '%';
                            }
                        }
                        /*quand on telecherger une photo*/
                        function telechargerPhoto() {
                            var reader = new FileReader(),
                                    typesImg = ['png', 'jpg', 'jpeg'],
                                    fileInput = document.getElementById('photo_ad'),
                                    img = document.getElementById('image');
                            ;
                            fileInput.onchange = function() {
                                var file = this.files[0], imgType;
                                imgType = file.name.split('.');
                                imgType = imgType[imgType.length - 1];
                                if (typesImg.indexOf(imgType) !== -1) {
                                    reader.onload = function() {
                                        img.src = this.result;
                                    };
                                    reader.readAsDataURL(file);
                                    img.style.display = 'none';
                                    animationPhoto(img, 0, 35);
                                }
                                else {
                                    alert('teléchargez une photo de type png, jpg ou jpeg');
                                    img.style.display = 'none';
                                    //il faut de eviter ce fichier qui est pas autorisé.même alert laisse le ficher selectionné.
                                }
                            };
                        }

                        (function() {
                            gererDate();
                            gererFonction();
                            telechargerPhoto();
                        })();
                    </script>
                </body>
                <?php
            }
        } else {
            header("Location: ../modele/deconnect_admin.php");
        }
    } else {
         header("Location: ../modele/deconnect_admin.php");
    }
    ?>

</html>