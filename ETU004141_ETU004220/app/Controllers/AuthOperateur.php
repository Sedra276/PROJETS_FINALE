<?php

namespace App\Controllers;

use App\Models\UtilisateurModel;

class AuthOperateur extends BaseController
{
    protected UtilisateurModel $utilisateurModel;

    public function __construct()
    {
        $this->utilisateurModel = new UtilisateurModel();
    }

    public function afficherFormulaireConnexion()
    {
        return view('operateur/login');
    }

    public function connexion()
    {
        $login      = (string) $this->request->getPost('login');
        $motDePasse = (string) $this->request->getPost('mot_de_passe');

        $utilisateur = $this->utilisateurModel->verifierIdentifiants($login, $motDePasse);

        if ($utilisateur === null) {
            return redirect()->back()->withInput()->with('erreur', 'Identifiants incorrects.');
        }

        session()->set([
            'operateur_id'   => $utilisateur['id'],
            'operateur_nom'  => $utilisateur['nom'],
            'operateur_role' => $utilisateur['role'],
        ]);

        return redirect()->to('/admin/gains');
    }

    public function deconnexion()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
