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

    /** GET /admin/types-operations - liste les types d'operation. */
    public function listerTypesOperation()
    {
        $types = $this->typeOperationModel->findAll();
        return view('operateur/types_operations/liste', ['types' => $types]);
    }

    /** GET /admin/types-operations/{id}/modifier - formulaire d'edition du libelle. */
    public function modifierTypeOperation(int $id)
    {
        $type = $this->typeOperationModel->find($id);
        if ($type === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        return view('operateur/types_operations/formulaire', ['type' => $type]);
    }

    /**
     * POST /admin/types-operations/{id}
     * Le "code" (DEPOT/RETRAIT/TRANSFERT) n'est JAMAIS modifiable : c'est un
     * referentiel fixe. Seul le libelle affiche peut etre change.
     */
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
