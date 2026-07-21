<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $allowedFields = ['numero_telephone', 'nom', 'prenom', 'solde', 'statut'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function listerTousLesComptes(): array
    {
        return $this->orderBy('date_creation', 'DESC')->findAll();
    }

    public function rechercherParNumero(string $numero)
    {
        $numero = $this->normaliserNumero($numero);
        return $this->where('numero_telephone', $numero)->first();
    }

    public function creerClient(string $numero)
    {
        $numero = $this->normaliserNumero($numero);
        $this->insert([
            'numero_telephone' => $numero,
            'solde' => 0,
            'statut' => 'ACTIF',
        ]);

        return $this->find($this->getInsertID());
    }

    private function normaliserNumero(string $numero): string
    {
        // Convertir le format international (+26134...) en format local (034...)
        if (strpos($numero, '+261') === 0) {
            $indicatif = substr($numero, 4, 2);
            $reste = substr($numero, 6);
            return '0' . $indicatif . $reste;
        }
        return $numero;
    }

    public function mettreAJourSolde(int $id, float $nouveauSolde)
    {
        return $this->update($id, ['solde' => $nouveauSolde]);
    }
}
