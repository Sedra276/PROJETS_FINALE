<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= esc($titre ?? 'Espace Operateur') ?> - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/admin/gains">Mobile Money - Back-office</a>
        <div class="navbar-nav">
            <a class="nav-link text-white" href="/admin/prefixes">Prefixes</a>
            <a class="nav-link text-white" href="/admin/types-operations">Types d'operation</a>
            <a class="nav-link text-white" href="/admin/baremes">Baremes</a>
            <a class="nav-link text-white" href="/admin/gains">Gains</a>
            <a class="nav-link text-white" href="/admin/comptes-clients">Comptes clients</a>
            <a class="nav-link text-white" href="/admin/deconnexion">Deconnexion</a>
        </div>
    </div>
</nav>
<div class="container">
    <?php if (session()->getFlashdata('succes')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('succes')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('erreurs')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('erreurs') as $erreur): ?>
                    <li><?= esc($erreur) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?= $this->renderSection('contenu') ?>
</div>
</body>
</html>
