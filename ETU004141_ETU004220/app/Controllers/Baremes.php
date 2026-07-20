<?php

namespace App\Controllers;

use App\Models\TrancheFraisModel;
use App\Models\TypeOperationModel;
use App\Models\OperateurConfigModel;

class Baremes extends BaseController
{
    protected TrancheFraisModel $trancheFraisModel;
    protected TypeOperationModel $typeOperationModel;
    protected OperateurConfigModel $operateurConfigModel;

    public function __construct()
    {
        $this->trancheFraisModel   = new TrancheFraisModel();
        $this->typeOperationModel  = new TypeOperationModel();
        $this->operateurConfigModel = new OperateurConfigModel();
    }

    public function listerBaremes()
    {
        $idTypeOperation   = $this->request->getGet('id_type_operation');
        $idOperateurConfig = $this->request->getGet('id_operateur_config');
        $types             = $this->typeOperationModel->findAll();
        $operateurs        = $this->operateurConfigModel->orderBy('prefixe', 'ASC')->findAll();

        $tranches = $this->trancheFraisModel->listerHistoriqueTranches(
            ! empty($idTypeOperation) ? (int) $idTypeOperation : null,
            ! empty($idOperateurConfig) ? (int) $idOperateurConfig : null,
        );

        return view('operateur/baremes/liste', [
            'tranches'                    => $tranches,
            'types'                       => $types,
            'operateurs'                  => $operateurs,
            'idTypeOperationSelectionne'  => $idTypeOperation,
            'idOperateurConfigSelectionne'=> $idOperateurConfig,
        ]);
    }

    public function nouveauBareme()
    {
        $types      = $this->typeOperationModel->findAll();
        $operateurs = $this->operateurConfigModel->orderBy('prefixe', 'ASC')->findAll();
        return view('operateur/baremes/formulaire', ['types' => $types, 'operateurs' => $operateurs]);
    }

    public function creerBareme()
    {
        $idTypeOperation   = (int) $this->request->getPost('id_type_operation');
        $idOperateurConfig = (int) $this->request->getPost('id_operateur_config');
        $montantMin        = (float) $this->request->getPost('montant_min');
        $montantMax        = (float) $this->request->getPost('montant_max');
        $typeCalcul        = (string) $this->request->getPost('type_calcul');
        $valeur            = (float) $this->request->getPost('valeur');

        if ($montantMin >= $montantMax) {
            return redirect()->back()->withInput()->with('erreur', 'Le montant minimum doit etre inferieur au montant maximum.');
        }

        if ($typeCalcul === 'POURCENTAGE' && ($valeur < 0 || $valeur > 100)) {
            return redirect()->back()->withInput()->with('erreur', 'Un pourcentage doit etre compris entre 0 et 100.');
        }

        if ($this->trancheFraisModel->chevaucheTrancheActive($idTypeOperation, $idOperateurConfig, $montantMin, $montantMax)) {
            return redirect()->back()->withInput()->with('erreur', 'Cette tranche chevauche une tranche active existante pour cet operateur.');
        }

        $this->trancheFraisModel->insert([
            'id_type_operation'   => $idTypeOperation,
            'id_operateur_config' => $idOperateurConfig,
            'montant_min'         => $montantMin,
            'montant_max'         => $montantMax,
            'type_calcul'         => $typeCalcul,
            'valeur'              => $valeur,
            'date_debut_validite' => date('Y-m-d H:i:s'),
            'date_fin_validite'   => null,
        ]);

        return redirect()->to('/admin/baremes')->with('succes', 'Tranche de frais creee.');
    }

    public function desactiverBareme(int $id)
    {
        $this->trancheFraisModel->desactiverTranche($id);
        return redirect()->to('/admin/baremes')->with('succes', 'Tranche desactivee (conservee en historique).');
    }
}
