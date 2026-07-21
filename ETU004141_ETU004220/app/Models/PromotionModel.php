<?php
namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model{

protected $table ='promotion';
protected $primaryKey = 'id';
protected $allowedFields = [
    'id_operateur_config','libelle','pourcentage',
    'date_debut','date_fin','actif',
];

protected $returnType = 'array';
protected $useTimestamps = false;

 protected $validationRules = [
        'prefixe' => 'required|regex_match[/^[0-9]{2,3}$/]|is_unique[operateur_config.prefixe,id,{id}]',
        'libelle'             => 'permit_empty|max_length[100]',
        'actif'               => 'permit_empty|in_list[0,1]',
        'est_notre_operateur' => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'prefixe' => [
            'regex_match' => "Le prefixe doit contenir 2 ou 3 chiffres (ex: 033, 037).",
            'is_unique'   => "Ce prefixe existe deja.",
        ],
    ];

    public function listerToutesAvecOperateur():array{
        return $this->select('promotion.*,operateur_config.libelle AS operateur_libelle,operateur_config.prefixe')
        ->join('operateur_config','operateur_config.id =promotion,id_operateur_config')
        ->orderBy('promotion.date_debut','DESC')
        ->findAll();
    }

    public function getPromotionActiveParOperateur(int $idOperateurConfig): ?array{
        return $this->where('id_operateur_config',$idOperateurConfig)
        ->where('actif',1)
        ->where('date_debut<=',date('Y-m-d H:i:s'))
        ->groupStart()
            ->where('date_fin',null)
            ->orwhere('date_fin >=',date('Y-m-d H:i:s'))
        ->groupEnd()
        ->orderBy('date_debut','DESC')
        ->first();
    }

    
}

?>