<?php
session_start();
include_once './modele/connect_bdd.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>gerer systeme</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="files/styles/gerer_sys_style.css"/>
    </head>
    <body>
        <?php
        if (isset($_SESSION['ps']) and isset($_SESSION['mp']) and isset($_SESSION['nom']) and isset($_SESSION['pnom']) and isset($_SESSION['fct'])) {
            $cin = $_SESSION['ps'];
            $nom = $_SESSION['nom'];
            $pnom = $_SESSION['pnom'];
            $fct = $_SESSION['fct'];
            include_once ('./modele/verify_connexion.php');
            if (verify_connexion('administrateur', array('cin_ad' => $cin, 'nom_ad' => $nom, 'prenom_ad' => $pnom, 'fonction_ad' => $fct))) {
                include_once './modele/classes/Departement.class.php';
                if (isset($_POST['ajt_dep'])) {
                    if (isset($_POST['nm_dep']) and strlen($_POST['nm_dep']) > 0) {
                        $nom_dep = htmlspecialchars($_POST['nm_dep']);
                        $dep_ajt = new Departement();
                        $dep_ajt->remplir_departement(NULL, $nom_dep, NULL);
                        if ($dep_ajt->stocker()) {
                            echo '<script>alert("departement a bien ajoute");location="gerer_sys.php";</script>';
                        } else {
                            echo '<script>alert("Erreur: departement non ajoute !!");location="gerer_sys.php";</script>';
                        }
                    } else {
                        echo '<p>vous etes pas entree un nom pour ce departement</p>';
                        echo '<script>alert("Erreur: nom non valide");</script>';
                    }
                } else {
                    $depts = Departement::tousDepartements();
                    ?>
                    <div class="p_up">
                        <center>
                            Tous les departements sont affiches au dessous, pour ajouter un departement clike ici
                            <input type="button" name="ajout_dep" id="ajout_dept" value="ajouter un departement"/>
                            <form id="form_ajt_dep" method="post" action="gerer_sys.php">
                                <p>
                                    <label for="nm_dep">Nom departement</label><input name="nm_dep" id="nm_dep" type="text" required="" autofocus=""/>
                                </p>
                                <p class="btns">
                                    <input type="submit" name="ajt_dep" id="ajt_dep" value="Ajouter"/>
                                    <input type="button" name="anl_ajt_dep" id="anl_ajt_dep" value="Annuler"/>
                                </p>
                            </form>
                        </center>
                    </div>
                    <?php
                    if (empty($depts)) {
                        echo '<script>alert("aucun departement trouve");</script>
                    <p>aucun departement trouve</p>';
                    } else {
                        foreach ($depts as $dep) {
                            ?>
                            <a class="dept" href="gerer_sys/page_dept.php?id=<?php echo $dep['id_d']; ?>"><?php echo $dep['nom_d']; ?></a>
                            <?php
                        }
                    }
                    ?>
                    <script>
                        function gererBtnsAjtDept() {
                            var btnAjt = document.getElementById('ajout_dept'),
                                    btnAjtDeptSubmit = document.getElementById('ajt_dep'),
                                    btnAnul = document.getElementById('anl_ajt_dep'),
                                    formAjt = document.getElementById('form_ajt_dep'),
                                    nom_dep = document.getElementById('nm_dep');
                            btnAjt.onclick = function() {
                                formAjt.style.display = "inline-block";
                                nom_dep.value = "";
                            };
                            btnAjtDeptSubmit.onclick = function() {
                                if (nom_dep.value.length > 0) {
                                    if (!confirm("confirmer l'ajoute du depratement")) {
                                        return false;
                                    }
                                } else {
                                    alert("nom non valide");
                                    return false;
                                }
                            };
                            btnAnul.onclick = function() {
                                formAjt.style.display = "none";
                            };
                        }
                        (function() {
                            gererBtnsAjtDept();
                        })();
                    </script>
                    <?php
                }
            } else {
                include_once './modele/deconnect_admin.php';
                header("Location: ./accueil_admin.php");
            }
        } else {
            include_once './modele/deconnect_admin.php';
            header("Location: ./accueil_admin.php");
        }
        ?>
    </body>
</html>