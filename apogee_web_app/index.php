<?php 
session_start();

?>
<html>
<head>
    <title>Teste mini projet POO</title>
<style>
a{
text-decoration:none;
position: absolute;
width:50%;
height:30%;
top:5%;
left:25%;
box-shadow: black 2px 2px 2px 2px;
-webkit-box-shadow: black 2px 2px 2px 2px;
border-radius:10px;
-webkit-border-radius:10px;
background-color:salmon;
cursor:pointer;
font-size:xx-large;
color:white;
font-family:"arial black";
}
#id2{
top:40%;
}
</style>

	</head>

<body>
<a href="app_admin/index.php" id="a1">Partie administration </a>
<a href="app_web_client/index.php" id="a2">Partie Etudiant et Professeur </a>
</body>
</html>