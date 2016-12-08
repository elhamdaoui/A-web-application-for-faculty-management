<?php

class Professeur extends Personne {

    protected $pwd = null;
    protected $id_f = null;
    protected $nom_f = null;
    protected $id_f_cor = null;
    protected $id_d_chef = null;
    protected $nom_d_chef = null;
    protected $date_ajt_p;

    /**
     * 
     */
    public function Professeur() {
        
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
     * @param type $nom_f
     * @param type $id_f_cor
     * @param type $id_d_chef
     * @param type $pwd
     */
    public function remplir_Professeur($cin, $nom, $prenom, $dateN, $adresse, $ntel, $photo, $sexe, $email, $nationalite, $nom_f, $id_f_cor, $id_d_chef, $date_ajt_p, $pwd) {
        parent::Personne($cin, $nom, $prenom, $dateN, $adresse, $ntel, $photo, $sexe, $email, $nationalite);
        $this->setIdF_par_Nom_F($nom_f);
        $this->setIdFCor($id_f_cor);
        $this->setIdDeptChef($id_d_chef);
        $this->setPwd($pwd);
        $this->setDateAjout($date_ajt_p);
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
        $result = $bdd->prepare('insert into professeur(cin_p,nom_p,prenom_p,sexe_p,email_p
            ,ntel_p,adresse_p,pwd_p,dateN_p,id_f,date_ajt_p,photo_p,nationalite) 
            values(:cin,:nom,:pnom,:sexe,:email,:ntel,:adresse,
            :pwd,:date_n,:id_f,:date,:photo,:nat)');
        $result->execute(array(
            'cin' => $this->getCin(),
            'nom' => $this->getNom(),
            'pnom' => $this->getPrenom(),
            'sexe' => $this->getSexe(),
            'email' => $this->getEmail(),
            'ntel' => $this->getNtel(),
            'adresse' => $this->getAdresse(),
            'pwd' => $this->getPwd(),
            'date_n' => $this->getDateN(),
            'id_f' => $this->get_id_f(),
            'date' => $this->getDateAjout(),
            'photo' => $this->getPhoto(),
            'nat' => $this->getNationalite()
        ));
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $cin
     */
    public function recuperer_Professeur($cin) {
        global $bdd;
        $result = $bdd->prepare('select cin_p,nom_p,prenom_p,sexe_p,email_p,ntel_p,adresse_p,pwd_p,
            dateN_p,id_f,id_f_cor,id_d_chef,photo_p,nationalite,date_ajt_p from professeur where cin_p=:cin');
        $result->execute(array('cin' => $cin));
        $don = $result->fetch();
        if (empty($don)) {
            return FALSE;
        }
        $nom_f = $this->setNomFil_par_IdFil($don['id_f']);
        $this->remplir_Professeur($don['cin_p'], $don['nom_p'], $don['prenom_p'], $don['dateN_p'], $don['adresse_p'], $don['ntel_p'], $don['photo_p'], $don['sexe_p'], $don['email_p'], $don['nationalite'], $nom_f, $don['id_f_cor'], $don['id_d_chef'], $don['date_ajt_p'], $don['pwd_p']);
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function supprimer() {
        global $bdd;
        if ($this->verifieChamp($this->getCin())) {
            $result = $bdd->prepare('delete from professeur where cin_p=:cin');
            $result->execute(array('cin' => $this->getCin()));
            $result->closeCursor();
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * @global type $bdd
     */
    public function setFiliereBDD() {
        global $bdd;
        $result = $bdd->prepare('update professeur set id_f=:idf where cin_p=:cin');
        $result->execute(array('idf' => $this->get_id_f(), 'cin' => $this->getCin()));
        $result->closeCursor();
    }

    /**
     * 
     * @global type $bdd
     */
    public function setCoordinateurFilBDD() {
        global $bdd;
        $result = $bdd->prepare('update filiere set cin_cor=:cin where id_f=:idf');
        $result->execute(array('idf' => $this->get_id_f(), 'cin' => $this->getCin()));
        $result->closeCursor();
        $result2 = $bdd->prepare('update professeur set id_f_cor=:idf where cin_p=:cin');
        $result2->execute(array('idf' => $this->get_id_f(), 'cin' => $this->getCin()));
        $result2->closeCursor();
    }

    /**
     * 
     * @global type $bdd
     */
    public function setCheufDeptBDD() {
        global $bdd;
        $result = $bdd->prepare('update departement set cin_chef=:cin where id_d=:idd');
        $result->execute(array('idd' => $this->getIdDepartement(), 'cin' => $this->getCin()));
        $result->closeCursor();
        $result2 = $bdd->prepare('update professeur set id_d_chef=:idd where cin_p=:cin');
        $result2->execute(array('idd' => $this->getIdDepartement(), 'cin' => $this->getCin()));
        $result2->closeCursor();
    }

    /**
     * @global type $bdd
     * @param type $attr
     * @param type $val
     * @return boolean
     * @throws Exception
     */
    public function modifierAttribue($attr, $val) {
        try {
            if (!$this->verifieChamp($attr) or !$this->verifieChamp($val)) {
                throw new Exception('des informations non trouves');
            }
            global $bdd;
            $result = $bdd->prepare('update professeur set ' . $attr . '=:val where cin_p=:cin');
            $result->execute(array('val' => $val, 'cin' => $this->getCin()));
            $result->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("Erreur:' . $e->getMessage() . ' ' . $this->getCin() . '");</script>';
            return FALSE;
        }
    }

    /**
     * methode permete d'afficher l'image d'un professeur 
     * a l'aide d'un fichier php qui peut afficher l'image
     * d'un prof sachant leur cin .
     * le chemin est donner au parametres car on sait pas ou nous trouverons
     * lorsque on fait appelle a cette methode.
     * @param type $cheminMoteurImgAct
     */
    public function afficherAvecMoteurImg($cheminMoteurImgAct) {
        ?>
        <img class="img_prof" src="<?php echo $cheminMoteurImgAct . '?id=' . $this->getCin(); ?>"/>
        <?php
    }

    /**
     * 
     * @param type $password
     */
    public function setPwd($password) {
        if ($this->verifieChamp($password))
            $this->pwd = htmlspecialchars($password);
//echo "<script>alert('pass_ad:$password');</script>";
    }

    /**
     * 
     * @param type $id_f
     */
    public function setIdFil($id_f) {
        if ($this->verifieChamp($id_f))
            $this->id_f = htmlspecialchars($id_f);
    }

    /**
     * 
     * @param type $nom_f
     */
    public function setNomFil($nom_f) {
        if ($this->verifieChamp($nom_f))
            $this->nom_f = htmlspecialchars($nom_f);
    }

    /**
     * 
     * @param type $id_f_cor
     */
    public function setIdFCor($id_f_cor) {
        if ($this->verifieChamp($id_f_cor))
            $this->id_f_cor = htmlspecialchars($id_f_cor);
    }

    /**
     * 
     * @param type $id_d
     */
    public function setIdDeptChef($id_d) {
        if ($this->verifieChamp($id_d))
            $this->id_d_chef = htmlspecialchars($id_d);
    }

    /**
     * 
     * @param type $nom_d
     */
    public function setNomDeptChef($nom_d) {
        if ($this->verifieChamp($nom_d))
            $this->nom_d_chef = htmlspecialchars($nom_d);
    }

    public function setDateAjout($date_ajt) {
        if ($this->verifieChamp($date_ajt))
            $this->date_ajt_p = htmlspecialchars($date_ajt);
    }

    /**
     * 
     * @return null
     */
    public function get_id_f() {
        if ($this->verifieChamp($this->id_f)) {
            return htmlspecialchars($this->id_f);
        }
        return NULL;
    }

    /**
     * 
     * @return null
     */
    public function get_nom_f() {
        if ($this->verifieChamp($this->nom_f)) {
            return htmlspecialchars($this->nom_f);
        }
        return NULL;
    }

    /**
     * 
     * @return null
     */
    public function get_id_f_cor() {
        if ($this->verifieChamp($this->id_f_cor)) {
            return htmlspecialchars($this->id_f_cor);
        }
        return NULL;
    }

    /**
     * 
     * @return null
     */
    public function get_id_deptChef() {
        if ($this->verifieChamp($this->id_d_chef)) {
            return htmlspecialchars($this->id_d_chef);
        }
        return NULL;
    }

    /**
     * 
     * @return null
     */
    public function get_nom_DeptChef() {
        if ($this->verifieChamp($this->nom_d_chef)) {
            return htmlspecialchars($this->nom_d_chef);
        }
        return NULL;
    }

    /**
     * 
     * @return null
     */
    public function getPwd() {
        if ($this->verifieChamp($this->pwd)) {
            return htmlspecialchars($this->pwd);
        }
        return NULL;
    }

    /**
     * 
     */
    public function getDateAjout() {
        if ($this->verifieChamp($this->date_ajt_p)) {
            return htmlspecialchars($this->date_ajt_p);
        }
        return NULL;
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function getDepartement() {
        global $bdd;
        $result = $bdd->prepare('select nom_d from departement where id_d=(select id_d from filiere where id_f=:id)');
        $result->execute(array('id' => $this->get_id_f()));
        $don = $result->fetch();
        $nom_d = $don['nom_d'];
        $result->closeCursor();
        return $nom_d;
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function getIdDepartement() {
        global $bdd;
        $result = $bdd->prepare('select id_d from filiere where id_f=:id');
        $result->execute(array('id' => $this->get_id_f()));
        $don = $result->fetch();
        $id_d = $don['id_d'];
        $result->closeCursor();
        return $id_d;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom_fil
     * @return type
     */
    public function setIdF_par_Nom_F($nom_fil) {
        if ($this->verifieChamp($nom_fil)) {
            global $bdd;
            $result = $bdd->prepare('select id_f from filiere where nom_f=:nom');
            $result->execute(array('nom' => $nom_fil));
            $donne = $result->fetch();
            $this->setIdFil($donne['id_f']);
            $result->closeCursor();
            return $donne['id_f'];
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @return type
     */
    public function setNomFil_par_IdFil($id_f) {
        if ($this->verifieChamp($id_f)) {
            global $bdd;
            $result = $bdd->prepare('select nom_f from filiere where id_f=:id');
            $result->execute(array('id' => $id_f));
            $donne = $result->fetch();
            $this->setNomFil($donne['nom_f']);
            $result->closeCursor();
            return $donne['nom_f'];
        }
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function NomDepartement() {
        global $bdd;
        $id_f = $this->get_id_f();
        $result = $bdd->prepare('select id_d from filiere where id_f=:id');
        $result->execute(array('id' => $id_f));
        $don = $result->fetch();
        $result2 = $bdd->prepare('select nom_d from departement where id_d=:id');
        $result2->execute(array('id' => $don['id_d']));
        $don2 = $result2->fetch();
        $dep = $don2['nom_d'];
        $result->closeCursor();
        $result2->closeCursor();
        return $dep;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom_d
     * @return type
     */
    public function setIdDeptChef_par_Nom_Dept($nom_d) {
        if ($this->verifieChamp($nom_d)) {
            global $bdd;
            $result = $bdd->prepare('select id_d from departement where nom_d=:nom');
            $result->execute(array('nom' => $nom_d));
            $donne = $result->fetch();
            $this->setIdDeptChef($donne['id_d']);
            $result->closeCursor();
            return $donne['id_d'];
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_d
     * @return type
     */
    public function setNomDeptChef_par_Id_Dept($id_d) {
        if ($this->verifieChamp($id_d)) {
            global $bdd;
            $result = $bdd->prepare('select nom_d from departement where id_d=:id');
            $result->execute(array('id' => $id_d));
            $donne = $result->fetch();
            $this->setNomDeptChef($donne['nom_d']);
            $result->closeCursor();
            return $donne['nom_d'];
        }
    }

    /**
     * verifier l'existence d'un etudiant
     * @global type $bdd
     * @return \type
     */
    public function estExiste() {
        global $bdd;
        $result1 = $bdd->prepare('select nom_p from professeur where cin_p=:cin');
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
     * routourne le nom du departemet si le prof est chef actuel du dept sinon False
     * @global type $bdd
     * @return boolean
     */
    public function chefActuelDepartemet() {
        global $bdd;
        $cin = $this->getCin();
        $id_d = $this->get_id_deptChef();
        $result = $bdd->prepare('select nom_d from departement where cin_chef=:cin and id_d=:id');
        $result->execute(array('cin' => $cin, 'id' => $id_d));
        $don = $result->fetch();
        if (empty($don)) {
            return FALSE;
        }
        $nom_d = $don['nom_d'];
        $result->closeCursor();
        return $nom_d;
    }

    /**
     * routourne le nom du filiere si le prof est coordinateur actuel du dept sinon False
     * @global type $bdd
     * @return boolean
     */
    public function corActuelFiliere() {
        global $bdd;
        $cin = $this->getCin();
        $id_f = $this->get_id_f_cor();
        $result = $bdd->prepare('select nom_f from filiere where cin_cor=:cin and id_f=:id');
        $result->execute(array('cin' => $cin, 'id' => $id_f));
        $don = $result->fetch();
        if (empty($don)) {
            return FALSE;
        }
        $nom_f = $don['nom_f'];
        $result->closeCursor();
        return $nom_f;
    }

    /**
     * retourne tous les modules etudié par un professeur 
     * @global type $bdd
     * @return array
     */
    public function getModulesProfesseur() {
        global $bdd;
        $mods = array();
        $result = $bdd->prepare('select f.nom_f,md.nom_s,nm.id_m,nm.nom_mod,nm.id_f from 
            nom_module nm,modul md,filiere f 
            where nm.cin_p=:cin 
            and md.id_m=nm.id_m 
            and f.id_f=nm.id_f');
        $result->execute(array('cin' => $this->getCin()));
        while ($don = $result->fetch()) {
            $mod = array('nom_f' => $don[0], 'nom_s' => $don[1], 'id_m' => $don[2], 'nom_mod' => $don[3], 'id_f' => $don[4]);
            array_push($mods, $mod);
        }
        $result->closeCursor();
        return $mods;
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_m
     * @param type $id_f
     * @return type
     * @throws Exception
     */
    public function getInscriptionModule($id_m, $id_f) {
        try {
            if (!$this->verifieChamp($id_f) || !$this->verifieChamp($id_m)) {
                throw new Exception('filiere ou module non existe');
            }
            global $bdd;
            $res = $bdd->prepare('select ins.cne_e,etd.numins_e,etd.nom_e,etd.prenom_e,ins.id_m,md.nom_mod,case when ins.note_N<0 then \'ABS\' else ins.note_N end as note_N,case when ins.note_R<0 then \'ABS\' else ins.note_R end as note_R,ins.date_ins,ins.nb_ins,
                case when ins.note_N<0 then \'ABS\' 
                when ins.note_N<10 and ins.note_R is null then \'RATT\' 
                when ins.note_N<10 and ins.note_R >=10 then \'VAR\' 
                when ins.note_N<10 and ins.note_R <10 then \'NV\' 
                when ins.note_N>=10 then \'VM\' 
                end as etat_V
                from  inscription ins,nom_module md,etudiant etd where md.id_f=:idf and md.id_m=:idm and md.cin_p=:cin and ins.cne_e=etd.cne_e 
                and ins.id_m=md.id_m and etd.id_f=md.id_f and ins.etat_V is null order by etd.nom_e');
            $res->execute(array('idf' => $id_f, 'idm' => $id_m, 'cin' => $this->getCin()));
            $don = $res->fetchAll();
            if (empty($don)) {
                throw new Exception('aucun etdudiant n\'inscrit dans ce module\n ou des infos sont fausses');
            }
            $res->closeCursor();
            return $don;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_m
     * @param type $id_f
     * @param type $atrr
     * @param type $cnesNotes
     * @throws Exception
     */
    public function setNotesPourModule($id_m, $id_f, $atrr, $cnesNotes) {
        try {
            if (!$this->verifieChamp($id_f) || !$this->verifieChamp($id_m) || empty($cnesNotes)) {
                throw new Exception('filiere ,module non existe ou bien aucun etudiants specifie ');
            }
            global $bdd;
            foreach ($cnesNotes as $cne => $note) {
                if ($note < 0){
                    $note = -1;
                }
                $res = $bdd->prepare('update inscription  set ' . $atrr . '=:note where cne_e=:cne and id_m=:idm');
                $res->execute(array('note' => $note, 'cne' => $cne, 'idm' => $id_m));
            }
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
        }
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function getIdsActualites() {
        global $bdd;
        $res = $bdd->prepare('select id_acc from actualite where cin_p=:cin order by date_acc desc');
        $res->execute(array('cin' => $this->getCin()));
        $don = $res->fetchAll();
        $res->closeCursor();
        return $don;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom
     * @param type $cin
     * @param type $pwd
     * @return boolean|\Professeur
     * @throws Exception
     */
    public static function existePourConnecter($nom, $cin, $pwd) {
        try {
            global $bdd;
            $result = $bdd->prepare('select cin_p,nom_p,prenom_p,sexe_p,email_p,ntel_p,adresse_p,pwd_p,
            dateN_p,id_f,id_f_cor,id_d_chef,photo_p,nationalite,date_ajt_p from professeur where cin_p=:cin and nom_p=:nom and pwd_p=:pwd');
            $result->execute(array('cin' => $cin, 'nom' => strtoupper($nom), 'pwd' => sha1($pwd)));
            $don = $result->fetch();
            if (empty($don)) {
                throw new Exception("compte n'existe pas");
            }
            $pr = new Professeur();
            $nom_f = $pr->setNomFil_par_IdFil($don['id_f']);
            $pr->remplir_Professeur($don['cin_p'], $don['nom_p'], $don['prenom_p'], $don['dateN_p'], $don['adresse_p'], $don['ntel_p'], $don['photo_p'], $don['sexe_p'], $don['email_p'], $don['nationalite'], $nom_f, $don['id_f_cor'], $don['id_d_chef'], $don['date_ajt_p'], $don['pwd_p']);
            $result->closeCursor();
            return $pr;
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $cne
     */
    public static function professeursActuellesPourEtudiant($cne) {
        global $bdd;
        $resMods = $bdd->prepare('select id_m from inscription where cne_e=:cne and etat_v is null');
        $resMods->execute(array('cne' => $cne));
        $idsmmods = array();
        while ($mod = $resMods->fetch()) {
            array_push($idsmmods, '\'' . $mod['id_m'] . '\'');
        }
        $idsmd = join($idsmmods, ',');
        $resProfs = $bdd->prepare('select md.id_m,md.nom_mod,m.nom_s,p.nom_p,p.prenom_p,p.email_p,p.ntel_p,p.cin_p 
            from nom_module md,modul m,professeur p 
            where md.id_m in (' . $idsmd . ') 
                and md.cin_p is not null
                and m.id_m=md.id_m 
                and p.cin_p=md.cin_p 
                and md.id_f=(select id_f from etudiant where cne_e=:cne)');
        $resProfs->execute(array('cne' => $cne));
        $profs = $resProfs->fetchAll();
        $resMods->closeCursor();
        $resProfs->closeCursor();
        return $profs;
    }

    /**
     * 
     * @global type $bdd
     * @return array
     */
    public static function tousProfesseur() {
        global $bdd;
        $prfs = array();
        $result = $bdd->query('select cin_p,nom_p,prenom_p,sexe_p,email_p,ntel_p,adresse_p,pwd_p, dateN_p,id_f,id_f_cor,id_d_chef,photo_p,nationalite,date_ajt_p from professeur');
        while ($don = $result->fetch()) {
            $pr = new Professeur();
            $nom_f = $pr->setNomFil_par_IdFil($don['id_f']);
            $pr->remplir_Professeur($don['cin_p'], $don['nom_p'], $don['prenom_p'], $don['dateN_p'], $don['adresse_p'], $don['ntel_p'], $don['photo_p'], $don['sexe_p'], $don['email_p'], $don['nationalite'], $nom_f, $don['id_f_cor'], $don['id_d_chef'], $don['date_ajt_p'], $don['pwd_p']);
            array_push($prfs, $pr);
        }
        $result->closeCursor();
        return $prfs;
    }

    /**
     * 
     * @global type $bdd
     * @return array
     */
    public static function tousProfesseursParFilieres() {
        global $bdd;
        $prfsFils = array();
        $result0 = $bdd->query('select id_f,nom_f from filiere');
        while ($fil = $result0->fetch()) {
            $id_ff = $fil['id_f'];
            $result = $bdd->prepare('select cin_p,nom_p,prenom_p,sexe_p,email_p,ntel_p,adresse_p,pwd_p, dateN_p,id_f,id_f_cor,id_d_chef,photo_p,nationalite,date_ajt_p from professeur where id_f=:idf');
            $result->execute(array('idf' => $id_ff));
            $prfs = array();
            while ($don = $result->fetch()) {
                $pr = new Professeur();
                $nom_f = $pr->setNomFil_par_IdFil($don['id_f']);
                $pr->remplir_Professeur($don['cin_p'], $don['nom_p'], $don['prenom_p'], $don['dateN_p'], $don['adresse_p'], $don['ntel_p'], $don['photo_p'], $don['sexe_p'], $don['email_p'], $don['nationalite'], $nom_f, $don['id_f_cor'], $don['id_d_chef'], $don['date_ajt_p'], $don['pwd_p']);
                array_push($prfs, $pr);
            }
            $result->closeCursor();
            $prfsFils[$fil['nom_f']] = $prfs;
        }
        $result->closeCursor();
        return $prfsFils;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nm
     * @return array
     */
    public static function tousProfesseursLikeNom($nm) {
        global $bdd;
        $prfs = array();
        $result = $bdd->query("select cin_p,nom_p,prenom_p,sexe_p,email_p,ntel_p,adresse_p,pwd_p, dateN_p,id_f,id_f_cor,id_d_chef,photo_p,nationalite,date_ajt_p from professeur where nom_p like '$nm%'");
        while ($don = $result->fetch()) {
            $pr = new Professeur();
            $nom_f = $pr->setNomFil_par_IdFil($don['id_f']);
            $pr->remplir_Professeur($don['cin_p'], $don['nom_p'], $don['prenom_p'], $don['dateN_p'], $don['adresse_p'], $don['ntel_p'], $don['photo_p'], $don['sexe_p'], $don['email_p'], $don['nationalite'], $nom_f, $don['id_f_cor'], $don['id_d_chef'], $don['date_ajt_p'], $don['pwd_p']);
            array_push($prfs, $pr);
        }
        $result->closeCursor();
        return $prfs;
    }

}
?>
        