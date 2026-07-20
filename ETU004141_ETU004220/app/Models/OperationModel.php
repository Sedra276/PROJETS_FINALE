<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_type_operation', 'id_client_source', 'id_client_destination', 'id_utilisateur',
        'montant', 'frais_appliques', 'solde_avant_source', 'solde_apres_source',
        'solde_avant_destination', 'solde_apres_destination', 'statut',
        'frais_retrait_inclus', 'montant_frais_retrait_inclus', 'id_lot_envoi',
    ];
    protected $returnType = 'array';

    public function enregistrerOperation(array $donnees)
    {
        $this->insert($donnees);
        return $this->getInsertID();
    }

    public function historiqueParClient(int $idClient, ?string $codeType = null)
{
    $builder = $this
        ->select('operation.*, type_operation.libelle AS type_libelle, 
                  client_source.numero_telephone AS numero_source,
                  client_destination.numero_telephone AS numero_destination')
        ->join('type_operation', 'type_operation.id = operation.id_type_operation')
        ->join('client AS client_source', 'client_source.id = operation.id_client_source', 'left')
        ->join('client AS client_destination', 'client_destination.id = operation.id_client_destination', 'left')
        ->groupStart()
            ->where('id_client_source', $idClient)
            ->orWhere('id_client_destination', $idClient)
        ->groupEnd()
        ->orderBy('date_operation', 'DESC');

    if ($codeType !== null) {
        $builder = $builder->where('type_operation.code', $codeType);
    }

    return $builder->findAll();
}

     public function calculerGainsParType(?string $dateDebut = null, ?string $dateFin = null): array
    {
        $builder = $this->db->table('operation o')
            ->select("t.libelle AS type_operation, 
                      CASE 
                          WHEN oc.est_notre_operateur IS NULL THEN 'Notre opérateur' 
                          WHEN oc.est_notre_operateur = 1 THEN 'Notre opérateur'
                          ELSE 'Autres opérateurs' 
                      END AS categorie_operateur,
                      COUNT(o.id) AS nombre_operations, SUM(o.frais_appliques) AS total_frais")
            ->join('type_operation t', 't.id = o.id_type_operation')
            ->join('client c_dest', 'c_dest.id = o.id_client_destination', 'left')
            ->join('operateur_config oc', 'oc.prefixe = SUBSTR(c_dest.numero_telephone, 1, 3)', 'left')
            ->where('o.statut', 'VALIDEE')
            ->groupBy('t.libelle, categorie_operateur')
            ->orderBy('t.libelle', 'ASC');

        if (! empty($dateDebut)) {
            $builder->where('o.date_operation >=', $dateDebut);
        }
        if (! empty($dateFin)) {
            $builder->where('o.date_operation <=', $dateFin);
        }

        return $builder->get()->getResultArray();
    }

    public function calculerMontantsAEnvoyer(?string $dateDebut = null, ?string $dateFin = null): array
    {
        $builder = $this->db->table('operation o')
            ->select("oc.libelle AS nom_operateur, oc.prefixe, SUM(o.montant) AS total_montant, COUNT(o.id) AS nombre_operations")
            ->join('type_operation t', 't.id = o.id_type_operation')
            ->join('client c_dest', 'c_dest.id = o.id_client_destination')
            ->join('operateur_config oc', 'oc.prefixe = SUBSTR(c_dest.numero_telephone, 1, 3)')
            ->where('t.code', 'TRANSFERT')
            ->where('o.statut', 'VALIDEE')
            ->where('oc.est_notre_operateur', 0)
            ->groupBy('oc.id');

        if (! empty($dateDebut)) {
            $builder->where('o.date_operation >=', $dateDebut);
        }
        if (! empty($dateFin)) {
            $builder->where('o.date_operation <=', $dateFin);
        }

        return $builder->get()->getResultArray();
    }

    public function historiqueParClientGroupe(int $idClient, ?string $codeType = null): array
    {
        $operations = $this->historiqueParClient($idClient, $codeType);

        $groupeParLot = [];
        foreach ($operations as $operation) {
            $idLot = $operation['id_lot_envoi'] ?? null;

            if ($idLot !== null) {

                if (!isset($groupeParLot[$idLot])) {
                    $groupeParLot[$idLot] = [
                        'type' => 'lot',
                        'id_lot' => $idLot,
                        'date_operation' => $operation['date_operation'],
                        'operations' => []
                    ];
                }
                $groupeParLot[$idLot]['operations'][] = $operation;
            } else {

                $groupeParLot[] = [
                    'type' => 'individuelle',
                    'operation' => $operation
                ];
            }
        }

        return $groupeParLot;
    }
}
