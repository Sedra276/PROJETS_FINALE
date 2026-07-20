<?php

namespace App\Controllers;

use App\Models\CommissionInteroperateurModel;
use App\Models\OperateurConfigModel;

class CommissionInteroperateurController extends BaseController
{
    protected CommissionInteroperateurModel $commissionModel;
    protected OperateurConfigModel $operateurConfigModel;

    public function __construct()
    {
        $this->commissionModel      = new CommissionInteroperateurModel();
        $this->operateurConfigModel = new OperateurConfigModel();
    }

    public function index()
    {
        return view('commission_interoperateur/index', [
            'commissions' => $this->commissionModel->listerAvecOperateur(),
        ]);
    }

    public function create()
    {
        return view('commission_interoperateur/form', [
            'commission'          => null,
            'operateursExternes'  => $this->operateurConfigModel->listerOperateursExternesActifs(),
            'erreurs'             => session()->getFlashdata('erreurs') ?? [],
        ]);
    }

    public function store()
    {
        $donnees = [
            'id_operateur_config' => (int) $this->request->getPost('id_operateur_config'),
            'pourcentage'         => $this->request->getPost('pourcentage'),
            'actif'               => $this->request->getPost('actif') ? 1 : 0,
        ];

        if (! $this->commissionModel->validate($donnees)) {
            return redirect()->to(site_url('admin/commissions-interoperateur/creer'))
                ->with('erreurs', $this->commissionModel->errors())
                ->withInput();
        }

        if ($donnees['actif'] && $this->commissionModel->existeCommissionActivePourOperateur($donnees['id_operateur_config'])) {
            return redirect()->to(site_url('admin/commissions-interoperateur/creer'))
                ->with('erreurs', ['id_operateur_config' => "Une commission active existe deja pour cet operateur."])
                ->withInput();
        }

        $this->commissionModel->insert($donnees);

        return redirect()->to(site_url('admin/commissions-interoperateur'))
            ->with('message', "Commission interoperateur creee.");
    }

    public function edit(int $id)
    {
        $commission = $this->commissionModel->find($id);
        if ($commission === null) {
            return redirect()->to(site_url('admin/commissions-interoperateur'))
                ->with('erreurs', ['id' => "Commission introuvable."]);
        }

        return view('commission_interoperateur/form', [
            'commission'         => $commission,
            'operateursExternes' => $this->operateurConfigModel->listerOperateursExternesActifs(),
            'erreurs'            => session()->getFlashdata('erreurs') ?? [],
        ]);
    }

    public function update(int $id)
    {
        $commission = $this->commissionModel->find($id);
        if ($commission === null) {
            return redirect()->to(site_url('admin/commissions-interoperateur'))
                ->with('erreurs', ['id' => "Commission introuvable."]);
        }

        $donnees = [
            'id_operateur_config' => (int) $this->request->getPost('id_operateur_config'),
            'pourcentage'         => $this->request->getPost('pourcentage'),
            'actif'               => $this->request->getPost('actif') ? 1 : 0,
        ];

        if (! $this->commissionModel->validate($donnees)) {
            return redirect()->to(site_url('admin/commissions-interoperateur/modifier/' . $id))
                ->with('erreurs', $this->commissionModel->errors())
                ->withInput();
        }

        if ($donnees['actif'] && $this->commissionModel->existeCommissionActivePourOperateur($donnees['id_operateur_config'], $id)) {
            return redirect()->to(site_url('admin/commissions-interoperateur/modifier/' . $id))
                ->with('erreurs', ['id_operateur_config' => "Une commission active existe deja pour cet operateur."])
                ->withInput();
        }

        $this->commissionModel->update($id, $donnees);

        return redirect()->to(site_url('admin/commissions-interoperateur'))
            ->with('message', "Commission interoperateur modifiee.");
    }

    public function toggleActif(int $id)
    {
        $this->commissionModel->basculerActif($id);

        return redirect()->to(site_url('admin/commissions-interoperateur'));
    }
}
