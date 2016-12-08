<?php
include_once './connect_bdd.php';
        if (isset($_GET['id'])) {
            global $bdd;
            $result = $bdd->prepare('select image_acc from actualite where id_acc=:id');
            $result->execute(array('id' => $_GET['id']));
            $donnes = $result->fetch();
            if (!empty($donnes)) {
                echo $donnes[0];
            } else {  
                echo "";
            }
        } else {
            echo "";
        }

?>
