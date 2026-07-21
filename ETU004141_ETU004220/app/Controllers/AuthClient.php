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
        if (!preg_match('/^(0[0-9]{8}|\+261[0-9]{9})$/', $numero)) {
            return redirect()->back()->with('erreur', "Format de numero invalide. Utilisez 0XX ou +261 XX");
        }

        // Normaliser le numero pour stockage et recherche (convertir +261 en 0)
        $numero = $this->normaliserNumero($numero);

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