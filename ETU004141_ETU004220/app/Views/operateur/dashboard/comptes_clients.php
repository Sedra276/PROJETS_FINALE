<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<h3 class="mb-3">Situation des comptes clients</h3>

<table class="table table-bordered align-middle">
    <thead>
        <tr><th>Numero</th><th>Nom</th><th>Prenom</th><th>Solde (Ar)</th><th>Statut</th><th>Date creation</th></tr>
    </thead>
    <tbody>
        <?php foreach ($comptes as $compte): ?>
        <tr>
            <td><?= esc($compte['numero_telephone']) ?></td>
            <td><?= esc($compte['nom']) ?></td>
            <td><?= esc($compte['prenom']) ?></td>
            <td><?= number_format((float) $compte['solde'], 0, ',', ' ') ?></td>
            <td>
                <?php if ($compte['statut'] === 'ACTIF'): ?>
                    <span class="badge bg-success">Actif</span>
                <?php else: ?>
                    <span class="badge bg-danger">Bloque</span>
                <?php endif; ?>
            </td>
            <td><?= esc($compte['date_creation']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($comptes)): ?>
        <tr><td colspan="6" class="text-center text-muted">Aucun client enregistre.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
