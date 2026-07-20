<?php

namespace App\Models;

use CodeIgniter\Model;


class ClientModel extends Model
{
    protected $table         = 'client';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['numero_telephone', 'nom', 'prenom', 'solde', 'statut', 'date_creation'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    /** Utilise par le dashboard operateur : liste tous les comptes clients (lecture seule). */
    public function listerTousLesComptes(): array
    {
        return $this->orderBy('date_creation', 'DESC')->findAll();
    }
}
