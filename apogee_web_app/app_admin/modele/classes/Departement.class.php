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
            $result = $bdd->prepare('select f.nom_f,nmd.id_f,nmd.id_m,md.nom_s,nmd.nom_mod,nmd.cin_p,md.id_eq from nom_module nmd,modul md,filiere f where nmd.id_f=f.id_f and md.id_m=nmd.id_m and f.id_f=:idf and f.id_d=:idd order by nmd.id_m');
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
     * methode qui retourne les Bac qui permet d'inscrire au cette fliere
     * @global type $bdd
     * @param type $idf
     * @return boolean|array
     * @throws Exception
     */
    public function getBacsFiliere($idf) {
        try {
            global $bdd;
            $bacts = array();
            if (!$this->verifyChamp($idf))
                throw new Exception("filiere non trouve");
            $res = $bdd->prepare('select nom_bac from fil_bac where id_f=:idf and (select id_d from filiere where id_f=:idf)=:idd');
            $res->execute(array('idf' => $idf, 'idd' => $this->getId()));
            while ($bc = $res->fetch()) {
                array_push($bacts, $bc['nom_bac']);
            }
            $res->closeCursor();
            return $bacts;
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

