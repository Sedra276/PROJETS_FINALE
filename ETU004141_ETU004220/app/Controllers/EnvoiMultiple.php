<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\OperateurConfigModel;
use App\Models\CommissionInteropModel;
use App\Libraries\FraisCalculatorService;

class EnvoiMultiple extends BaseController
{
    public function formulaireEnvoiMultiple()
    {
        return view('client/envoi_multiple');
    }

    public function effectuerEnvoiMultiple()
    {
        $destinataires = $this->request->getPost('destinataires');

        if (empty($destinataires) || !is_array($destinataires)) {
            return redirect()->back()->with('erreur', 'Veuillez ajouter au moins un destinataire');
        }

        $telephoneLength = config('App')->telephoneLength;
        $modeleClient = new ClientModel();
        $modeleOperateur = new OperateurConfigModel();
        $calculateurFrais = new FraisCalculatorService();

        $source = $modeleClient->find(session()->get('client_id'));
        
        // Détection opérateur source (notre opérateur) pour le calcul des frais
        $infoOperateurSource = $modeleOperateur->determinerOperateur($source['numero_telephone']);
        
        $destinatairesValides = [];
        $totalDebit = 0;

        // Valider et calculer pour chaque destinataire
        foreach ($destinataires as $index => $dest) {
            $numero = $dest['numero'] ?? '';
            $montant = (float) ($dest['montant'] ?? 0);

            // Validation numéro
            if (strlen($numero) !== $telephoneLength || !ctype_digit($numero)) {
                return redirect()->back()->with('erreur', "Le destinataire {$index} a un numero invalide");
            }

            // Validation montant
            if ($montant <= 0) {
                return redirect()->back()->with('erreur', "Le montant du destinataire {$index} est invalide");
            }

            // Détection opérateur du destinataire
            $infoOperateur = $modeleOperateur->determinerOperateur($numero);
            
            if ($infoOperateur['operateur'] === null) {
                return redirect()->back()->with('erreur', "Destinataire {$index} : prefixe invalide");
            }

            // Récupérer destinataire
            $destination = $modeleClient->rechercherParNumero($numero);
            if ($destination === null) {
                // Créer le compte si c'est un opérateur externe
                if (!$infoOperateur['est_interne']) {
                    $destination = $modeleClient->creerClient($numero);
                } else {
                    return redirect()->back()->with('erreur', "Destinataire {$index} introuvable (doit etre deja connecte)");
                }
            }

            if ($destination['id'] === $source['id']) {
                return redirect()->back()->with('erreur', "Destinataire {$index} invalide (vous-même)");
            }

            // Vérifier doublon
            if (isset($destinatairesValides[$destination['id']])) {
                return redirect()->back()->with('erreur', "Destinataire {$index} en double");
            }

            // Calculer frais pour ce destinataire (selon opérateur source)
            $fraisTransfert = $calculateurFrais->calculerFrais(3, $infoOperateurSource['operateur']['id'], $montant);
            
            // Calcul commission interopérateur
            $commissionInterop = 0;
            if (!$infoOperateur['est_interne']) {
                $modeleCommission = new CommissionInteropModel();
                $commissionInterop = $modeleCommission->calculerCommission($infoOperateur['operateur']['id'], $montant);
            }

            $totalDestinataire = $montant + $fraisTransfert + $commissionInterop;
            $totalDebit += $totalDestinataire;

            $destinatairesValides[$destination['id']] = [
                'destination' => $destination,
                'montant' => $montant,
                'frais_transfert' => $fraisTransfert,
                'commission_interop' => $commissionInterop,
                'total' => $totalDestinataire,
                'est_interne' => $infoOperateur['est_interne']
            ];
        }

        // Vérifier solde suffisant
        if ($source['solde'] < $totalDebit) {
            return redirect()->back()->with('erreur', 'Solde insuffisant pour le total');
        }

        // Transaction unique
        $db = db_connect();
        $db->transStart();

        $modeleOperation = new OperationModel();
        $soldeAvantSource = $source['solde'];
        $soldeApresSource = $soldeAvantSource - $totalDebit;

        // Débiter source une seule fois
        $modeleClient->mettreAJourSolde($source['id'], $soldeApresSource);

        // Générer ID de lot (timestamp)
        $idLot = time();

        // Créditer chaque destinataire et enregistrer opération
        foreach ($destinatairesValides as $dest) {
            $destination = $dest['destination'];
            $soldeAvantDestination = $destination['solde'];
            $soldeApresDestination = $soldeAvantDestination + $dest['montant'];

            $modeleClient->mettreAJourSolde($destination['id'], $soldeApresDestination);

            $modeleOperation->enregistrerOperation([
                'id_type_operation' => 3,
                'id_client_source' => $source['id'],
                'id_client_destination' => $destination['id'],
                'montant' => $dest['montant'],
                'frais_appliques' => $dest['frais_transfert'],
                'solde_avant_source' => $soldeAvantSource,
                'solde_apres_source' => $soldeApresSource,
                'solde_avant_destination' => $soldeAvantDestination,
                'solde_apres_destination' => $soldeApresDestination,
                'statut' => 'VALIDEE',
                'id_lot_envoi' => $idLot,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('erreur', 'Erreur lors de l\'envoi multiple');
        }

        return redirect()->to('/client/solde')->with('succes', 'Envoi multiple effectué avec succès');
    }
}
