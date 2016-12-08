
<?php
// On démarre la session
session_start();
include_once './modele/connect_bdd.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>FPS: faculte polydisciplinaire de safi</title>
        <link rel="shortcut icon" type="image/x-icon" href="files/icns/fps.png" />
        <link rel="stylesheet" href="files/styles/accueil_style.css"/>
        <link rel="stylesheet" href="files/styles/affich_act_style.css"/>

    </head>
    <body>

        <header></header>
        <section>
            <nav name="navigations">
                <a name="acc_a" href="accueil.php" id="acc_a"><label>Accueil<label></a>
                            <a name="inf_a" href="accueil.php?esp=infos" id="inf_a"><label>Informations<label></a>
                                        <a name="etd_a" href="accueil.php?esp=etudiant" id="etd_a"><label>Espace étudiant<label></a>
                                                    <a name="prof_a" href="accueil.php?esp=professeur" id="prof_a"><label>Espace professeur</label></a>
                                                    </nav>
                                                    <div name="corps" class="corps">
                                                        <?php
                                                        if (isset($_GET['esp']) && strcmp($_GET['esp'], 'professeur') == 0) {
                                                            if (isset($_SESSION['psprof'])) {
                                                                header("Location: esp_prof/esp_prof.php");
                                                            }
                                                            ?>
                                                            <form method="post" action="modele/connect_professeur.php">
                                                                <div><p>connectez vous</p></div>
                                                                <div><label for="nom_prof">Nom</label><input type="text" name="nom_prof" id="nom_prof" required autofocus/></div>
                                                                <div><label for="cin_prof">CIN</label><input type="text" name="cin_prof" id="cin_prof" required /></div>
                                                                <div><label for="pwd_prof">Mot de passe</label><input type="password" name="pwd_prof" id="pwd_prof" required/></div>
                                                                <div><input type="submit" value="Connexion" id="connect_prof"/></div>
                                                                <?php
                                                                if (isset($_GET['cnt_prof'])) {
                                                                    ?>
                                                                    <div style="color: #ff0066;padding: 2%;font-weight: bold;">Entrez des informations corrects s'il vous plait</div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </form>
                                                            <?php
                                                        } else if (isset($_GET['esp']) && strcmp($_GET['esp'], 'etudiant') == 0) {
                                                            if (isset($_SESSION['psetd'])) {
                                                                header("Location: esp_etd/esp_etd.php");
                                                            }
                                                            ?>
                                                            <form method="post" action="modele/connect_etudiant.php">
                                                                <div><p>connectez-vous</p></div>
                                                                <div><label for="nom_e">Nom</label><input type="text" name="nom_e" id="nom_e" required autofocus/></div>
                                                                <div><label for="cne_e">CNE</label><input type="number" name="cne_e" id="cne_e" required/></div>
                                                                <div><label for="cin_e">CIN</label><input type="text" name="cin_e" id="cin_e" required/></div>
                                                                <div><label for="pwd_e">Mot de passe</label><input type="password" name="pwd_e" id="pwd_e" required/></div>
                                                                <div><input type="submit" value="Connexion" id="connect_e"/></div>
                                                                <div><a href="esp_etd/ajout_etd.php" id="inscrire_e">inscrivez-vous!</a>
                                                                    <?php
                                                                    if (isset($_GET['cnt_etd'])) {
                                                                        ?>
                                                                        <div style="color: #ff0066;padding: 2%;font-weight: bold;margin-top: 0%;">Entrez des informations corrects </div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </form>
                                                            <?php
                                                        } else if (isset($_GET['esp']) && strcmp($_GET['esp'], 'infos') == 0) {
                                                            ?>
                                                            <p> informations</p>
                                                            <?php
                                                        } else {
                                                            /* acceuil */
                                                            ?>
                                                            <iframe src="actualites.php" class="actualites" name="fenact"></iframe>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div name="pub" class="pub">
                                                        <div id="calendrier">
                                                            <p id="cld_annee">
                                                                <img src="files/icns/prev_20.png" id="prev_annee"/>
                                                                <strong id="cld_annee_p"></strong>
                                                                <img src="files/icns/next_20.png" id="next_annee"/>
                                                            </p>
                                                            <p id="cld_mois">
                                                                <img src="files/icns/prev_20.png" id="prev_mois"/>
                                                                <strong id="cld_mois_p"></strong>
                                                                <img src="files/icns/next_20.png" id="next_mois"/>
                                                            </p>
                                                            <p id="cld_jours"></p>
                                                        </div>
                                                        <div id="date"><p></p></div>
                                                        <div id="heure"><p></p></div>
                                                    </div>
                                                    </section>
                                                    <footer><p>Copie Right FPS 2014</p></footer>

                                                    <script>

                                                        function Calendrier() {
                                                            this.mois = null;
                                                            this.jour = null;
                                                            this.annee = null;
                                                            this.dayOFweek = null;
                                                            this.bis = null;
                                                            this.jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                                            this.tousMois = {
                                                                'janvier': 31, 'fevrier': 28, 'mars': 31, 'avril': 30,
                                                                'mai': 31, 'juin': 30, 'juillet': 31, 'aout': 31, 'septembre': 30,
                                                                'octobre': 31, 'novembre': 30, 'decembre': 31
                                                            };
                                                            this.remplir = function() {
                                                                var sysdate = new Date();
                                                                this.mois = sysdate.getMonth();
                                                                this.jour = sysdate.getDate();
                                                                this.annee = sysdate.getFullYear();
                                                                this.dayOFweek = sysdate.getDay();
                                                                if (Math.abs((this.annee - 2012)) % 4 === 0) {
                                                                    this.tousMois['fevrier'] = 29;
                                                                }
                                                            };
                                                            this.afficher = function() {
                                                                var cld_annee = document.getElementById('cld_annee_p'),
                                                                        cld_mois = document.getElementById('cld_mois_p'),
                                                                        cld_jours = document.getElementById('cld_jours');
                                                                if ((this.annee - 2012) % 4 === 0) {
                                                                    this.tousMois['fevrier'] = 29;
                                                                } else {
                                                                    this.tousMois['fevrier'] = 28;
                                                                }
                                                                cld_annee.innerHTML = '' + this.annee;
                                                                var i = 0, mmois;
                                                                for (var mm in this.tousMois) {
                                                                    if (i === this.mois) {
                                                                        cld_mois.innerHTML = '' + mm;
                                                                        mmois = mm;
                                                                    }
                                                                    i++;
                                                                }
                                                                var nbjrs = this.tousMois[mmois];
                                                                var jr1 = new Date(this.annee, this.mois, 1).getDay(),
                                                                        nb = jr1, emp, now = new Date();
                                                                cld_jours.innerHTML = '';
                                                                for (var j = 0; j < this.jours.length; j++) {
                                                                    var lef = 0;
                                                                    if (j > 0)
                                                                        lef = (j * 13) + j;
                                                                    cld_jours.innerHTML += '<em class="day" style="left:' + lef + '%;">' + this.jours[j].substring(0, 1) + '</em>';
                                                                }
                                                                cld_jours.innerHTML += '<br/>';
                                                                var k = 0, dek = 0, t = 1;
                                                                while ((jr1--) !== 0) {
                                                                    dek = ((k % 7) * 13) + k;
                                                                    k++;
                                                                    cld_jours.innerHTML += '<em  style="left:' + dek + '%;">&nbsp;</em>';
                                                                }
                                                                for (var i = 1; i <= nbjrs; i++) {
                                                                    dek = ((k % 7) * 13) + (k % 7);
                                                                    k++;
                                                                    emp = '';
                                                                    emp += '<em';
                                                                    if (this.annee === now.getFullYear()
                                                                            && this.mois === now.getMonth()
                                                                            && i === now.getDate()) {
                                                                        emp += ' class="now"';
                                                                    }
                                                                    emp += ' style="left:' + dek + '%;">' + i + '</em>';
                                                                    cld_jours.innerHTML += emp;
                                                                    if ((i + nb) % 7 === 0) {
                                                                        cld_jours.innerHTML += '<br/>';
                                                                        t++;
                                                                    }
                                                                }
                                                                t++;
                                                                var ems = cld_jours.getElementsByTagName('em'), p = t;
                                                                var hei = (100 / t) - 1, nvide = 0;
                                                                for (var u = 1; u <= ems.length; u++) {
                                                                    ems[u - 1].style.height = hei + '%';
                                                                    ems[u - 1].style.top = (hei * (t - p)) + t - p + '%';
                                                                    ems[u - 1].style.borderRadius = '7px';
                                                                    if (ems[u - 1].innerHTML === '&nbsp;') {
                                                                        ems[u - 1].style.backgroundColor = '#cccccc';
                                                                        ems[u - 1].style.borderStyle = 'inset';
                                                                        nvide++;//pour remplir les cases vides par les jours des mois precd;
                                                                    }
                                                                    if (u % 7 === 0 && u > 1)
                                                                        p--;
                                                                }
                                                            };
                                                            this.gerer = function() {
                                                                var nextAnne = document.getElementById('next_annee'),
                                                                        prevAnne = document.getElementById('prev_annee'),
                                                                        nextMois = document.getElementById('next_mois'),
                                                                        prevMois = document.getElementById('prev_mois');
                                                                this.remplir();
                                                                this.afficher();
                                                                var cal = this;
                                                                nextAnne.onclick = function() {
                                                                    cal.annee += 1;
                                                                    cal.afficher();
                                                                };
                                                                prevAnne.onclick = function() {
                                                                    cal.annee -= 1;
                                                                    cal.afficher();
                                                                };
                                                                nextMois.onclick = function() {
                                                                    cal.mois = (cal.mois + 1) % 12;
                                                                    cal.afficher();
                                                                };
                                                                prevMois.onclick = function() {
                                                                    if (cal.mois === 0)
                                                                        cal.mois = 11;
                                                                    else
                                                                        cal.mois -= 1;
                                                                    cal.afficher();
                                                                };

                                                            };
                                                        }
                                                        /*une fonction qui affiche la date systeme , date de l'ordinateur de l'utilisateur.*/
                                                        function afficherDate() {
                                                            var h = document.getElementById('heure'),
                                                                    d = document.getElementById('date');
                                                            var date = new Date(),
                                                                    hh = date.getHours(), mm = date.getMinutes(), ss = date.getSeconds(),
                                                                    aaaa = date.getFullYear(), mois = date.getMonth() + 1, jour = date.getDate();
                                                            if (hh < 10) {
                                                                hh = '0' + hh;
                                                            }
                                                            if (mm < 10) {
                                                                mm = '0' + mm;
                                                            }
                                                            if (ss < 10) {
                                                                ss = '0' + ss;
                                                            }
                                                            if (mois < 10) {
                                                                mois = '0' + mois;
                                                            }
                                                            if (jour < 10) {
                                                                jour = '0' + jour;
                                                            }
                                                            var text = '<center>' + hh + '<blink>:</blink>' + mm + '<blink>:</blink>'
                                                                    + ss + '</center>',
                                                                    dat = '<center>' + aaaa + '/' + mois + '/' + jour + '</center>';
                                                            h.innerHTML = text;
                                                            //d.innerHTML = dat;
                                                        }
                                                        /*texte*/
                                                        function gererTexte() {
                                                            //essayer de faire la comptabilité 
                                                        }
                                                        function setClass(idd, cla) {

                                                            var th = document.getElementById(idd);
                                                            th.className = cla;
                                                        }
                                                        /*ce va exécuter */
                                                        (function() {
                                                            var intervalID = setInterval('afficherDate()', 1000);
                                                            var call = new Calendrier();
                                                            call.gerer();
                                                            /*document.onload=*/
                                                        })();
                                                    </script>
                                                    </body>
                                                    </html>