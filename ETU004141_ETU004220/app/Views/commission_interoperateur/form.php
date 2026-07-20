<?php // Vue: formulaire creation/modification commission interoperateur - sans design (V2 - tache 2 ETU004141) ?>

<h1><?= $commission ? 'Modifier' : 'Ajouter' ?> une commission interoperateur</h1>

<?php if (! empty($erreurs)): ?>
    <ul>
        <?php foreach ($erreurs as $erreur): ?>
            <li><?= esc($erreur) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="<?= $commission
    ? site_url('operateur/commissions-interoperateur/modifier/' . $commission['id'])
    : site_url('operateur/commissions-interoperateur') ?>" method="post">
    <?= csrf_field() ?>

    <p>
        <label for="id_operateur_config">Operateur externe</label><br>
        <select name="id_operateur_config" id="id_operateur_config" required>
            <option value="">-- Choisir un operateur --</option>
            <?php foreach ($operateursExternes as $operateur): ?>
                <option value="<?= esc($operateur['id']) ?>"
                    <?= ($commission && (int) $commission['id_operateur_config'] === (int) $operateur['id']) ? 'selected' : '' ?>>
                    <?= esc($operateur['libelle']) ?> (<?= esc($operateur['prefixe']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="pourcentage">Pourcentage (0 a 100)</label><br>
        <input type="number" step="0.01" min="0" max="100" name="pourcentage" id="pourcentage"
               value="<?= esc($commission['pourcentage'] ?? old('pourcentage', '')) ?>" required>
    </p>

    <p>
        <label for="actif">
            <input type="checkbox" name="actif" id="actif" value="1"
                <?= (! $commission || $commission['actif']) ? 'checked' : '' ?>>
            Active
        </label>
    </p>

    <p>
        <button type="submit"><?= $commission ? 'Enregistrer' : 'Creer' ?></button>
        <a href="<?= site_url('operateur/commissions-interoperateur') ?>">Annuler</a>
    </p>
</form>
