<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionInteropModel extends Model
{
    protected $table = 'commission_interoperateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_operateur_config', 'pourcentage', 'actif'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    /**
     * Récupère la commission pour un opérateur externe
     * @return array|null La commission ou null si non trouvée/inactive
     */
    public function getCommissionParOperateur(int $idOperateurConfig): ?array
    {
        return $this->where('id_operateur_config', $idOperateurConfig)
            ->where('actif', 1)
            ->first();
    }

    /**
     * Calcule le montant de la commission interopérateur
     * @return float Le montant de la commission (0 si non trouvée)
     */
    public function calculerCommission(int $idOperateurConfig, float $montant): float
    {
        $commission = $this->getCommissionParOperateur($idOperateurConfig);
        
        if ($commission === null) {
            return 0.0;
        }

        return round($montant * ((float) $commission['pourcentage'] / 100), 2);
    }
}
