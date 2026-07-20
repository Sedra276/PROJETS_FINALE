<?php // Vue: liste des commissions interoperateur - sans design (V2 - tache 2 ETU004141) ?>

<h1>Commissions interoperateur</h1>

<?php if (session()->getFlashdata('message')): ?>
    <p><?= esc(session()->getFlashdata('message')) ?></p>
<?php endif; ?>

<?php if (session()->getFlashdata('erreurs')): ?>
    <ul>
        <?php foreach (session()->getFlashdata('erreurs') as $erreur): ?>
            <li><?= esc($erreur) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="<?= site_url('operateur/commissions-interoperateur/creer') ?>">Ajouter une commission</a></p>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Operateur</th>
            <th>Prefixe</th>
            <th>Pourcentage</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($commissions)): ?>
            <tr>
                <td colspan="5">Aucune commission configuree.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($commissions as $commission): ?>
                <tr>
                    <td><?= esc($commission['libelle']) ?></td>
                    <td><?= esc($commission['prefixe']) ?></td>
                    <td><?= esc($commission['pourcentage']) ?> %</td>
                    <td><?= $commission['actif'] ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <a href="<?= site_url('operateur/commissions-interoperateur/modifier/' . $commission['id']) ?>">Modifier</a>
                        |
                        <form action="<?= site_url('operateur/commissions-interoperateur/basculer/' . $commission['id']) ?>" method="post" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit"><?= $commission['actif'] ? 'Desactiver' : 'Activer' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
