<?php

namespace App\Controllers;

use App\Models\TrancheFraisModel;
use App\Models\TypeOperationModel;

class Baremes extends BaseController
{
    protected TrancheFraisModel $trancheFraisModel;
    protected TypeOperationModel $typeOperationModel;

    public function __construct()
    {
        $this->trancheFraisModel  = new TrancheFraisModel();
        $this->typeOperationModel = new TypeOperationModel();
    }

    /** GET /admin/baremes - liste les tranches, filtrable par type d'operation. */
    public function listerBaremes()
    {
        $idTypeOperation = $this->request->getGet('id_type_operation');
        $types           = $this->typeOperationModel->findAll();

        if (! empty($idTypeOperation)) {
            $tranches = $this->trancheFraisModel->listerHistoriqueTranches((int) $idTypeOperation);
        } else {
            $tranches = $this->trancheFraisModel->orderBy('date_debut_validite', 'DESC')->findAll();
        }

        return view('operateur/baremes/liste', [
            'tranches'                  => $tranches,
            'types'                     => $types,
            'idTypeOperationSelectionne'=> $idTypeOperation,
        ]);
    }

    /** GET /admin/baremes/nouveau - formulaire de creation d'une tranche. */
    public function nouveauBareme()
    {
        $types = $this->typeOperationModel->findAll();
        return view('operateur/baremes/formulaire', ['types' => $types]);
    }

    /**
     * POST /admin/baremes - enregistre une nouvelle tranche de frais.
     * Regles metier : montant_min < montant_max, pas de chevauchement avec
     * une tranche ACTIVE existante, valeur >= 0, et si POURCENTAGE alors
     * 0 <= valeur <= 100.
     */
    public function creerBareme()
    {
        $idTypeOperation = (int) $this->request->getPost('id_type_operation');
        $montantMin      = (float) $this->request->getPost('montant_min');
        $montantMax      = (float) $this->request->getPost('montant_max');
        $typeCalcul      = (string) $this->request->getPost('type_calcul');
        $valeur          = (float) $this->request->getPost('valeur');

        if ($montantMin >= $montantMax) {
            return redirect()->back()->withInput()->with('erreur', 'Le montant minimum doit etre inferieur au montant maximum.');
        }

        if ($typeCalcul === 'POURCENTAGE' && ($valeur < 0 || $valeur > 100)) {
            return redirect()->back()->withInput()->with('erreur', 'Un pourcentage doit etre compris entre 0 et 100.');
        }

        if ($this->trancheFraisModel->chevaucheTrancheActive($idTypeOperation, $montantMin, $montantMax)) {
            return redirect()->back()->withInput()->with('erreur', 'Cette tranche chevauche une tranche active existante.');
        }

        $this->trancheFraisModel->insert([
            'id_type_operation'   => $idTypeOperation,
            'montant_min'         => $montantMin,
            'montant_max'         => $montantMax,
            'type_calcul'         => $typeCalcul,
            'valeur'              => $valeur,
            'date_debut_validite' => date('Y-m-d H:i:s'),
            'date_fin_validite'   => null,
        ]);

        return redirect()->to('/admin/baremes')->with('succes', 'Tranche de frais creee.');
    }

    /**
     * POST /admin/baremes/{id}/desactiver
     * Historise la tranche (date_fin_validite). Jamais de suppression
     * physique : les operations passees restent coherentes avec le frais
     * qui etait applique au moment ou elles ont ete faites.
     */
    public function desactiverBareme(int $id)
    {
        $this->trancheFraisModel->desactiverTranche($id);
        return redirect()->to('/admin/baremes')->with('succes', 'Tranche desactivee (conservee en historique).');
    }
}
