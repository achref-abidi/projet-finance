<?php

function calculNbrJours($dateFuture, $dateCourante, $nbrJoursBanque = 1){
    
    $tab = explode('/', $dateFuture);
    $d = mktime(null, null, null, $tab[1], $tab[0], $tab[2]);

    $tab2 = explode('/', $dateCourante);
    $d2 = mktime(null, null, null, $tab2[1], $tab2[0], $tab2[2]);

    return ceil(($d - $d2) / 3600 /24) + $nbrJoursBanque;
}

function calculEscompte($valeurNominale, $echeance, $tauxEscompte = 10, $nbrJoursBanque = 1){
    $nbrJours = calculNbrJours($echeance,  date('d/m/Y'), $nbrJoursBanque);
    $escompte  = ($valeurNominale * $tauxEscompte * $nbrJours) / 36000 ;

    return round($escompte, 3);
}


function calculMontant($bordereau, &$total){
    $total = array(
        'total' => array(
            'com' => 0,
            'valeur nominale' => 0,
            'escompte' => 0,
            'tva/com' => 0
        ),

        'agios' => 0,
        'montant net' => 0

    );

    for ($i=0; $i<count($bordereau); $i++){
        $total['total']['com'] += $bordereau[$i]['com fixe'];
        $total['total']['valeur nominale'] += $bordereau[$i]['valeur nominale'];
        $total['total']['tva/com'] += $bordereau[$i]['tva/com'];   
        $total['total']['escompte'] += $bordereau[$i]['escompte'];
    }

    $total['agios'] = $total['total']['com'] + $total['total']['escompte'] + $total['total']['tva/com'];
    $total['montant net'] = $total['total']['valeur nominale'] - $total['agios'];
}

function calculCommission($banqueOrigine, $lieuOrigine ,$banqueOperation, $lieuOperation ,$coms){
    if($lieuOrigine == $lieuOperation){
        if($banqueOrigine == $banqueOperation){
            return $coms[0];
        }else{
            return $coms[1];
        }
    }else{
        if($banqueOrigine == $banqueOperation){
            return $coms[2];
        }else{
            return $coms[3];
        }
    }
}

function mkBordereau ($tabOperation, &$bordereau, &$total, $params){

    for ($i=0; $i<count($tabOperation); $i++){
        foreach($tabOperation[$i] as $key => $value){
            $bordereau[$i][$key] = $tabOperation[$i][$key];
        }
        $bordereau[$i]['jours agios'] = calculNbrJours($bordereau[$i]['echeance'], date('d/m/Y'));
        $bordereau[$i]['escompte'] = calculEscompte($bordereau[$i]['valeur nominale'],$bordereau[$i]['echeance'], $params['taux_escompte'],$params['jours_banque'] );

        $bordereau[$i]['com fixe'] = calculCommission($params['banque_origine'], $params['lieu_origine'], $bordereau[$i]['banque'], $bordereau[$i]['lieu'], $params['coms'] );

        $bordereau[$i]['tva/com'] = 0.18 * $bordereau[$i]['com fixe'];
        
    }

    calculMontant($bordereau, $total);
}

?>