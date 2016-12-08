<?php

class DelibirationEtudiants {
    /* la filiere */

    protected $idf = null;
    /* la semestre si */
    protected $si = null;
    /* la semestre sj de meme anne que si */
    protected $sj = null;
    /* les ids des tous les modules de Si */
    protected $tabIdsMdsSi = array();
    /* les ids des tous les modules de Sj */
    protected $tabIdsMdsSj = array();
    /* tableau contient tous les etudiants du Si */
    protected $tabCneSi = array();
    /* tableau contient tous les etudiants du Sj */
    protected $tabCneSj = array();
    /* tableau contient tous les cne des etudiants validants Si pour les afficher apres la delibiration */
    protected $tabCneVSi = array();
    /* tableau contient tous les cne des etudiants validants Sj pour les afficher apres la delibiration */
    protected $tabCneVSj = array();
    /* un tblaeu contient les infos sur les modules en nb_ins>3
     * pour afficher que ces etudiant termine le nombre d'inscriptions maximal
     */
    protected $tabFinisNbIns = array();
    /* tableau contient les cne des etds qui obtenir DEUG */
    protected $tabCneDip2ans = array();
    /* tableau contient les infos des etds qui obtenir DEUG pour les afficher */
    protected $tabShowDip2 = array();
    /* tableau contient les cne des etds qui obtenir LICENCE */
    protected $tabCneDip3ans = array();
    /* tableau contient les infos des etds qui obtenir DEUG pour les afficher */
    protected $tabShowDip3 = array();

    /**/
    protected $pretAdelibrer = TRUE;
    /**/
    public $nonDilibrer = FALSE;
    public function DelibirationEtudiants($idf, $si, $sj) {
        $this->setIdF($idf);
        $this->setSi($si);
        $this->setSj($sj);
        /* verification de presence du filiere et les semestres */
        if (!$this->verifieFiliereSemestres()) {
            $this->nonDilibrer = TRUE;
            return FALSE;
        }
        /* verifie que tous les modules des semstres d'annee ont des professeurs responsbles */
        $mdlsPrb = $this->verrifieExistenceProfResMod();
        if (!empty($mdlsPrb)) {
            $str = join($mdlsPrb, '\n');
            echo '<script>alert("la delibiration ne peut etre fait \ncar les modules :\n' . $str . '\n ont pas des professeurs responsable. ! ou la delibiration est deja faite");</script>';
            $this->nonDilibrer = TRUE;
            return FALSE;
        
        }
        /* verifie que tous les sessions des ds sont faites */
        $seesDs = $this->verifieNotesEntrer();
        if (!empty($seesDs)) {
            $str = join($seesDs, '\n');
            echo '<script>alert("la delibiration ne peut etre fait car:\n' . $str . '\npour au moins un etudiants");</script>';
            $this->nonDilibrer = TRUE;
            return FALSE;
        }
        /* recuperer tous les modules existe de semestre Si */
        $this->remplirTabIdsMdsSi();
        /* recuperer tous les modules existe de semestre Sj */
        $this->remplirTabIdsMdsSj();
        /* recuperer les etudiant du Si */
        $this->remplirTabSi();
        /* recuperer les etudiant du Sj */
        $this->remplirTabSj();
        /* deliberer les modules actuels pour chaque etudiant dans le semestre si  */
        $modlsSi = $this->delibrerSemestre_Si_Etds();
        /* deliberer les modules actuels pour chaque etudiant dans le semestre sj */
        $modlsSj = $this->delibrerSemestre_Sj_Etds();
        /* valider la delibiration de Si en BDD */
        $this->delibrerBdd_Modules($modlsSi);
        /* valider la delibiration de Sj en BDD */
        $this->delibrerBdd_Modules($modlsSj);
        /* les diplomes */
        $this->gererDiplomes();
        $this->setDiplomesBdd();
        echo '<pre>deug Cne<br/>';
        print_r($this->tabCneDip2ans);
        echo '</pre>';
        echo '<pre>licence Cne<br/>';
        print_r($this->tabCneDip3ans);
        echo '</pre>';
        $this->nonDilibrer = FALSE;
    }

    public function setIdF($idf) {
        $this->idf = $idf;
    }

    public function setSi($si) {
        $this->si = $si;
    }

    public function setSj($sj) {
        $this->sj = $sj;
    }

    public function getIdf() {
        return $this->idf;
    }

    public function getSi() {
        return $this->si;
    }

    public function getSj() {
        return $this->sj;
    }

    public function getTabCneSi() {
        return $this->tabCneSi;
    }

    public function getTabCneSj() {
        return $this->tabCneSj;
    }

    public function getTabCneVSi() {
        return $this->tabCneVSi;
    }

    public function getTabCneVSj() {
        return $this->tabCneVSj;
    }

    public function getTabIdsMdsSi() {
        return $this->tabIdsMdsSi;
    }

    public function getTabIdsMdsSj() {
        return $this->tabIdsMdsSj;
    }

    public function getTabFinisNbIns() {
        return $this->tabFinisNbIns;
    }

    public function getTabCneDip3ans() {
        return $this->tabCneDip3ans;
    }

    public function getTabCneDip2ans() {
        return $this->tabCneDip2ans;
    }

    public function getTabShowDip2() {
        return $this->tabShowDip2;
    }

    public function getTabShowDip3() {
        return $this->tabShowDip3;
    }
    public function getNonDelibrer() {
        return $this->nonDilibrer;
    }

    /**
     * methode qui permette de remplir le tableau des ids modules de semstre Si 
     * @global type $bdd
     * @return array
     */
    public function remplirTabIdsMdsSi() {
        global $bdd;
        $res = $bdd->prepare('select id_m from modul where nom_s=:sem order by id_m');
        $res->execute(array('sem' => $this->getSi()));
        while ($don = $res->fetch()) {
            array_push($this->tabIdsMdsSi, $don['id_m']);
        }
        $res->closeCursor();
    }

    /**
     * methode qui permette de remplir le tableau des ids modules de semstre Si 
     * @global type $bdd
     * @return array
     */
    public function remplirTabIdsMdsSj() {
        global $bdd;
        $res = $bdd->prepare('select id_m from modul where nom_s=:sem order by id_m');
        $res->execute(array('sem' => $this->getSj()));
        while ($don = $res->fetch()) {
            array_push($this->tabIdsMdsSj, $don['id_m']);
        }
        $res->closeCursor();
    }

    /**
     * methode qui remplir le tblaeau tabCneSi qui contient
     * les cne des tous les etudiants de si
     * @global type $bdd
     */
    public function remplirTabSi() {
        try {
            global $bdd;
            $result = $bdd->prepare('select DISTINCT(ins.cne_e) as cne_e 
                                   from inscription ins,nom_module md,modul m,etudiant etd
                                    where ins.id_m=md.id_m 
                                      and m.id_m=md.id_m 
                                      and etd.id_f=md.id_f
                                      and etd.cne_e=ins.cne_e
                                      and ins.etat_v is null
                                      and m.nom_s=:si 
                                      and md.id_f=:idf;');
            $result->execute(array('si' => $this->si, 'idf' => $this->idf));
            while ($don = $result->fetch()) {
                array_push($this->tabCneSi, $don['cne_e']);
            }
            $result->closeCursor();
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
            $this->pretAdelibrer = FALSE;
        }
    }

    /**
     * methode qui remplir le tblaeau tabCneSj qui contient
     * les cne des tous les etudiants de sj
     * @global type $bdd
     */
    public function remplirTabSj() {

        try {
            global $bdd;
            $result = $bdd->prepare('select DISTINCT(ins.cne_e) as cne_e 
                                   from inscription ins,nom_module md,modul m,etudiant etd
                                    where ins.id_m=md.id_m 
                                      and m.id_m=md.id_m 
                                      and etd.id_f=md.id_f
                                      and etd.cne_e=ins.cne_e
                                      and ins.etat_v is null
                                      and m.nom_s=:sj 
                                      and md.id_f=:idf;');
            $result->execute(array('sj' => $this->sj, 'idf' => $this->idf));
            while ($don = $result->fetch()) {
                array_push($this->tabCneSj, $don['cne_e']);
            }
            $result->closeCursor();
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
            $this->pretAdelibrer = FALSE;
        }
    }

    /**
     * methode qui permette de retourner tous les modules actuels du semstre $sem
     * d'un etudiant qui a le cne $cne , pour delibrer ces modules
     * @global type $bdd
     * @param type $cne
     * @param type $sem
     */
    public function getIdsModulActuel_etudiant($cne, $sem) {
        global $bdd;
        $tabRet = array();
        $resinsMod = $bdd->prepare('select DISTINCT(ins.id_m) as id_m
                                        from inscription ins,nom_module md,modul m,etudiant etd
                                        where ins.id_m=md.id_m
                                          and ins.cne_e=:cne 
                                          and m.id_m=md.id_m 
                                          and etd.id_f=md.id_f
                                          and etd.cne_e=ins.cne_e
                                          and ins.etat_v is null
                                          and m.nom_s=:sem 
                                          and md.id_f=:fil');
        $resinsMod->execute(array('cne' => $cne, 'sem' => $sem, 'fil' => $this->getIdf()));
        while ($donmod = $resinsMod->fetch()) {
            array_push($tabRet, $donmod['id_m']);
        }
        $resinsMod->closeCursor();
        return $tabRet;
    }

    /**
     * retourne pour un etd  tout les infos des moduls où leurs ids dans $tabIdsMod
     * @global type $bdd
     * @param type $cne : cne d'etudiant
     * @param type $tabIdsMod : les modules qu'on veut
     */
    public function getInscripModuls_etd($cne, $tabIdsMod) {
        global $bdd;
        $tabret = array();
        foreach ($tabIdsMod as $idm) {
            $res = $bdd->prepare('select cne_e,id_m,note_n,note_r,date_ins,nb_ins,etat_v 
        from inscription where cne_e=:cne and id_m=:idm');
            $res->execute(array('cne' => $cne, 'idm' => $idm));
            $tabret[$idm] = $res->fetch();
        }
        return $tabret;
    }

    /**
     * les modules deja etudie par un etudiant du semestre
     * @global type $bdd
     * @param type $cne
     * @param type $sem
     * @return array
     */
    public function getIdsModulFais_etudiant($cne, $sem) {
        global $bdd;
        $tabRet = array();
        $resinsMod = $bdd->prepare('select DISTINCT(ins.id_m) as id_m
                                        from inscription ins,nom_module md,modul m,etudiant etd
                                        where ins.id_m=md.id_m
                                          and ins.cne_e=:cne 
                                          and m.id_m=md.id_m 
                                          and etd.id_f=md.id_f
                                          and etd.cne_e=ins.cne_e
                                          and ins.etat_v is not null
                                          and m.nom_s=:sem 
                                          and md.id_f=:fil');
        $resinsMod->execute(array('cne' => $cne, 'sem' => $sem, 'fil' => $this->getIdf()));
        while ($donmod = $resinsMod->fetch()) {
            array_push($tabRet, $donmod['id_m']);
        }
        $resinsMod->closeCursor();
        return $tabRet;
    }

    /**
     * methode qui permette de delibirer les modules actuels du Si et Sj 
     * pour chaque etudiant.et si un etudiant :
     * -termine si on le stocke dans $tabTerSi(cne,moysi) pour traiter la comphensation avec Sj.
     * -termine sj on le stocke dans $tabTerSj(cne,moysj) pour traiter la comphensation avec Si.
     * -termine les 2 premiers ans (cad termine S3 et S4)on le stocke dans 
     *     $tabDip2=intersection($tabTerSi,$tabTerSj) si Si=S3 et Sj=S4 (n'oublier pas de verifie 
     *      sont aussi terminer(M07,M14 :prob corresp il peut termine S3 et S4 sans les vlider ...wa chtéééé,
     *      et si le coordonnateur delibrer S5 et S6 est un etudiant non encore termine S4(1module reste)
     *      mais termine S5 et S6 donc il est compte dans les etds diplomee , et le calcule du moyenne du diplome,
     *      ne peut calculer que si tous les modls valides ..... iwa B9iiiina bla licence )).
     * -termine les 3 premiers ans (cad termine S5 et S6)on le stocke dans 
     *     $tabDip3=intersection($tabTerSi,$tabTerSj) si Si=S5 et Sj=S6
     * -fait la 3éme inscription dans un module ,on le stocke dans $tab3ins
     * -  
     * @global type $bdd
     */
    public function delibrerSemestre_Si_Etds() {
        $tabret = array();
        foreach ($this->getTabCneSi() as $cnei) {
            $idsModsActSi = $this->getIdsModulActuel_etudiant($cnei, $this->getSi());
            $idsModsFaisSi = $this->getIdsModulFais_etudiant($cnei, $this->getSi());
            $idsModsActSj = $this->getIdsModulActuel_etudiant($cnei, $this->getSj());
            $idsModsFaisSj = $this->getIdsModulFais_etudiant($cnei, $this->getSj());
            $insModulsActSi = $this->getInscripModuls_etd($cnei, $idsModsActSi);
            $insModulsFaisSi = $this->getInscripModuls_etd($cnei, $idsModsFaisSi);
            $insModulsActSj = $this->getInscripModuls_etd($cnei, $idsModsActSj);
            $insModulsFaisSj = $this->getInscripModuls_etd($cnei, $idsModsFaisSj);
            /* delibiration de modules actuels pour chaque etudiant de Si */
            if (!empty($insModulsActSi)) {
                $insModulsActSitraite = array();
                foreach ($insModulsActSi as $inMod) {
                    $note_n = $inMod['note_n'];
                    $note_r = $inMod['note_r'];
                    $etatv = '';
                    if ($note_n == -1) {
                        $etatv = 'ABS';
                    } elseif ($note_n < 10) {
                        if ($note_r != NULL) {
                            if ($note_r >= 10) {
                                $etatv = 'VAR';
                            } elseif ($note_n >= 5 or $note_r >= 5) {
                                $etatv = 'NV';
                            } else {
                                $etatv = 'RI';
                            }
                        }
                        /* si l note_r est nulle et la note_n nulle ou <10 donc la delibiration
                         *  ne peut etre fait (session rattrpage non encore faite)
                         * j'ai deja fait des testes,meme si la note_n est nulle
                         */
                    } else {
                        $etatv = 'VM';
                    }
                    $inMod['etat_v'] = $etatv;
                    array_push($insModulsActSitraite, $inMod);
                }
                $insModulsActSi = $insModulsActSitraite;
            }
            /* delibiration de modules actuels pour chaque etudiant de Si */
            if (!empty($insModulsActSj)) {
                $insModulsActSjTraite = array();
                foreach ($insModulsActSj as $inMod) {
                    $note_n = $inMod['note_n'];
                    $note_r = $inMod['note_r'];
                    $etatv = '';
                    if ($note_n == -1) {
                        $etatv = 'ABS';
                    } elseif ($note_n < 10) {
                        if ($note_r != NULL) {
                            if ($note_r >= 10) {
                                $etatv = 'VAR';
                            } elseif ($note_n >= 5 or $note_r >= 5) {
                                $etatv = 'NV';
                            } else {
                                $etatv = 'RI';
                            }
                        }
                        /* si l note_r est nulle et la note_n nulle ou <10 donc la delibiration
                         *  ne peut etre fait (session rattrpage non encore faite)
                         * j'ai deja fait des testes,meme si la note_n est nulle
                         */
                    } else {
                        $etatv = 'VM';
                    }
                    $inMod['etat_v'] = $etatv;
                    array_push($insModulsActSjTraite, $inMod);
                }
                $insModulsActSj = $insModulsActSjTraite;
            }

            /* regrouper toutes les modules de Si du etdint $cnei */
            $modulesSi = $insModulsActSi + $insModulsFaisSi;
            $modulesSj = $insModulsActSj + $insModulsFaisSj;
            /* on verifie si l'etudiant termine la semestre Si, si le cas donc on 
             * passe a la comphensation si possible
             */
            $moyenneSi = null;
            $moyenneSj = null;
            $n = count($this->getTabIdsMdsSi());
            if (count($modulesSi) == $n) {
                $moyenneSi = 0;
                foreach ($modulesSi as $mod) {
                    $note_n = $mod['note_n'];
                    $note_r = $mod['note_r'];
                    $etat = $mod['etat_v'];
                    if (strcmp($etat, 'ABS') == 0 || strcmp($etat, 'RI') == 0) {
                        $moyenneSi = null;
                    } else {
                        if (is_numeric($moyenneSi)) {
                            $moyenneSi+=max(array($note_n, $note_r));
                        }
                    }
                }

                if (is_numeric($moyenneSi)) {
                    $moyenneSi/=$n;
                    if ($moyenneSi >= 10) {
                        /* on ajoute le cne dans le tableu qui contient les etds valide SI */
                        array_push($this->tabCneVSi, $cnei);
                        $insTabTraite = array();
                        foreach ($insModulsActSi as $mdvc) {
                            $ett = $mdvc['etat_v'];
                            if (strcmp($ett, 'NV') == 0) {
                                $mdvc['etat_v'] = 'VC';
                            }
                            array_push($insTabTraite, $mdvc);
                        }
                        $insModulsActSi = $insTabTraite;
                    } else {
                        $moyenneSj = null;

                        $m = count($this->getTabIdsMdsSj());
                        if (count($modulesSj) == $m) {
                            $moyenneSj = 0;
                            foreach ($modulesSj as $mod) {
                                $note_n = $mod['note_n'];
                                $note_r = $mod['note_r'];
                                $etat = $mod['etat_v'];
                                if (strcmp($etat, 'ABS') == 0 || strcmp($etat, 'RI') == 0) {
                                    $moyenneSj = null;
                                } else {
                                    if (is_numeric($moyenneSj)) {
                                        $moyenneSj+=max(array($note_n, $note_r));
                                    }
                                }
                            }
                        }
                        if (is_numeric($moyenneSj)) {
                            $moyenneSj/=$m;
                            $moyenneAnne = ($moyenneSi + $moyenneSj) / 2;
                            if ($moyenneAnne >= 10) {
                                /* on ajoute le cne dans le tableu qui contient les etds valide SI */
                                array_push($this->tabCneVSi, $cnei);
                                $insTabTraite = array();
                                foreach ($insModulsActSi as $mdvcs) {
                                    $ettt = $mdvcs['etat_v'];
                                    if (strcmp($ettt, 'NV') == 0) {
                                        $mdvcs['etat_v'] = 'VCS';
                                    }
                                    array_push($insTabTraite, $mdvcs);
                                }
                                $insModulsActSi = $insTabTraite;
                            }
                        }
                    }
                }
            }
            array_push($tabret, $insModulsActSi);
        }
        return $tabret;
    }

    /**
     * la meme chose que delibrerSemestre_Si_Etds() mais cette fois on va traiter
     * les etudiants du semestre SJ 
     * @return array
     */
    public function delibrerSemestre_Sj_Etds() {
        $tabret = array();
        foreach ($this->getTabCneSj() as $cnei) {
            $idsModsActSj = $this->getIdsModulActuel_etudiant($cnei, $this->getSj());
            $idsModsFaisSj = $this->getIdsModulFais_etudiant($cnei, $this->getSj());
            $idsModsActSi = $this->getIdsModulActuel_etudiant($cnei, $this->getSi());
            $idsModsFaisSi = $this->getIdsModulFais_etudiant($cnei, $this->getSi());
            $insModulsActSj = $this->getInscripModuls_etd($cnei, $idsModsActSj);
            $insModulsFaisSj = $this->getInscripModuls_etd($cnei, $idsModsFaisSj);
            $insModulsActSi = $this->getInscripModuls_etd($cnei, $idsModsActSi);
            $insModulsFaisSi = $this->getInscripModuls_etd($cnei, $idsModsFaisSi);
            /* delibiration de modules actuels pour chaque etudiant de Sj */
            if (!empty($insModulsActSj)) {
                $insModulsActSjtraite = array();
                foreach ($insModulsActSj as $inMod) {
                    $note_n = $inMod['note_n'];
                    $note_r = $inMod['note_r'];
                    $etatv = '';
                    if ($note_n == -1) {
                        $etatv = 'ABS';
                    } elseif ($note_n < 10) {
                        if ($note_r != NULL) {
                            if ($note_r >= 10) {
                                $etatv = 'VAR';
                            } elseif ($note_n >= 5 or $note_r >= 5) {
                                $etatv = 'NV';
                            } else {
                                $etatv = 'RI';
                            }
                        }
                        /* si l note_r est nulle et la note_n nulle ou <10 donc la delibiration
                         *  ne peut etre fait (session rattrpage non encore faite)
                         * j'ai deja fait des testes,meme si la note_n est nulle
                         */
                    } else {
                        $etatv = 'VM';
                    }
                    $inMod['etat_v'] = $etatv;
                    array_push($insModulsActSjtraite, $inMod);
                }
                $insModulsActSj = $insModulsActSjtraite;
            }
            /* delibiration de modules actuels pour chaque etudiant de Sj */
            if (!empty($insModulsActSi)) {
                $insModulsActSiTraite = array();
                foreach ($insModulsActSi as $inMod) {
                    $note_n = $inMod['note_n'];
                    $note_r = $inMod['note_r'];
                    $etatv = '';
                    if ($note_n == -1) {
                        $etatv = 'ABS';
                    } elseif ($note_n < 10) {
                        if ($note_r != NULL) {
                            if ($note_r >= 10) {
                                $etatv = 'VAR';
                            } elseif ($note_n >= 5 or $note_r >= 5) {
                                $etatv = 'NV';
                            } else {
                                $etatv = 'RI';
                            }
                        }
                        /* si l note_r est nulle et la note_n nulle ou <10 donc la delibiration
                         *  ne peut etre fait (session rattrpage non encore faite)
                         * j'ai deja fait des testes,meme si la note_n est nulle
                         */
                    } else {
                        $etatv = 'VM';
                    }
                    $inMod['etat_v'] = $etatv;
                    array_push($insModulsActSiTraite, $inMod);
                }
                $insModulsActSi = $insModulsActSiTraite;
            }

            /* regrouper toutes les modules de Si du etdint $cnei */
            $modulesSj = $insModulsActSj + $insModulsFaisSj;
            $modulesSi = $insModulsActSi + $insModulsFaisSi;
            /* on verifie si l'etudiant termine la semestre Sj, si le cas donc on 
             * passe a la comphensation si possible
             */
            $moyenneSj = null;
            $moyenneSi = null;
            $n = count($this->getTabIdsMdsSj());
            if (count($modulesSj) == $n) {
                $moyenneSj = 0;
                foreach ($modulesSj as $mod) {
                    $note_n = $mod['note_n'];
                    $note_r = $mod['note_r'];
                    $etat = $mod['etat_v'];
                    if (strcmp($etat, 'ABS') == 0 || strcmp($etat, 'RI') == 0) {
                        $moyenneSj = null;
                    } else {
                        if (is_numeric($moyenneSj)) {
                            $moyenneSj+=max(array($note_n, $note_r));
                        }
                    }
                }

                if (is_numeric($moyenneSj)) {
                    $moyenneSj/=$n;
                    if ($moyenneSj >= 10) {
                        /* on ajoute le cne dans le tableu qui contient les etds valide SJ */
                        array_push($this->tabCneVSj, $cnei);
                        $insTabTraite = array();
                        foreach ($insModulsActSj as $mdvc) {
                            $ett = $mdvc['etat_v'];
                            if (strcmp($ett, 'NV') == 0) {
                                $mdvc['etat_v'] = 'VC';
                            }
                            array_push($insTabTraite, $mdvc);
                        }
                        $insModulsActSj = $insTabTraite;
                    } else {
                        $moyenneSi = null;

                        $m = count($this->getTabIdsMdsSi());
                        if (count($modulesSi) == $m) {
                            $moyenneSi = 0;
                            foreach ($modulesSi as $mod) {
                                $note_n = $mod['note_n'];
                                $note_r = $mod['note_r'];
                                $etat = $mod['etat_v'];
                                if (strcmp($etat, 'ABS') == 0 || strcmp($etat, 'RI') == 0) {
                                    $moyenneSi = null;
                                } else {
                                    if (is_numeric($moyenneSi)) {
                                        $moyenneSi+=max(array($note_n, $note_r));
                                    }
                                }
                            }
                        }
                        if (is_numeric($moyenneSi)) {
                            $moyenneSi/=$m;
                            $moyenneAnne = ($moyenneSj + $moyenneSi) / 2;
                            if ($moyenneAnne >= 10) {
                                /* on ajoute le cne dans le tableu qui contient les etds valide SJ */
                                array_push($this->tabCneVSj, $cnei);
                                $insTabTraite = array();
                                foreach ($insModulsActSj as $mdvcs) {
                                    $ettt = $mdvcs['etat_v'];
                                    if (strcmp($ettt, 'NV') == 0) {
                                        $mdvcs['etat_v'] = 'VCS';
                                    }
                                    array_push($insTabTraite, $mdvcs);
                                }
                                $insModulsActSj = $insTabTraite;
                            }
                        }
                    }
                }
            }
            array_push($tabret, $insModulsActSj);
        }
        return $tabret;
    }

    /**
     * 
     * @global type $bdd
     * @param type $tbModuls
     */
    public function delibrerBdd_Modules($tbEtdsModuls) {
        global $bdd;
        if (!empty($tbEtdsModuls)) {
            foreach ($tbEtdsModuls as $tbModuls) {

                foreach ($tbModuls as $mod) {
                    $cne = $mod['cne_e'];
                    $idm = $mod['id_m'];
                    $etat_v = $mod['etat_v'];
                    $nb_ins = $mod['nb_ins'] + 1;
                    $datAct = date('Y-m-d');
                    if (strcmp($etat_v, 'RI') == 0 or strcmp($etat_v, 'NV') == 0 or strcmp($etat_v, 'ABS') == 0) {
                        if ($nb_ins > 3) {
                            /* ajouter au tableau des etudiants qui depassent le nb ins admis */
                            array_push($this->tabFinisNbIns, $mod);
                        }
                        $res1 = $bdd->prepare('update inscription set etat_v=null,note_N=null,note_R=null,nb_ins=:nb,date_ins=:datea where cne_e=:cne and id_m=:idm');
                        $res1->execute(array(
                            'cne' => $cne,
                            'idm' => $idm,
                            'nb' => $nb_ins,
                            'datea' => $datAct));
                    } else {
                        $res2 = $bdd->prepare('update inscription set etat_v=:etat where cne_e=:cne and id_m=:idm');
                        $res2->execute(array('etat' => $etat_v, 'cne' => $cne, 'idm' => $idm));
                        $resMeq = $bdd->prepare('select id_eq from modul where id_m=:id and id_eq is not null');
                        $resMeq->execute(array('id' => $idm));
                        $don = $resMeq->fetch();
                        if (!empty($don)) {
                            $resInsMod = $bdd->prepare('insert into inscription(cne_e,id_m,date_ins) 
                            values(:cne,:idm,:date_in)');
                            $resInsMod->execute(array('cne' => $cne, 'idm' => $don['id_eq'], 'date_in' => $datAct));
                        }
                    }
                }
            }
        }
    }

    /**
     * determiner les etudiants qu'ont obtenu des diplomes soit 2ans ou 3ans
     * et stocker leur cne dans les tableaux, pour apres inserer leur dipome dans 
     * la base de donner et les afficher au coordonnateur; 
     */
    public function gererDiplomes() {
        $vsi = $this->getTabCneVSi();
        $vsj = $this->getTabCneVSj();
        /* etds validants Si mais nv Sj 'peut y valider au annee preced */
        $dif1 = array_diff($vsi, $vsj);
        /* etds validants Sj mais nv Si 'peut y valider au annee preced */
        $dif2 = array_diff($vsj, $vsi);
        /* etds valid Si et Sj au cet annee */
        $inter = array_intersect($vsi, $vsj);
        /* concatennation du 3 tabs */
        $etds = array();
        foreach ($dif1 as $v) {
            array_push($etds, $v);
        }
        foreach ($dif2 as $v) {
            array_push($etds, $v);
        }
        foreach ($inter as $v) {
            array_push($etds, $v);
        }
        if (strcmp($this->getSi(), 'S4') == 0 or strcmp($this->getSj(), 'S4') == 0) {
            foreach ($etds as $cne) {
                array_push($this->tabCneDip2ans, $cne);
            }
        }
        if (strcmp($this->getSi(), 'S6') == 0 or strcmp($this->getSj(), 'S6') == 0) {
            foreach ($etds as $cne) {
                array_push($this->tabCneDip3ans, $cne);
            }
        }
    }

    /**
     * 
     * @global type $bdd
     */
    public function setDiplomesBdd() {
        $dip2 = $this->getTabCneDip2ans();
        $dip3 = $this->getTabCneDip3ans();
        
        $datAct = date('Y-m-d');
        global $bdd;
        if (strcmp($this->getSi(), 'S4') == 0 or strcmp($this->getSj(), 'S4') == 0) {
            foreach ($dip2 as $cne) {
                $etdDip = array();
                $sems = array('S1', 'S2', 'S3', 'S4');
                $moyennedip = 0;
                $resEtd = $bdd->query("select numins_e,nom_e,prenom_e from etudiant where cne_e=$cne");
                $etd = $resEtd->fetch();
                $etdDip['numins_e'] = $etd['numins_e'];
                $etdDip['nom_e'] = $etd['nom_e'];
                $etdDip['prenom_e'] = $etd['prenom_e'];
                foreach ($sems as $sem) {
                    $res0 = $bdd->prepare('select note_n,note_r from inscription ins where cne_e=:cne and id_m in(select id_m from modul where nom_s=:sem)');
                    $res0->execute(array('cne' => $cne, 'sem' => $sem));
                    $n = 0;
                    $moyennei = 0;
                    while ($ins = $res0->fetch()) {
                        $moyennei+=max(array($ins['note_n'], $ins['note_r']));
                        $n++;
                    }
                    $moyennei/=$n;
                    $etdDip[$sem] = $moyennei;
                    $moyennedip+=$moyennei;
                }
                $moyennedip/=count($sems);
                $mention = '';
                if ($moyennedip < 12) {
                    $mention = 'Passable';
                } elseif ($moyennedip < 14) {
                    $mention = 'Assez Bien';
                } elseif ($moyennedip < 16) {
                    $mention = 'Bien';
                } elseif ($moyennedip >= 16) {
                    $mention = 'Tres Bien';
                }
                $etdDip['moyenne'] = $moyennedip;
                $etdDip['mention'] = $mention;
                $res = $bdd->prepare('insert into etudiant_dip(nom_dip,cne_e,date_obt,moyenne,mention_dip) 
                    values(:nom,:cne,:dte,:moy,:ment)');
                $res->execute(array(
                    'nom' => 'DEUG',
                    'cne' => $cne,
                    'dte' => $datAct,
                    'moy' => $moyennedip,
                    'ment' => $mention));

                array_push($this->tabShowDip2, $etdDip);
            }
        }
        /**/
        if (strcmp($this->getSj(), 'S6') == 0 or strcmp($this->getSi(), 'S6') == 0) {
            foreach ($dip3 as $cne) {
                $etdDip = array();
                $sems = array('S1', 'S2', 'S3', 'S4', 'S5', 'S6');
                $moyennedip = 0;
                $resEtd = query("select numins_e,nom_e,prenom_e from etudiant where cne_e=$cne");
                $etd = $resEtd->fetch();
                $etdDip['numins_e'] = $etd['numins_e'];
                $etdDip['nom_e'] = $etd['nom_e'];
                $etdDip['prenom_e'] = $etd['prenom_e'];
                foreach ($sems as $sem) {
                    $res0 = $bdd->prepare('select note_n,note_r from inscription ins where cne_e=:cne and id_m in(select id_m from modul where nom_s=:sem)');
                    $res0->execute(array('cne' => $cne, 'sem' => $sem));
                    $n = 0;
                    $moyennei = 0;
                    while ($ins = $res0->fetch()) {
                        $moyenne+=max(array($ins['note_n'], $ins['note_r']));
                        $n++;
                    }
                    $moyennei/=$n;
                    $etdDip[$sem] = $moyennei;
                    $moyennedip+=$moyennei;
                }
                $moyennedip/=count($sems);
                $mention = '';
                if ($moyennedip < 12) {
                    $mention = 'Passable';
                } elseif ($moyennedip < 14) {
                    $mention = 'Assez Bien';
                } elseif ($moyennedip < 16) {
                    $mention = 'Bien';
                } elseif ($moyennedip >= 16) {
                    $mention = 'Tres Bien';
                }
                $etdDip['moyenne'] = $moyennedip;
                $etdDip['mention'] = $mention;
                $res = $bdd->prepare('insert into etudiant_dip(nom_dip,cne_e,date_obt,moyenne,mention_dip) 
                    vlues(:nom,:cne,:dte,:moy,:ment');
                $res->execute(array(
                    'nom' => 'DEUG',
                    'cne' => $cne,
                    'dte' => $datAct,
                    'moy' => $moyennedip,
                    'ment' => $mention));

                array_push($this->tabShowDip3, $etdDip);
            }
        }
    }

    /**
     * methode qui permette de retourner des infos sur les modules où le
     * professeur n'est pas encore fais la session normale ou rattrpage pour
     * au moins un etudiants.les problemes sont deja traités par autres methodes
     * ,sachant que l'appel de cette methode sera faite apres. 
     * @global type $bdd
     * @return array
     */
    public function verifieNotesEntrer() {
        global $bdd;
        $tabRet = array();
        $res = $bdd->prepare('select DISTINCT(ins.id_m),m.nom_s,md.nom_mod,p.nom_p,p.prenom_p,case when ins.note_n is null then \'Session normale\' else \'Session rattrapage\' end as sess
                            from inscription ins,modul m,nom_module md,professeur p
                            where ins.id_m=m.id_m 
                              and m.id_m=md.id_m
                                  and (ins.note_n is null or (ins.note_n>=0 and ins.note_n<10 and ins.note_r is null)) 
                                  and ins.etat_v is null
                                  and md.id_f=:idf 
                                  and md.id_f=(select id_f from etudiant where cne_e=ins.cne_e) 
                                  and m.nom_s in (:si,:sj)  
                                  and p.cin_p=md.cin_p;');
        $res->execute(array('idf' => $this->getIdf(), 'si' => $this->getSi(), 'sj' => $this->getSj()));
        while ($donmod = $res->fetch()) {
            array_push($tabRet, 'M. ' . $donmod['nom_p'] . ' ' . $donmod['prenom_p'] . ' pas encore entrer les notes du ' . $donmod['sess'] . ' pour ' . $donmod['nom_s'] . '_' . $donmod['id_m'] . '_' . $donmod['nom_mod']);
        }
        $res->closeCursor();
        return $tabRet;
    }

    /**
     * methode qui permette de retourner un tableau qui contient les modules
     * qui sont enseigner par ucun professeur. chaque case de ce tableau contient
     * la concatination d'id et le nom du module.on va utiliser ce tablaeu pour 
     * la verification. si le tableau est vide donc tout est bon. blabla ;)
     * @global type $bdd
     * @return array
     */
    public function verrifieExistenceProfResMod() {
        $ret = array();
        global $bdd;
        $res = $bdd->prepare('select id_m,nom_mod,case when cin_p is null then \'-\' else cin_p end as cin_p 
                                from nom_module 
                                where id_m in(select id_m from modul where nom_s in (:si,:sj)) and id_f=:idf and cin_p is null');
        $res->execute(array('si' => $this->getSi(), 'sj' => $this->getSj(), 'idf' => $this->getIdf()));
        while ($don = $res->fetch()) {
            array_push($ret, $don['id_m'] . '_' . $don['nom_mod']);
        }
        $res->closeCursor();
        return $ret;
    }

    /**
     * methode permette de verification , si la filiere est existe
     * et les semestre sont de meme annee
     * @global type $bdd
     * @return boolean
     * @throws Exception
     */
    public function verifieFiliereSemestres() {
        try {
            global $bdd;
            $res = $bdd->prepare('select id_f from filiere where id_f=:idf');
            $res->execute(array('idf' => $this->idf));
            $don = $res->fetchAll();
            if (empty($don)) {
                throw new Exception('Filiere non trouve');
            }
            $res->closeCursor();
            $resSem = $bdd->prepare('select nom_s from semestre where nom_s=:si and nom_s_eq=:sj');
            $resSem->execute(array('si' => $this->si, 'sj' => $this->sj));
            $donSem = $resSem->fetchAll();
            $resSem->closeCursor();
            if (empty($donSem)) {
                throw new Exception('semestre non trouve,ou les deux semestres ne sont pas dans meme anne');
            }
            return TRUE;
        } catch (Exception $e) {
            echo '<script>alert("' . $e->getMessage() . '");</script>';
        }
        $this->pretAdelibrer = FALSE;
        return FALSE;
    }
/**
 * 
 */
    public function showInformationsDelibiration() {
        echo '<script>alert("La delibiration est faite avec succes");</script>';
        $tabSi = $this->getTabCneSi();
        $tabSj = $this->getTabCneSj();
        $tabFinis = $this->getTabFinisNbIns();
        $tabShowDip2 = $this->getTabShowDip2();
        $tabShowDip3 = $this->getTabShowDip3();
        /* on fait une fct qui remplir une mtrice de tous moduls du Si avec leur etat_v */
    }

}

?>
