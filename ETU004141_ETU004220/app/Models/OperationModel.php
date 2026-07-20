<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * NOTE IMPORTANTE : l'ECRITURE dans la table operation (depot, retrait,
 * transfert) appartient au Binome 2. Ce modele n'est utilise ici QUE pour
 * la lecture agregee des gains (dashboard operateur, Binome 1).
 * Si le Binome 2 a deja cree OperationModel.php, fusionnez les deux fichiers.
 */
class OperationModel extends Model
{
    protected $table         = 'operation';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_type_operation', 'id_client_source', 'id_client_destination', 'id_utilisateur',
        'montant', 'frais_appliques', 'solde_avant_source', 'solde_apres_source',
        'solde_avant_destination', 'solde_apres_destination', 'date_operation', 'statut',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    /**
     * Agrege les frais percus par type d'operation sur une periode donnee.
     * Lecture seule : le dashboard operateur n'ecrit jamais dans cette table.
     */
    public function calculerGainsParType(?string $dateDebut = null, ?string $dateFin = null): array
    {
        $builder = $this->db->table('operation o')
            ->select("t.libelle AS type_operation, COUNT(o.id) AS nombre_operations, SUM(o.frais_appliques) AS total_frais")
            ->join('type_operation t', 't.id = o.id_type_operation')
            ->where('o.statut', 'VALIDEE')
            ->groupBy('t.libelle');

        if (! empty($dateDebut)) {
            $builder->where('o.date_operation >=', $dateDebut);
        }
        if (! empty($dateFin)) {
            $builder->where('o.date_operation <=', $dateFin);
        }

        return $builder->get()->getResultArray();
    }
}
