<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= esc($titre ?? 'Espace Operateur') ?> - Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        /* Adaptation Bootstrap vers le thème Client */
        body {
            background-color: var(--mm-fond) !important;
        }
        .navbar {
            background-color: var(--mm-noir) !important;
        }
        .navbar-brand {
            color: var(--mm-or) !important;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }
        .nav-link {
            color: var(--mm-blanc) !important;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }
        .nav-link:hover {
            color: var(--mm-or) !important;
        }
        .container {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--mm-texte);
            margin-bottom: 20px;
        }
        .table {
            background-color: var(--mm-blanc);
            border-radius: var(--mm-rayon);
            overflow: hidden;
            box-shadow: 0 0 15px rgba(0,0,0,0.02);
        }
        .table thead th {
            background-color: var(--mm-noir-doux) !important;
            color: var(--mm-blanc) !important;
            font-weight: 500;
            border: none;
        }
        .table td, .table th {
            border-color: var(--mm-bordure);
            padding: 12px 16px;
        }
        .btn-primary {
            background-color: var(--mm-noir) !important;
            border-color: var(--mm-noir) !important;
            color: var(--mm-or) !important;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: var(--mm-noir-doux) !important;
            border-color: var(--mm-noir-doux) !important;
        }
        .alert-success {
            background-color: var(--mm-vert-fond) !important;
            color: var(--mm-vert) !important;
            border: none;
            border-radius: 10px;
        }
        .alert-danger {
            background-color: var(--mm-rouge-fond) !important;
            color: var(--mm-rouge) !important;
            border: none;
            border-radius: 10px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid var(--mm-bordure);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--mm-or-fonce);
            box-shadow: none;
        }
        .card {
            border-radius: var(--mm-rayon);
            border: 0.5px solid var(--mm-bordure);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand" href="/admin/gains">Mobile Money - Admin</a>
        <div class="navbar-nav">
            <a class="nav-link" href="/admin/prefixes">Prefixes</a>
            <a class="nav-link" href="/admin/types-operations">Types d'operation</a>
            <a class="nav-link" href="/admin/baremes">Baremes</a>
            <a class="nav-link" href="/admin/commissions-interoperateur">Commissions</a>
            <a class="nav-link" href="/admin/gains">Gains</a>
            <a class="nav-link" href="/admin/montants-operateurs">Montants à envoyer</a>
            <a class="nav-link" href="/admin/comptes-clients">Comptes clients</a>
            <a class="nav-link" style="color: var(--mm-or) !important; font-weight: 600;" href="/admin/deconnexion">Deconnexion</a>
        </div>
    </div>
</nav>
<div class="container pb-5">
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
