<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Espace Operateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width:400px;margin-top:80px;">
    <h3 class="mb-4 text-center">Connexion Back-office</h3>
    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('erreur')) ?></div>
    <?php endif; ?>
    <form method="post" action="/admin/login">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Login</label>
            <input type="text" name="login" class="form-control" value="<?= esc(old('login')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>
</div>
</body>
</html>
