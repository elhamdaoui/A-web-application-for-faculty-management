<?php

class Etudiant extends Personne {

    protected $cne = null;
    protected $lieun = null;
    protected $nom_bac = null;
    protected $numins = null;
    protected $dateins = null;
    protected $id_f = null;
    protected $nom_f = null;
    protected $moyenne_bac = null;
    protected $date_bac = null;
    protected $type_bac = null;
    protected $nat_bac = null;
    protected $pwd = null;

    /**
     * 
     */
    public function Etudiant() {
        
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
     * @param type $cne
     * @param type $lieun
     * @param type $nom_bac
     * @param type $numins
     * @param type $dateins
     * @param type $nom_f
     * @param type $moyenne_bac
     * @param type $date_bac
     * @param type $type_bac
     * @param type $nat_bac
     * @param type $pwd
     */
    public function remplir_etudiant($cin, $nom, $prenom, $dateN, $adresse, $ntel, $photo, $sexe, $email, $nationalite, $cne, $lieun, $nom_bac, $numins, $dateins, $nom_f, $moyenne_bac, $date_bac, $type_bac, $nat_bac, $pwd) {
        $this->Personne($cin, $nom, $prenom, $dateN, $adresse, $ntel, $photo, $sexe, $email, $nationalite);
        $this->setCne($cne);
        $this->setLieun($lieun);
        $this->setNomBac($nom_bac);
        $this->setNumins($numins);
        $this->setDateins($dateins);
        $this->setNomFil($nom_f);
        $this->setId_f_Par_nom_Fil($nom_f);
        $this->setMoyenneBac($moyenne_bac);
        $this->setDateBac($date_bac);
        $this->setTypeBac($type_bac);
        $this->setNatBac($nat_bac);
        $this->setPwd($pwd);
        $this->fautes . +'.!!';
    }

    /**
     * 
     * @global type $bdd
     * @param type $cne
     * @return boolean
     */
    public function recuperer_etudiant($cne) {
        global $bdd;
        $result = $bdd->prepare('select *from etudiant where cne_e=:cne');
        $result->execute(array('cne' => $cne));
        $etd = $result->fetch();
        if (empty($etd)) {
            $result->closeCursor();
            return FALSE;
        }
        $nom_f = $this->setNomFil_Par_idFil($etd['id_f']);
        $this->remplir_etudiant($etd['cin_e'], $etd['nom_e'], $etd['prenom_e'], $etd['dateN_e'], $etd['adresse_e'], $etd['nTel_e'], $etd['photo_e'], $etd['sexe_e'], $etd['email_e'], $etd['nationalite'], $etd['cne_e'], $etd['lieuN_e'], $etd['nom_bac'], $etd['numins_e'], $etd['dateins_e'], $nom_f, $etd['moyenne_bac'], $etd['date_bac'], $etd['type_bac'], $etd['nat_bac'], $etd['pwd_e']);
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function stocker_etudiant() {
        global $bdd;
        if ($this->estExiste() || $this->estPresAStocker() == FALSE) {
            if ($this->estExiste()) {
                $this->fautes.='CNE deja utiliser, ';
                $this->pret_a_stocker = FALSE;
            }
            return FALSE;
        }
        $result = $bdd->prepare('insert into etudiant(cne_e,cin_e,nom_e,prenom_e,dateN_e,adresse_e,nTel_e,photo_e,
            sexe_e,email_e,nationalite,lieuN_e,nom_bac,numins_e,dateins_e,id_f,moyenne_bac,
            date_bac,type_bac,nat_bac,pwd_e) 
            values(:cne,:cin,:nom,:prenom,:dateN_e,:adresse_e,:ntel_e,:photo_e,
            :sexe_e,:email_e,:nationalite,:lieuN,:nom_bac,:numins,:dateins,:id_f,
            :moyenne_bac,:date_bac,:type_bac,:nat_bac,:pwd)');
        $result->execute(array('cne' => $this->getCne(),
            'cin' => $this->getCin(),
            'nom' => $this->getNom(),
            'prenom' => $this->getPrenom(),
            'dateN_e' => $this->getDateN(),
            'adresse_e' => $this->getAdresse(),
            'ntel_e' => $this->getNtel(),
            'photo_e' => $this->getPhoto(),
            'sexe_e' => $this->getSexe(),
            'email_e' => $this->getEmail(),
            'nationalite' => $this->getNationalite(),
            'lieuN' => $this->getLieun(),
            'nom_bac' => $this->getNomBac(),
            'numins' => $this->getNumins(),
            'dateins' => $this->getDateins(),
            'id_f' => $this->getId_f(),
            'moyenne_bac' => $this->getMoyenneBac(),
            'date_bac' => $this->getDateBac(),
            'type_bac' => $this->getTypeBac(),
            'nat_bac' => $this->getNatBac(),
            'pwd' => sha1($this->getCne())));
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function supprimerEtudiant() {
        global $bdd;
        try {
            $result = $bdd->prepare('delete from etudiant where cne_e=:cne');
            $result->execute(array('cne' => $this->getCne()));
            $result->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * @global type $bdd
     * @return string|boolean
     * 
     * methode qui permette de valider l'inscription d'un etudiant
     * cad: donne un numInscription pour l'etudiant est augmenter le nombre d'inscription
     * de cette filiere pour l'annee actuelle,et donner les modules du 1er annee de cette filiere 
     * a cet etudiant, si le service d'inscription de cette filiere est desactive ou l'etudiant est
     * deja inscrit donc on fait rien. 
     * 
     */
    public function validerInscription() {
        global $bdd;
        $num_ins = $this->getNumins();
        try {
            if (isset($num_ins) and $num_ins != NULL) {
                throw new Exception('etudiant deja inscrit');
            }

            $result_fil = $bdd->prepare('select nb_ins_anne from filiere where id_f=:id_f and nb_ins_anne is not null');
            $result_fil->execute(array('id_f' => $this->getId_f()));
            $don_fil = $result_fil->fetch();
            if (empty($don_fil)) {
                $result_fil->closeCursor();
                throw new Exception('le service d\'inscription pour \nla filiere <' . $this->getNomFil() . '> est desactive');
            }
            $nb_ins = $don_fil['nb_ins_anne'];
            $nb_ins++;
            $num_ins = $this->getNomFil() . $nb_ins;
            /* donner le numinscription pour l'etudiant */
            $result_etd = $bdd->prepare('update etudiant set numins_e=:num,dateins_e=:date where cne_e=:cne');
            $result_etd->execute(array('num' => $num_ins, 'cne' => $this->getCne(), 'date' => date('Y-m-d')));
            /* augmenter le nb inscription du filiere */
            $result_fil_2 = $bdd->prepare('update filiere set nb_ins_anne=nb_ins_anne+1 where id_f=:id_f');
            $result_fil_2->execute(array('id_f' => $this->getId_f()));
            /* inscrit l'etudiant dans les modules du 1er annee du filiere */
            $result_modules = $bdd->query("select id_m from modul where nom_s in ('S1','S2')");
            while ($modul = $result_modules->fetch()) {
                $id_m = $modul['id_m'];
                $cn_e = $this->getCne();
                $date_e_ins = date('Y-m-d');
                $result_inscri = $bdd->prepare('insert into inscription(cne_e,id_m,date_ins,nb_ins) 
                    values(:cne,:id_m,:date_ins,1)');
                $result_inscri->execute(array('cne' => $cn_e, 'id_m' => $id_m, 'date_ins' => $date_e_ins));
            }
            $result_fil->closeCursor();
            $result_etd->closeCursor();
            $result_fil_2->closeCursor();
            $result_modules->closeCursor();
            $result_inscri->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
        }
        /* return non important jamais retourne */
        return FALSE;
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function estExiste() {
        global $bdd;
        $result = $bdd->prepare('select *from etudiant where cne_e=:cne');
        $result->execute(array('cne' => $this->getCne()));
        $donne = $result->fetch();
        if (empty($donne)) {
            $result->closeCursor();
            return FALSE;
        }
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @param type $cne
     */
    public function setCne($cne) {
        if ($this->verifieChamp($cne) and preg_match("#^[0-9]{10,}$#", $cne)) {
            $this->cne = htmlspecialchars($cne);
        } else {
            //echo "<script>alert('cne:$cne');</script>";
            $this->fautes.='CNE, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    /**
     * 
     * @param type $lieun
     */
    public function setLieun($lieun) {
        if ($this->verifieChamp($lieun)) {
            $this->lieun = htmlspecialchars($lieun);
        } else {
            //echo "<script>alert('lieun:$lieun');</script>";
            $this->fautes.='lieuNaissance, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    /**
     * 
     * @param type $nom_bac
     */
    public function setNomBac($nom_bac) {
        if ($this->verifieChamp($nom_bac)) {
            $this->nom_bac = htmlspecialchars($nom_bac);
        } else {
            //echo "<script>alert('nom_bac:$nom_bac');</script>";
            $this->fautes.='nom du Bac, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    /**
     * 
     * @param type $numins
     */
    public function setNumins($numins) {
        if ($this->verifieChamp($numins)) {
            $this->numins = htmlspecialchars($numins);
        }
    }

    /**
     * 
     * @param type $dateins
     */
    public function setDateins($dateins) {
        if ($this->verifieChamp($dateins)) {
            $this->dateins = htmlspecialchars($dateins);
        } else {
            $this->pret_a_stocker = FALSE;
            // echo "<script>alert('dateIns:$dateins');</script>";
        }
    }

    /**
     * 
     * @param type $id_f
     */
    public function setId_f($id_f) {
        if ($this->verifieChamp($id_f)) {
            $this->id_f = htmlspecialchars($id_f);
        } else {
            $this->pret_a_stocker = FALSE;
            echo "<script>alert('id_f:$id_f');</script>";
        }
    }

    /**
     * 
     * @param type $nom_f
     */
    public function setNomFil($nom_f) {
        if ($this->verifieChamp($nom_f)) {
            $this->nom_f = $nom_f;
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $nomFil
     * @return type
     */
    public function setId_f_Par_nom_Fil($nomFil) {
        global $bdd;
        $result = $bdd->prepare('select id_f from filiere where nom_f=:nomF');
        $result->execute(array('nomF' => $nomFil));
        $etd = $result->fetch();
        if (!empty($etd)) {
            $this->setId_f($etd['id_f']);
            return $etd['id_f'];
        } else {
            $this->pret_a_stocker = FALSE;
            $this->fautes.='filiere non trouvée, ';
            //echo "<script>alert('nom_fi:$nomFil');</script>";
        }
        $result->closeCursor();
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @return type
     */
    public function setNomFil_Par_idFil($id_f) {
        global $bdd;
        $result = $bdd->prepare('select nom_f from filiere where id_f=:id');
        $result->execute(array('id' => $id_f));
        $etd = $result->fetch();
        if (!empty($etd)) {
            $this->setNomFil($etd['nom_f']);
            return $etd['nom_f'];
        } else {
            $this->pret_a_stocker = FALSE;
            $this->fautes.='filiere non trouvée, ';
            //echo "<script>alert('nom_fi:$nomFil');</script>";
        }
        $result->closeCursor();
    }

    /**
     * 
     * @param type $moyenne_bac
     */
    public function setMoyenneBac($moyenne_bac) {
        if ($this->verifieChamp($moyenne_bac) and preg_match("#^[1-2][0-9]\.[0-9]{1,}$#", $moyenne_bac) and $moyenne_bac <= 20) {
            $this->moyenne_bac = htmlspecialchars($moyenne_bac);
        } else {
            $this->pret_a_stocker = FALSE;
            $this->fautes.='Moyenne du bac, ';
            //echo "<script>alert('moyenne:$moyenne_bac');</script>";
        }
    }

    /**
     * 
     * @param type $date_bac
     */
    public function setDateBac($date_bac) {
        if ($this->verifieChamp($date_bac) and $this->verifieDate($date_bac)) {
            $this->date_bac = htmlspecialchars($date_bac);
        } else {
            $this->pret_a_stocker = FALSE;
            $this->fautes.='Date du bac, ';
            //echo "<script>alert('date_bac:$date_bac');</script>";
        }
    }

    /**
     * 
     * @param type $type_bac
     */
    public function setTypeBac($type_bac) {
        if ($this->verifieChamp($type_bac)) {
            $this->type_bac = htmlspecialchars($type_bac);
        } else {
            $this->pret_a_stocker = FALSE;
            $this->fautes.='Type du bac, ';
            //echo "<script>alert('type_bc:$type_bac');</script>";
        }
    }

    /**
     * 
     * @param type $nat_bac
     */
    public function setNatBac($nat_bac) {
        if ($this->verifieChamp($nat_bac)) {
            $this->nat_bac = htmlspecialchars($nat_bac);
        } else {
            $this->pret_a_stocker = FALSE;
            $this->fautes.='Nationalite du Bac, ';
            //echo "<script>alert('nat_bc:$nat_bac');</script>";
        }
    }

    /**
     * 
     * @param type $pwd
     */
    public function setPwd($pwd) {
        if ($this->verifieChamp($pwd)) {
            $this->pwd = htmlspecialchars($pwd);
        }
    }

    /**
     * 
     * @return type
     */
    public function getCne() {
        if ($this->verifieChamp($this->cne))
            return htmlspecialchars($this->cne);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getLieun() {
        if ($this->verifieChamp($this->lieun))
            return htmlspecialchars($this->lieun);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getNomBac() {
        if ($this->verifieChamp($this->nom_bac))
            return htmlspecialchars($this->nom_bac);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getNumins() {
        if ($this->verifieChamp($this->numins))
            return htmlspecialchars($this->numins);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getDateins() {
        if ($this->verifieChamp($this->dateins))
            return htmlspecialchars($this->dateins);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getId_f() {
        if ($this->verifieChamp($this->id_f))
            return htmlspecialchars($this->id_f);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getNomFil() {
        if ($this->verifieChamp($this->nom_f))
            return htmlspecialchars($this->nom_f);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getMoyenneBac() {
        if ($this->verifieChamp($this->moyenne_bac))
            return htmlspecialchars($this->moyenne_bac);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getDateBac() {
        if ($this->verifieChamp($this->date_bac))
            return htmlspecialchars($this->date_bac);
        return NULL;
    }

    public function getAnneeDeDateDabe() {
        $date = $this->getDateBac();
        $tab = str_split($date, 4);
        return $tab[0];
    }

    /**
     * 
     * @return type
     */
    public function getTypeBac() {
        if ($this->verifieChamp($this->type_bac))
            return htmlspecialchars($this->type_bac);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getNatBac() {
        if ($this->verifieChamp($this->nat_bac))
            return htmlspecialchars($this->nat_bac);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getPwd() {
        if ($this->verifieChamp($this->pwd))
            return htmlspecialchars($this->pwd);
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function toString() {
        return $this->getCin() . '  ' . $this->getCne() . '  ' . $this->getNom() . '  ' . $this->getPrenom();
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @return type
     */
    public static function recuperer_nomF_par_id_F($id_f) {
        global $bdd;
        $result = $bdd->query('select nom_f from filiere where id_f=' . $id_f);
        $don = $result->fetch();
        return $don['nom_f'];
    }

    /**
     * methode permete d'afficher l'image d'un etudiant 
     * a l'aide d'un fichier php qui peut afficher l'image
     * d'un etudiant sachant leur cne .
     * le chemin est donner au parametres car on sait pas ou nous trouverons
     * lorsque on fait appelle a cette methode.
     * @param type $cheminMoteurImgAct
     */
    public function afficherAvecMoteurImg($cheminMoteurImgAct) {
        ?>
        <img class="img_etd" src="<?php echo $cheminMoteurImgAct . '?id=' . $this->getCne(); ?>"/>
        <?php
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function getReclamations() {
        global $bdd;
        $result = $bdd->prepare('select id_r,type_r,contenu_r,etat_r,date_r from  reclamation where cne_e=:cne');
        $result->execute(array('cne' => $this->getCne()));
        $dons = $result->fetchAll();
        $result->closeCursor();
        return $dons;
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function supprimerReclamation($id_r) {
        global $bdd;
        $result = $bdd->prepare('delete from  reclamation where cne_e=:cne and id_r=:id');
        $result->execute(array('cne' => $this->getCne(), 'id' => $id_r));
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_r
     * @param type $etat
     * @return boolean
     */
    public function modifierEtatReclamation($id_r, $etat) {
        global $bdd;
        $result = $bdd->prepare('update reclamation set etat_r=:etat where cne_e=:cne and id_r=:id');
        $result->execute(array('cne' => $this->getCne(), 'id' => $id_r, 'etat' => $etat));
        $result->closeCursor();
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $idm
     * @param type $notenr
     * @return boolean
     * @throws Exception
     */
    public function annulerAbsenceModulSession($idm, $notenr) {
        global $bdd;
        try {
            if (strcmp($notenr, 'note_r') != 0 and strcmp($notenr, 'note_n') != 0) {
                throw new Exception(' on a seulement note normale ou rattrapage');
            }
            $resm = $bdd->prepare('select id_m from inscription where id_m=:idm and cne_e=:cne and :nte=-1');
            $resm->execute(array('idm' => $idm, 'cne' => $this->getCne(), 'nte' => $notenr));
            $resm->closeCursor();
            $don = $resm->fetch();
            if(strcmp($notenr, 'note_n')==0){
            $resI = $bdd->prepare('update inscription set note_n=0 where cne_e=:cne and id_m=:idm');
            }
            if(strcmp($notenr, 'note_r')==0){
            $resI = $bdd->prepare('update inscription set note_r=0 where cne_e=:cne and id_m=:idm');
            }
            $resI->execute(array('idm' => $idm, 'cne' => $this->getCne()));
            return TRUE;
            if (empty($don)) {
                throw new Exception('etudiant non inscrit dans ce module ou non absent ...');
            }
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '")</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function getDiplomes() {
        global $bdd;
        $cne = $this->getCne();
        $result = $bdd->prepare('select nom_dip,cne_e,date_obt,type_dip,moyenne,mention_dip from etudiant_dip where cne_e=:cne');
        $result->execute(array('cne' => $cne));
        $don = $result->fetchAll();
        $result->closeCursor();
        return $don;
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function situationPedagogique() {
        global $bdd;
        $cne = $this->getCne();
        $setuation = array();
        $resSem = $bdd->query('select nom_s from semestre order by nom_s');
        while ($sem = $resSem->fetch()) {
            $resMod = $bdd->prepare('select ins.cne_e,ins.note_n,ins.note_r,ins.date_ins,ins.nb_ins,ins.etat_v,ins.id_m,md.nom_mod,m.nom_s 
                from inscription ins,modul m,nom_module md 
                where ins.cne_e=:cne and md.id_m=ins.id_m and md.id_f=:idf and m.id_m=ins.id_m and m.nom_s=:sem 
                order by md.id_m');
            $resMod->execute(array('cne' => $cne, 'idf' => $this->getId_f(), 'sem' => $sem['nom_s']));
            $setua = $resMod->fetchAll();
            $resMod->closeCursor();
            if (!empty($setua)) {
                $setuation[$sem['nom_s']] = $setua;
            }
        }
        $resSem->closeCursor();
        return $setuation;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nomFil
     * @return array
     */
    public static function etudiantsFiliere($nomFil) {
        global $bdd;
        $etudiants = array();
        $e = new Etudiant();
        $e->setId_f_Par_nom_Fil($nomFil);
        $result = $bdd->prepare('select *from etudiant where id_f=:id_ff');
        $result->execute(array('id_ff' => $e->getId_f()));
        while ($etd = $result->fetch()) {
            $etudiant = new Etudiant();
            $nom_fil = $etudiant->setNomFil_Par_idFil($etd['id_f']);
            $etudiant->remplir_etudiant($etd['cin_e'], $etd['nom_e'], $etd['prenom_e'], $etd['dateN_e'], $etd['adresse_e'], $etd['nTel_e'], $etd['photo_e'], $etd['sexe_e'], $etd['email_e'], $etd['nationalite'], $etd['cne_e'], $etd['lieuN_e'], $etd['nom_bac'], $etd['numins_e'], $etd['dateins_e'], $nom_fil, $etd['moyenne_bac'], $etd['date_bac'], $etd['type_bac'], $etd['nat_bac'], NULL);
            array_push($etudiants, $etudiant);
        }
        return $etudiants;
    }

    /**
     * 
     * @global type $bdd
     * @return boolean|array
     */
    public static function tousEtudiants() {
        global $bdd;
        $etudiants = array();
        $result = $bdd->query('select *from etudiant');
        while ($etd = $result->fetch()) {
            $etudiant = new Etudiant();
            $nom_fil = $etudiant->setNomFil_Par_idFil($etd['id_f']);
            $etudiant->remplir_etudiant($etd['cin_e'], $etd['nom_e'], $etd['prenom_e'], $etd['dateN_e'], $etd['adresse_e'], $etd['nTel_e'], $etd['photo_e'], $etd['sexe_e'], $etd['email_e'], $etd['nationalite'], $etd['cne_e'], $etd['lieuN_e'], $etd['nom_bac'], $etd['numins_e'], $etd['dateins_e'], $nom_fil, $etd['moyenne_bac'], $etd['date_bac'], $etd['type_bac'], $etd['nat_bac'], $etd['pwd_e']);
            array_push($etudiants, $etudiant);
        }
        $result->closeCursor();
        if (empty($etudiants))
            return FALSE;
        return $etudiants;
    }

    /**
     * 
     * @global type $bdd
     * @param type $tabAssocPass
     * @return boolean|array
     */
    public static function trouverEtudiants($tabAssocPass) {
        global $bdd;
        $etudiants = array();
        $tabAssoc = array();
        foreach ($tabAssocPass as $cle => $val) {
            if (strcmp($cle, 'nom_f') == 0) {
                $nom_f = htmlspecialchars($val);
                $d = new Etudiant();
                $d->setId_f_Par_nom_Fil($nom_f);
                $id = $d->getId_f();
                $tabAssoc['id_f'] = $id;
            } else {
                $tabAssoc[$cle] = $val;
            }
        }
        $str = 'select *from etudiant ';
        $i = 1;
        foreach ($tabAssoc as $cle => $val) {
            if ($i == 1) {
                $str.='where ';
            }
            if (strcmp($cle, 'nom_f') != 0) {
                if (is_string($val))
                    $str.=$cle . ' like(\'' . $val . '%\') ';
                else
                    $str.=$cle . '=' . $val . ' ';
            }
            if (($i++) < count($tabAssoc))
                $str.=' and ';
        }
        $result = $bdd->query($str);
        while ($etd = $result->fetch()) {
            $etudiant = new Etudiant();
            $nom_fil = $etudiant->setNomFil_Par_idFil($etd['id_f']);
            $etudiant->remplir_etudiant($etd['cin_e'], $etd['nom_e'], $etd['prenom_e'], $etd['dateN_e'], $etd['adresse_e'], $etd['nTel_e'], $etd['photo_e'], $etd['sexe_e'], $etd['email_e'], $etd['nationalite'], $etd['cne_e'], $etd['lieuN_e'], $etd['nom_bac'], $etd['numins_e'], $etd['dateins_e'], $nom_fil, $etd['moyenne_bac'], $etd['date_bac'], $etd['type_bac'], $etd['nat_bac'], $etd['pwd_e']);
            array_push($etudiants, $etudiant);
        }
        $result->closeCursor();
        if (empty($etudiants))
            return FALSE;
        return $etudiants;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom_f
     * @param type $inscription
     * @return boolean|array
     */
    public static function etudiantsInscrire($nom_f, $inscription) {

        global $bdd;
        $etudiants = array();
        $req = '';
        if (strcmp($nom_f, 'tous') != 0) {
            $etd = new Etudiant();
            $id_f = $etd->setId_f_Par_nom_Fil($nom_f);
            if ($inscription == TRUE) {
                $req = 'select *from etudiant where id_f=' . $id_f . ' and numins_e is not null';
            } else {
                $req = 'select *from etudiant where id_f=' . $id_f . ' and numins_e is null';
            }
        } else {
            if ($inscription == TRUE) {
                $req = 'select *from etudiant where numins_e is not null';
            } else {
                $req = 'select *from etudiant where numins_e is null';
            }
        }
        $result = $bdd->query($req);
        while ($etd = $result->fetch()) {
            $etudiant = new Etudiant();
            $nom_fil = $etudiant->setNomFil_Par_idFil($etd['id_f']);
            $etudiant->remplir_etudiant($etd['cin_e'], $etd['nom_e'], $etd['prenom_e'], $etd['dateN_e'], $etd['adresse_e'], $etd['nTel_e'], $etd['photo_e'], $etd['sexe_e'], $etd['email_e'], $etd['nationalite'], $etd['cne_e'], $etd['lieuN_e'], $etd['nom_bac'], $etd['numins_e'], $etd['dateins_e'], $nom_fil, $etd['moyenne_bac'], $etd['date_bac'], $etd['type_bac'], $etd['nat_bac'], $etd['pwd_e']);
            array_push($etudiants, $etudiant);
        }
        $result->closeCursor();
        if (empty($etudiants)) {
            return FALSE;
        }
        return $etudiants;
    }

}
?>
