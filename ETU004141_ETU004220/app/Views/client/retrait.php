<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<h1 class="bloc-titre"> Retrait</h1>

<?php if (session()->getFlashdata('erreur')): ?>
    <div class="message message--erreur">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><circle cx="12" cy="16.5" r=".5" fill="currentColor"/></svg>
        <?= esc(session()->getFlashdata('erreur')) ?>
    </div>
<?php endif; ?>

<div class="carte">
    <form method="post" action="<?= site_url('client/retrait') ?>">
        <div class="champ">
            <label class="champ__label">Montant à retirer (Ar)</label>
            <input type="number" step="0.01" min="1" name="montant"
                   class="champ__input" placeholder="0" required>
        </div>
        <button type="submit" class="bouton-principal">Retirer →</button>
    </form>
</div>

<a href="<?= site_url('client/solde') ?>" class="lien-retour">← Retour</a>

<?= $this->endSection() ?>