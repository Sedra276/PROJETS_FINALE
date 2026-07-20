<?php

namespace App\Controllers;

use App\Models\OperationModel;

class Historique extends BaseController
{
    public function voirHistorique()
    {
        $clientId = session()->get('client_id');

        if ($clientId === null) {
            return redirect()->to('/client/connexion')->with('erreur', 'Veuillez vous connecter');
        }

        $modeleOperation = new OperationModel();
        $codeType = $this->request->getGet('type');

        $operations = $modeleOperation->historiqueParClientGroupe(
            $clientId,
            $codeType ?: null
        );

        return view('client/historique', ['operations' => $operations]);
    }
}