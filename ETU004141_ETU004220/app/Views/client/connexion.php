<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<div class="page-connexion">
    <div class="page-connexion__carte">

        <div class="page-connexion__logo">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFC72C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="6" width="18" height="13" rx="2"/>
                <path d="M3 10h18"/>
                <circle cx="16" cy="14" r="1.3" fill="#FFC72C" stroke="none"/>
            </svg>
        </div>

        <div class="page-connexion__titre">Mobile Money</div>
        <div class="page-connexion__soustitre">Entrez votre numéro pour continuer</div>

        <?php if (session()->getFlashdata('erreur')): ?>
            <div class="message message--erreur">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/></svg>
                <?= esc(session()->getFlashdata('erreur')) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('client/connexion') ?>" style="text-align:left;">
            <div class="champ">
                <label class="champ__label">Numéro de téléphone</label>
                <input type="text" name="numero_telephone" class="champ__input"
                       placeholder="033 12 345 67" autocomplete="tel" required>
            </div>
            <button type="submit" class="bouton-principal" style="margin-top:4px;">Continuer</button>
        </form>

        <p style="margin-top:18px;font-size:11px;color:var(--mm-texte-pale);"> Connexion sécurisée</p>
    </div>
</div>

<?= $this->endSection() ?>