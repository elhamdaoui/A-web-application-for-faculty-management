<?php


function verify_connexion($table, $tabAssoc) {
    global $bdd;
    $req = 'select *from ' . $table . ' where ';
    $i = 1;
    foreach ($tabAssoc as $cle => $val) {
        if (is_string($val))
            $req.=$cle . '=\'' . $val . '\' ';
        else
            $req.=$cle . '=' . $val . ' ';
        if (($i++) < count($tabAssoc))
            $req.=' and ';
    }
    $result = $bdd->query($req);
    $don = $result->fetch();
    if (empty($don)) {
        return FALSE;
    }
    return TRUE;
}

