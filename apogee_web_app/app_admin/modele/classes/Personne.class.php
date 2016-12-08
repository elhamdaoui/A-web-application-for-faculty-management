<?php


class Personne {

    protected $cin = null;
    protected $nom = null;
    protected $prenom = null;
    protected $dateN = null;
    protected $adresse = null;
    protected $ntel = null;
    protected $photo = null;
    protected $sexe = null;
    protected $email = null;
    protected $nationalite = null;
    protected $pret_a_stocker = FALSE;
    protected $fautes = 'vous avez des fautes :<br/>';

    public function Personne($cin, $nom, $prenom, $dateN, $adresse, $ntel, $photo, $sexe, $email, $nationalite) {
        $this->pret_a_stocker = TRUE;
        $this->setCin($cin);
        $this->setNom($nom);
        $this->setPrenom($prenom);
        $this->setDateN($dateN);
        $this->setAdresse($adresse);
        $this->setNtel($ntel);
        $this->setPhoto($photo);
        $this->setSexe($sexe);
        $this->setEmail($email);
        $this->setNationalite($nationalite);
    }

    public function setCin($cin) {
        if ($this->verifieChamp($cin) and preg_match("#^[A-Z]+[0-9]+$#", $cin))
            $this->cin = htmlspecialchars($cin);
        else {
            //echo "<script>alert('cin:$cin');</script>";
            $this->fautes.='CIN, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setNom($nom) {
        if ($this->verifieChamp($nom))
            $this->nom = htmlspecialchars(strtoupper($nom));
        else {
            //echo "<script>alert('nom:$nom');</script>";
            $this->fautes.='Nom, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setPrenom($prenom) {
        if ($this->verifieChamp($prenom))
            $this->prenom = htmlspecialchars(strtoupper($prenom));
        else {
            // echo "<script>alert('prenom:$prenom');</script>";
            $this->fautes.='Prenom, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setDateN($dateN) {
        if ($this->verifieChamp($dateN) and $this->verifieDate($dateN))
            $this->dateN = htmlspecialchars($dateN);
        else {
            //echo "<script>alert('dateN:$dateN');</script>";
            $this->fautes.='dateNaissance, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setAdresse($adresse) {
        if ($this->verifieChamp($adresse))
            $this->adresse = htmlspecialchars($adresse);
        else {
            //echo "<script>alert('adresse:$adresse');</script>";
            $this->fautes.='adresse, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setNtel($ntel) {
        if ($this->verifieChamp($ntel) and preg_match("#^0[56][0-9]{8}$#", $ntel))
            $this->ntel = htmlspecialchars($ntel);
        else {
            //echo "<script>alert('ntel:$ntel');</script>";
            $this->fautes.='numTéléphone, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setPhoto($photo) {
        if ($this->verifieChamp($photo)) {
            if (is_array($photo)) {
                $infosPhoto = pathinfo($photo['name']);
                $exts = array('jpg', 'png', 'jpeg');
                if ($photo['size'] < 512 * 1024 and in_array(strtolower($infosPhoto['extension']), $exts)) {
                    $photo = file_get_contents($photo['tmp_name']);
                    $this->photo = $photo;
                } else {
                    //echo "<script>alert('faute:photo');</script>";
                    $this->fautes.='photo dépasse la taille maximale ou d\'une extention non valides, ';
                    $this->pret_a_stocker = FALSE;
                }
            } else {
                /* c'est pas une fautes , car où on veut recuperer une personne de 
                 * notre base de données on le recupére sans photo
                 * pour conserver notre site un peut rapide.
                 * et on va utiliser moteurImg.php pour recuperer une image 
                 * s'on l'a besoin. 
                 */
                $this->fautes.='photo introuvable, ';
            }
        } else {
            $this->fautes.='photo null, ';
        }
    }

    public function setSexe($sexe) {
        if ($this->verifieChamp($sexe) and (strcmp($sexe, 'H') == 0 or strcmp($sexe, 'F') == 0))
            $this->sexe = htmlspecialchars($sexe);
        else {
            //echo "<script>alert('sexe:$sexe');</script>";
            $this->fautes.='Sexe, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setEmail($email) {
        if ($this->verifieChamp($email) and preg_match("#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$#", $email))
            $this->email = htmlspecialchars($email);
        else {
            //echo "<script>alert('email:$email');</script>";
            $this->fautes.='email du forme inacceptable, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function setNationalite($nationalite) {
        if ($this->verifieChamp($nationalite))
            $this->nationalite = htmlspecialchars($nationalite);
        else {
            //echo "<script>alert('nationalite:$nationalite');</script>";
            $this->fautes.='Nationnalite, ';
            $this->pret_a_stocker = FALSE;
        }
    }

    public function getCin() {
        return $this->cin;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getDateN() {
        return $this->dateN;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getNtel() {
        return $this->ntel;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function getSexe() {
        return $this->sexe;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getNationalite() {
        return $this->nationalite;
    }

    public function getFautes() {
        return $this->fautes;
    }

    protected function verifieChamp($champ) {
        if (empty($champ) || $champ == NULL || !isset($champ))
            return FALSE;
        return TRUE;
    }

    protected function verifieDate($dat) {
        if (preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $dat)) {
            return true;
        }
        return FALSE;
    }

    public function estPresAStocker() {
        return $this->pret_a_stocker;
    }

}

?>
