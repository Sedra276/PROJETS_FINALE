<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h3 class="mb-4">Connexion</h3>

            <?php if (session()->getFlashdata('erreur')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('client/connexion') ?>">
                <div class="mb-3">
                    <label class="form-label">Numero de telephone</label>
                    <input type="text" name="numero_telephone" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>