<?php
session_start();
include_once '../modele/connect_bdd.php';

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
<!DOCTYPE html>
<html>
    <?php
    if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
        $cin = $_SESSION['ps'];
        $pwd = $_SESSION['mp'];
        $nom = $_SESSION['nom'];
        $pnom = $_SESSION['pnom'];
        $fct = $_SESSION['fct'];
        include_once ('../modele/verify_connexion.php');
        if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'pwd_ad' => $pwd, 'fonction_ad' => $fct))) {
            if (isset($_POST['ajouter_p'])) {
                if (isset($_POST['cin_p']) and isset($_POST['nom_p']) and isset($_POST['pnom_p']) and isset($_POST['sexe_p']) and isset($_POST['nat_p']) and isset($_POST['daten_jj_p']) and isset($_POST['daten_mm_p']) and isset($_POST['daten_aa_p']) and isset($_POST['email_p']) and isset($_POST['ntel_p']) and isset($_POST['fil_p']) and isset($_POST['adresse_p']) and isset($_FILES['photo_p']) and $_FILES['photo_p']['error'] == 0) {
                    $allMonths = array('*', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'decembre');

                    if (($mois = array_search(strtolower($_POST['daten_mm_p']), $allMonths)) <= 0)
                        header('Location: ajout_prof.php?fx=date non valide');
                    $cin_p = $_POST['cin_p'];
                    $photo_p = $_FILES['photo_p'];
                    $day = htmlspecialchars($_POST['daten_jj_p']);
                    $year = htmlspecialchars($_POST['daten_aa_p']);
                    if (intval($mois, 10) < 10)
                        $mois = '0' . intval($mois, 10);
                    if (intval($day, 10) < 10)
                        $day = '0' . intval($day, 10);
                    $dateN_p = $year . '-' . $mois . '-' . $day;
                    $nom_p = $_POST['nom_p'];
                    $pnom_p = $_POST['pnom_p'];
                    $sexe_p = $_POST['sexe_p'];
                    $nat_p = $_POST['nat_p'];
                    $email_p = $_POST['email_p'];
                    $adresse_p = $_POST['adresse_p'];
                    $ntel_p = $_POST['ntel_p'];
                    $nom_f = $_POST['fil_p'];
                    $date_ajt_p = date('Y-m-d');
                    $pwd = sha1($cin_p);
                    include_once '../modele/classes/Personne.class.php';
                    include_once '../modele/classes/Professeur.class.php';
                    $pr = new Professeur();
                    $pr->remplir_Professeur($cin_p, $nom_p, $pnom_p, $dateN_p, $adresse_p, $ntel_p, $photo_p, $sexe_p, $email_p, $nat_p, $nom_f, NULL, NULL, $date_ajt_p, $pwd);
                if($pr->stocker()){
                    echo '<script>alert("professeur a bien ajoute");location="ajout_prof.php";</script>';
                }else{
                echo '<script>location="ajout_prof.php?fx='.$pr->getFautes().'";</script>';
                }
                    
                }else {
                    echo '<script>alert("cin non");</script>';
                    echo '<script>location="ajout_prof.php?fx=des champs sont vides";</script>';
                }
            } else {
                ?>

                <head>
                    <title>ajout un professeur</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <link rel="stylesheet" href="../files/styles/ajout_prof_style.css"/>
                </head>
                <body>
                    <p class="titre">Remplirez les champs du professeur</p>
                    <form method="post" action="ajout_prof.php" enctype="multipart/form-data" class="form_prof">
                        <div>
                            <label for="cin_p">CIN</label><input type="text" name="cin_p" id="cin_p"  autofocus="" required=""/>
                        </div>
                        <div>
                            <label for="nom_p">Nom</label><input type="text" name="nom_p" id="nom_p" required=""/>
                        </div>
                        <div>
                            <label for="pnom_p">Prenom</label><input type="text" name="pnom_p" id="pnom_p" required=""/>
                        </div>
                        <div>
                            <label for="sexe_h">Sexe</label>
                            <label for="sexe_h" id="sexe">Homme</label><input type="radio" name="sexe_p" id="sexe_h" value="H" checked=""/>
                            <label for="sexe_f" id="sexe">Femme</label><input type="radio" name="sexe_p" id="sexe_f" value="F" />
                        </div>
                        <div><label for="photo_p">Photo</label><input type="file" name="photo_p" id="photo_p" required=""/></div>
                        <div><label for="nat_p">Nationalite</label><input type="text" name="nat_p" id="nat_p" value="marocaine" required=""/></div>
                        <div><label>Date de Naissance</label>
                            <select name="daten_jj_p" id="daten_jj_p" required=""></select>
                            <select name="daten_mm_p" id="daten_mm_p" required=""></select>
                            <select name="daten_aa_p" id="daten_aa_p" required=""></select>
                        </div>
                        <div><label for="email_p">Email</label><input type="email" name="email_p" id="email_p" required=""/></div>
                        <div><label for="ntel_p">N telephone</label><input type="tel" name="ntel_p" id="ntel_p" required=""/></div>
                        <div><label for="adresse_p">Adresse</label><input type="text" name="adresse_p" id="adresse_p" required=""/></div>
                        <div><label for="fil_p">Filiere</label><select name="fil_p" id="fil_p" required="">
            <?php ttFiliere(); ?>
                            </select></div>
                        <div class="btn"><input type="submit" name="ajouter_p" id="ajouter_p" value="Ajouter"/>
                            <input type="button" name="annuler_p" id="annuler_p" value="Annuler"/>
                        </div>
                        <p><img id="image" name="image" src="../files/imgs/admin.jpg"/><p>
            <?php
            if (isset($_GET['fx'])) {
                ?>               
                            <p id="message" name="message" style="display:inline-block;">
                                <?php echo $_GET['fx']; ?>
                            </p>
                                <?php
                            }
                            ?>
                    </form>
                    <script>
                        function gererDate() {
                            var j = document.getElementById('daten_jj_p'),
                                    m = document.getElementById('daten_mm_p'),
                                    a = document.getElementById('daten_aa_p');
                            var moisJours = {
                                'Janvier': 31, 'Fevrier': 29, 'Mars': 31, 'Avril': 30, 'Mai': 31, 'Juin': 30, 'Juillet': 31, 'Aout': 31, 'Septembre': 30, 'Octobre': 31, 'Novembre': 30, 'Decembre': 31
                            };
                            var anne = new Date().getFullYear();
                            for (var an = anne; an > anne - 70; an--) {
                                var sel = '';
                                if (an === anne - 28)
                                    sel = 'selected';
                                a.innerHTML += '<option value="' + an + '" ' + sel + '>' + an + '</option>';
                            }
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
                        /**
                         *gerer l'animationd d'image
                         */
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
                        /**
                         * gerer le telechargement d'une photo puis l'afficher
                         **/
                        function telechargerPhoto() {
                            var reader = new FileReader(),
                                    typesImg = ['png', 'jpg', 'jpeg'],
                                    fileInput = document.getElementById('photo_p'),
                                    img = document.getElementById('image');
                            ;
                            fileInput.onchange = function() {
                                var file = this.files[0], imgType;
                                imgType = file.name.split('.');
                                imgType = imgType[imgType.length - 1];
                                if (typesImg.indexOf(imgType.toLowerCase()) !== -1) {
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

                        /*gerer les boutons annuler et ajouter  d'un prof*/
                        function gererBtnAnulAjouter() {
                            var anul = document.getElementById('annuler_p'),
                                    ajt = document.getElementById('ajouter_p');
                            anul.onclick = function() {
                                if (confirm('vous vouler retourner  sans ajouter des profs')) {
                                    parent.location = "../gerer_prof.php";
                                }
                            };
                            ajt.onclick = function() {
                                if (!confirm('vous vouler ajouter ce professeur')) {
                                    return  false;
                                }
                            };
                        }
                        /*ce que va executer*/
                        (function() {
                            gererDate();
                            telechargerPhoto();
                            gererBtnAnulAjouter();
                        })();

                    </script>
                </body>



            <?php
        }
    } else {
        include
                header("Location: ../modele/deconnect_admin.php");
    }
} else {
    header("Location: ../modele/deconnect_admin.php");
}
?>
</html>