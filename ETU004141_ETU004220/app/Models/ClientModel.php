<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * NOTE IMPORTANTE : la table client et sa logique d'ecriture (creation,
 * credit/debit du solde) appartiennent au Binome 2. Ce modele est une
 * version MINIMALE utilisee UNIQUEMENT en lecture seule par le dashboard
 * operateur (Binome 1). Si le Binome 2 a deja cree ClientModel.php,
 * fusionnez les deux fichiers au lieu de le dupliquer (conflit Git sinon).
 */
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
