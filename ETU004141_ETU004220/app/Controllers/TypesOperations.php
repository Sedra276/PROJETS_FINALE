<?php

namespace App\Controllers;

use App\Models\TypeOperationModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class TypesOperations extends BaseController
{
    protected TypeOperationModel $typeOperationModel;

    public function __construct()
    {
        $this->typeOperationModel = new TypeOperationModel();
    }

    public function listerTypesOperation()
    {
        $types = $this->typeOperationModel->findAll();
        return view('operateur/types_operations/liste', ['types' => $types]);
    }

    public function modifierTypeOperation(int $id)
    {
        $type = $this->typeOperationModel->find($id);
        if ($type === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        return view('operateur/types_operations/formulaire', ['type' => $type]);
    }

    public function enregistrerModificationTypeOperation(int $id)
    {
        $libelle = $this->request->getPost('libelle');

        if (empty($libelle)) {
            return redirect()->back()->withInput()->with('erreur', 'Le libelle est obligatoire.');
        }

        $this->typeOperationModel->update($id, ['libelle' => $libelle]);
        return redirect()->to('/admin/types-operations')->with('succes', "Type d'operation modifie.");
    }
}
