<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <a href="<?= site_url('client/deconnexion') ?>" class="app-entete__deconnexion">Déconnexion</a>
        </header>
    <?php endif; ?>

    <main class="app-contenu">
        <?= $this->renderSection('contenu') ?>
    </main>

    <?php if (session()->get('client_id')): ?>
        <nav class="nav-bas">
            <a href="<?= site_url('client/solde') ?>"
               class="nav-bas__lien <?= (str_contains(uri_string(), 'solde') ? 'nav-bas__lien--actif' : '') ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9.5 12 3l9 6.5V21H3z"/>
                </svg>
                Accueil
            </a>
            <a href="<?= site_url('client/historique') ?>"
               class="nav-bas__lien <?= (str_contains(uri_string(), 'historique') ? 'nav-bas__lien--actif' : '') ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>
                </svg>
                Historique
            </a>
        </nav>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>