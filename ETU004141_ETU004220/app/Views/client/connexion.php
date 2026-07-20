<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<div class="page-connexion">
    <div class="page-connexion__carte">
        <div class="page-connexion__logo">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1A1A1A" stroke-width="2"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><circle cx="16" cy="14" r="1.2" fill="#1A1A1A"/></svg>
        </div>
        <div class="page-connexion__titre">Mobile Money</div>
        <div class="page-connexion__soustitre">Entrez votre numero pour continuer</div>

        <?php if (session()->getFlashdata('erreur')): ?>
            <div class="message message--erreur"><?= esc(session()->getFlashdata('erreur')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('client/connexion') ?>">
            <div class="champ">
                <input type="text" name="numero_telephone" class="champ__input" placeholder="033 12 345 67" required>
            </div>
            <button type="submit" class="bouton-principal">Continuer</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>