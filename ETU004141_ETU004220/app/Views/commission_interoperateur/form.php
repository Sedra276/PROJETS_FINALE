<?= $this->extend('layouts/operateur') ?>

<?= $this->section('contenu') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h2 class="h5 mb-0"><?= $commission ? 'Modifier' : 'Ajouter' ?> une commission interoperateur</h2>
            </div>
            <div class="card-body">
                <form action="<?= $commission
                    ? site_url('commissions-interoperateur/modifier/' . $commission['id'])
                    : site_url('commissions-interoperateur') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="id_operateur_config" class="form-label">Operateur externe</label>
                        <select name="id_operateur_config" id="id_operateur_config" class="form-select" required>
                            <option value="">-- Choisir un operateur --</option>
                            <?php foreach ($operateursExternes as $operateur): ?>
                                <option value="<?= esc($operateur['id']) ?>"
                                    <?= ($commission && (int) $commission['id_operateur_config'] === (int) $operateur['id']) ? 'selected' : '' ?>>
                                    <?= esc($operateur['libelle']) ?> (<?= esc($operateur['prefixe']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="pourcentage" class="form-label">Pourcentage (0 a 100)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" name="pourcentage" id="pourcentage"
                                   class="form-control" value="<?= esc($commission['pourcentage'] ?? old('pourcentage', '')) ?>" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" name="actif" id="actif" value="1" class="form-check-input"
                            <?= (! $commission || $commission['actif']) ? 'checked' : '' ?>>
                        <label for="actif" class="form-check-label">Active</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('commissions-interoperateur') ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary"><?= $commission ? 'Enregistrer' : 'Creer' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
