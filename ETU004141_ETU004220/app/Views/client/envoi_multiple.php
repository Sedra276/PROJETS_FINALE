<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<h1 class="bloc-titre"> Envoi Multiple</h1>

<?php if (session()->getFlashdata('erreur')): ?>
    <div class="message message--erreur">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><circle cx="12" cy="16.5" r=".5" fill="currentColor"/></svg>
        <?= esc(session()->getFlashdata('erreur')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('succes')): ?>
    <div class="message message--succes">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
        <?= esc(session()->getFlashdata('succes')) ?>
    </div>
<?php endif; ?>

<div class="carte">
    <form method="post" action="<?= site_url('client/envoi-multiple') ?>" id="envoiMultipleForm">

        <div id="destinatairesContainer">
            <div class="destinataire-row">
                <div class="champ">
                    <label class="champ__label">Destinataire 1 — Numéro</label>
                    <input type="text" name="destinataires[0][numero]" class="champ__input"
                           placeholder="033 98 765 43" required>
                </div>
                <div class="champ">
                    <label class="champ__label">Destinataire 1 — Montant (Ar)</label>
                    <input type="number" step="0.01" min="1" name="destinataires[0][montant]"
                           class="champ__input montant-input" placeholder="0" required>
                </div>
            </div>
        </div>

        <button type="button" class="bouton-secondaire" onclick="ajouterDestinataire()" style="margin-bottom:20px;">
            + Ajouter un destinataire
        </button>

        <!-- Total estimé -->
        <div style="background:var(--mm-primary-bg);border-radius:12px;padding:14px 16px;margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;font-weight:700;color:var(--mm-texte-doux);text-transform:uppercase;letter-spacing:.6px;">Total estimé</span>
            <span style="font-family:'Roboto Mono',monospace;font-weight:700;font-size:18px;color:var(--mm-primary);">
                <span id="totalEstime">0</span> Ar
            </span>
        </div>

        <button type="submit" class="bouton-principal">Envoyer à tous →</button>
    </form>
</div>

<a href="<?= site_url('client/solde') ?>" class="lien-retour">← Retour</a>

<script>
let destinataireCount = 1;

function ajouterDestinataire() {
    destinataireCount++;
    const container = document.getElementById('destinatairesContainer');
    const newRow = document.createElement('div');
    newRow.className = 'destinataire-row';
    newRow.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <span style="font-size:13px;font-weight:700;color:var(--mm-primary);">Destinataire ${destinataireCount}</span>
            <button type="button" class="bouton-supprimer" onclick="supprimerDestinataire(this)">✕ Supprimer</button>
        </div>
        <div class="champ">
            <label class="champ__label">Numéro</label>
            <input type="text" name="destinataires[${destinataireCount - 1}][numero]"
                   class="champ__input" placeholder="033 98 765 43" required>
        </div>
        <div class="champ">
            <label class="champ__label">Montant (Ar)</label>
            <input type="number" step="0.01" min="1" name="destinataires[${destinataireCount - 1}][montant]"
                   class="champ__input montant-input" placeholder="0" required>
        </div>
    `;
    container.appendChild(newRow);
    // Écouter les changements sur le nouveau champ
    newRow.querySelector('.montant-input').addEventListener('input', calculerTotal);
}

function supprimerDestinataire(button) {
    button.closest('.destinataire-row').remove();
    calculerTotal();
}

function calculerTotal() {
    const montantInputs = document.querySelectorAll('.montant-input');
    let total = 0;
    montantInputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalEstime').textContent = total.toLocaleString('fr-FR');
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.montant-input').forEach(input => {
        input.addEventListener('input', calculerTotal);
    });
});
</script>

<?= $this->endSection() ?>
