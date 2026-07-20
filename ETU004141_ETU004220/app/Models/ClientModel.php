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
    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $allowedFields = ['numero_telephone', 'nom', 'prenom', 'solde', 'statut'];
    protected $returnType = 'array';

    public function rechercherParNumero(string $numero)
    {
        return $this->where('numero_telephone', $numero)->first();
    }

    public function creerClient(string $numero)
    {
        $this->insert([
            'numero_telephone' => $numero,
            'solde' => 0,
            'statut' => 'ACTIF',
        ]);

        return $this->find($this->getInsertID());
    }

    public function mettreAJourSolde(int $id, float $nouveauSolde)
    {
        return $this->update($id, ['solde' => $nouveauSolde]);
    }
}
