<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<div class="container mt-5">
    <h3 class="mb-4">Historique des operations</h3>

    <form method="get" action="<?= site_url('client/historique') ?>" class="row g-2 mb-4">
        <div class="col-auto">
            <select name="type" class="form-select">
                <option value="">Tous les types</option>
                <option value="DEPOT">Depot</option>
                <option value="RETRAIT">Retrait</option>
                <option value="TRANSFERT">Transfert</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Filtrer</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Frais</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($operations)): ?>
                <tr>
                    <td colspan="5" class="text-center">Aucune opération trouvée</td>
                </tr>
            <?php else: ?>
                <?php foreach ($operations as $ligne): ?>
                    <tr>
                        <td><?= esc($ligne['date_operation']) ?></td>
                        <td><?= esc($ligne['type_libelle']) ?></td>
                        <td><?= number_format($ligne['montant'], 0, ',', ' ') ?> Ar</td>
                        <td><?= number_format($ligne['frais_appliques'], 0, ',', ' ') ?> Ar</td>
                        <td><?= esc($ligne['statut']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="<?= site_url('client/solde') ?>" class="btn btn-link">Retour</a>
</div>

<?= $this->endSection() ?>