<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<?php if (session()->getFlashdata('succes')): ?>
    <div class="message message--succes"><?= esc(session()->getFlashdata('succes')) ?></div>
<?php endif; ?>

<div class="carte-solde">
    <div class="carte-solde__libelle">
        Solde disponible
        <button type="button" class="carte-solde__bouton-oeil" id="bouton-oeil-solde">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
    </div>
    <div class="carte-solde__montant" id="montant-solde" data-montant="<?= esc($client['solde']) ?>">
        <?= number_format($client['solde'], 0, ',', ' ') ?> Ar
    </div>
</div>

<div class="actions-rapides">
    <a href="<?= site_url('client/depot') ?>" class="action-rapide">
        <span class="action-rapide__icone">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v13m0 0-5-5m5 5 5-5"/><path d="M4 20h16"/></svg>
        </span>
        <span class="action-rapide__label">Depot</span>
    </a>
    <a href="<?= site_url('client/retrait') ?>" class="action-rapide">
        <span class="action-rapide__icone">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V7m0 0 5 5m-5-5-5 5"/><path d="M4 4h16"/></svg>
        </span>
        <span class="action-rapide__label">Retrait</span>
    </a>
    <a href="<?= site_url('client/transfert') ?>" class="action-rapide">
        <span class="action-rapide__icone">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8h14m0 0-4-4m4 4-4 4"/><path d="M20 16H6m0 0 4-4m-4 4 4 4"/></svg>
        </span>
        <span class="action-rapide__label">Transfert</span>
    </a>
</div>

<script>
const boutonOeil = document.getElementById('bouton-oeil-solde');
const montantSolde = document.getElementById('montant-solde');
let soldeVisible = true;

boutonOeil.addEventListener('click', function () {
    if (soldeVisible) {
        montantSolde.textContent = '•••• Ar';
    } else {
        const montant = parseFloat(montantSolde.dataset.montant);
        montantSolde.textContent = montant.toLocaleString('fr-FR') + ' Ar';
    }
    soldeVisible = !soldeVisible;
});
</script>

<?= $this->endSection() ?>