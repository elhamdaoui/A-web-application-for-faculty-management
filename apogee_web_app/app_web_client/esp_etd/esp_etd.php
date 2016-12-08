<?php
session_start();
include_once '../modele/connect_bdd.php';

/**
 * 
 * @param type $sexe
 * @return string
 */
function returnMdOrMr($sexe) {
    if (strcmp(strtolower($sexe), 'h') == 0) {
        return 'MR. ';
    } else {
        return 'Md. ';
    }
}

/**
 * 
 */
if (isset($_SESSION['psetd']) and isset($_SESSION['pwdetd']) and isset($_SESSION['nometd']) and isset($_SESSION['pnometd'])) {
    $pse = htmlspecialchars($_SESSION['psetd']);
    $nome = htmlspecialchars($_SESSION['nometd']);
    $pwde = htmlspecialchars($_SESSION['pwdetd']);
    $pnome = htmlspecialchars($_SESSION['pnometd']);
    include_once '../modele/verify_connexion.php';
    if (verify_connexion('etudiant', array('cne_e' => $pse, 'nom_e' => $nome, 'prenom_e' => $pnome, 'pwd_e' => $pwde))) {
        include_once '../modele/classes/Personne.class.php';
        include_once '../modele/classes/Etudiant.class.php';
        $etd = new Etudiant();
        $etd->recuperer_etudiant($pse);
        ?>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>FPS::<?php echo $etd->getNom() . ' ' . $etd->getPrenom(); ?></title>
                <link rel="shortcut icon" type="image/x-icon" href="../files/icns/fps.png" />
                <link rel="stylesheet" href="../files/styles/esp_etd_style.css"/>
                </style>
            </head>
            <body>
                <nav>
                    <img src="../modele/moteur_img.php?tab=etudiant&attr=photo_e&colid=cne_e&id=<?php echo $etd->getCne(); ?>" id="img_etd" title="<?php echo $etd->getNom() . ' ' . $etd->getPrenom(); ?>"/>
                    <a name="nc" id="aa" href="page_etd.php?choix=mninfs" target="fen">Mes informations</a>
                    <a name="nc" id="ab" href="page_etd.php?choix=prmcmpte" target="fen">Parametres Compte</a>
                    <a name="nc" id="ac" href="page_etd.php?choix=mnesp" target="fen">Mon Espace</a>
                    <a name="nc" id="ad" href="page_etd.php?choix=masitu" target="fen">Ma Situation</a>
                    <a name="nc" id="ae" href="page_etd.php?choix=mesdip" target="fen">Mes Diplomes</a>
                    <a name="nc" id="af" href="page_etd.php?choix=mesprfs" target="fen">Mes Professeurs</a>
                    <a name="nc" id="ag" href="page_etd.php?choix=mesrecs" target="fen">Mes Reclamations</a>                    
                    <a name="nc" id="ah" href="../accueil.php"> Accueil</a>
                    <a name="nc" id="ai" href="../modele/deconnect.php">Deconnexion</a>
                </nav>
                <iframe id="fen" name="fen" src="page_etd.php?choix=mnesp"></iframe>
                <script>
                    function gererClickLiens() {
                        var liens = document.getElementsByTagName('a');
                        for (var i = 0; i < liens.length; i++) {
                            liens[i].onclick = function() {
                                if (this.name === 'nc') {
                                     for (var j = 0; j < liens.length; j++) {
                                            if (liens[j].name === 'cc') {
                                                liens[j].name = 'nc';
                                                liens[j].style.backgroundColor = '#00c576';
                                            }
                                        }
                                    this.style.transitionDuration = '1s';
                                    this.style.transform = 'scaleX(0)';
                                    var elm = this;
                                    setTimeout(function() {
                                        elm.style.backgroundColor = 'blue';
                                        elm.style.transform = 'scaleX(1)';
                                    }, 1020);
                                    this.name = 'cc';
                                }
                            };
                        }
                    }
                    /**/
                    (function() {
                        gererClickLiens();
                    })();
                </script>
            </body>
        </html>
        <?php
    } else {
        include_once '../modele/deconnect.php';
    }
} else {
    include_once '../modele/deconnect.php';
}
?>