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

        $telephoneLength = config('App')->telephoneLength;
        if (strlen($numero) !== $telephoneLength || !ctype_digit($numero)) {
            return redirect()->back()->with('erreur', "Le numero doit contenir exactement {$telephoneLength} chiffres");
        }

        $modeleOperateur = new OperateurConfigModel();
        $infoOperateur = $modeleOperateur->determinerOperateur($numero);

        if ($infoOperateur['operateur'] === null) {
            return redirect()->back()->with('erreur', 'Prefixe invalide');
        }

        if (!$infoOperateur['est_interne']) {
            return redirect()->back()->with('erreur', 'Connexion reservee aux clients de notre operateur uniquement');
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