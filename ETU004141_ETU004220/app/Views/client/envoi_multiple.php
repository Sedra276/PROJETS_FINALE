<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<h1 class="bloc-titre">Envoi Multiple</h1>

<?php if (session()->getFlashdata('erreur')): ?>
    <div class="message message--erreur"><?= esc(session()->getFlashdata('erreur')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('succes')): ?>
    <div class="message message--succes"><?= esc(session()->getFlashdata('succes')) ?></div>
<?php endif; ?>

<div class="carte">
    <form method="post" action="<?= site_url('client/envoi-multiple') ?>" id="envoiMultipleForm">
        <div id="destinatairesContainer">
            <div class="destinataire-row">
                <div class="champ">
                    <label class="champ__label">Numero du destinataire 1</label>
                    <input type="text" name="destinataires[0][numero]" class="champ__input" placeholder="033 98 765 43" required>
                </div>
                <div class="champ">
                    <label class="champ__label">Montant</label>
                    <input type="number" step="0.01" min="1" name="destinataires[0][montant]" class="champ__input" placeholder="0" required>
                </div>
            </div>
        </div>
        
        <button type="button" class="bouton-secondaire" onclick="ajouterDestinataire()">+ Ajouter un destinataire</button>
        <div style="margin-top: 20px;">
            <div class="champ">
                <label class="champ__label">Total estimé: <span id="totalEstime">0</span> Ar</label>
            </div>
        </div>
        <button type="submit" class="bouton-principal">Envoyer à tous</button>
    </form>
</div>

<a href="<?= site_url('client/solde') ?>" class="lien-retour">Retour</a>

<script>
let destinataireCount = 1;

function ajouterDestinataire() {
    destinataireCount++;
    const container = document.getElementById('destinatairesContainer');
    const newRow = document.createElement('div');
    newRow.className = 'destinataire-row';
    newRow.innerHTML = `
        <div class="champ">
            <label class="champ__label">Numero du destinataire ${destinataireCount}</label>
            <input type="text" name="destinataires[${destinataireCount - 1}][numero]" class="champ__input" placeholder="033 98 765 43" required>
        </div>
        <div class="champ">
            <label class="champ__label">Montant</label>
            <input type="number" step="0.01" min="1" name="destinataires[${destinataireCount - 1}][montant]" class="champ__input" placeholder="0" required>
        </div>
        <button type="button" class="bouton-supprimer" onclick="supprimerDestinataire(this)">Supprimer</button>
    `;
    container.appendChild(newRow);
}

function supprimerDestinataire(button) {
    const row = button.parentElement;
    row.remove();
    calculerTotal();
}

function calculerTotal() {
    const montantInputs = document.querySelectorAll('input[name^="destinataires"][name$="[montant]"]');
    let total = 0;
    montantInputs.forEach(input => {
        const valeur = parseFloat(input.value) || 0;
        total += valeur;
    });
    document.getElementById('totalEstime').textContent = total.toLocaleString('fr-FR');
}

document.addEventListener('DOMContentLoaded', function() {
    const montantInputs = document.querySelectorAll('input[name^="destinataires"][name$="[montant]"]');
    montantInputs.forEach(input => {
        input.addEventListener('input', calculerTotal);
    });
});
</script>

<?= $this->endSection() ?>
