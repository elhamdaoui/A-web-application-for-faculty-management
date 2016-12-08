<?php


class Module {

    protected $id = null;
    protected $semestre = null;
    protected $id_eq = null;
    protected $pret = FALSE;
    protected $fautes = 'vous avez des fautes :<br/>';

    public function Module() {
        
    }

    public function remplir_M($id, $semestre, $id_eq) {
        $this->pret = TRUE;
        $this->setId($id);
        $this->setSemestre($semestre);
        $this->setIdEq($id_eq);
    }

    public function setId($id) {
        if ($this->verifyChamp($id)) {
            $this->id = htmlspecialchars($id);
        } else {
            $this->pret = FALSE;
            $this->fautes+='id, ';
        }
    }

    public function setSemestre($semstre) {
        if ($this->verifyChamp($semstre)) {
            $this->semestre = htmlspecialchars($semstre);
        } else {
            $this->pret = FALSE;
            $this->fautes+='semestre, ';
        }
    }

    public function setIdEq($id_eq) {
        if (isset($id_eq))
            $this->id_eq = htmlspecialchars($id_eq);
    }

    public function getId() {
        return htmlspecialchars($this->id);
    }

    public function getSemestre() {
        return htmlspecialchars($this->semestre);
    }

    public function getIdEq() {
        return htmlspecialchars($this->id_eq);
    }

    public function verifyChamp($champ) {
        return (isset($champ) and !empty($champ));
    }

    public function estExiste() {
        global $bdd;
        $result = $bdd->prepare('select *from modul where id_m=:id');
        $result->execute(array('id' => $this->getId()));
        $donne = $result->fetch();
        if (empty($donne)) {
            $result->closeCursor();
            return FALSE;
        }
        $result->closeCursor();
        return TRUE;
    }

    public function stockerModule() {
        global $bdd;
        if ($this->estExiste()|| $this->pret==FALSE) {
            /*$this->pret = FALSE;
             * prob , si Module_C existe dans Modul mais n'existe pas dans nom_module.
             */
            $this->fautes+='module existe dans modul, ';
            return FALSE;
        }
        $result = $bdd->query('insert into modul(id_m,nom_s,id_eq) values(:id,:nom,:id_eq)');
        $result->execute(array('id' => $this->getId(), 'nom' => $this->getSemestre(), 'id_eq' => $this->getIdEq()));
        $result->closeCursor();
        return TRUE;
    }

    public function recuperer($id) {
        global $bdd;
        $result = $bdd->prepare('select *from modul where id_m=:id');
        $result->execute(array('id' => $id));
        $don = $result->fetch();
        $this->remplir_M($don['id_m'],$don['nom_s'], $don['id_eq']);
        $result->closeCursor();
    }

}
