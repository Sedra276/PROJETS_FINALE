<?php

namespace App\Controllers;

use App\Models\ClientModel;

class Solde extends BaseController
{
    public function voirSolde()
    {
        $clientId = session()->get('client_id');

        if ($clientId === null) {
            return redirect()->to('/client/connexion')->with('erreur', 'Veuillez vous connecter');
        }

        $modeleClient = new ClientModel();
        $client = $modeleClient->find($clientId);

        if ($client === null) {
            return redirect()->to('/client/connexion')->with('erreur', 'Client non trouvé');
        }

        return view('client/solde', ['client' => $client]);
    }
}