<?= $this->extend('layout/base') ?>

<?= $this->section('contenu') ?>

<h1 class="bloc-titre">Historique</h1>

<form method="get" action="<?= site_url('client/historique') ?>" class="filtre-historique">
    <select name="type">
        <option value="">Tous les types</option>
        <option value="DEPOT">Depot</option>
        <option value="RETRAIT">Retrait</option>
        <option value="TRANSFERT">Transfert</option>
    </select>
    <button type="submit">Filtrer</button>
</form>

<div class="liste-operations">
    <?php foreach ($operations as $item): ?>
        <?php if ($item['type'] === 'individuelle'): ?>
            <?php $ligne = $item['operation']; ?>
            <div class="ligne-operation">
                <div>
                    <div class="ligne-operation__type"><?= esc($ligne['type_libelle']) ?></div>
                    <div class="ligne-operation__date"><?= esc($ligne['date_operation']) ?></div>
                    <?php if ($ligne['type_libelle'] === 'Transfert'): ?>
                        <?php if ($ligne['id_client_source'] == session()->get('client_id')): ?>
                            <div class="ligne-operation__destinataire">Vers: <?= esc($ligne['numero_destination']) ?></div>
                        <?php else: ?>
                            <div class="ligne-operation__destinataire">De: <?= esc($ligne['numero_source']) ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="ligne-operation__montant"><?= number_format($ligne['montant'], 0, ',', ' ') ?> Ar</div>
                    <div class="ligne-operation__frais">frais <?= number_format($ligne['frais_appliques'], 0, ',', ' ') ?> Ar</div>
                </div>
            </div>
        <?php elseif ($item['type'] === 'lot'): ?>
            <div class="ligne-operation lot-envoi">
                <div class="lot-header">
                    <div class="ligne-operation__type">Envoi Multiple (Lot #<?= esc($item['id_lot']) ?>)</div>
                    <div class="ligne-operation__date"><?= esc($item['date_operation']) ?></div>
                </div>
                <div class="lot-details">
                    <?php foreach ($item['operations'] as $ligne): ?>
                        <div class="lot-item">
                            <div>
                                <?php if ($ligne['id_client_source'] == session()->get('client_id')): ?>
                                    <div class="ligne-operation__destinataire">Vers: <?= esc($ligne['numero_destination']) ?></div>
                                <?php else: ?>
                                    <div class="ligne-operation__destinataire">De: <?= esc($ligne['numero_source']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="ligne-operation__montant"><?= number_format($ligne['montant'], 0, ',', ' ') ?> Ar</div>
                                <div class="ligne-operation__frais">frais <?= number_format($ligne['frais_appliques'], 0, ',', ' ') ?> Ar</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?= $this->endSection() ?>