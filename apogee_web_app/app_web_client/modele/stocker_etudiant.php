<?php

if (isset($_POST['cin_e']) and isset($_POST['cne_e']) and isset($_POST['nom_e']) and isset($_POST['prenom_e']) and isset($_POST['daten_jj_e']) and isset($_POST['daten_mm_e']) and isset($_POST['daten_aa_e']) and isset($_POST['lieun_e']) and isset($_POST['email_e']) and isset($_POST['ntel_e']) and isset($_POST['adresse_e']) and isset($_POST['sexe_e']) and isset($_POST['nationalite_e']) and isset($_POST['nom_bac']) and isset($_POST['type_bac']) and isset($_POST['date_bac']) and isset($_POST['nat_bac']) and isset($_POST['moyenne_bac']) and isset($_POST['filiere_e']) and isset($_FILES['photo_e']) and $_FILES['photo_e']['error'] == 0) {


    $allMonths = array('*', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'aout', 'septembre', 'octobre', 'novembre', 'decembre');

    if (($mois = array_search(strtolower($_POST['daten_mm_e']), $allMonths)) <= 0)
        header('Location: ../gerer_etd/inscription.php?fx=date non valides');
    /* attention modifier l'emplacement du page acceuil lorseque */
    include_once ("./Etudiant.class.php");
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
        header('Location:../inscription.php?fx=' . $etd->getFautes());
    } else {
        ?>
        <html>
            <head>
                <meta charset="UTF-8" />
                <title> Vous étes inscrit</title>
                <link rel="stylesheet" href="stk_etd_style.css"/>
            </head>
            <body>
                <div><p id="cont"></p><label>votre inscription est faite avec succés</label></div>
                <script>
                    var dv = document.getElementById('cont'), i = 0;
                    var interv = setInterval(function() {
                        if (i <= 100) {
                            dv.style.width = i + '%';
                            i += 1;
                        } else {
                            //clearInterval(interv);
                            i=120;
                            document.location = "../accueil.php";
                        }
                    }, 15);

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
     header('Location: ../inscription.php?fx=s\'il vous plait remplir tous les champs');
}
?>
