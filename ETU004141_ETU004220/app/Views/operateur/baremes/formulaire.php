<?= $this->extend('layouts/operateur') ?>
<?= $this->section('contenu') ?>

<h3>Nouvelle tranche de frais</h3>

<form method="post" action="/admin/baremes">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label class="form-label">Type d'operation</label>
        <select name="id_type_operation" class="form-select" required>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type['id'] ?>"><?= esc($type['libelle']) ?> (<?= esc($type['code']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="row">
        <div class="col mb-3">
            <label class="form-label">Montant min</label>
            <input type="number" step="0.01" name="montant_min" class="form-control" required>
        </div>
        <div class="col mb-3">
            <label class="form-label">Montant max</label>
            <input type="number" step="0.01" name="montant_max" class="form-control" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Type de calcul</label>
        <select name="type_calcul" class="form-select" required>
            <option value="MONTANT_FIXE">Montant fixe</option>
            <option value="POURCENTAGE">Pourcentage</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Valeur (montant en Ar, ou % si pourcentage)</label>
        <input type="number" step="0.01" name="valeur" class="form-control" required>
    </div>
    <button class="btn btn-primary">Enregistrer</button>
    <a href="/admin/baremes" class="btn btn-link">Annuler</a>
</form>

<?= $this->endSection() ?>
