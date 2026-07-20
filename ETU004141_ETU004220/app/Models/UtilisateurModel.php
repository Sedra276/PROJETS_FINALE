<?php

namespace App\Models;

use CodeIgniter\Model;

class UtilisateurModel extends Model
{
    protected $table            = 'utilisateur';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nom', 'login', 'mot_de_passe', 'role'];
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $validationRules = [
        'nom'   => 'required|min_length[2]',
        'login' => 'required|min_length[3]|is_unique[utilisateur.login,id,{id}]',
        'role'  => 'required|in_list[ADMIN,AGENT]',
    ];

    public function verifierIdentifiants(string $login, string $motDePasseSaisi): ?array
    {
        $utilisateur = $this->where('login', $login)->first();

        if ($utilisateur === null) {
            return null;
        }

        if ($motDePasseSaisi !== $utilisateur['mot_de_passe']) {
            return null;
        }

        return $utilisateur;
    }

    public function creerUtilisateur(string $nom, string $login, string $motDePasse, string $role): int|false
    {
        return $this->insert([
            'nom'          => $nom,
            'login'        => $login,
            'mot_de_passe' => $motDePasse,
            'role'         => $role,
        ]);
    }
}
