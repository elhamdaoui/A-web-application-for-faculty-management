<?php

session_start();
$_SESSION = array();
session_destroy();
setcookie('ps','');
setcookie('mp','');
setcookie('nom','');
setcookie('pnom','');
setcookie('fct','');
setcookie('id_f','');
header("location: ../accueil_admin.php");
?>
