<?php
session_start();
include_once '../modele/connect_bdd.php';
global $bdd;
if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
    $cin = $_SESSION['ps'];
    $pwd = $_SESSION['mp'];
    $nom = $_SESSION['nom'];
    $pnom = $_SESSION['pnom'];
    $fct = $_SESSION['fct'];
    include_once ('../modele/verify_connexion.php');
    if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {
        if (isset($_POST['cin_e']) and isset($_POST['cne_e']) and isset($_POST['nom_e']) and isset($_POST['prenom_e']) and isset($_POST['daten_jj_e']) and isset($_POST['daten_mm_e']) and isset($_POST['daten_aa_e']) and isset($_POST['lieun_e']) and isset($_POST['email_e']) and isset($_POST['ntel_e']) and isset($_POST['adresse_e']) and isset($_POST['sexe_e']) and isset($_POST['nationalite_e']) and isset($_POST['nom_bac']) and isset($_POST['type_bac']) and isset($_POST['date_bac']) and isset($_POST['nat_bac']) and isset($_POST['moyenne_bac']) and isset($_POST['filiere_e']) and isset($_FILES['photo_e']) and $_FILES['photo_e']['error'] == 0) {


            $allMonths = array('*', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'decembre');

            if (($mois = array_search(strtolower($_POST['daten_mm_e']), $allMonths)) <= 0)
                header('Location:ajout_etd.php?fx=date non valides');

            include_once '../modele/classes/Personne.class.php';
            include_once ("../modele/classes/Etudiant.class.php");
            $cne_e = $_POST['cne_e'];
            $photo_e = $_FILES['photo_e'];
            $day = htmlspecialchars($_POST['daten_jj_e']);
            $year = htmlspecialchars($_POST['daten_aa_e']);
            if (intval($mois, 10) < 10)
                $mois = '0' . intval($mois, 10);
            if (intval($day, 10) < 10)
                $day = '0' . intval($day, 10);
            $dateN_e = $year . '-' . $mois . '-' . $day;
            $cin_e = htmlspecialchars($_POST['cin_e']);
            $nom_e = htmlspecialchars(strtoupper($_POST['nom_e']));
            $prenom_e = htmlspecialchars(strtoupper($_POST['prenom_e']));
            $lieun_e = htmlspecialchars($_POST['lieun_e']);
            $email_e = htmlspecialchars($_POST['email_e']);
            $adresse_e = htmlspecialchars($_POST['adresse_e']);
            $ntel_e = htmlspecialchars($_POST['ntel_e']);
            $sexe_e = htmlspecialchars($_POST['sexe_e']);
            $nationalite_e = htmlspecialchars($_POST['nationalite_e']);
            $dateins_e = date('Y-m-d');
            $nom_f = $_POST['filiere_e'];
            $nom_bac = htmlspecialchars($_POST['nom_bac']);
            $moyenne_bac = htmlspecialchars($_POST['moyenne_bac']);
            $date_bac = $_POST['date_bac'] . '-01-01';
            $type_bac = htmlspecialchars($_POST['type_bac']);
            $nat_bac = htmlspecialchars($_POST['nat_bac']);
            $etd = new Etudiant();
            $etd->remplir_etudiant($cin_e, $nom_e, $prenom_e, $dateN_e, $adresse_e, $ntel_e, $photo_e, $sexe_e, $email_e, $nationalite_e, $cne_e, $lieun_e, $nom_bac, NULL, $dateins_e, $nom_f, $moyenne_bac, $date_bac, $type_bac, $nat_bac, NULL);
            if (!$etd->stocker_etudiant()) {
                header('Location:ajout_etd.php?fx=' . $etd->getFautes());
            } else {
                /* si l'etudiant est bien stocker */
                ?>
                <html>
                    <head>
                        <meta charset="UTF-8" />
                        <title> Stocker etudiant</title>
                        <link rel="stylesheet" href="../files/styles/stk_etd_style.css"/>
                    </head>
                    <body>
                        <div><p id="cont"></p><label>etudiant jouter avec succes</label></div>
                        <script>
                            var dv = document.getElementById('cont'), i = 0;
                            var interv = setInterval(function() {
                                if (i <= 100) {
                                    dv.style.width = i + '%';
                                    i += 1;
                                } 
                            }, 15);
                            setTimeout(function(){
                                clearInterval(interv);
                                location="ajout_etd.php";
                            },2000);

                            //execute la fonction interv;
                            (function() {
                                inetrv();
                            })();
                        </script>
                    </body>
                </html>
                <?php
            }
        } else {
            ?>

            <!DOCTYPE html>
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                    <title>inscription</title>
                    <link rel="shortcut icon" type="image/x-icon" href="files/icns/fps.png" />
                    <link rel="stylesheet" href="../files/styles/ajout_etd_style.css"/>
                    <style>
                        #titre{
                            width:90%;
                            left:5%; 
                        }
                        .form_etd{
                            height: 108%;
                            bottom: -18%;
                            width:90%;
                            left:5%;
                        }
                    </style>
                </head>
                <body>
                    <p id="titre">Remplirez les champs</p>
                    <form method="post" action="ajout_etd.php" enctype="multipart/form-data" class="form_etd">
                        <!-- Grâce à enctype, le navigateur du visiteur sait qu'il s'apprête à envoyer des fichiers. -->

                        <fieldset> <legend>Informations personnelles</legend>
                            <div><label for="cin_e">CIN </label><input type="text" placeholder="cin" name="cin_e" id="cin_e" required autofocus/></div>
                            <div><label for="cne_e">CNE </label><input type="text" placeholder="cne" name="cne_e" id="cne_e" required/></div>
                            <div><label for="nom_e">Nom  </label><input type="text" placeholder="nom" name="nom_e" id="nom_e" required/></div>
                            <div><label for="prenom_e">Prenom  </label><input type="text" placeholder="prenom" name="prenom_e" id="prenom_e" required/></div>
                            <div><label for="daten_e">Date de naissance </label>
                                <select name="daten_jj_e" id="daten_jj_e"  class="ddn" required></select>
                                <select name="daten_mm_e" id="daten_mm_e"  class="ddn" required></select>
                                <input type="number" name="daten_aa_e" id="daten_aa_e" value="1993" class="ddn" required/></div>
                            <div><label for="lieun_e">Lieu de naissance </label><input type="text" name="lieun_e" id="lieun_e" placeholder="Lieu de naissance" required/></div>
                            <div><label for="email_e">Email </label><input type="email" name="email_e" id="email_e" placeholder="email" required/></div>
                            <div><label for="ntel_e">N° de téléphone </label><input type="tel" name="ntel_e" id="ntel_e" maxlength="15" placeholder="N° de téléphone" required/></div>
                            <div><label for="adresse_e">Adresse </label><input type="text" name="adresse_e" id="adresse_e" placeholder="adresse" required/></div>
                            <div><label for="homme">Sexe </label>
                                <input type="radio" value="H" name="sexe_e" id="homme" class="radio"/><label for="homme" class="sx">Homme</label>
                                <input type="radio" value="F"  name="sexe_e" id="femme" checked="true" class="radio"/><label for="femme" class="sx">Femme</label>
                            </div>
                            <div><label for="nationalite_e">Nationalité </label><input type="text" name="nationalite_e" id="nationalite_e" value="Marocaine"/></div>
                            <div><label for="photo_e">Photo </label><input type="file" name="photo_e" id="photo_e" required/></div>
                        </fieldset>
                        <fieldset> <legend>Informations concerne votre baccalaureat</legend>
                            <div><label for="nom_bac">Choix baccalaureat</label><select id="nom_bac" name="nom_bac"></select></div>
                            <div><label for="nat_bac">nationalite bac</label><input type="text" value="marocaine" id="nat_bac" name="nat_bac" required/></div>
                            <div><label for="date_bac">Annee bac</label><input type="number"  id="date_bac" value="2013" name="date_bac" required/></div>
                            <div><label for="type_bac">Type</label>
                                <input type="radio" name="type_bac" id="libre" value="libre" class="radio"/><label class="sx" for="libre">libre</label>
                                <input type="radio" name="type_bac" id="normal" value="normal" class="radio" checked="true"/><label class="sx" for="normal">normal</label> 
                            </div>
                            <div><label for="moyenne">Moyenne</label><input type="text" maxlength="5" name="moyenne_bac" id="moyenne_bac" style="width:20%;" required/>
                                <label id="mention" class="sx" style="margin-left:8.7%;">mention</label>
                            </div>
                        </fieldset>
                        <fieldset > <legend>Informations concerne votre choix </legend>
                            <div><label for="filiere_e">Filiére </label><select id="filiere_e" name="filiere_e"></select></div>
                        </fieldset>
                        <fieldset style="height:3.5%;">
                            <div>
                                <button type="submit" name="valider" id="valider" value="valider" class="btn" >valider</button>
                                <button type="reset" name="initialiser" id="initialiser" value="initialiser" class="btn">initialiser</button>
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

                        function telechargerPhoto() {
                            var reader = new FileReader(),
                                    typesImg = ['png', 'jpg', 'jpeg'],
                                    fileInput = document.getElementById('photo_e'),
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
                                    animationPhoto(img, 0, 30);
                                }
                                else {
                                    alert('teléchargez une photo de type png, jpg ou jpeg');
                                    img.style.display = 'none';
                                    //il faut de eviter ce fichier qui est pas autorisé.même alert laisse le ficher selectionné.
                                }
                            };
                        }


                        function gererDate() {
                            var j = document.getElementById('daten_jj_e'), m = document.getElementById('daten_mm_e');
                            var moisJours = {
                                'Janvier': 31, 'Fevrier': 29, 'Mars': 31, 'Avril': 30, 'Mai': 31, 'Juin': 30, 'Juillet': 31, 'Aout': 31, 'Septembre': 30, 'Octobre': 31, 'Novembre': 30, 'Decembre': 31
                            };
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

                        function gererMoyenne() {
                            var moy = document.getElementById('moyenne_bac'), ment = document.getElementById('mention');
                            moy.onkeyup = function() {
                                if (this.value.length === 2) {
                                    this.value += '.';
                                }
                            };
                            moy.onblur = function() {
                                var m = parseFloat(this.value), nan = isNaN(this.value), c;
                                if (nan || m < 10 || m > 20) {

                                    if ((c = prompt("désolé votre moyenne est fausse !!\n rentrer la moyenne", ""))) {
                                        this.value = c;
                                        moy.onblur();
                                    }
                                    else {
                                        this.value = "";
                                        moy.onblur();
                                    }
                                } else {
                                    var men;
                                    if (m < 12)
                                        men = "Passable";
                                    else if (m < 14)
                                        men = "Assez bien";
                                    else if (m < 16)
                                        men = "Bien";
                                    else
                                        men = "Très Bien";
                                    ment.textContent = '' + men;
                                }
                            };
                        }

                        /*gerer les actions des bouttons*/
                        function gererBtns() {
                            var init = document.getElementById('initialiser');
                            init.onclick = function() {
                                var imge = document.getElementById('image');
                                imge.style.display = 'none';
                            };
                            var sub = document.getElementById('valider');
                            sub.onsubmit = function() {
                                if (!gererAnneBac()) {//on ajoute tous les conditions ici !!!???
                                    alert('année bac non valide');
                                    return false;
                                }
                            };
                        }
                        /*gérer la relation entre le type du bac et les filieres */
                        function Bac(nom) {
                            this.nom = nom;
                            this.fils = new Array();
                            this.ajoutFil = function(fil) {
                                this.fils.push(fil);
                            };
                            this.afficherFils = function() {
                                var f = document.getElementById('filiere_e');
                                f.innerHTML = '';
                                var a = '';
                                for (var i = 0; i < this.fils.length; i++) {
                                    a += this.fils[i];
                                    f.innerHTML += '<option value="' + this.fils[i] + '">' + this.fils[i] + '</option>';
                                }
                            };
                        }

                        function gererBac() {
                            var bacs = new Array();
                            var b = document.getElementById('nom_bac');
                            b.innerHTML = '';
            <?php
            $resb = $bdd->query('select nom_bac from baccalaureat');
            while ($donb = $resb->fetch()) {
                echo 'var bacc=new Bac(\'' . $donb['nom_bac'] . '\');';
                ?>
                                b.innerHTML += '<option value="<?php echo $donb['nom_bac']; ?>"><?php echo $donb['nom_bac']; ?></option>';
                <?php
                $resf = $bdd->query('select nom_f from filiere where id_f=any(select id_f from fil_bac where nom_bac=\'' . $donb['nom_bac'] . '\')');
                while ($donf = $resf->fetch()) {
                    echo 'bacc.ajoutFil(\'' . $donf['nom_f'] . '\');';
                }
                echo 'bacs.push(bacc);';
            }
            $resb->closeCursor();
            $resf->closeCursor();
            ?>
                            b.onchange = function() {
                                for (var i = 0; i < bacs.length; i++) {
                                    if (bacs[i].nom === this.value) {
                                        bacs[i].afficherFils();
                                    }
                                }
                            };
                            b.options[6].selected = true;//selction par default
                            b.onchange();//execution de l'évenement change pour afficher les filieres correspondante.
                        }
                        /*gerer annee bac*/
                        function gererAnneBac() {
                            var anb = document.getElementById('date_bac');
                            if (isNaN(anb.value)) {
                                return false;
                            }
                            return true;
                        }
                        /*gererCne*/
                        function gererCne() {
                            var cne = document.getElementById('cne_e');
                            cne.onkeyup = function() {
                                var val = this.value.length;
                                if (val > 0) {
                                    document.getElementById('message').style.display = 'none';
                                }
                            };
                        }
                        //ce que va exécuter.   
                        (function() {
                            gererMoyenne();
                            gererDate();
                            telechargerPhoto();
                            gererBtns();
                            gererBac();
                            gererAnneBac();
                            gererCne();
                        })();
                    </script>
                </body>
            </html>


            <?php
        }//fin teste si on veut stocker ou remplir
    } else {
        header("Location: ../modele/deconnect_admin.php");
    }
} else {
    header("Location: ../modele/deconnect_admin.php ");
}
?>