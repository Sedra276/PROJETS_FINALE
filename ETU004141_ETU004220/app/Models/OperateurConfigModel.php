<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurConfigModel extends Model
{
    protected $table         = 'operateur_config';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['prefixe', 'libelle', 'actif'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $validationRules = [
        'prefixe' => 'required|regex_match[/^[0-9]{2,3}$/]|is_unique[operateur_config.prefixe,id,{id}]',
        'libelle' => 'permit_empty|max_length[100]',
        'actif'   => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'prefixe' => [
            'regex_match' => "Le prefixe doit contenir 2 ou 3 chiffres (ex: 033, 037).",
            'is_unique'   => "Ce prefixe existe deja.",
        ],
    ];

    /** Renvoie uniquement les prefixes actifs (utilise par le Binome 2 pour valider un numero). */
    public function listerPrefixesActifs(): array
    {
        return $this->where('actif', 1)->findAll();
    }

    /** Verifie si un numero de telephone commence par un prefixe actif connu. */
    public function numeroRespectePrefixeValide(string $numeroTelephone): bool
    {
        foreach ($this->listerPrefixesActifs() as $prefixeConfig) {
            if (strpos($numeroTelephone, $prefixeConfig['prefixe']) === 0) {
                return true;
            }
        }
        return false;
    }
}
