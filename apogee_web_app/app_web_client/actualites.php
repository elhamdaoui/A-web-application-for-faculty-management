<?php
session_start();
include_once './modele/connect_bdd.php';
?>
<html>
    <head>
        <meta charset="utf-8" />
        <title>FPS: actualites</title>
        <link rel="shortcut icon" type="image/x-icon" href="./files/icns/fps.png" />
        <link rel="stylesheet" href="files/styles/actualites_style.css"/>
 
    </head>
    <body>
        <?php
        include_once './modele/classes/Actualite.class.php';
        $acts = Actualite::actualitesAllsAdmins();
        if (empty($acts)) {
            ?>
            <p class="nonacts">BienVenue au Site officiel du Faculte ploydisciplinaire du safi</p>
            <?php
        }else{
            foreach ($acts as $act){
                ?>
            <div class="act">
                <div class="infs">
                    <div class="tit"><?php echo $act->getTitre();?></div>
                    <div class="cont"><?php echo $act->getContenu();?></div>
                    <div class="date"><?php echo $act->getDate();?></div>
                </div>
                <img src="modele/moteur_img.php?tab=actualite&attr=image_acc&colid=id_acc&id=<?php echo $act->getId();?>"/>
            </div>
                    <?php
            }
        }
        ?>
        <script>
        </script>
    </body>
</html>