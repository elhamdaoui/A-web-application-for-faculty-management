<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @param type $sexe
 */
function afficherSexe($sexe) {
    if (strcmp(strtolower($sexe), 'h') == 0) {
        echo 'Homme';
    } else {
        echo 'Femme';
    }
}

if (isset($_SESSION['psprof']) and isset($_SESSION['pwdprof']) and isset($_SESSION['nomprof']) and isset($_SESSION['pnomprof']) and isset($_SESSION['idfprof'])) {
    $psp = htmlspecialchars($_SESSION['psprof']);
    $nomp = htmlspecialchars($_SESSION['nomprof']);
    $pwdp = htmlspecialchars($_SESSION['pwdprof']);
    $pnomp = htmlspecialchars($_SESSION['pnomprof']);
    $idfp = htmlspecialchars($_SESSION['idfprof']);
    include_once '../modele/verify_connexion.php';
    if (verify_connexion('professeur', array('cin_p' => $psp, 'nom_p' => $nomp, 'prenom_p' => $pnomp, 'pwd_p' => $pwdp, 'id_f' => $idfp))) {
        include_once '../modele/classes/Personne.class.php';
        include_once '../modele/classes/Professeur.class.php';
        $pr = new Professeur();
        $pr->recuperer_Professeur($psp);
        if (isset($_GET['mod']) and strcmp($_GET['mod'], 'true') == 0) {
            if (isset($_GET['pwd']) and strcmp(sha1(htmlspecialchars($_GET['pwd'])), $pr->getPwd()) == 0) {
                $em = '';
                $ntl = '';
                $adr = '';
                $nvpw = '';
                $mes = '';
                if (isset($_GET['email']) and strlen($_GET['email']) > 1) {
                    $em = htmlspecialchars($_GET['email']);
                    if ($pr->setEmail($em)) {
                        $mes.=' email modifie\n';
                        $pr->modifierAttribue('email_p', $em);
                    } else {
                        $mes.='Erreur: email non valide\n';
                    }
                }
                if (isset($_GET['ntel']) and strlen($_GET['ntel']) > 1) {
                    $ntl = htmlspecialchars($_GET['ntel']);
                    if ($pr->setNtel($ntl)) {
                        $mes.=' num du telephone modifie \n';
                        $pr->modifierAttribue('ntel_p', $ntl);
                    } else {
                        $mes.='Erreur: numero du telephone non valide\n';
                    }
                }
                if (isset($_GET['adresse']) and strlen($_GET['adresse']) > 1) {
                    $adr = htmlspecialchars($_GET['adresse']);
                    $mes.=' adresse,';
                    $pr->modifierAttribue('adresse_p', $adr);
                }
                if (isset($_GET['nvpwd']) and strlen($_GET['nvpwd']) > 1) {
                    $nvpw = htmlspecialchars($_GET['nvpwd']);
                    $mes.=' mot de passe,';
                    $pr->modifierAttribue('pwd_p', sha1($nvpw));
                    $pr->setPwd(sha1($nvpw));
                    $_SESSION['pwdprof'] = $pr->getPwd();
                }
                if(isset($_FILES['img_pf']) and $_FILES['img_pf']['error']==0){
                    
                    $a=$pr->setPhoto($_FILES['img_pf']);
                    if($a==1){
                     $mes.='image,';   
                     $pr->modifierAttribue('photo_p', $pr->getPhoto());
                    }elseif($a==0){
                      echo '<script>alert("Image non modifier: '.$pr->getFautes().'");</script>';  
                    }
                }
                if (!strlen($mes) > 0) {
                    $mes = 'aucune modification\n';
                }else{
                    $mes.='  modifiee';
                }

                echo '<p>on va modifie</p>';
                echo '<script>alert("' . $mes . '");location="page_prof.php";</script>';
            } else {
                header("Location: page_prof.php?fx=mot de passe incorect");
            }
        } else {
            ?>
            <!DOCTYPE html>
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                    <title>profile professeur</title>
                    <link rel="stylesheet" href="../files/styles/page_prof_style.css"/>
                </head>
                <body>
                    <div id="ip_prof">
                        <div class="infs">
                            <em>Nom</em><strong><?php echo $pr->getNom(); ?></strong>
                            <em>Prenom</em><strong><?php echo $pr->getPrenom(); ?></strong>
                            <em>CIN</em><strong><?php echo $pr->getCin(); ?></strong>
                            <em>Sexe</em><strong><?php afficherSexe($pr->getSexe()); ?></strong>
                            <em>Nationnalite</em><strong><?php echo $pr->getNationalite(); ?></strong>
                            <em>Date Naissance</em><strong><?php echo $pr->getDateN(); ?></strong>
                            <em>Filiere</em><strong><?php echo $pr->get_nom_f(); ?></strong>
                            <em>Date d'ajoute </em><strong><?php echo $pr->getDateAjout(); ?></strong>
                        </div>
                        <div class="image">
                            <img src="../modele/moteur_img.php?tab=professeur&attr=photo_p&colid=cin_p&id=<?php echo $pr->getCin(); ?>"/>
                            <p id="btn_iim"><input type="button" name="modif_img_prof" id="modif_img_prof" value="changer photo"/></p>
                        </div>
                    </div>
                    <div id="md_prof" >
                        <div>
                            <em>Email</em> <h3 id="h_md_email"><?php echo $pr->getEmail(); ?></h3><input type="button" name="" id="md_email" value="modifier" />
                        </div>
                        <div>
                            <em>N° telephone</em> <h3 id="h_md_ntel"><?php echo $pr->getNtel(); ?></h3><input type="button" name="" id="md_ntel" value="modifier"/>
                        </div>
                        <div>
                            <em>Adresse</em> <h3 id="h_md_adrs"><?php echo $pr->getAdresse(); ?></h3><input type="button" name="" id="md_adrs" value="modifier"/>
                        </div>
                        <div id="div_im"></div>
                        <div id="div_pwd" style="display:none;width:100%;">
                            <div> <em>Nouveau mot de passe</em><input type="password" id="nv_paw_1" style="width:38.8%;margin-left: 1%;"/></div>
                            <div><em>confirmer mot de passe</em><input type="password" id="nv_paw_2" style="width:38.8%;margin-left: 1%;"/></div>
                        </div>
                        <div>
                            <em>Votre mot de passe</em><input type="password" id="psd" style="width:38.8%;margin-left: 1%;"/><b style="color:red;background-color: white;margin-left: 1%;"><?php
                                if (isset($_GET['fx'])) {
                                    echo $_GET['fx'];
                                }
                                ?></b>
                        </div>
                        <div>
                            <input type="button" id="btn_md_pwd" name="btn_md_pwd" value="modifier mot de passe"/>
                            <input type="button" id="btn_enre" name="btn_enre" value="Enregistrer les modifications" />
                        </div>
                    </div>
                </body>
                <script>
                    function gererModification(id) {
                        var b = document.getElementById(id), h = document.getElementById('h_' + id);
                        b.onclick = function() {
                            text = h.textContent;
                            h.innerHTML = '<input id="i_' + id + '" value="' + text + '" type="text" style="width:80%;" autofocus=""/>';
                        };
                    }
                    /**
                     * 
                     *
                     * @returns {undefined}             
                     */
                    function gererClqBtns() {
                        var md_pwd = document.getElementById('btn_md_pwd'),
                                btn_enre = document.getElementById('btn_enre'),
                                btn_img = document.getElementById('modif_img_prof');
                        btn_img.onclick = function() {
                            var pim = document.getElementById('div_im');
                            pim.innerHTML = '<em>nouvelle photo</em><form id="way_img" action="" method="post" enctype="multipart/form-data"><input type="file" name="img_pf" id="img_pf" style="margin-left:1%;width:100%;"/></form>';
                            this.disabled = true;
                            var file = document.getElementById('img_pf');
                            file.onchange = function() {
                                //alert(this.files[0].name);

                            };
                        };
                        md_pwd.onclick = function() {
                            document.getElementById('div_pwd').style.display = 'inline-block';
                        };
                        btn_enre.onclick = function() {
                            if (!confirm('estes-vous sur de cette modification ?')) {
                                return false;
                            }
                            var lien = "page_prof.php?mod=true";
                            if (document.getElementById('i_md_ntel')) {
                                var t = document.getElementById('i_md_ntel').value;
                                if (t.length >= 1) {
                                    lien += '&ntel=' + t;
                                }
                            }
                            if (document.getElementById('i_md_email')) {
                                var t = document.getElementById('i_md_email').value;
                                if (t.length >= 1) {
                                    lien += '&email=' + t;
                                }
                            }
                            if (document.getElementById('i_md_adrs')) {
                                var t = document.getElementById('i_md_adrs').value;
                                if (t.length >= 1) {
                                    lien += '&adresse=' + t;
                                }
                            }
                            if (document.getElementById('nv_paw_1')) {
                                var t = document.getElementById('nv_paw_1').value;
                                var p = document.getElementById('nv_paw_2').value;
                                if (t.length > 1 || p.length > 1) {
                                    if (t === p) {
                                        lien += '&nvpwd=' + t;
                                    } else {
                                        alert("mots de passe non identiques");
                                        return false;
                                    }
                                }
                            }
                            var pwd = document.getElementById('psd').value;
                            if (pwd.length < 1) {
                                alert("votre mot de passe non valide");
                                return false;
                            }
                            lien += '&pwd=' + pwd;
                            if (document.getElementById('way_img')) {
                            document.getElementById('way_img').action=lien; 
                            document.getElementById('way_img').submit();
                        }else{
                            location = lien;
                        }
                        };
                        
                    }
                    (function() {
                        gererModification('md_email');
                        gererModification('md_ntel');
                        gererModification('md_adrs');

                        gererClqBtns();
                    })();
                </script>
            </html>
            <?php
        }
    } else {
//include_once '../modele/deconnect.php';
        echo '<script>parent.location="../modele/deconnect.php"</script>';
    }
} else {
    echo '<script>parent.location="../modele/deconnect.php"</script>';
}
?>
