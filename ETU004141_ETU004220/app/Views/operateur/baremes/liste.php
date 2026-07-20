<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Baremes de frais</h3>
    <a href="/admin/baremes/nouveau" class="btn btn-primary">+ Nouvelle tranche</a>
</div>

<form method="get" action="/admin/baremes" class="row g-2 mb-3">
    <div class="col-auto">
        <select name="id_type_operation" class="form-select">
            <option value="">Tous les types</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type['id'] ?>" <?= (string) $idTypeOperationSelectionne === (string) $type['id'] ? 'selected' : '' ?>>
                    <?= esc($type['libelle']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <select name="id_operateur_config" class="form-select">
            <option value="">Tous les operateurs</option>
            <?php foreach ($operateurs as $operateur): ?>
                <option value="<?= $operateur['id'] ?>" <?= (string) $idOperateurConfigSelectionne === (string) $operateur['id'] ? 'selected' : '' ?>>
                    <?= esc($operateur['libelle']) ?> (<?= esc($operateur['prefixe']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filtrer</button></div>
</form>

<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Type</th><th>Operateur</th><th>Montant min</th><th>Montant max</th><th>Type de calcul</th>
            <th>Valeur</th><th>Debut validite</th><th>Fin validite</th><th>Statut</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tranches as $tranche): ?>
        <?php $active = empty($tranche['date_fin_validite']); ?>
        <tr class="<?= $active ? '' : 'table-secondary' ?>">
            <td><?= esc($tranche['id_type_operation']) ?></td>
            <td><?= esc($tranche['id_operateur_config']) ?></td>
            <td><?= esc($tranche['montant_min']) ?></td>
            <td><?= esc($tranche['montant_max']) ?></td>
            <td><?= esc($tranche['type_calcul']) ?></td>
            <td><?= esc($tranche['valeur']) ?><?= $tranche['type_calcul'] === 'POURCENTAGE' ? ' %' : ' Ar' ?></td>
            <td><?= esc($tranche['date_debut_validite']) ?></td>
            <td><?= esc($tranche['date_fin_validite'] ?? '-') ?></td>
            <td>
                <?php if ($active): ?>
                    <span class="badge bg-success">Active</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Historisee</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($active): ?>
                <form method="post" action="/admin/baremes/<?= $tranche['id'] ?>/desactiver">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-warning" onclick="return confirm('Desactiver cette tranche ?')">Desactiver</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($tranches)): ?>
        <tr><td colspan="10" class="text-center text-muted">Aucune tranche enregistree.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
