<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Libraries\FraisCalculatorService;

class Transfert extends BaseController
{
    public function formulaireTransfert()
    {
        return view('client/transfert');
    }

    public function effectuerTransfert()
    {
        $numeroDestinataire = $this->request->getPost('numero_destinataire');
        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Montant invalide');
        }

        $modeleClient = new ClientModel();
        $source = $modeleClient->find(session()->get('client_id'));
        $destination = $modeleClient->rechercherParNumero($numeroDestinataire);

        if ($destination === null) {
            return redirect()->back()->with('erreur', 'Destinataire introuvable');
        }

        if ($destination['id'] === $source['id']) {
            return redirect()->back()->with('erreur', 'Destinataire invalide');
        }

        $calculateurFrais = new FraisCalculatorService();
        $frais = $calculateurFrais->calculerFrais(3, $montant);
        $total = $montant + $frais;

        if ($source['solde'] < $total) {
            return redirect()->back()->with('erreur', 'Solde insuffisant');
        }

        $db = db_connect();
        $db->transStart();

        $modeleOperation = new OperationModel();

        $soldeAvantSource = $source['solde'];
        $soldeApresSource = $soldeAvantSource - $total;

        $soldeAvantDestination = $destination['solde'];
        $soldeApresDestination = $soldeAvantDestination + $montant;

        $modeleClient->mettreAJourSolde($source['id'], $soldeApresSource);
        $modeleClient->mettreAJourSolde($destination['id'], $soldeApresDestination);

        $modeleOperation->enregistrerOperation([
            'id_type_operation' => 3,
            'id_client_source' => $source['id'],
            'id_client_destination' => $destination['id'],
            'montant' => $montant,
            'frais_appliques' => $frais,
            'solde_avant_source' => $soldeAvantSource,
            'solde_apres_source' => $soldeApresSource,
            'solde_avant_destination' => $soldeAvantDestination,
            'solde_apres_destination' => $soldeApresDestination,
            'statut' => 'VALIDEE',
        ]);

        $db->transComplete();

        return redirect()->to('/client/solde')->with('succes', 'Transfert effectue');
    }
}