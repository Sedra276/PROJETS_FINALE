<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurConfigModel extends Model
{
    protected $table = 'operateur_config';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prefixe', 'libelle', 'actif'];
    protected $returnType = 'array';

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
}