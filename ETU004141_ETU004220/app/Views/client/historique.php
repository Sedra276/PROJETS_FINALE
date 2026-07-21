<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<h1 class="bloc-titre"> Historique</h1>

<form method="get" action="<?= site_url('client/historique') ?>" class="filtre-historique">
    <select name="type">
        <option value="">Tous les types</option>
        <option value="DEPOT"    <?= (($type ?? '') === 'DEPOT'    ? 'selected' : '') ?>>Dépôt</option>
        <option value="RETRAIT"  <?= (($type ?? '') === 'RETRAIT'  ? 'selected' : '') ?>>Retrait</option>
        <option value="TRANSFERT"<?= (($type ?? '') === 'TRANSFERT'? 'selected' : '') ?>>Transfert</option>
    </select>
    <button type="submit">Filtrer</button>
</form>

<div class="liste-operations">
    <?php foreach ($operations as $item): ?>
        <?php if ($item['type'] === 'individuelle'): ?>
            <?php $ligne = $item['operation']; ?>
            <div class="ligne-operation">
                <div>
                    <div class="ligne-operation__type">
                        <?php
                            $icons = ['Dépôt' => '', 'Depot' => '', 'Retrait' => '', 'Transfert' => ''];
                            echo ($icons[$ligne['type_libelle']] ?? '💸') . ' ' . esc($ligne['type_libelle']);
                        ?>
                    </div>
                    <div class="ligne-operation__date"><?= esc($ligne['date_operation']) ?></div>
                    <?php if ($ligne['type_libelle'] === 'Transfert'): ?>
                        <?php if ($ligne['id_client_source'] == session()->get('client_id')): ?>
                            <div class="ligne-operation__destinataire">→ <?= esc($ligne['numero_destination']) ?></div>
                        <?php else: ?>
                            <div class="ligne-operation__destinataire">← <?= esc($ligne['numero_source']) ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div style="text-align:right;">
                    <div class="ligne-operation__montant"><?= number_format($ligne['montant'], 0, ',', ' ') ?> Ar</div>
                    <div class="ligne-operation__frais">frais <?= number_format($ligne['frais_appliques'], 0, ',', ' ') ?> Ar</div>
                </div>
            </div>

        <?php elseif ($item['type'] === 'lot'): ?>
            <div class="ligne-operation lot-envoi">
                <div class="lot-header">
                    <div>
                        <div class="ligne-operation__type"> Envoi Multiple — Lot #<?= esc($item['id_lot']) ?></div>
                        <div class="ligne-operation__date"><?= esc($item['date_operation']) ?></div>
                    </div>
                    <span style="font-size:11px;background:var(--mm-primary-bg);color:var(--mm-primary);padding:3px 8px;border-radius:6px;font-weight:700;">
                        <?= count($item['operations']) ?> dest.
                    </span>
                </div>
                <div class="lot-details">
                    <?php foreach ($item['operations'] as $ligne): ?>
                        <div class="lot-item">
                            <div>
                                <?php if ($ligne['id_client_source'] == session()->get('client_id')): ?>
                                    <div class="ligne-operation__destinataire">→ <?= esc($ligne['numero_destination']) ?></div>
                                <?php else: ?>
                                    <div class="ligne-operation__destinataire">← <?= esc($ligne['numero_source']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div style="text-align:right;">
                                <div class="ligne-operation__montant"><?= number_format($ligne['montant'], 0, ',', ' ') ?> Ar</div>
                                <div class="ligne-operation__frais">frais <?= number_format($ligne['frais_appliques'], 0, ',', ' ') ?> Ar</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (empty($operations)): ?>
        <div style="text-align:center;padding:40px 20px;color:var(--mm-texte-pale);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.4;margin-bottom:12px;"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            <p style="font-size:14px;margin:0;">Aucune opération trouvée</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>