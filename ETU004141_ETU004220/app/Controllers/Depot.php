<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;

class Depot extends BaseController
{
    public function formulaireDepot()
    {
        return view('client/depot');
    }

    public function effectuerDepot()
    {
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Montant invalide');
        }

        $db = db_connect();
        $db->transStart();

        $modeleClient = new ClientModel();
        $modeleOperation = new OperationModel();

        $client = $modeleClient->find(session()->get('client_id'));
        $soldeAvant = $client['solde'];
        $soldeApres = $soldeAvant + $montant;

        $modeleClient->mettreAJourSolde($client['id'], $soldeApres);

        $modeleOperation->enregistrerOperation([
            'id_type_operation' => 1,
            'id_client_destination' => $client['id'],
            'montant' => $montant,
            'frais_appliques' => 0,
            'solde_avant_destination' => $soldeAvant,
            'solde_apres_destination' => $soldeApres,
            'statut' => 'VALIDEE',
        ]);

        $db->transComplete();

        return redirect()->to('/client/solde')->with('succes', 'Depot effectue');
    }
}