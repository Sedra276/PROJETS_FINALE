<?php

namespace App\Libraries;

use App\Models\PromotionModel;
use App\Models\TrancheFraisModel;
use App\Models\PromotionModelModel;

/**
 * Service UNIQUE de calcul des frais.
 * Regle de maintenabilite : aucune autre classe ne doit recalculer les frais.
 * Si la regle de gestion change (ex: passage a un pourcentage), on ne touche
 * qu'ici + la table tranche_frais, jamais dans les controleurs.
 */
class FraisCalculatorService
{
    protected TrancheFraisModel $trancheFraisModel;
    protected PromotionModel $promotionModel;


    public function __construct()
    {
        $this->trancheFraisModel = new TrancheFraisModel();
        $this->promotionModel = new PromotionModel();
    }

    /**
     * Calcule le frais applicable pour un type d'operation, un operateur
     * (le bareme differe d'un operateur a l'autre) et un montant donnes.
     * Renvoie 0.0 si aucune tranche active ne correspond (cas du DEPOT).
     */
    public function calculerFrais(int $idTypeOperation, int $idOperateurConfig, float $montant): float
    {
        $tranche = $this->trancheFraisModel
            ->where('id_type_operation', $idTypeOperation)
            ->where('id_operateur_config', $idOperateurConfig)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_fin_validite', null)
            ->orderBy('date_debut_validite', 'DESC')
            ->first();

        if ($tranche === null) {
            return 0.0;
        }

        if ($tranche['type_calcul'] === 'POURCENTAGE') {
            return round($montant * ((float) $tranche['valeur'] / 100), 2);
        }

        // MONTANT_FIXE
      $frais = round($montant *((float) $tranche['valeur']/100),2);


      //application pro
    }
}
