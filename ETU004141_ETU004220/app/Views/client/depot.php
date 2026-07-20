<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h3 class="mb-4">Depot</h3>

            <?php if (session()->getFlashdata('erreur')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('client/depot') ?>">
                <div class="mb-3">
                    <label class="form-label">Montant</label>
                    <input type="number" step="0.01" min="1" name="montant" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Deposer</button>
            </form>

            <a href="<?= site_url('client/solde') ?>" class="btn btn-link mt-3">Retour</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>