<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurConfigModel extends Model
{
    protected $table         = 'operateur_config';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['prefixe', 'libelle', 'actif', 'est_notre_operateur'];
    protected $returnType    = 'array';
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

    public function listerPrefixesActifs(): array
    {
        return $this->where('actif', 1)->findAll();
    }

    public function numeroRespectePrefixeValide(string $numeroTelephone): bool
    {
        foreach ($this->listerPrefixesActifs() as $prefixeConfig) {
            if (strpos($numeroTelephone, $prefixeConfig['prefixe']) === 0) {
                return true;
            }
        }
        return false;
    }

    public function prefixeEstValide(string $numero): bool
    {
        $prefixes = $this->where('actif', 1)->findAll();

        foreach ($prefixes as $ligne) {
            if (strpos($numero, $ligne['prefixe']) === 0) {
                return true;
            }
        }

        return false;
    }

    public function listerOperateursExternesActifs(): array
    {
        return $this->where('est_notre_operateur', 0)
            ->where('actif', 1)
            ->orderBy('libelle', 'ASC')
            ->findAll();
    }

    public function trouverNotreOperateur(): ?array
    {
        return $this->where('est_notre_operateur', 1)->first();
    }

    public function estPrefixeExterne(string $numeroTelephoneDestinataire): bool
    {
        foreach ($this->where('actif', 1)->findAll() as $operateur) {
            if (strpos($numeroTelephoneDestinataire, $operateur['prefixe']) === 0) {
                return (int) $operateur['est_notre_operateur'] === 0;
            }
        }
        return false;}

    public function determinerOperateur(string $numero): array
    {
        $prefixes = $this->where('actif', 1)->findAll();

        foreach ($prefixes as $ligne) {
            if (strpos($numero, $ligne['prefixe']) === 0) {
                return [
                    'est_interne' => (bool) $ligne['est_notre_operateur'],
                    'operateur' => $ligne
                ];
            }
        }

        return ['est_interne' => false, 'operateur' => null];
    }
}
