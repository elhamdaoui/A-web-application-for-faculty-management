<?php


class Module_C extends Module {

    protected $nom = null;
    protected $nom_fil = null;
    protected $id_fil = null;
    protected $cin_res = null;

    public function Module_C() {
        
    }

    public function remplir($id, $semestre, $id_eq, $nom, $nom_fil, $id_fil, $cin_res) {
        parent::remplir_M($id, $semestre, $id_eq);
        $this->setNom($nom);
        $this->setNomFil($nom_fil);
        $this->setIdFil($id_fil);
        $this->setCinRes($cin_res);
    }

    public function stocker() {
        if(!$this->estExiste()){
        global $bdd;
        $result = $bdd->prepare('insert into nom_module(id_m,id_f,cin_p,nom_mod) values(:id_m,:id_f,:cin_p,:nom_m)');
        $result->execute(array('id_m' => $this->getId(), 'id_f' => $this->getIdFil(),
            'cin_p' => $this->getCinRes(), 'nom_m' => $this->getNom()));
        $result->closeCursor();
        return TRUE;
        }else{
            return False;
        }
    }

    public function recuperer($id_m, $id_f) {
        global $bdd;
        parent::recuperer($id_m);
        $result = $bdd->prepare('select *from nom_module where id_m=:id_m and id_f=:id_f');
        $result->execute(array('id_m' => $id_m, 'id_f' => $id_f));
        $donne = $result->fetch();
        $this->remplir($this->getId(), $this->getSemestre(), $this->getIdEq(), $donne['nom_mod'], NULL, $donne['id_f'], $donne['cin_p']);
        $this->setNomFil_by_IdFil($donne['id_f']);
        $result->closeCursor();
    }

    public function setNom($nom) {
        if ($this->verifyChamp($nom)) {
            $this->nom = htmlspecialchars($nom);
        } else {
            $this->pret = FALSE;
            $this->fautes+='nom module, ';
        }
    }

    public function setNomFil($nom_fil) {
        if ($this->verifyChamp($nom_fil))
            $this->nom_fil = htmlspecialchars($nom_fil);
    }

    public function setIdFil($id_fil) {
        if ($this->verifyChamp($id_fil))
            $this->id_fil = htmlspecialchars($id_fil);
    }

    public function setCinRes($cin_res) {
        if ($this->verifyChamp($cin_res)) {
            $this->cin_res = htmlspecialchars($cin_res);
        }
    }

    public function getNom() {
        return htmlspecialchars($this->nom);
    }

    public function getNomFil() {
        return htmlspecialchars($this->nom_fil);
    }

    public function getCinRes() {
        return htmlspecialchars($this->cin_res);
    }

    public function getIdFil() {
        return htmlspecialchars($this->id_fil);
    }

    public function setIdFil_by_NomFil() {
        global $bdd;
        $result = $bdd->prepare('select id_f from filiere where nom_f=:nom');
        $result->execute(array('nom' => $this->getNomFil()));
        $donne = $result->fetch();
        $this->setIdFil($donne['id_f']);
        $result->closeCursor();
    }

    public function setNomFil_by_IdFil() {
        global $bdd;
        $result = $bdd->prepare('select nom_f from filiere where id_f=:id');
        $result->execute(array('id' => $this->getIdFil()));
        $donne = $result->fetch();
        $this->setNomFil($donne['nom_f']);
        $result->closeCursor();
    }

    public function estExiste() {
        global $bdd;
        $result = $bdd->prepare('select *from nom_module where id_m=:id and id_f=:id_fi');
        $result->execute(array('id' => $this->getId(), 'id_fi' => $this->getIdFil()));
        $donne = $result->fetch();
        if (empty($donne)) {
            $result->closeCursor();
            return FALSE;
        }
        $result->closeCursor();
        return TRUE;
    }

    public function toString() {
        $dd = '';
        $d = 'id=' . $this->getId() . ', s='
                . $this->getSemestre() . ', idQ='
                . $this->getIdEq() . ', nom='
                . $this->getNom() . ', idF='
                . $this->getIdFil() . ', nomF='
                . $this->getNomFil() . ', cinP:'
                . $this->getCinRes();
        return $dd . $d;
    }

}


