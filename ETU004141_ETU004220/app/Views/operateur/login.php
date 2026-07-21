<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Back-office Mobile Money</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Même palette que la connexion client */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: #1A1A1A;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Halo doré — identique à .page-connexion::before */
        body::before {
            content: '';
            position: absolute;
            top: -100px; left: 50%;
            transform: translateX(-50%);
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,199,44,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-card {
            background: #fff;
            border-radius: 22px;
            padding: 38px 30px 32px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 24px 80px rgba(0,0,0,0.4);
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255,199,44,0.2);
        }

        /* Logo — identique à .page-connexion__logo */
        .login-logo {
            width: 58px; height: 58px;
            border-radius: 16px;
            background: #1A1A1A;
            border: 2px solid #FFC72C;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 6px 24px rgba(255,199,44,0.25);
        }

        .login-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: #1A1A1A;
            margin-bottom: 4px;
        }

        .login-sub {
            font-size: 13px;
            color: #5A5A52;
            margin-bottom: 28px;
        }

        /* Même style que .champ__label */
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #5A5A52;
            text-align: left;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group { margin-bottom: 18px; text-align: left; }

        /* Même style que .champ__input */
        .form-control {
            width: 100%;
            height: 48px;
            border-radius: 10px;
            border: 1.5px solid #E7E5DC;
            padding: 0 14px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            background: #FAFAF7;
            color: #1A1A1A;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-control:focus {
            border-color: #E6A812;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255,199,44,0.18);
        }
        .form-control::placeholder { color: #9A9A90; }

        /* Même style que .bouton-principal */
        .btn-connexion {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 12px;
            background: #1A1A1A;
            color: #FFC72C;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(26,26,26,0.2);
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            margin-top: 8px;
        }
        .btn-connexion:hover {
            background: #2B2B28;
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(26,26,26,0.28);
        }
        .btn-connexion:active { transform: scale(0.97); }

        /* Même style que .message--erreur */
        .alert-erreur {
            background: #FCEAE8;
            color: #C0392B;
            border-left: 3px solid #C0392B;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 11px;
            color: #9A9A90;
        }
    </style>
</head>

<body>
    <div class="login-card">

        <div class="login-logo">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#FFC72C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="6" width="18" height="13" rx="2"/>
                <path d="M3 10h18"/>
                <circle cx="16" cy="14" r="1.3" fill="#FFC72C" stroke="none"/>
            </svg>
        </div>

        <div class="login-title">Mobile Money</div>
        <div class="login-sub">Espace Opérateur — Connexion</div>

        <?php if (session()->getFlashdata('erreur')): ?>
            <div class="alert-erreur">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/></svg>
                <?= esc(session()->getFlashdata('erreur')) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/admin/login">
            <?= csrf_field() ?>
            <div class="form-group">
                <label class="form-label">Identifiant</label>
                <input type="text" name="login" class="form-control"
                       value="<?= esc(old('login') ?? 'admin') ?>"
                       placeholder="admin"
                       autocomplete="username"
                       required>
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="mot_de_passe" class="form-control"
                       value="admin123"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       required>
            </div>
            <button type="submit" class="btn-connexion">Se connecter</button>
        </form>

        <p class="footer-note"> Accès réservé aux administrateurs</p>
    </div>
</body>
</html>