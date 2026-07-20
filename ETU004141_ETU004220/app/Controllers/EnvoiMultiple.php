<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\OperateurConfigModel;
use App\Models\CommissionInteroperateurModel;
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

        $infoOperateurSource = $modeleOperateur->determinerOperateur($source['numero_telephone']);

        $destinatairesValides = [];
        $totalDebit = 0;

        foreach ($destinataires as $index => $dest) {
            $numero = $dest['numero'] ?? '';
            $montant = (float) ($dest['montant'] ?? 0);

            if (strlen($numero) !== $telephoneLength || !ctype_digit($numero)) {
                return redirect()->back()->with('erreur', "Le destinataire {$index} a un numero invalide");
            }

            if ($montant <= 0) {
                return redirect()->back()->with('erreur', "Le montant du destinataire {$index} est invalide");
            }

            $infoOperateur = $modeleOperateur->determinerOperateur($numero);

            if ($infoOperateur['operateur'] === null) {
                return redirect()->back()->with('erreur', "Destinataire {$index} : prefixe invalide");
            }

            $destination = $modeleClient->rechercherParNumero($numero);
            if ($destination === null) {

                if (!$infoOperateur['est_interne']) {
                    $destination = $modeleClient->creerClient($numero);
                } else {
                    return redirect()->back()->with('erreur', "Destinataire {$index} introuvable (doit etre deja connecte)");
                }
            }

            if ($destination['id'] === $source['id']) {
                return redirect()->back()->with('erreur', "Destinataire {$index} invalide (vous-même)");
            }

            if (isset($destinatairesValides[$destination['id']])) {
                return redirect()->back()->with('erreur', "Destinataire {$index} en double");
            }

            $fraisAppliques = 0;
            if (!$infoOperateur['est_interne']) {
                $modeleCommission = new CommissionInteroperateurModel();
                $fraisAppliques = $modeleCommission->calculerCommission($infoOperateur['operateur']['id'], $montant);
            } else {
                $fraisAppliques = $calculateurFrais->calculerFrais(3, $infoOperateurSource['operateur']['id'], $montant);
            }

            $totalDestinataire = $montant + $fraisAppliques;
            $totalDebit += $totalDestinataire;

            $destinatairesValides[$destination['id']] = [
                'destination' => $destination,
                'montant' => $montant,
                'frais_appliques' => $fraisAppliques,
                'total' => $totalDestinataire,
                'est_interne' => $infoOperateur['est_interne']
            ];
        }

        if ($source['solde'] < $totalDebit) {
            return redirect()->back()->with('erreur', 'Solde insuffisant pour le total');
        }

        $db = db_connect();
        $db->transStart();

        $modeleOperation = new OperationModel();
        $soldeAvantSource = $source['solde'];
        $soldeApresSource = $soldeAvantSource - $totalDebit;

        $modeleClient->mettreAJourSolde($source['id'], $soldeApresSource);

        $idLot = time();

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
                'frais_appliques' => $dest['frais_appliques'],
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
