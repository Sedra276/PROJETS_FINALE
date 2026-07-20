<?php

namespace App\Models;

use CodeIgniter\Model;

class TrancheFraisModel extends Model
{
    protected $table         = 'tranche_frais';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_type_operation', 'montant_min', 'montant_max',
        'type_calcul', 'valeur', 'date_debut_validite', 'date_fin_validite',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $validationRules = [
        'id_type_operation' => 'required|integer',
        'montant_min'       => 'required|numeric',
        'montant_max'       => 'required|numeric',
        'type_calcul'       => 'required|in_list[MONTANT_FIXE,POURCENTAGE]',
        'valeur'            => 'required|numeric|greater_than_equal_to[0]',
    ];

    /** Tranches actuellement actives (non historisees) d'un type d'operation. */
    public function listerTranchesActives(int $idTypeOperation): array
    {
        return $this->where('id_type_operation', $idTypeOperation)
            ->where('date_fin_validite', null)
            ->orderBy('montant_min', 'ASC')
            ->findAll();
    }

    /** Historique complet (actives + desactivees) d'un type d'operation. */
    public function listerHistoriqueTranches(int $idTypeOperation): array
    {
        return $this->where('id_type_operation', $idTypeOperation)
            ->orderBy('date_debut_validite', 'DESC')
            ->findAll();
    }

    /**
     * Verifie qu'une nouvelle tranche [montantMin, montantMax] ne chevauche
     * aucune tranche ACTIVE existante du meme type d'operation.
     */
    public function chevaucheTrancheActive(int $idTypeOperation, float $montantMin, float $montantMax, ?int $idExclu = null): bool
    {
        foreach ($this->listerTranchesActives($idTypeOperation) as $tranche) {
            if ($idExclu !== null && (int) $tranche['id'] === $idExclu) {
                continue;
            }
            $seChevauchent = $montantMin <= (float) $tranche['montant_max']
                && $montantMax >= (float) $tranche['montant_min'];
            if ($seChevauchent) {
                return true;
            }
        }
        return false;
    }

    /**
     * Desactive une tranche (historisation par date_fin_validite).
     * Regle de maintenabilite : ne JAMAIS supprimer physiquement une tranche,
     * sinon les operations passees perdent leur coherence avec le frais applique.
     */
    public function desactiverTranche(int $idTranche): bool
    {
        return (bool) $this->update($idTranche, ['date_fin_validite' => date('Y-m-d H:i:s')]);
    }
}
