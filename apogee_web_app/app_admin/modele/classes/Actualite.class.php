<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Actualite
 *
 * @author abdelmajid
 */
class Actualite {

    protected $id = null;
    protected $titre = null;
    protected $contenu = null;
    protected $image = null;
    protected $date_ac = null;
    protected $cin_p = null;
    protected $id_m = null;
    protected $id_f = null;
    protected $cin_ad = null;
    protected $fautes = null;
    protected $pret_a_stocker = FALSE;

    public function Actualite() {
        
    }

    public function remplir($titre, $contenu, $date_ac, $image, $cin_ad, $cin_p, $id_m, $id_f, $id) {
        $this->pret_a_stocker = TRUE;
        $this->setTitre($titre);
        $this->setContenu($contenu);
        if (isset($date_ac)) {
            $this->setDate($date_ac);
        } else {
            $this->setDateActuelle();
        }
        $this->setImage($image);
        $this->setCinAd($cin_ad);
        $this->setId($id);
        $this->setIdF($id_f);
        $this->setIdM($id_m);
        $this->setCinP($cin_p);
    }

    /**/

    public function stocker() {
        if ($this->estPresAStocker()) {
            global $bdd;
            if ($this->getCinP() != null) {
                $req = 'insert into actualite(titre_acc,contenu_acc,image_acc,date_acc,cin_p,id_f,id_m) 
                         values(:titre,:contenu,:image,:date,:cin_p,:id_f,:id_m)';
                $result = $bdd->prepare($req);
                $result->execute(array(
                    'titre' => $this->getTitre(),
                    'contenu' => $this->getContenu(),
                    'image' => $this->getImage(),
                    'date' => $this->getDate(),
                    'cin_p' => $this->getCinP(),
                    'id_f' => $this->getIdF(),
                    'id_m' => $this->getIdM()
                ));
            } else {
                $req = 'insert into actualite(titre_acc,contenu_acc,image_acc,date_acc,cin_ad) 
                         values(:titre,:contenu,:image,:date,:cin_ad)';
                $result = $bdd->prepare($req);
                $result->execute(array(
                    'titre' => $this->getTitre(),
                    'contenu' => $this->getContenu(),
                    'image' => $this->getImage(),
                    'date' => $this->getDate(),
                    'cin_ad' => $this->getCinAd()
                ));
            }

            $result->closeCursor();
            return TRUE;
        }
        return FALSE;
    }

    /*
     * supprimer une actualite
     */

    public function supprimer() {
        global $bdd;
        $result = $bdd->prepare('delete from actualite where id_acc=:id');
        $result->execute(array('id' => $this->getId()));
        $result->closeCursor();
        return TRUE;
    }

    /**
     * modifier le titre et le contenu et l'image s'il est existe d'une actualite
     */
    public function modifierTCI() {
        global $bdd;
        $image = $this->getImage();
        $titre = $this->getTitre();
        $contenu = $this->getContenu();
        $req = 'update actualite set titre_acc=:titre,contenu_acc=:contenu';
        if (isset($image) and $image != NULL) {
            $req.=',image_acc=:image';
        }
        $req.=' where id_acc=:id';
        $result = $bdd->prepare($req);
        if (isset($image) and $image != NULL) {
            $result->execute(array('titre' => $titre, 'contenu' => $contenu, 'image' => $image, 'id' => $this->getId()));
        } else {
            $result->execute(array('titre' => $titre, 'contenu' => $contenu, 'id' => $this->getId()));
        }
        $result->closeCursor();
        return TRUE;
    }

    /*
     * recuperer une actualite
     */

    public function recuperer($id) {
        global $bdd;
        $result = $bdd->prepare('select *from actualite where id_acc=:id');
        $result->execute(array('id' => $id));
        $don = $result->fetch();
        if (empty($don)) {
            $result->closeCursor();
            return FALSE;
        }
        $this->remplir($don['titre_acc'], $don['contenu_acc'], $don['date_acc'], $don['image_acc'], $don['cin_ad'], $don['cin_p'], $don['id_m'], $don['id_f'], $don['id_acc']);
        $result->closeCursor();
    }

    /* verifiye si l'actualite a une image */

    public function aUneImage() {
        global $bdd;
        $id = $this->getId();
        $result = $bdd->prepare('select id_acc from actualite where id_acc=:id and image_acc is not null');
        $result->execute(array('id' => $id));
        $don = $result->fetch();
        $result->closeCursor();
        if (empty($don)) {
            return FALSE;
        }
        return TRUE;
    }

    /* affichage d'une image */

    public function afficheImage() {
        global $bdd;
        $id = $this->getId();
        $result = $bdd->prepare('select image_acc from actualite where id_acc=:id');
        $result->execute(array('id' => $id));
        $don = $result->fetch();
        $im = $don['image_acc'];
        $file_name = 'uploads_imgs/image_' . $id . '.bin';
        $image = fopen($file_name, 'w');
        fwrite($image, $im);
        /* un prob : la premiere image stocker est la seule qui se lire */
        echo '<img class="image_acc" src="' . $file_name . '" />';
        fclose($image);
        $result->closeCursor();
    }

    /* affichage d'une actualite */

    public function afficher() {
        ?>
        <div class="conte">
            <div class="actualite" onclick="alert('id <?php echo $this->getId(); ?>');">
                <p class="img_ac">
                    <?php
                    if ($this->aUneImage()) {
                        $this->afficheImage();
                    }
                    ?>
                </p>
                <article>
                    <h2 class="titre"><?php echo $this->getTitre(); ?></h2>
                    <p class="contenu"><?php echo $this->getContenu(); ?></p>
                </article>
            </div>
            <?php
            $cin_ad = $this->getCinAd();
            $cin_p = $this->getCinP();
            $infos = '';
            if (isset($cin_ad) and $cin_ad != NULL) {
                global $bdd;
                $result = $bdd->prepare('select nom_ad,prenom_ad,fonction_ad,id_fil from administrateur where cin_ad=:cin');
                $result->execute(array('cin' => $cin_ad));
                $don = $result->fetch();
                /* n'oublier pas d'ajouter une lien vers page infos admin ou professeur qui poste */
                $infos = '<strong>M. ' . $don['nom_ad'] . ' ' . $don['prenom_ad'] . '</strong>
               <strong>' . $don['fonction_ad'];
                if (strcmp($don['fonction_ad'], 'responsable') == 0) {
                    $id_f = $don['id_fil'];
                    $result = $bdd->query('select nom_f from filiere where id_f=' . $id_f);
                    $fil = $result->fetch();
                    $infos.=' du filiere ' . $fil['nom_f'];
                }
                $result->closeCursor();
                $infos.='</strong>';
            } else if (isset($cin_p) and $cin_p != NULL) {
                global $bdd;
                $result = $bdd->prepare('select nom_p,prenom_p from professeur where cin_p=:cin');
                $result->execute(array('cin' => $cin_p));
                $don = $result->fetch();
                /* n'oublier pas d'ajouter une lien vers page infos admin ou professeur qui poste */
                $infos = '<strong>M. ' . $don['nom_p'] . ' ' . $don['prenom_p'] . ' Professeur </strong>';
                $result2 = $bdd->prepare('select nom_mod,nom_f,nom_s from filiere f,modul md,nom_module m where (m.cin_p,m.id_f,m.id_m)=(:cin_p,:id_f,:id_m) and f.id_f=m.id_f and md.id_m=m.id_m');
                $result2->execute(array('cin_p' => $cin_p, 'id_f' => $this->getIdF(), 'id_m' => $this->getIdM()));
                $don2 = $result2->fetch();
                if (!empty($don2)) {
                    $infos.='de <strong>' . $don2['nom_f'] . '_' . $don2['nom_s'] . '_' . $this->getIdM() . '_' . $don2['nom_mod'] . '</strong>';
                }
                $result->closeCursor();
                $result2->closeCursor();
            }
            ?>
            <!-- cette paragraphe ne s'affiche à un etudiant-->
            <p class="infos">poster par <?php echo $infos; ?><em id="acc_date"><?php echo $this->getDate(); ?></em></p>
            <p class="btn_actalite">&nbsp;
                <input type="button" value="modifier" id="modifier" name="modifier" onclick="btnModifier(<?php echo $this->getId(); ?>);"/>
                <input type="button" value="supprimer" id="supprimer" name="supprimer" onclick="btnSupprimer(<?php echo $this->getId(); ?>);"/>
            </p>
        </div>
        <?php
    }

    /**
     * 
     * @global type $bdd
     * @global type $bdd
     * @param type $cheminMoteurImgAct
     */
    public function afficherAvecMoteurImg($cheminMoteurImgAct) {
        ?>
        <div class="conte">
            <div class="actualite" onclick="alert('id <?php echo $this->getId(); ?>');">
                <p class="img_ac">
                    <?php
                    if ($this->aUneImage()) {
                        ?>
                        <img class="image_acc" src="<?php echo $cheminMoteurImgAct . '?id=' . $this->getId(); ?>"/>
                        <?php
                    }
                    ?>
                </p>
                <article>
                    <h2 class="titre"><?php echo $this->getTitre(); ?></h2>
                    <p class="contenu"><?php echo $this->getContenu(); ?></p>
                </article>
            </div>
            <?php
            $cin_ad = $this->getCinAd();
            $cin_p = $this->getCinP();
            $infos = '';
            if (isset($cin_ad) and $cin_ad != NULL) {
                global $bdd;
                $result = $bdd->prepare('select nom_ad,prenom_ad,fonction_ad,id_fil from administrateur where cin_ad=:cin');
                $result->execute(array('cin' => $cin_ad));
                $don = $result->fetch();
                /* n'oublier pas d'ajouter une lien vers page infos admin ou professeur qui poste */
                $infos = '<strong>M. ' . $don['nom_ad'] . ' ' . $don['prenom_ad'] . '</strong>
               <strong>' . $don['fonction_ad'];
                if (strcmp($don['fonction_ad'], 'responsable') == 0) {
                    $id_f = $don['id_fil'];
                    $result = $bdd->query('select nom_f from filiere where id_f=' . $id_f);
                    $fil = $result->fetch();
                    $infos.=' du filiere ' . $fil['nom_f'];
                }
                $result->closeCursor();
                $infos.='</strong>';
            } else if (isset($cin_p) and $cin_p != NULL) {
                global $bdd;
                $result = $bdd->prepare('select nom_p,prenom_p from professeur where cin_p=:cin');
                $result->execute(array('cin' => $cin_p));
                $don = $result->fetch();
                /* n'oublier pas d'ajouter une lien vers page infos admin ou professeur qui poste */
                $infos = '<strong>M. ' . $don['nom_p'] . ' ' . $don['prenom_p'] . ' Professeur </strong>';
                $result2 = $bdd->prepare('select nom_mod,nom_f,nom_s from filiere f,modul md,nom_module m where (m.cin_p,m.id_f,m.id_m)=(:cin_p,:id_f,:id_m) and f.id_f=m.id_f and md.id_m=m.id_m');
                $result2->execute(array('cin_p' => $cin_p, 'id_f' => $this->getIdF(), 'id_m' => $this->getIdM()));
                $don2 = $result2->fetch();
                if (!empty($don2)) {
                    $infos.='de <strong>' . $don2['nom_f'] . '_' . $don2['nom_s'] . '_' . $this->getIdM() . '_' . $don2['nom_mod'] . '</strong>';
                }
                $result->closeCursor();
                $result2->closeCursor();
            }
            ?>
            <!-- cette paragraphe ne s'affiche à un etudiant-->
            <p class="infos">poster par <?php echo $infos; ?><em id="acc_date"><?php echo $this->getDate(); ?></em></p>
            <p class="btn_actalite">&nbsp;
                <input type="button" value="modifier" id="modifier" name="modifier" onclick="btnModifier(<?php echo $this->getId(); ?>);"/>
                <input type="button" value="supprimer" id="supprimer" name="supprimer" onclick="btnSupprimer(<?php echo $this->getId(); ?>);"/>
            </p>
        </div>
        <?php
    }

    /**
     * 
     * @param type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function setTitre($titre) {
        if ($this->verifieChamp($titre)) {
            $this->titre = htmlspecialchars($titre);
        }
    }

    public function setContenu($contenu) {
        if ($this->verifieChamp($contenu)) {
            $this->contenu = htmlspecialchars($contenu);
        }
    }

    public function setDate($date_ac) {
        if ($this->verifieChamp($date_ac)) {
            $this->date_ac = htmlspecialchars($date_ac);
        }
    }

    public function setDateActuelle() {
        $date_ac = date('Y-m-d');
        $this->date_ac = htmlspecialchars($date_ac);
    }

    public function setImage($photo) {
        if ($this->verifieChamp($photo)) {
            if (is_array($photo)) {
                $infosPhoto = pathinfo($photo['name']);
                $exts = array('jpg', 'png', 'jpeg');
                if ($photo['size'] < 512 * 1024 and in_array($infosPhoto['extension'], $exts)) {
                    $photo = file_get_contents($photo['tmp_name']);
                    $this->image = $photo;
                } else {
                    //echo "<script>alert('faute:photo');</script>";
                    $this->fautes.='photo dépasse la taille maximale ou d\'une extention non valides, ';
                    $this->pret_a_stocker = FALSE;
                }
            }
        }
    }

    public function setCinP($cin_p) {
        if ($this->verifieChamp($cin_p)) {
            $this->cin_p = htmlspecialchars($cin_p);
        }
    }

    public function setIdF($id_f) {
        if ($this->verifieChamp($id_f)) {
            $this->id_f = htmlspecialchars($id_f);
        }
    }

    public function setIdM($id_m) {
        if ($this->verifieChamp($id_m)) {
            $this->id_m = htmlspecialchars($id_m);
        }
    }

    public function setCinAd($cin_ad) {
        if ($this->verifieChamp($cin_ad)) {
            $this->cin_ad = htmlspecialchars($cin_ad);
        }
    }

    public function getId() {
        return htmlspecialchars($this->id);
    }

    public function getTitre() {
        return htmlspecialchars($this->titre);
    }

    public function getContenu() {
        return htmlspecialchars($this->contenu);
    }

    public function getIdM() {
        return htmlspecialchars($this->id_m);
    }

    public function getIdF() {
        return htmlspecialchars($this->id_f);
    }

    public function getCinP() {
        return htmlspecialchars($this->cin_p);
    }

    public function getCinAd() {
        return htmlspecialchars($this->cin_ad);
    }

    public function getDate() {
        return $this->date_ac;
    }

    public function getImage() {
        return $this->image;
    }

    public function estPresAStocker() {
        return $this->pret_a_stocker;
    }

    public function getFautes() {
        return htmlentities($this->fautes);
    }

    protected function verifieChamp($champ) {
        if (empty($champ) || $champ == NULL || !isset($champ))
            return FALSE;
        return TRUE;
    }

    /* toutes les actualtes ordonnes du la plus recente au la plus aucienne */

    public static function tousActualites() {
        global $bdd;
        $acts = array();
        $result = $bdd->query('select *from actualite order by date_acc desc,id_acc desc');
        while ($ac = $result->fetch()) {
            $act = new Actualite();
            $act->remplir($ac['titre_acc'], $ac['contenu_acc'], $ac['date_acc'], $ac['image_acc'], $ac['cin_ad'], $ac['cin_p'], $ac['id_m'], $ac['id_f'], $ac['id_acc']);
            array_push($acts, $act);
        }
        $result->closeCursor();
        return $acts;
    }

    /* actualites d'un admin */

    public static function actualitesAdmin($cin_ad) {
        global $bdd;
        $acts = array();
        $result = $bdd->prepare('select *from actualite where cin_ad=:cin order by date_acc desc,id_acc desc');
        $result->execute(array('cin' => $cin_ad));
        while ($ac = $result->fetch()) {
            $act = new Actualite();
            $act->remplir($ac['titre_acc'], $ac['contenu_acc'], $ac['date_acc'], $ac['image_acc'], $ac['cin_ad'], $ac['cin_p'], $ac['id_m'], $ac['id_f'], $ac['id_acc']);
            array_push($acts, $act);
        }
        $result->closeCursor();
        return $acts;
    }

    /* actualites des responsables */

    public static function actualitesResponsables() {
        global $bdd;
        $acts = array();
        $result = $bdd->query("select *from actualite a where a.cin_ad is not null and (select fonction_ad from administrateur where cin_ad=a.cin_ad)='responsable' order by date_acc desc,id_acc desc");
        while ($ac = $result->fetch()) {
            $act = new Actualite();
            $act->remplir($ac['titre_acc'], $ac['contenu_acc'], $ac['date_acc'], $ac['image_acc'], $ac['cin_ad'], $ac['cin_p'], $ac['id_m'], $ac['id_f'], $ac['id_acc']);
            array_push($acts, $act);
        }
        $result->closeCursor();
        return $acts;
    }

    /* actualites des professeurs */

    public static function actualitesProfesseurs() {
        global $bdd;
        $acts = array();
        $result = $bdd->query('select *from actualite where cin_p is not null order by date_acc desc,id_acc desc');
        while ($ac = $result->fetch()) {
            $act = new Actualite();
            $act->remplir($ac['titre_acc'], $ac['contenu_acc'], $ac['date_acc'], $ac['image_acc'], $ac['cin_ad'], $ac['cin_p'], $ac['id_m'], $ac['id_f'], $ac['id_acc']);
            array_push($acts, $act);
        }
        $result->closeCursor();
        return $acts;
    }

}

