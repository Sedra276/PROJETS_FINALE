<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Prefixes</h3>
    <a href="/admin/prefixes/nouveau" class="btn btn-primary">+ Nouveau prefixe</a>
</div>

<table class="table table-bordered align-middle">
    <thead>
        <tr><th>Prefixe</th><th>Libelle</th><th>Actif</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($prefixes as $prefixe): ?>
        <tr>
            <td><?= esc($prefixe['prefixe']) ?></td>
            <td><?= esc($prefixe['libelle']) ?></td>
            <td>
                <?php if ($prefixe['actif']): ?>
                    <span class="badge bg-success">Actif</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Inactif</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="/admin/prefixes/<?= $prefixe['id'] ?>/modifier" class="btn btn-sm btn-secondary">Modifier</a>
                <form method="post" action="/admin/prefixes/<?= $prefixe['id'] ?>/activer-desactiver" class="d-inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-warning">
                        <?= $prefixe['actif'] ? 'Desactiver' : 'Activer' ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($prefixes)): ?>
        <tr><td colspan="4" class="text-center text-muted">Aucun prefixe enregistre.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
