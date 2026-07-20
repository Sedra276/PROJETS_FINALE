<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table         = 'type_operation';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['code', 'libelle'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $validationRules = [
        'code'    => 'required|in_list[DEPOT,RETRAIT,TRANSFERT]',
        'libelle' => 'required|max_length[100]',
    ];

    /** Recherche un type d'operation par son code (DEPOT, RETRAIT, TRANSFERT). */
    public function trouverParCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }
}
