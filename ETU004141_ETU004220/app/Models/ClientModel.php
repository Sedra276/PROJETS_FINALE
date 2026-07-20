<?php

namespace App\Models;

use CodeIgniter\Model;

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
