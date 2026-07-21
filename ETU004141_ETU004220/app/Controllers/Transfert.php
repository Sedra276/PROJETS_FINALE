<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\OperateurConfigModel;
use App\Models\CommissionInteroperateurModel;
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
        if (!preg_match('/^(0[0-9]{8}|\+261[0-9]{9})$/', $numeroDestinataire)) {
            return redirect()->back()->with('erreur', "Format de numero invalide. Utilisez 0XX ou +261 XX");
        }

        // Normaliser le numero pour stockage et recherche (convertir +261 en 0)
        $numeroDestinataire = $this->normaliserNumero($numeroDestinataire);

        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Montant invalide');
        }

        $modeleClient = new ClientModel();
        $modeleOperateur = new OperateurConfigModel();
        $source = $modeleClient->find(session()->get('client_id'));

        $infoOperateurDest = $modeleOperateur->determinerOperateur($numeroDestinataire);

        if ($infoOperateurDest['operateur'] === null) {
            return redirect()->back()->with('erreur', 'Prefixe du destinataire invalide');
        }

        $infoOperateurSource = $modeleOperateur->determinerOperateur($source['numero_telephone']);

        $destination = $modeleClient->rechercherParNumero($numeroDestinataire);

        if ($destination === null) {

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

        $montantFraisRetraitInclus = 0;
        if ($fraisRetraitInclus) {
            $montantFraisRetraitInclus = $calculateurFrais->calculerFrais(2, $infoOperateurSource['operateur']['id'], $montant);
        }

        $fraisAppliques = 0;
        if (!$infoOperateurDest['est_interne']) {
            $modeleCommission = new CommissionInteroperateurModel();
            $fraisAppliques = $modeleCommission->calculerCommission($infoOperateurDest['operateur']['id'], $montant);
        } else {
            $fraisAppliques = $calculateurFrais->calculerFrais(3, $infoOperateurSource['operateur']['id'], $montant);
        }

        $total = $montant + $fraisAppliques + $montantFraisRetraitInclus;

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
            'frais_appliques' => $fraisAppliques,
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

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erreur', 'Erreur lors du transfert');
        }

        return redirect()->to('/client/solde')->with('succes', 'Transfert effectue');
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
}