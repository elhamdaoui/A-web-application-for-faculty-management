<?php

class Admin extends Personne {

    protected $fonction = null;
    /* password par default est CIN */
    protected $id_f = null;
    protected $nom_f = null;
    protected $pwd = null;

    /**
     * 
     */
    public function Admin() {
        
    }

    /**
     * 
     * @param type $cin
     * @param type $nom
     * @param type $prenom
     * @param type $dateN
     * @param type $adresse
     * @param type $ntel
     * @param type $photo
     * @param type $sexe
     * @param type $email
     * @param type $nationalite
     * @param type $fonction
     * @param type $pwd
     * @param type $id_f
     * @param type $nom_f
     */
    public function remplir_Admin($cin, $nom, $prenom, $dateN, $adresse, $ntel, $photo, $sexe, $email, $nationalite, $fonction, $pwd, $id_f = null, $nom_f = null) {
        $this->Personne($cin, $nom, $prenom, $dateN, $adresse, $ntel, $photo, $sexe, $email, $nationalite);
        $this->setFonction($fonction);
        $this->setPwd($pwd);
        //if($id_f!=NULL) echo 'id_f non null';
        if (strcmp(strtolower($fonction), 'responsable') == 0) {
            if ($this->verifieChamp($id_f)) {
                $this->setId_F($id_f);
                $this->setNomFil_by_IdFil($id_f);
            } else if ($this->verifieChamp($nom_f)) {
                $this->setNomFil($nom_f);
                $this->setIdFil_by_NomFil($nom_f);
            }
        }
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function stocker() {

        if ($this->estExiste() || !$this->estPresAStocker()) {

            return FALSE;
        }

        global $bdd;
        $req = 'nom_ad,prenom_ad,dateN_ad,pwd_ad,email_ad,fonction_ad,cin_ad,
            photo_ad,sexe_ad,adresse_ad,ntel_ad,nationalite';
        $val = ':nom,:prenom,:daten,:pwd,:email,:fonction,:cin,:photo,:sexe,:adresse,:ntel,:nat';
        $arr = array('nom' => $this->getNom(),
            'prenom' => $this->getPrenom(),
            'daten' => $this->getDateN(),
            'pwd' => sha1($this->getCin()),
            'email' => $this->getEmail(),
            'fonction' => $this->getFonction(),
            'cin' => $this->getCin(),
            'photo' => $this->getPhoto(),
            'sexe' => $this->getSexe(),
            'adresse' => $this->getAdresse(),
            'ntel' => $this->getNtel(),
            'nat' => $this->getNationalite());
        if ($this->verifieChamp($this->getId_fil())) {
            $req.=',id_fil';
            $val.=',:id_f';
            $arr['id_f'] = $this->getId_fil();
        }
        $result = $bdd->prepare('insert into administrateur(' . $req . ') values(' . $val . ')');
        $result->execute($arr);
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $cin
     */
    public function recuperer_Admin($cin) {
        global $bdd;
        $result = $bdd->prepare('select *from administrateur where cin_ad=:cin');
        $result->execute(array('cin' => $cin));
        $don = $result->fetch();
        $this->remplir_Admin($don['CIN_AD'], $don['NOM_AD'], $don['PRENOM_AD'], $don['dateN_ad'], $don['adresse_ad'], $don['ntel_ad'], $don['PHOTO_AD'], $don['sexe_ad'], $don['EMAIL_AD'], $don['nationalite'], $don['FONCTION_AD'], $don['PWD_AD'], $don['id_fil']);
        $this->setNomFil_by_IdFil($don['id_fil']);
        $result->closeCursor();
    }

    /**
     * 
     * @global type $bdd
     * @param type $tabAssoc
     */
    public function modifier($tabAssoc) {
        if ($this->verifieChamp($tabAssoc)) {
            global $bdd;
            $req = 'update administrateur set ';
            $i = 1;
            foreach ($tabAssoc as $cle => $val) {
                if (is_string($val)) {
                    $req.=$cle . '=\'' . $val . '\'';
                } else {
                    $req.=$cle . '=' . $val;
                }
                if ($i < count($tabAssoc)) {
                    $req.=', ';
                }
                $i++;
            }
            $req.=' where cin_ad=\'' . $this->getCin() . '\'';
            $bdd->query($req);
        }
    }

    /**
     * 
     * @global type $bdd
     */
    public function setFilNullBdd() {
        global $bdd;
        $result = $bdd->prepare('update administrateur set id_fil=null where cin_ad=:cin');
        $result->execute(array('cin' => $this->getCin()));
        $result->closeCursor();
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function supprimer() {
        global $bdd;
        if ($this->verifieChamp($this->getCin())) {
            $result = $bdd->prepare('delete from administrateur where cin_ad=:cin');
            $result->execute(array('cin' => $this->getCin()));
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function estExiste() {
        global $bdd;
        $result1 = $bdd->prepare('select *from administrateur where cin_ad=:cin');
        $result1->execute(array('cin' => $this->getCin()));
        $don1 = $result1->fetch();
        $result1->closeCursor();
        if (!empty($don1)) {
            $this->fautes.='cin deja utiliser, ';
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * @param type $fct
     */
    public function setFonction($fct) {
        if ($this->verifieChamp($fct)) {
            $this->fonction = strtolower(htmlspecialchars($fct));
        } else {
            $this->pret_a_stocker = FALSE;
            $this->fautes.='Fonction, ';
            //echo "<script>alert('fct-ad:$fct');</script>";
        }
    }

    /*
      public function setLogin($login) {
      $this->login = htmlspecialchars($login);
      }
     */

    /**
     * 
     * @param type $password
     */
    public function setPwd($password) {
        $this->pwd = htmlspecialchars($password);
        //echo "<script>alert('pass_ad:$password');</script>";
    }

    /**
     * 
     * @param type $id_f
     */
    public function setId_F($id_f) {
        if ($this->verifieChamp($id_f)) {
            $this->id_f = htmlspecialchars($id_f);
        }
        //echo "<script>alert('pass_ad:$password');</script>";
    }

    /**
     * 
     * @param type $nom_f
     */
    public function setNomFil($nom_f) {
        if ($this->verifieChamp($nom_f)) {
            $this->nom_f = htmlspecialchars($nom_f);
        }
        //echo "<script>alert('pass_ad:$password');</script>";
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom_fil
     */
    public function setIdFil_by_NomFil($nom_fil) {
        if ($this->verifieChamp($nom_fil)) {
            global $bdd;
            $result = $bdd->prepare('select id_f from filiere where nom_f=:nom');
            $result->execute(array('nom' => $nom_fil));
            $donne = $result->fetch();
            $this->setId_F($donne['id_f']);
            $result->closeCursor();
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     */
    public function setNomFil_by_IdFil($id_f) {
        if ($this->verifieChamp($id_f)) {
            global $bdd;
            $result = $bdd->prepare('select nom_f from filiere where id_f=:id');
            $result->execute(array('id' => $id_f));
            $donne = $result->fetch();
            $this->setNomFil($donne['nom_f']);
            $result->closeCursor();
        }
    }

    /**
     * 
     * @return null
     */
    public function getFonction() {
        if ($this->verifieChamp($this->fonction))
            return htmlspecialchars($this->fonction);
        return null;
    }

    /*
      public function getLogin() {
      if($this->verifieChamp($this->login))
      return htmlspecialchars($this->login);
      return null;
      }
     */

    /**
     * 
     * @return null
     */
    public function getPwd() {
        if ($this->verifieChamp($this->pwd))
            return htmlspecialchars($this->pwd);
        return null;
    }

    /**
     * 
     * @return null
     */
    public function getId_fil() {
        if ($this->verifieChamp($this->id_f))
            return htmlspecialchars($this->id_f);
        return null;
    }

    /**
     * 
     * @return null
     */
    public function getNomFil() {
        if ($this->verifieChamp($this->pwd))
            return htmlspecialchars($this->nom_f);
        return NULL;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom
     * @param type $cin
     * @param type $pwd
     * @return boolean|\Admin
     */
    public static function existePourConnecter($nom, $cin, $pwd) {
        /* le PWD ici est non chifrée */
        global $bdd;
        $result = $bdd->prepare('select *from administrateur where cin_ad=:cin and nom_ad=:nom and pwd_ad=:pwd');
        $result->execute(array(
            'cin' => htmlspecialchars($cin),
            'nom' => htmlspecialchars($nom),
            'pwd' => sha1(htmlspecialchars($pwd))
        ));
        $don = $result->fetch();
        if (empty($don)) {
            return FALSE;
        }
        $ad = new Admin();
        $ad->recuperer_Admin($cin);
        return $ad;
    }

    /**
     * 
     * @global type $bdd
     * @return array
     */
    public static function tousAdmins() {
        global $bdd;
        $admins = array();
        $result = $bdd->query('select *from administrateur');
        while ($don = $result->fetch()) {
            $adm = new Admin();
            $adm->remplir_Admin($don['CIN_AD'], $don['NOM_AD'], $don['PRENOM_AD'], $don['dateN_ad'], $don['adresse_ad'], $don['ntel_ad'], $don['PHOTO_AD'], $don['sexe_ad'], $don['EMAIL_AD'], $don['nationalite'], $don['FONCTION_AD'], $don['PWD_AD'], $don['id_fil']);
            $adm->setNomFil_by_IdFil($don['id_fil']);
            array_push($admins, $adm);
        }
        return $admins;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom
     * @return array
     */
    public static function adminsLikeNom($nom) {
        global $bdd;
        $admins = array();
        $result = $bdd->query("select *from administrateur where nom_ad like '$nom%'");
        //$result->execute(array('nom' => $nom));
        while ($don = $result->fetch()) {
            $adm = new Admin();
            $adm->remplir_Admin($don['CIN_AD'], $don['NOM_AD'], $don['PRENOM_AD'], $don['dateN_ad'], $don['adresse_ad'], $don['ntel_ad'], $don['PHOTO_AD'], $don['sexe_ad'], $don['EMAIL_AD'], $don['nationalite'], $don['FONCTION_AD'], $don['PWD_AD'], $don['id_fil']);
            $adm->setNomFil_by_IdFil($don['id_fil']);
            array_push($admins, $adm);
        }
        return $admins;
    }

}

?>
