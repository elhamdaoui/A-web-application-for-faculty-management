<?php
include '../connect_bdd.php';
/*
  $req=$bdd->query('select nom_e,prenom_e from etudiant');
  $rs=$req->fetchAll();
  foreach ($rs as $cle =>$val){
  $val['nom_e']=htmlspecialchars($val['nom_e']);
  $val['prenom_e']=  nl2br(htmlspecialchars($val['prenom_e']),TRUE);
  //nl2br(string) ajout un retour a la ligne au fin de chaine::ne marchera pas je sais pas prq!!
  echo $cle.'     '.htmlspecialchars($val['nom_e']).'     '.$val['prenom_e'].'<br/>';


  }
  echo sha1('a').'   '.  md5('a').'<br/>';
  echo sha1('b').'   '.  md5('b').'<br/>';
  echo sha1('aaaaaaaaaaaa').'   '.  md5('aaaaaaaaaaaa').'<br/>';
  echo sha1('ba').'   '.  md5('ba').'<br/>';
 */
/*
  include './Etudiant.class.php';
  $rech = Etudiant::trouverEtudiants(array('cne_e' => '1145456789', 'prenom_e' => 'ABDELMAJId'));
  if (empty($rech)) {
  echo 'oooooooooh l\'etudiant n\'existe pas ';
  } else {
  echo '<pre>';
  echo '<img src="moteur_img.php?id='.$rech[0]->getCne().'"/>';
  print_r($rech[0]);
  echo '</pre>';
  echo '<img src="'.$rech[0]->getPhoto().'"/>';
  if (is_a($rech[0], 'Etudiant')) {
  $rech[0]->setCne('8888');
  if ($rech[0]->stocker_etudiant() == TRUE) {
  echo 'l_etudiant est bien stocker !<br/>';

  } else {
  echo 'l_etudiant est existe déjà ou bien prob de requete,n_est pas stocker !!<br/>';
  }
  }
  }
 */
/*
  $etd= new Etudiant();
  $etd->setId_f_Par_nom_Fil('SMP');
  echo $etd->getId_f();
 */
//@[a-zA-Z]\.[a-zA-Z]{2,4}
//#[aaaaa]#:calsse des chars
//^
/*
  include_once ("./Module_C.class.php");
  $mod= new Module_C();
  $mod->recuperer('M01', 1);
  echo '<pre>'.$mod->toString().'</pre>';
  $mod->setId('M02');
  if(!$mod->stocker()){
  echo '<br/>module existe<br/>';
  }
 */
/*
  if (isset($_FILES['phot'])) {
  include_once './Personne.class.php';
  include_once './Admin.class.php';
  $ad = new Admin();
  $ad->remplir_Admin('PO99988', 'root', 'root', '1980-12-10', 'adresse lyon', '0655555555', $_FILES['phot'], 'H', 'root@root.adm', 'isrealien', 'responsable',NULL,NULL,'SMA');
  echo $ad->getId_fil().'_'.$ad->getNomFil().'<br/>';
  if($ad->estExiste()){
  echo ' existe ';
  }
  if($ad->estPresAStocker()){
  echo ' pres a stovker ';
  }
  / if ($ad->stocker()) {
  echo 'admin bien stocker';
  } else {
  echo 'admin ni stocker !!!' . $ad->getFautes();
  }
  } else {
  ?>
  <form method="post" action="teste.php" enctype="multipart/form-data">
  <input type="file" name='phot'/>
  <input type='submit' value='envoyer'/>
  </form>
  <?php
  }
 */
/*
  include_once ("./Admin.class.php");
  $ad= new Admin();
  $ad->setCin('HH878787');
  $d=$ad->estExiste();
  if($d){
  echo 'yeh';
  }else{
  echo 'noooo';
  }
 */
/* $ad=  Admin::existePourConnecter('llll', 'MM676767','lklkl45657');
  if($ad){
  echo 'yeees';
  }else{
  echo 'oooh NO !';
  } */
/*
  include_once './verify_connexion.php';
  if(verify_connexion('administrateur', array('cin_ad'=>'MM676767','nom_ad'=>'admin'))){
  echo 'connexion';
  }else{
  echo 'non conexion';
  }
 */
/*
  $uid=  getcwd();
  echo $uid;
  echo '<br/>'.sha1('root');

 */

/* include_once './Actualite.class.php';
  //$ac=new Actualite();
  // $ac->recuperer(1);

  $acts=  Actualite::tousActualites();


  foreach ($acts as $ac) {

  if($ac->aUneImage()){
  echo 'image existe';
  }else{
  echo 'non image';
  }
  echo '<img src="../../gerer_act/moteur_img_act.php?id='.$ac->getId().'"/>';
  $ac->afficheImage();
  echo '<pre>';
  print_r($ac);
  echo '</pre>';
  }
 */
/*
  include_once './Personne.class.php';
  include_once './Etudiant.class.php';
  $etd=new Etudiant();
  $etd->recuperer_etudiant(3333333333);
  echo '<pre>';
  print_r($etd);
  echo '</pre>';
  $etd->afficherAvecMoteurImg('../../gerer_etd/moteur_img_etd.php');
 */
/*
include_once './Personne.class.php';
include_once './Professeur.class.php';
$pr = new Professeur();
$cin = 'HH455678';
$pr->recuperer_Professeur($cin);
echo '<pre>';
print_r($pr);
echo '</pre>';
echo $pr->NomDepartement();
$cheminMoteurImgAct='../../gerer_prof/moteur_img_prof.php';
$pr->afficherAvecMoteurImg($cheminMoteurImgAct);
$pr->getModulesProfesseur();
 */
/*
include_once './Departement.class.php';
$depts=  Departement::tousDepartements();
echo '<pre>';
print_r($depts);
echo '</pre>';
 */
//include '../moteur_img_act.php?id=1';

include_once './DelibirationEtudiants.class.php';
$del=new DelibirationEtudiants(1,'S1','S2');

        //print_r($del->getTabIdsMdsSi());
        //print_r($del->getTabIdsMdsSj());
echo '<br/>delibiration *****************<br/>';
echo '<pre>';

print_r($del->getTabCneDip2ans());
echo '</pre>';

?>
<img src="../moteur_img_act.php?id=15" id="img_acc" />
<script>
</script>