<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionInteroperateurModel extends Model
{
    protected $table         = 'commission_interoperateur';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['id_operateur_config', 'pourcentage', 'actif'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $validationRules = [
        'id_operateur_config' => 'required|integer',
        'pourcentage'         => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'actif'               => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'pourcentage' => [
            'greater_than_equal_to' => "Le pourcentage doit etre compris entre 0 et 100.",
            'less_than_equal_to'    => "Le pourcentage doit etre compris entre 0 et 100.",
        ],
    ];

    
    public function listerAvecOperateur(): array
    {
        return $this->select('commission_interoperateur.*, operateur_config.libelle, operateur_config.prefixe')
            ->join('operateur_config', 'operateur_config.id = commission_interoperateur.id_operateur_config')
            ->orderBy('operateur_config.libelle', 'ASC')
            ->findAll();
    }

  
    public function trouverCommissionActivePourOperateur(int $idOperateurConfig): ?array
    {
        return $this->where('id_operateur_config', $idOperateurConfig)
            ->where('actif', 1)
            ->first();
    }

   
    public function existeCommissionActivePourOperateur(int $idOperateurConfig, ?int $idExclu = null): bool
    {
        $builder = $this->where('id_operateur_config', $idOperateurConfig)->where('actif', 1);

        if ($idExclu !== null) {
            $builder = $builder->where('id !=', $idExclu);
        }

        return $builder->first() !== null;
    }

    
    public function basculerActif(int $idCommission): bool
    {
        $commission = $this->find($idCommission);
        if ($commission === null) {
            return false;
        }

        return (bool) $this->update($idCommission, ['actif' => $commission['actif'] ? 0 : 1]);
    }
}
