<?php

namespace App\Libraries;

class FraisCalculatorService
{
    public function calculerFrais(int $idTypeOperation, float $montant): float
    {
        $db = \Config\Database::connect();

        $ligne = $db->table('tranche_frais')
            ->where('id_type_operation', $idTypeOperation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_fin_validite', null)
            ->get()
            ->getRowArray();

        if ($ligne === null) {
            return 0;
        }

        if ($ligne['type_calcul'] === 'POURCENTAGE') {
            return round($montant * ($ligne['valeur'] / 100), 2);
        }

        return (float) $ligne['valeur'];
    }
}