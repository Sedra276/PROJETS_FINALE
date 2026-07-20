<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<h3 class="mb-3">Situation des montants à envoyer aux autres opérateurs</h3>

<form method="get" action="/admin/montants-operateurs" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="date_debut" class="form-control" value="<?= esc($dateDebut ?? '') ?>">
    </div>
    <div class="col-auto">
        <input type="date" name="date_fin" class="form-control" value="<?= esc($dateFin ?? '') ?>">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filtrer</button></div>
</form>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Opérateur Destinataire</th>
            <th>Préfixe</th>
            <th>Nombre de transferts</th>
            <th>Total Montant à envoyer (Ar)</th>
        </tr>
    </thead>
    <tbody>
        <?php $totalGeneral = 0; ?>
        <?php foreach ($montants as $ligne): ?>
        <tr>
            <td><?= esc($ligne['nom_operateur']) ?></td>
            <td><?= esc($ligne['prefixe']) ?></td>
            <td><?= esc($ligne['nombre_operations']) ?></td>
            <td><?= number_format((float) $ligne['total_montant'], 0, ',', ' ') ?></td>
        </tr>
        <?php $totalGeneral += (float) $ligne['total_montant']; ?>
        <?php endforeach; ?>
        <?php if (empty($montants)): ?>
        <tr><td colspan="4" class="text-center text-muted">Aucun transfert vers un autre opérateur sur cette période.</td></tr>
        <?php endif; ?>
    </tbody>
    <?php if (! empty($montants)): ?>
    <tfoot>
        <tr class="fw-bold">
            <td colspan="3">Total général à envoyer</td>
            <td class="text-danger"><?= number_format($totalGeneral, 0, ',', ' ') ?></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>

<?= $this->endSection() ?>
