<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">Mobile Money</a>
        <?php if (session()->get('client_id')): ?>
            <div class="d-flex">
                <span class="navbar-text text-white me-3"><?= esc(session()->get('client_numero')) ?></span>
                <a href="<?= site_url('client/deconnexion') ?>" class="btn btn-outline-light btn-sm">Deconnexion</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<?= $this->renderSection('contenu') ?>

<footer class="text-center text-muted py-4 mt-5">
    Mobile Money - Version 1
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>