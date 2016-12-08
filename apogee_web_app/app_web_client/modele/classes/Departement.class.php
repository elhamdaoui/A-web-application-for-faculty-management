<?php

/**
 * Description of Departement
 *
 * @author abdelmajid
 */
class Departement {

    protected $id = null;
    /* id : c'est l'id du departement , on la besoin lorsqu'on va recuperer un
     * departement , mais en stockage des depts l'id increment automatiquement dans
     * BDD (AUTO_INCREMENT).
     */
    protected $nom = null;
    protected $cin_cheuf = null;

    /**
     * constructeur par default
     */
    public function Departement() {
        
    }

    /**
     * 
     * @param type $id
     * @param type $nom
     * @param type $cin_p
     */
    public function remplir_departement($id, $nom, $cin_cheuf) {
        $this->setId($id);
        $this->setNom($nom);
        $this->setCinCheuf($cin_cheuf);
    }

    /**
     * 
     * @global type $bdd
     */
    public function stocker() {
        global $bdd;
        try {
            $result = $bdd->prepare('insert into departement(nom_d) values(:nom)');
            $result->execute(array('nom' => $this->getNom()));
            $result->closeCursor();
            $this->setCheufBDD($this->getCinCheuf());
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $id
     */
    public function recuperer_departement($id) {
        if ($this->verifyChamp($id)) {
            global $bdd;
            $result = $bdd->prepare('select id_d,nom_d,cin_chef from departement where id_d=:id');
            $result->execute(array('id' => $id));
            $dept = $result->fetch();
            if (empty($dept)) {
                $result->closeCursor();
                return FALSE;
            }
            $this->remplir_departement($dept['id_d'], $dept['nom_d'], $dept['cin_chef']);
            $result->closeCursor();
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * @global type $bdd
     * @return boolean
     */
    public function supprimer() {
        global $bdd;
        $id = $this->getId();
        if ($this->verifyChamp($id)) {
            try {
                $result = $bdd->prepare('delete from departement where id_d=:id');
                $result->execute(array('id' => $id));
                $result->closeCursor();
                return TRUE;
            } catch (Exception $e) {
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom
     * @return boolean
     */
    public function updateNomBDD($nom) {
        global $bdd;
        try {
            if (!$this->verifyChamp($nom))
                throw new Exception("impossible de modifier ce departement infos sont fausses");
            $result = $bdd->prepare('update departement set nom_d=:nom where id_d=:id');
            $result->execute(array('nom' => $nom, 'id' => $this->getId()));
            $result->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $cin_p
     */
    public function setCheufBDD($cin_p) {
        if ($this->verifyChamp($cin_p)) {
            global $bdd;
            $result = $bdd->prepare('update departement set cin_chef=:cin where id_d=:id');
            $result->execute(array('cin' => $cin_p, 'id' => $this->getId()));
            $result->closeCursor();
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $nom_fil
     * @return boolean
     */
    public function ajouteFiliere($nom_fil) {
        global $bdd;
        if ($this->verifyChamp($nom_fil)) {
            $result = $bdd->prepare('insert into filiere(nom_f,id_d) values(:nom_f,:idd)');
            $result->execute(array('nom_f' => $nom_fil, 'idd' => $this->getId()));
            $result->closeCursor();
            $result2 = $bdd->query('select id_m,id_f from modul,filiere f where f.nom_f=\'' . $nom_fil . '\'');
            while ($mod = $result2->fetch()) {
                $idm = $mod['id_m'];
                $idf = $mod['id_f'];
                $result3 = $bdd->prepare('insert into nom_module(id_f,id_m,nom_mod) values(:idf,:idm,:nmmd)');
                $result3->execute(array('idf' => $idf, 'idm' => $idm, 'nmmd' => 'sans nom'));
                $result3->closeCursor();
            }
            $result2->closeCursor();
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @return boolean
     */
    public function supprimerFiliere($id_f) {
        global $bdd;
        try {
            if (!$this->verifyChamp($id_f))
                throw new Exception("impossible de trouver cette filiere");
            $result = $bdd->prepare('delete from filiere where id_f=:idf and id_d=:idd');
            $result->execute(array('idf' => $id_f, 'idd' => $this->getId()));
            $result->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
        return FALSE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $idf
     * @param type $newNomf
     * @return boolean
     */
    public function updateNomFilBDD($idf, $newNomf) {
        global $bdd;
        try {
            if (!$this->verifyChamp($idf) || !$this->verifyChamp($newNomf))
                throw new Exception("impossible de modifier cette filiere des infos sont fausses");
            $result = $bdd->prepare('update filiere set nom_f=:nom where id_f=:idf and id_d=:id');
            $result->execute(array('nom' => $newNomf, 'idf' => $idf, 'id' => $this->getId()));
            $result->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @return boolean
     * @throws Exception
     */
    public function getModulesFiliere($id_f) {
        global $bdd;
        try {
            if (!$this->verifyChamp($id_f))
                throw new Exception("filiere non trouve");
            $result = $bdd->prepare('select f.nom_f,nmd.id_f,nmd.id_m,md.nom_s,sem.nom_s_eq,nmd.nom_mod,nmd.cin_p,md.id_eq from nom_module nmd,modul md,semestre sem,filiere f where nmd.id_f=f.id_f and md.id_m=nmd.id_m and sem.nom_s=md.nom_s and f.id_f=:idf and f.id_d=:idd order by nmd.id_m');
            $result->execute(array('idf' => $id_f, 'idd' => $this->getId()));
            $mods = $result->fetchAll();
            $result->closeCursor();
            if (empty($mods)) {
                throw new Exception("filiere ne comporte aucun module");
            }
            $modules = array();
            $results = $bdd->query("select nom_s from semestre order by nom_s");
            while ($sem = $results->fetch()) {
                $arrmodsem = array();
                $s = $sem['nom_s'];
                for ($i = 0; $i < count($mods); $i++) {
                    if (strcmp($s, $mods[$i]['nom_s']) == 0) {
                        array_push($arrmodsem, $mods[$i]);
                    }
                }
                $modules[$s] = $arrmodsem;
            }
            $results->closeCursor();
            return $modules;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @param type $id_m
     * @return boolean
     * @throws Exception
     */
    public function getUnModuleFiliere($id_f, $id_m) {
        global $bdd;
        try {
            if (!$this->verifyChamp($id_f) || !$this->verifyChamp($id_m))
                throw new Exception("filiere ou module non trouve");
            $result = $bdd->prepare('select f.nom_f,nmd.id_f,md.nom_s,nmd.id_m,md.id_eq,nmd.nom_mod,nmd.cin_p from nom_module nmd,filiere f,modul md where nmd.id_m=:idm and nmd.id_f=:idf and f.id_f=nmd.id_f and md.id_m=nmd.id_m and f.id_d=:idd');
            $result->execute(array('idf' => $id_f, 'idm' => $id_m, 'idd' => $this->getId()));
            $mod = $result->fetch();
            $result->closeCursor();
            if (empty($mod)) {
                throw new Exception("filiere pas de ce departement ou ...");
            }
            return $mod;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @param type $id_m
     * @param type $newNom
     * @return boolean
     * @throws Exception
     */
    public function updateNomModule($id_f, $id_m, $newNom) {
        global $bdd;
        try {
            if (!$this->verifyChamp($id_f) || !$this->verifyChamp($id_m) || !$this->verifyChamp($newNom))
                throw new Exception("filiere ou module non trouve ou nom module nulle");
            $result = $bdd->prepare('update nom_module set nom_mod=:nom where id_m=:idm and id_f=:idf');
            $result->execute(array('idf' => $id_f, 'idm' => $id_m, 'nom' => $newNom));
            $result->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @global type $bdd
     * @param type $id_f
     * @param type $id_m
     * @param type $cin_p
     * @return boolean
     * @throws Exception
     */
    public function updateResponsableModule($id_f, $id_m, $cin_p) {
        global $bdd;
        try {
            if (!$this->verifyChamp($id_f) || !$this->verifyChamp($id_m) || !$this->verifyChamp($cin_p))
                throw new Exception("filiere ou module non trouve ou prof non existe");
            $result = $bdd->prepare('update nom_module set cin_p=:cin where id_m=:idm and id_f=:idf');
            $result->execute(array('idf' => $id_f, 'idm' => $id_m, 'cin' => $cin_p));
            $result->closeCursor();
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @param type $id
     */
    public function setId($id) {
        if ($this->verifyChamp($id)) {
            $this->id = htmlspecialchars($id);
        }
    }

    /**
     * 
     * @param type $nom
     */
    public function setNom($nom) {
        if ($this->verifyChamp($nom)) {
            $this->nom = htmlspecialchars($nom);
        }
    }

    /**
     * 
     * @param type $cin
     */
    public function setCinCheuf($cin_cheuf) {
        if ($this->verifyChamp($cin_cheuf)) {
            $this->cin_cheuf = htmlspecialchars($cin_cheuf);
        }
    }

    /**
     * 
     * @return null
     */
    public function getId() {
        if ($this->verifyChamp($this->id)) {
            return htmlspecialchars($this->id);
        }
        return NULL;
    }

    /**
     * 
     * @return null
     */
    public function getNom() {
        if ($this->verifyChamp($this->nom)) {
            return htmlspecialchars($this->nom);
        }
        return NULL;
    }

    /**
     * 
     * @return null
     */
    public function getCinCheuf() {
        if ($this->verifyChamp($this->cin_cheuf)) {
            return htmlspecialchars($this->cin_cheuf);
        }
        return NULL;
    }

    /**
     * 
     * @global type $bdd
     * @param type $idf
     * @return boolean
     * @throws Exception
     */
    public function recupererFiliere($idf) {
        try {
            global $bdd;
            if (!$this->verifyChamp($idf))
                throw new Exception('id filiere non valide ');
            $result = $bdd->prepare('select id_f,nom_f,cin_cor,id_d,nb_ins_anne from filiere where id_f=:idf and id_d=:idd');
            $result->execute(array('idf' => $idf, 'idd' => $this->getId()));
            $fils = $result->fetch();
            if (empty($fils))
                throw new Exception('filiere non trouve dans ce departement ');
            $result->closeCursor();
            return $fils;
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public function toutesFilieres() {
        global $bdd;
        $result = $bdd->prepare('select id_f,nom_f,cin_cor,id_d,nb_ins_anne from filiere where id_d=:idd');
        $result->execute(array('idd' => $this->getId()));
        $fils = $result->fetchAll();
        $result->closeCursor();
        return $fils;
    }

    /**
     * 
     * @global type $bdd
     * @return array
     */
    public function tousProfesseurs() {
        global $bdd;
        $prfs = array();
        try {
            $result = $bdd->prepare('select cin_p from professeur where id_f in (select id_f from filiere where id_d=:id)');
            $result->execute(array('id' => $this->getId()));
            while ($don = $result->fetch()) {
                $pr = new Professeur();
                $pr->recuperer_Professeur($don['cin_p']);
                array_push($prfs, $pr);
            }
            $result->closeCursor();
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
            return $prfs;
        }
        return $prfs;
    }

    /**
     * 
     * @param type $champ
     * @return boolean
     */
    public function verifyChamp($champ) {
        if (isset($champ) and !empty($champ) and $champ != NULL) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * @param type $idmm
     * @param type $idmf
     */
    public static function getInscriptionsModuleFiliere($idmm, $idmf) {
        try {
            $dp = new Departement();
            if (!$dp->verifyChamp($idmm) || !$dp->verifyChamp($idmf)) {
                throw new Exception('filiere ou module non existe');
            }
            global $bdd;
            $res = $bdd->prepare('select ins.cne_e,etd.numins_e,etd.nom_e,etd.prenom_e,prf.nom_p,prf.prenom_p,ins.id_m,md.nom_mod,case when ins.note_N<0 then \'ABS\' else ins.note_N end as note_N,case when ins.note_R<0 then \'ABS\' else ins.note_R end as note_R,ins.date_ins,ins.nb_ins,
                case when ins.note_N<0 then \'ABS\' 
                when ins.note_N<10 and ins.note_R is null then \'RATT\' 
                when ins.note_N<10 and ins.note_R >=10 then \'VAR\' 
                when ins.note_N<10 and ins.note_R <10 then \'NV\' 
                when ins.note_N>=10 then \'VM\' 
                end as etat_V
                from  inscription ins,nom_module md,etudiant etd,professeur prf where md.id_f=:idf and md.id_m=:idm and md.cin_p=prf.cin_p and ins.cne_e=etd.cne_e 
                and ins.id_m=md.id_m and etd.id_f=md.id_f and ins.etat_V is null order by etd.nom_e');
            $res->execute(array('idf' => $idmf, 'idm' => $idmm));
            $don = $res->fetchAll();
            if (empty($don)) {
                throw new Exception('aucun etdudiant n\'inscrit dans ce module\n ou des infos sont fausses,comme aucun professeur enseigne ce module');
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
     * @param type $ids
     * @param type $idf
     * @return type
     * @throws Exception
     */
    public static function getInscriptionsSemstreFiliere($ids, $idf) {
        try {
            $dp = new Departement();
            if (!$dp->verifyChamp($ids) || !$dp->verifyChamp($idf)) {
                throw new Exception('filiere ou semstre non existe');
            }
            global $bdd;
            $tabEtdsSem = array();
            /* recuperer les etudiants inscrivent dans cette semsetre */
            $resEtds = $bdd->prepare('select distinct ins.cne_e,etd.numins_e,etd.nom_e,etd.prenom_e,ins.nb_ins
  from inscription ins,etudiant etd
   where ins.etat_v is null and ins.cne_e=etd.cne_e and etd.id_f=:idf and ins.id_m in(
     select md.id_m from nom_module md where id_f=:idff and id_m in(select id_m from modul where nom_s=:sem)
	 )');
            $resEtds->execute(array('idf' => $idf, 'idff' => $idf, 'sem' => $ids));
            $donetds = $resEtds->fetchAll();
            $resEtds->closeCursor();
            if (empty($donetds)) {
                throw new Exception('aucun etudiant inscrit dans cette semstre');
            }
            $resSem = $bdd->prepare('select m.id_m,md.nom_mod from modul m,nom_module md where md.id_m=m.id_m and md.id_f=:idf and m.nom_s=:sem');
            $resSem->execute(array('sem' => $ids, 'idf' => $idf));
            $donSem = $resSem->fetchAll();
            $resSem->closeCursor();
            if (empty($donSem)) {
                throw new Exception('aucun module dans cette semstre');
            }
            /* recuperer les modules du semstre */
            $inf = array();
            foreach ($donSem as $module) {
                $modlname = $module['id_m'] . '_' . $module['nom_mod'];
                array_push($inf, substr($modlname, 0, 9) . '<br/>' . substr($modlname, 9, 18));
            }
            array_push($tabEtdsSem, $inf);
            /* recuperer les notes des modules du cette semstre pour les etudiants inscrivent dans cette sem */
            foreach ($donetds as $etd) {
                $cne_e = $etd['cne_e'];
                $tabES = array();
                $tabES['numins_e'] = $etd['numins_e'];
                $tabES['nom_e'] = $etd['nom_e'];
                $tabES['prenom_e'] = $etd['prenom_e'];
                $moy = 0;
                $co = 0;
                $valid = '';
                $nvalid = 0;
                $vc = 0;
                foreach ($donSem as $module) {
                    $resInsEtd = $bdd->prepare('select case when (ins.note_n is not null and ins.note_r is null) or(ins.note_n>ins.note_r)then ins.note_n 
                             else ins.note_r end as note,ins.etat_v 
                             from inscription ins where ins.cne_e=:cne and ins.id_m=:idm');
                    $resInsEtd->execute(array('cne' => $cne_e, 'idm' => $module['id_m']));
                    $donInsEtd = $resInsEtd->fetch();
                    $resInsEtd->closeCursor();
                    $noteM = 'X';
                    if (!empty($donInsEtd)) {
                        $noteM = $donInsEtd['note'];
                    }
                    $tabES[$module['id_m'] . '_' . $module['nom_mod']] = $noteM;
                    if (is_numeric($moy)) {
                        if (strcmp($noteM, 'X') == 0) {
                            $moy = "--";
                            $valid = '--';
                        } elseif ($noteM == -1) {
                            $moy = "ABS";
                            $valid = "ABS";
                        } elseif (is_numeric($noteM)) {
                            $moy+=$noteM;
                            $co++;
                            if ($noteM < 5) {
                                $nvalid++;
                            } else {
                                if ($noteM < 10) {
                                    $vc++;
                                }
                            }
                        } else {
                            $moy = ' ';
                            $valid = ' ';
                        }
                    }
                }
                if (is_numeric($moy) and $co > 5) {
                    $moy/=$co;
                    if ($nvalid > 0) {
                        $valid = 'NV';
                    } else {
                        if ($moy < 10) {
                            $valid = 'NV';
                        } else{
                            if ($vc > 0) {
                                $valid = 'VC';
                            } else {
                                $valid = 'VM';
                            }
                        }
                    }
                }
                $tabES['moyenne'] = substr($moy, 0, 6);
                $tabES['validation'] = $valid;
                array_push($tabEtdsSem, $tabES);
            }
            return $tabEtdsSem;
        } catch (Exception $e) {
            echo '<script>alert("Erreur: ' . $e->getMessage() . '");</script>';
            return FALSE;
        }
    }

    /**
     * 
     * @global type $bdd
     * @return type
     */
    public static function tousDepartements() {
        global $bdd;
        $result = $bdd->query('select id_d,nom_d,cin_chef from departement');
        $depts = $result->fetchAll();
        $result->closeCursor();
        return $depts;
    }

}

