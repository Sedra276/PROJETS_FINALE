<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="app-cadre">

    <?php if (session()->get('client_id')): ?>
        <header class="app-entete">
            <div>
                <div class="app-entete__titre">Mobile Money</div>
                <div class="app-entete__numero"><?= esc(session()->get('client_numero')) ?></div>
            </div>
            <a href="<?= site_url('client/deconnexion') ?>" class="app-entete__deconnexion">Deconnexion</a>
        </header>
    <?php endif; ?>

    <main class="app-contenu">
        <?= $this->renderSection('contenu') ?>
    </main>

    <?php if (session()->get('client_id')): ?>
        <nav class="nav-bas">
            <a href="<?= site_url('client/solde') ?>" class="nav-bas__lien">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V21H3z"/></svg>
                Accueil
            </a>
            <a href="<?= site_url('client/historique') ?>" class="nav-bas__lien">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                Historique
            </a>
        </nav>
    <?php endif; ?>

</div>

</body>
</html>