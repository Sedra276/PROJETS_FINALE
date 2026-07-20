<?= $this->extend('layouts/operateur') ?>

<?= $this->section('contenu') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Commissions interoperateur</h1>
    <a href="<?= site_url('admin/commissions-interoperateur/creer') ?>" class="btn btn-primary">Ajouter une commission</a>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Operateur</th>
                    <th>Prefixe</th>
                    <th>Pourcentage</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($commissions)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Aucune commission configuree.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($commissions as $commission): ?>
                        <tr>
                            <td class="align-middle"><?= esc($commission['libelle']) ?></td>
                            <td class="align-middle"><?= esc($commission['prefixe']) ?></td>
                            <td class="align-middle"><?= esc($commission['pourcentage']) ?> %</td>
                            <td class="align-middle">
                                <?php if ($commission['actif']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end align-middle">
                                <a href="<?= site_url('admin/commissions-interoperateur/modifier/' . $commission['id']) ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
                                <form action="<?= site_url('admin/commissions-interoperateur/basculer/' . $commission['id']) ?>" method="post" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <?php if ($commission['actif']): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Desactiver</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success">Activer</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
