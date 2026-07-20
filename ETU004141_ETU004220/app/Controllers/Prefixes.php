<?php

namespace App\Controllers;

use App\Models\OperateurConfigModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Prefixes extends BaseController
{
    protected OperateurConfigModel $operateurConfigModel;

    public function __construct()
    {
        $this->operateurConfigModel = new OperateurConfigModel();
    }

    public function listerPrefixes()
    {
        $prefixes = $this->operateurConfigModel->orderBy('prefixe', 'ASC')->findAll();
        return view('operateur/prefixes/liste', ['prefixes' => $prefixes]);
    }

    public function nouveauPrefixe()
    {
        return view('operateur/prefixes/formulaire', ['prefixe' => null]);
    }

    public function creerPrefixe()
    {
        $donnees = [
            'prefixe' => $this->request->getPost('prefixe'),
            'libelle' => $this->request->getPost('libelle'),
            'actif'   => 1,
        ];

        if (! $this->operateurConfigModel->validate($donnees)) {
            return redirect()->back()->withInput()->with('erreurs', $this->operateurConfigModel->errors());
        }

        $this->operateurConfigModel->insert($donnees);
        return redirect()->to('/admin/prefixes')->with('succes', 'Prefixe cree.');
    }

    public function modifierPrefixe(int $id)
    {
        $prefixe = $this->operateurConfigModel->find($id);
        if ($prefixe === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        return view('operateur/prefixes/formulaire', ['prefixe' => $prefixe]);
    }

    public function enregistrerModificationPrefixe(int $id)
    {
        $donnees = [
            'id'      => $id,
            'prefixe' => $this->request->getPost('prefixe'),
            'libelle' => $this->request->getPost('libelle'),
        ];

        if (! $this->operateurConfigModel->validate($donnees)) {
            return redirect()->back()->withInput()->with('erreurs', $this->operateurConfigModel->errors());
        }

        $this->operateurConfigModel->update($id, [
            'prefixe' => $donnees['prefixe'],
            'libelle' => $donnees['libelle'],
        ]);

        return redirect()->to('/admin/prefixes')->with('succes', 'Prefixe modifie.');
    }

    public function activerDesactiverPrefixe(int $id)
    {
        $prefixe = $this->operateurConfigModel->find($id);
        if ($prefixe === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->operateurConfigModel->update($id, ['actif' => $prefixe['actif'] ? 0 : 1]);
        return redirect()->to('/admin/prefixes')->with('succes', 'Statut du prefixe mis a jour.');
    }
}
