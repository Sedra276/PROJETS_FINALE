<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<h1 class="bloc-titre">Depot</h1>

<?php if (session()->getFlashdata('erreur')): ?>
    <div class="message message--erreur"><?= esc(session()->getFlashdata('erreur')) ?></div>
<?php endif; ?>

<div class="carte">
    <form method="post" action="<?= site_url('client/depot') ?>">
        <div class="champ">
            <label class="champ__label">Montant</label>
            <input type="number" step="0.01" min="1" name="montant" class="champ__input" placeholder="0" required>
        </div>
        <button type="submit" class="bouton-principal">Deposer</button>
    </form>
</div>

<a href="<?= site_url('client/solde') ?>" class="lien-retour">Retour</a>

<?= $this->endSection() ?>