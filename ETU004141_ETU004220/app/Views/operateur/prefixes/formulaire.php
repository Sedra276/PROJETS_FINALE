<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<h3><?= $prefixe ? 'Modifier' : 'Nouveau' ?> prefixe</h3>

<form method="post" action="<?= $prefixe ? '/admin/prefixes/' . $prefixe['id'] : '/admin/prefixes' ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Prefixe (2 ou 3 chiffres, ex: 033)</label>
        <input type="text" name="prefixe" class="form-control"
               value="<?= esc(old('prefixe', $prefixe['prefixe'] ?? '')) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Libelle</label>
        <input type="text" name="libelle" class="form-control"
               value="<?= esc(old('libelle', $prefixe['libelle'] ?? '')) ?>">
    </div>
    <button class="btn btn-primary">Enregistrer</button>
    <a href="/admin/prefixes" class="btn btn-link">Annuler</a>
</form>

<?= $this->endSection() ?>
