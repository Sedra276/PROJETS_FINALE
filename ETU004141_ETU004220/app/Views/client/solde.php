<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <h3 class="mb-4">Mon solde</h3>

            <?php if (session()->getFlashdata('succes')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('succes')) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body text-center">
                    <p class="mb-1">Numero : <?= esc($client['numero_telephone']) ?></p>
                    <h2><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</h2>
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <a href="<?= site_url('client/depot') ?>" class="btn btn-outline-primary">Depot</a>
                <a href="<?= site_url('client/retrait') ?>" class="btn btn-outline-primary">Retrait</a>
                <a href="<?= site_url('client/transfert') ?>" class="btn btn-outline-primary">Transfert</a>
                <a href="<?= site_url('client/historique') ?>" class="btn btn-outline-secondary">Historique</a>
                <a href="<?= site_url('client/deconnexion') ?>" class="btn btn-link">Deconnexion</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>