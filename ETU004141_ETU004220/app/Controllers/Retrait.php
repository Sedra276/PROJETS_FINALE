<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Libraries\FraisCalculatorService;

class Retrait extends BaseController
{
    public function formulaireRetrait()
    {
        return view('client/retrait');
    }

    public function effectuerRetrait()
    {
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Montant invalide');
        }

        $modeleClient = new ClientModel();
        $client = $modeleClient->find(session()->get('client_id'));

        $calculateurFrais = new FraisCalculatorService();
        $frais = $calculateurFrais->calculerFrais(2, $montant);
        $total = $montant + $frais;

        if ($client['solde'] < $total) {
            return redirect()->back()->with('erreur', 'Solde insuffisant');
        }

        $db = db_connect();
        $db->transStart();

        $modeleOperation = new OperationModel();
        $soldeAvant = $client['solde'];
        $soldeApres = $soldeAvant - $total;

        $modeleClient->mettreAJourSolde($client['id'], $soldeApres);

        $modeleOperation->enregistrerOperation([
            'id_type_operation' => 2,
            'id_client_source' => $client['id'],
            'montant' => $montant,
            'frais_appliques' => $frais,
            'solde_avant_source' => $soldeAvant,
            'solde_apres_source' => $soldeApres,
            'statut' => 'VALIDEE',
        ]);

        $db->transComplete();

        return redirect()->to('/client/solde')->with('succes', 'Retrait effectue');
    }
}