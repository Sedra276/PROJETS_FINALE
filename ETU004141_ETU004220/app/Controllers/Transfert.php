<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\OperateurConfigModel;
use App\Models\CommissionInteropModel;
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
        $fraisRetraitInclus = $this->request->getPost('frais_retrait_inclus') === '1';

        $telephoneLength = config('App')->telephoneLength;
        if (strlen($numeroDestinataire) !== $telephoneLength || !ctype_digit($numeroDestinataire)) {
            return redirect()->back()->with('erreur', "Le numero du destinataire doit contenir exactement {$telephoneLength} chiffres");
        }

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Montant invalide');
        }

        $modeleClient = new ClientModel();
        $modeleOperateur = new OperateurConfigModel();
        $source = $modeleClient->find(session()->get('client_id'));
        
        // Détection opérateur du destinataire
        $infoOperateurDest = $modeleOperateur->determinerOperateur($numeroDestinataire);
        
        if ($infoOperateurDest['operateur'] === null) {
            return redirect()->back()->with('erreur', 'Prefixe du destinataire invalide');
        }

        // Détection opérateur source (notre opérateur)
        $infoOperateurSource = $modeleOperateur->determinerOperateur($source['numero_telephone']);

        $destination = $modeleClient->rechercherParNumero($numeroDestinataire);

        if ($destination === null) {
            // Créer le compte si c'est un opérateur externe
            if (!$infoOperateurDest['est_interne']) {
                $destination = $modeleClient->creerClient($numeroDestinataire);
            } else {
                return redirect()->back()->with('erreur', 'Destinataire introuvable (doit etre deja connecte)');
            }
        }

        if ($destination['id'] === $source['id']) {
            return redirect()->back()->with('erreur', 'Destinataire invalide');
        }

        $calculateurFrais = new FraisCalculatorService();
        // Les frais sont calculés selon l'opérateur SOURCE (notre opérateur)
        $fraisTransfert = $calculateurFrais->calculerFrais(3, $infoOperateurSource['operateur']['id'], $montant);

        // Calcul des frais de retrait inclus si demandé
        $montantFraisRetraitInclus = 0;
        if ($fraisRetraitInclus) {
            $montantFraisRetraitInclus = $calculateurFrais->calculerFrais(2, $infoOperateurSource['operateur']['id'], $montant);
        }

        // Calcul commission interopérateur
        $commissionInterop = 0;
        if (!$infoOperateurDest['est_interne']) {
            $modeleCommission = new CommissionInteropModel();
            $commissionInterop = $modeleCommission->calculerCommission($infoOperateurDest['operateur']['id'], $montant);
        }

        $total = $montant + $fraisTransfert + $montantFraisRetraitInclus + $commissionInterop;

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

        $donneesOperation = [
            'id_type_operation' => 3,
            'id_client_source' => $source['id'],
            'id_client_destination' => $destination['id'],
            'montant' => $montant,
            'frais_appliques' => $fraisTransfert,
            'solde_avant_source' => $soldeAvantSource,
            'solde_apres_source' => $soldeApresSource,
            'solde_avant_destination' => $soldeAvantDestination,
            'solde_apres_destination' => $soldeApresDestination,
            'statut' => 'VALIDEE',
            'frais_retrait_inclus' => $fraisRetraitInclus ? 1 : 0,
            'montant_frais_retrait_inclus' => $montantFraisRetraitInclus,
        ];

        $modeleOperation->enregistrerOperation($donneesOperation);

        $db->transComplete();

        return redirect()->to('/client/solde')->with('succes', 'Transfert effectue');
    }
}