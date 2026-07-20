<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<h3 class="mb-3">Types d'operation</h3>

<table class="table table-bordered align-middle">
    <thead>
        <tr><th>Code</th><th>Libelle</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($types as $type): ?>
        <tr>
            <td><span class="badge bg-info text-dark"><?= esc($type['code']) ?></span></td>
            <td><?= esc($type['libelle']) ?></td>
            <td><a href="/admin/types-operations/<?= $type['id'] ?>/modifier" class="btn btn-sm btn-secondary">Modifier le libelle</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p class="text-muted">Le code (DEPOT / RETRAIT / TRANSFERT) est un referentiel fixe et n'est jamais modifiable.</p>

<?= $this->endSection() ?>
