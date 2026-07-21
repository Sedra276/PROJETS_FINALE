<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titre ?? 'Espace Opérateur') ?> — Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Roboto+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        /* ===================================================
           ADMIN — Cohérence visuelle avec la palette Noir+Or
           =================================================== */
        body {
            background-color: #F7F6F1 !important;
        }

        /* Navbar — même style que .app-entete */
        .navbar {
            background: #1A1A1A !important;
            border-bottom: 2px solid #FFC72C !important;
            padding: 12px 28px !important;
        }

        .navbar-brand {
            color: #FFC72C !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 700 !important;
            font-size: 17px !important;
            letter-spacing: 0.3px;
        }

        .nav-link {
            color: rgba(255,255,255,0.6) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
            transition: color 0.2s, background 0.2s !important;
        }
        .nav-link:hover {
            color: #FFC72C !important;
            background: rgba(255,199,44,0.1) !important;
        }
        .nav-link.active { color: #FFC72C !important; }

        /* Lien déconnexion — identique au bouton client */
        .nav-link.deconnexion {
            color: #FFC72C !important;
            border: 1.5px solid rgba(255,199,44,0.5) !important;
            margin-left: 6px !important;
            font-weight: 600 !important;
        }
        .nav-link.deconnexion:hover {
            background: #FFC72C !important;
            color: #1A1A1A !important;
            border-color: #FFC72C !important;
        }

        /* Contenu */
        .container, .container-fluid {
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 700 !important;
            color: #1A1A1A !important;
        }

        /* Cards */
        .card {
            border: 1px solid #E7E5DC !important;
            border-radius: 16px !important;
            box-shadow: 0 2px 10px rgba(26,26,26,0.06) !important;
        }
        .card-header {
            background: #1A1A1A !important;
            color: #FFC72C !important;
            border-radius: 14px 14px 0 0 !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600 !important;
            border: none !important;
        }

        /* Tables */
        .table {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(26,26,26,0.06);
        }
        .table thead th {
            background: #1A1A1A !important;
            color: #FFC72C !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border: none !important;
            padding: 14px 16px !important;
        }
        .table td, .table th {
            border-color: #E7E5DC !important;
            padding: 12px 16px !important;
            vertical-align: middle !important;
            font-size: 14px;
        }
        .table tbody tr:hover { background-color: #FFFBEE !important; }
        .table tfoot tr {
            background: #F7F6F1 !important;
            font-weight: 700 !important;
        }

        /* Boutons Bootstrap — cohérents avec .bouton-principal */
        .btn-primary {
            background: #1A1A1A !important;
            border-color: #1A1A1A !important;
            color: #FFC72C !important;
            border-radius: 10px !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600 !important;
            padding: 8px 18px !important;
            transition: background 0.2s, transform 0.15s !important;
        }
        .btn-primary:hover {
            background: #2B2B28 !important;
            border-color: #2B2B28 !important;
            color: #FFC72C !important;
            transform: translateY(-1px) !important;
        }

        .btn-secondary {
            background: transparent !important;
            border-color: #1A1A1A !important;
            color: #1A1A1A !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
        }
        .btn-secondary:hover {
            background: #1A1A1A !important;
            color: #FFC72C !important;
        }

        .btn-outline-secondary {
            border-color: #1A1A1A !important;
            color: #1A1A1A !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
        }
        .btn-outline-secondary:hover {
            background: #1A1A1A !important;
            color: #FFC72C !important;
        }

        .btn-danger {
            border-radius: 10px !important;
            font-weight: 600 !important;
        }
        .btn-warning {
            border-radius: 10px !important;
            font-weight: 600 !important;
            color: #1A1A1A !important;
        }

        /* Formulaires */
        .form-control, .form-select {
            border-radius: 10px !important;
            border: 1.5px solid #E7E5DC !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
            background: #FAFAF7 !important;
            color: #1A1A1A !important;
            transition: border-color 0.2s, box-shadow 0.2s !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #E6A812 !important;
            box-shadow: 0 0 0 3px rgba(255,199,44,0.18) !important;
            background: #fff !important;
        }
        .form-label {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #5A5A52 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        /* Alertes */
        .alert-success {
            background: #E6F5ED !important;
            color: #1A8A48 !important;
            border: none !important;
            border-left: 3px solid #1A8A48 !important;
            border-radius: 10px !important;
            font-weight: 500 !important;
        }
        .alert-danger {
            background: #FCEAE8 !important;
            color: #C0392B !important;
            border: none !important;
            border-left: 3px solid #C0392B !important;
            border-radius: 10px !important;
            font-weight: 500 !important;
        }

        /* Badge */
        .badge.bg-warning { color: #1A1A1A !important; }
    </style>
</head>
<body>

<!-- Navbar Admin — même identité visuelle que le header client -->
<nav class="navbar navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand" href="/admin/gains">Mobile Money — Admin</a>
        <div class="navbar-nav ms-auto d-flex flex-row flex-wrap gap-1 align-items-center">
            <a class="nav-link" href="/admin/prefixes">Préfixes</a>
            <a class="nav-link" href="/admin/types-operations">Types d'op.</a>
            <a class="nav-link" href="/admin/baremes">Barèmes</a>
            <a class="nav-link" href="/admin/commissions-interoperateur">Commissions</a>
            <a class="nav-link" href="/admin/gains">Gains</a>
            <a class="nav-link" href="/admin/montants-operateurs">Montants</a>
            <a class="nav-link" href="/admin/comptes-clients">Comptes</a>
            <a class="nav-link deconnexion" href="/admin/deconnexion">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container pb-5">

    <?php if (session()->getFlashdata('succes')): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
            <?= esc(session()->getFlashdata('succes')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/></svg>
            <?= esc(session()->getFlashdata('erreur')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erreurs')): ?>
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('erreurs') as $erreur): ?>
                    <li><?= esc($erreur) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('contenu') ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
