<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperateurConfigModel;

class AuthClient extends BaseController
{
    public function formulaireConnexion()
    {
        return view('client/connexion');
    }

    public function connexion()
    {
        $numero = $this->request->getPost('numero_telephone');

        if (empty($numero)) {
            return redirect()->back()->with('erreur', 'Numero obligatoire');
        }

        $modeleOperateur = new OperateurConfigModel();

        if (!$modeleOperateur->prefixeEstValide($numero)) {
            return redirect()->back()->with('erreur', 'Prefixe invalide');
        }

        $modeleClient = new ClientModel();
        $client = $modeleClient->rechercherParNumero($numero);

        if ($client === null) {
            $client = $modeleClient->creerClient($numero);
        }

        if ($client['statut'] === 'BLOQUE') {
            return redirect()->back()->with('erreur', 'Compte bloque');
        }

        session()->set('client_id', $client['id']);
        session()->set('client_numero', $client['numero_telephone']);

        return redirect()->to('/client/solde');
    }

    public function deconnexion()
    {
        session()->destroy();
        return redirect()->to('/client/connexion');
    }
}