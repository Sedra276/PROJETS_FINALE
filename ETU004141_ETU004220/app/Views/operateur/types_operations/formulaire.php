<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<h3>Modifier le libelle : <?= esc($type['code']) ?></h3>

<form method="post" action="/admin/types-operations/<?= $type['id'] ?>">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Libelle</label>
        <input type="text" name="libelle" class="form-control" value="<?= esc(old('libelle', $type['libelle'])) ?>" required>
    </div>
    <button class="btn btn-primary">Enregistrer</button>
    <a href="/admin/types-operations" class="btn btn-link">Annuler</a>
</form>

<?= $this->endSection() ?>
