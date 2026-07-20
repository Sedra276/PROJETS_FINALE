<?php

namespace App\Controllers;

use App\Models\OperationModel;
use App\Models\ClientModel;

class DashboardOperateur extends BaseController
{
    protected OperationModel $operationModel;
    protected ClientModel $clientModel;

    public function __construct()
    {
        $this->operationModel = new OperationModel();
        $this->clientModel    = new ClientModel();
    }

    /** GET /admin/gains - "Situation gain" : frais percus par type d'operation. */
    public function afficherGains()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $gains = $this->operationModel->calculerGainsParType($dateDebut ?: null, $dateFin ?: null);

        return view('operateur/dashboard/gains', [
            'gains'     => $gains,
            'dateDebut' => $dateDebut,
            'dateFin'   => $dateFin,
        ]);
    }

    /** GET /admin/comptes-clients - "Situation des comptes clients", lecture seule. */
    public function afficherComptesClients()
    {
        $comptes = $this->clientModel->listerTousLesComptes();
        return view('operateur/dashboard/comptes_clients', ['comptes' => $comptes]);
    }

    /** GET /admin/montants-operateurs - "Situation des montants à envoyer à chaque opérateur" */
    public function afficherMontantsAEnvoyer()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $montants = $this->operationModel->calculerMontantsAEnvoyer($dateDebut ?: null, $dateFin ?: null);

        return view('operateur/dashboard/montants_operateurs', [
            'montants'  => $montants,
            'dateDebut' => $dateDebut,
            'dateFin'   => $dateFin,
        ]);
    }
}
