<?php

session_start();
$_SESSION = array();
session_destroy();
setcookie('psetd','');
setcookie('mpetd','');
setcookie('nometd','');
setcookie('pnometd','');
setcookie('idfetd','');
header("Location: ../accueil.php");
?>
