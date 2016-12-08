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
    /* tableau contient tous les cne des etudiants pre a comphenser Si */
    protected $tabCnePCSi = array();
    /* tableau contient tous les cne des etudiants pre a comphenser Sj */
    protected $tabCnePCSj = array();
    /* tableau contient tous les cne des etudiants validant Si */
    protected $tabCneVSi = array();
    /* tableau contient tous les cne des etudiants validant Sj */
    protected $tabCneVSj = array();
    /* tableau contient tous les cne des etudiants qui pas valide Si pour leur donner les rienscriptions */
    protected $tabCneNVSi = array();
    /* tableau contient tous les cne des etudiants qui pas valide Sj pour leur donner les rienscriptions */
    protected $tabCneNVSj = array();
    /* !!!!!!!!!!!!!!!!! pense à -ajouter un tblaeu contient les infos sur les modules en nb_ins>3
     * pour fficher que ces etudiant termine le nombre d'inscriptions adimissible -,cette etape sera fait
     * la derinere apres la delibiration et l rienscription , on va rechercher dans l table inscription
     * de notre base avec les modules Si et Sj
     */
    protected $tabFinisNbIns = array();
    protected $pretAdelibrer = TRUE;

    public function DelibirationEtudiants($idf, $si, $sj) {
        $this->setIdF($idf);
        $this->setSi($si);
        $this->setSj($sj);

        /* verification de presence du filiere et les semestres */
        if (!$this->verifieFiliereSemestres()) {
            return FALSE;
        }
        /* verifie que tous les modules des semstres d'annee ont des professeurs responsbles */
        $mdlsPrb = $this->verrifieExistenceProfResMod();
        if (!empty($mdlsPrb)) {
            $str = join($mdlsPrb, '\n');
            echo '<script>alert("la delibiration ne peut etre fait \ncar les modules :\n' . $str . '\n ont pas des professeurs responsable. ! ou la delibiration est deja faite");</script>';
            return FALSE;
        }
        /* verifie que tous les sessions des ds sont faites */
        $seesDs = $this->verifieNotesEntrer();
        if (!empty($seesDs)) {
            $str = join($seesDs, '\n');
            echo '<script>alert("la delibiration ne peut etre fait car:\n' . $str . '\npour au moins un etudiants");</script>';
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
        /* deliberer les modules actuels pour chaque etudiant dans le semestre si et sj */

        return TRUE;
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

    public function getTabCneNVSi() {
        return $this->tabCneNVSi;
    }

    public function getTabCneNVSj() {
        return $this->tabCneNVSj;
    }

    public function getTabIdsMdsSi() {
        return $this->tabIdsMdsSi;
    }

    public function getTabIdsMdsSj() {
        return $this->tabIdsMdsSj;
    }

    public function getTabCnePCSi() {
        return $this->tabCnePCSi;
    }

    public function getTabCnePCSj() {
        return $this->tabCnePCSj;
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
        ;
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
    public function delibrerSemestresEtds() {
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
                foreach ($insModulsActSi as $inMod) {
                    $note_n = $inMod['note_n'];
                    $note_r = $inMod['note_r'];
                    $etatv = '';
                    if ($note_n == -1) {
                        $etatv = 'ABS';
                    } elseif ($note_n < 10) {
                        if ($note_r != NULL) {
                            if ($note_r > 10) {
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
                }
            }
            /* delibiration de modules actuels pour chaque etudiant de Si */
            if (!empty($insModulsActSj)) {
                foreach ($insModulsActSj as $inMod) {
                    $note_n = $inMod['note_n'];
                    $note_r = $inMod['note_r'];
                    $etatv = '';
                    if ($note_n == -1) {
                        $etatv = 'ABS';
                    } elseif ($note_n < 10) {
                        if ($note_r != NULL) {
                            if ($note_r > 10) {
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
                }
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
                    if (strcmp($etatv, 'ABS') == 0 || strcmp($etatv, 'RI') == 0) {
                        $moyenneSi = null;
                    } else {
                        if ($moyenneSi != null) {
                            $moyenneSi+=max(array($note_n, $note_r));
                        }
                    }
                }
                if ($moyenneSi != null) {
                    if ($moyenneSi >= 10) {
                        foreach ($insModulsActSi as $mdvc) {
                            $ett = $mdvc['etat_v'];
                            if (strcmp($ett, 'NV') == 0) {
                                $mdvc['etat_v'] = 'VC';
                            }
                        }
                    } else {
                        $moyenneSj = null;

                        $m = count($this->getTabIdsMdsSj());
                        if (count($modulesSj) == $m) {
                            $moyenneSj = 0;
                            foreach ($modulesSj as $mod) {
                                $note_n = $mod['note_n'];
                                $note_r = $mod['note_r'];
                                $etat = $mod['etat_v'];
                                if (strcmp($etatv, 'ABS') == 0 || strcmp($etatv, 'RI') == 0) {
                                    $moyenneSj = null;
                                } else {
                                    if ($moyenneSj != null) {
                                        $moyenneSj+=max(array($note_n, $note_r));
                                    }
                                }
                            }
                        }
                        if ($moyenneSj != null) {
                            $moyenneAnne = ($moyenneSi + $moyenneSj) / 2;
                            if ($moyenneAnne >= 10) {
                                foreach ($insModulsActSi as $mdvcs) {
                                    $ettt = $mdvcs['etat_v'];
                                    if (strcmp($ettt, 'NV') == 0) {
                                        $mdvcs['etat_v'] = 'VC';
                                    }
                                }
                            }
                        }
                    }
                }
            }
            echo '<pre>----' . $cnei . '---<br/>';
            print_r($insModulsActSi);
            echo '</pre>';
        }
    }

    public function delibrerModulsActuelsPourTsEtdSems() {
        global $bdd;
        /* -----------traitement du semestre Si----------- */
        foreach ($this->getTabCneSi() as $cnei) {
            $preaComphenserSi = FALSE;
            $moyenneSi = 0;
            echo '----' . $cnei . '  acts<br/>';
            /* tous module actuel d'etd $cnei */
            $modsActSi = $this->getIdsModulActuel_etudiant($cnei, $this->getSi());
            /* les notes,infos du tous mod act */
            $inscrmodsActSi = $this->getInscripModuls_etd($cnei, $modsActSi);
            /* tous module fais d'etd $cnei */
            $modsAucSi = $this->getIdsModulFais_etudiant($cnei, $this->getSi());
            /* les notes,infos du tous mod act */
            $inscrmodAucSi = $this->getInscripModuls_etd($cnei, $modsAucSi);
            /* tous les moduls Si de cet etudiant */
            $tousModsSi = $modsActSi + $modsAucSi;
            $diff = array_diff($this->getTabIdsMdsSi(), $tousModsSi);
            /* si l'etdiant etudie tous les modules du Si */
            if (empty($diff)) {
                $preaComphenserSi = TRUE;
                echo '<script>alert("' . $cnei . '");</script>';
            }
            /* parcourir les mods act */
            foreach ($inscrmodsActSi as $inAcMod) {
                $idm = $inAcMod['id_m'];
                $note_n = $inAcMod['note_n'];
                $note_r = $inAcMod['note_r'];
                $nbins = $inAcMod['nb_ins'];
                $etatv = '';

                echo $idm . ' NN=' . $note_n . ' NR=' . $note_r . ' ABER=';
                if ($note_n == NULL) {
                    echo ' null';
                } elseif ($note_n == -1) {
                    echo ' ABS';
                    $etatv = 'ABS';
                    $preaComphenserSi = FALSE;
                    $moyenneSi = NULL;
                } elseif ($note_n < 10) {
                    if ($note_r != NULL) {
                        if ($note_r > 10) {
                            echo ' VAR';
                            $etatv = 'VAR';
                            if ($moyenneSi != NULL) {
                                $moyenneSi+=$note_r;
                            }
                        } elseif ($note_n >= 5 or $note_r >= 5) {
                            echo ' NV';
                            $etatv = 'NV';
                            if ($moyenneSi != NULL) {
                                $moyenneSi+=max(array($note_n, $note_r));
                            }
                        } else {
                            echo ' RI';
                            $etatv = 'RI';
                            $preaComphenserSi = FALSE;
                            $moyenneSi = NULL;
                        }
                    }
                    /* si l note_r est nulle et la note_n nulle ou <10 donc la delibiration
                     *  ne peut etre fait (session rattrpage non encore faite)
                     * j'ai deja fait des testes,meme si la note_n est nulle
                     */
                } else {
                    echo ' VM';
                    $etatv = 'VM';
                    if ($moyenneSi != NULL) {
                        $moyenneSi+=$note_n;
                    }
                }
                $inAcMod['etat_v'] = $etatv;
            }

            if ($preaComphenserSi == TRUE) {
                /* si l'etudiant est pres a comphenser Si on le stocke dans le $tabCnePCSi */
                array_push($this->getTabCnePCSi(), $cnei);
            } else {
                /* sinon,on modifier les modules NV par RI */
                foreach ($inscrmodsActSi as $inAcMod) {
                    $moyenneSi = NULL;
                    if (strcmp($inAcMod['etat_v'], 'NV') == 0) {
                        $inAcMod['etat_v'] = 'RI';
                    }
                }
            }

            /* prcourir les modules deja fait */
            foreach ($inscrmodAucSi as $inAuMod) {
                $idm = $inAuMod['id_m'];
                $note_n = $inAuMod['note_n'];
                $note_r = $inAcMod['note_r'];
                //$nbins = $inAuMod['nb_ins'];
                //$etat_v = $inAuMod['etat_v'];
                if ($modsActSi != NULL) {
                    $moyenneSi+=max(array($note_n, $note_r));
                }
            }
            if ($moyenneSi != NULL) {
                $moyenneSi/=count($this->getTabIdsMdsSi());
                if ($moyenneSi >= 10) {
                    if (strcmp($inAcMod['etat_v'], 'NV') == 0) {
                        $inAcMod['etat_v'] = 'VC';
                    }
                } else {
                    $moyenneSj = 0;
                    $mdsSj = $this->getIdsModulFais_etudiant($cnei, $this->getSj());
                    $modlsIns = $this->getInscripModuls_etd($cnei, $mdsSj);
                    if (empty(array_diff($this->getTabIdsMdsSj(), $mdsSj))) {
                        /* donc la semestre de meme anne et terminer on peut 
                         * maintenant verfier si il ya une comphensation Du 
                         * semestre Si par Sj. 
                         */
                        foreach ($modlsIns as $inMd) {
                            $iidm = $inMd['id_m'];
                            $note_n = $inMd['note_n'];
                            $note_r = $inMd['note_r'];
                            $moyenneSj+=max(array($note_n, $note_r));
                        }
                        $moyenneSj/=count($this->getTabIdsMdsSj());
                        if ($moyenneSj >= 10) {
                            /* comphensation */
                        } else {
                            
                        }
                    }
                }
            }
        }







        /* -----------traitement du semestre Si----------- */
        foreach ($this->getTabCneSj() as $cnej) {
            echo '----' . $cnej . '  acts<br/>';
            $modsActSj = $this->getIdsModulActuel_etudiant($cnej, $this->getSj());
            $inscrmodsActSj = $this->getInscripModuls_etd($cnej, $modsActSj);
            print_r($inscrmodsActSj);
            echo '----aucs<br/>';
            $modsAucSj = $this->getIdsModulFais_etudiant($cnej, $this->getSj());
            $inscrmodsAucSj = $this->getInscripModuls_etd($cnej, $modsAucSj);
            print_r($inscrmodsAucSj);

            echo 'intersect des mods acts et aucs si egale $this->tabIdsMdsSj on peut faire la comphenstion du Sj<br/>';
            $tousModsSj = $modsActSj + $modsAucSj;
            print_r($tousMods);
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

}

?>
