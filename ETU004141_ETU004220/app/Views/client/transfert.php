<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<h1 class="bloc-titre"> Transfert</h1>

<?php if (session()->getFlashdata('erreur')): ?>
    <div class="message message--erreur">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><circle cx="12" cy="16.5" r=".5" fill="currentColor"/></svg>
        <?= esc(session()->getFlashdata('erreur')) ?>
    </div>
<?php endif; ?>

<div class="carte">
    <form method="post" action="<?= site_url('client/transfert') ?>">
        <div class="champ">
            <label class="champ__label">Numéro du destinataire</label>
            <input type="text" name="numero_destinataire" class="champ__input"
                   placeholder="033 98 765 43" autocomplete="off" required>
        </div>
        <div class="champ">
            <label class="champ__label">Montant (Ar)</label>
            <input type="number" step="0.01" min="1" name="montant"
                   class="champ__input" placeholder="0" required>
        </div>
        <div class="champ">
            <label class="champ__checkbox">
                <input type="checkbox" name="frais_retrait_inclus" value="1">
                Inclure les frais de retrait pour le destinataire
            </label>
        </div>
        <button type="submit" class="bouton-principal">Transférer →</button>
    </form>
</div>

<div style="margin-top: 20px; text-align: center;">
    <a href="<?= site_url('client/envoi-multiple') ?>" class="lien-secondaire">
         Envoyer à plusieurs destinataires
    </a>
</div>

<a href="<?= site_url('client/solde') ?>" class="lien-retour">
    ← Retour
</a>

<?= $this->endSection() ?>