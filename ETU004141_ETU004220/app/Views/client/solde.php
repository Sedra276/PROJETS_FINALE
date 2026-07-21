<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<?php if (session()->getFlashdata('succes')): ?>
    <div class="message message--succes">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
        <?= esc(session()->getFlashdata('succes')) ?>
    </div>
<?php endif; ?>

<!-- Carte Solde -->
<div class="carte-solde">
    <div class="carte-solde__bande"></div>
    <div class="carte-solde__libelle">
        Solde disponible
        <button type="button" class="carte-solde__bouton-oeil" id="bouton-oeil-solde" title="Afficher / Masquer">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </button>
    </div>
    <div class="carte-solde__montant" id="montant-solde" data-montant="<?= esc($client['solde']) ?>">
        <?= number_format($client['solde'], 0, ',', ' ') ?> Ar
    </div>
</div>

<!-- Actions rapides -->
<div class="actions-rapides">
    <a href="<?= site_url('client/depot') ?>" class="action-rapide">
        <span class="action-rapide__icone">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 4v13m0 0-5-5m5 5 5-5"/><path d="M4 20h16"/>
            </svg>
        </span>
        <span class="action-rapide__label">Dépôt</span>
    </a>
    <a href="<?= site_url('client/retrait') ?>" class="action-rapide">
        <span class="action-rapide__icone">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20V7m0 0 5 5m-5-5-5 5"/><path d="M4 4h16"/>
            </svg>
        </span>
        <span class="action-rapide__label">Retrait</span>
    </a>
    <a href="<?= site_url('client/transfert') ?>" class="action-rapide">
        <span class="action-rapide__icone">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 8h14m0 0-4-4m4 4-4 4"/><path d="M20 16H6m0 0 4-4m-4 4 4 4"/>
            </svg>
        </span>
        <span class="action-rapide__label">Transfert</span>
    </a>
</div>

<div style="text-align:center; margin-top:-8px; margin-bottom:20px;">
    <a href="<?= site_url('client/envoi-multiple') ?>" class="lien-secondaire">
        Envoi multiple →
    </a>
</div>

<script>
const boutonOeil = document.getElementById('bouton-oeil-solde');
const montantSolde = document.getElementById('montant-solde');
let soldeVisible = true;

const iconOeil    = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>`;
const iconOeilOff = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M1 1l22 22"/></svg>`;

boutonOeil.addEventListener('click', function () {
    if (soldeVisible) {
        montantSolde.textContent = '•••• Ar';
        boutonOeil.innerHTML = iconOeilOff;
    } else {
        const montant = parseFloat(montantSolde.dataset.montant);
        montantSolde.textContent = montant.toLocaleString('fr-FR') + ' Ar';
        boutonOeil.innerHTML = iconOeil;
    }
    soldeVisible = !soldeVisible;
});
</script>

<?= $this->endSection() ?>