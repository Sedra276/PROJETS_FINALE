<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<h3 class="mb-3">Situation des gains</h3>

<form method="get" action="/admin/gains" class="row g-2 mb-3">
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
        <tr><th>Type d'operation</th><th>Nombre d'operations</th><th>Total des frais percus (Ar)</th></tr>
    </thead>
    <tbody>
        <?php $totalGeneral = 0; ?>
        <?php foreach ($gains as $ligne): ?>
        <tr>
            <td><?= esc($ligne['type_operation']) ?></td>
            <td><?= esc($ligne['nombre_operations']) ?></td>
            <td><?= number_format((float) $ligne['total_frais'], 0, ',', ' ') ?></td>
        </tr>
        <?php $totalGeneral += (float) $ligne['total_frais']; ?>
        <?php endforeach; ?>
        <?php if (empty($gains)): ?>
        <tr><td colspan="3" class="text-center text-muted">Aucune operation sur cette periode.</td></tr>
        <?php endif; ?>
    </tbody>
    <?php if (! empty($gains)): ?>
    <tfoot>
        <tr class="fw-bold">
            <td colspan="2">Total general</td>
            <td><?= number_format($totalGeneral, 0, ',', ' ') ?></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>

<?= $this->endSection() ?>
