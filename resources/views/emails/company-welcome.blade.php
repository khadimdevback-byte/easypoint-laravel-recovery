<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur EasyPoint</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #2e7d64, #1a5a48);
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .body {
            padding: 40px;
        }
        .body h2 {
            color: #1f2937;
            font-size: 20px;
            margin-top: 0;
        }
        .body p {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .company-info {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 16px 0;
        }
        .company-info .label {
            font-weight: 600;
            color: #166534;
        }
        .credentials-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px 24px;
            margin: 20px 0;
        }
        .credentials-box .label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        .credentials-box .value {
            color: #1f2937;
            font-weight: 500;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: #ffffff;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        .credentials-box .value:last-child {
            margin-bottom: 0;
        }
        .btn {
            display: inline-block;
            background: #2e7d64;
            color: #ffffff;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 16px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1a5a48;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        .footer a {
            color: #2e7d64;
            text-decoration: none;
        }
        .highlight {
            color: #2e7d64;
            font-weight: 600;
        }
        .role-badge {
            display: inline-block;
            background: #2e7d64;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur EasyPoint</h1>
        </div>

        <div class="body">
            <h2>Bonjour {{ $user->first_name }} {{ $user->name }},</h2>

            <p>
                Nous sommes ravis de vous accueillir sur <span class="highlight">EasyPoint</span>,
                la plateforme de gestion de points de vente nouvelle génération.
            </p>

            <div class="company-info">
                <span class="label">Société :</span>
                {{ $company->compagny_name }}
                <br>
                <span class="label">Rôle :</span>
                <span class="role-badge">{{ ucfirst($user->user_type) }}</span>
            </div>

            <p>
                Votre compte a été créé avec succès. Voici vos identifiants de connexion :
            </p>

            <div class="credentials-box">
                <div class="label">Email</div>
                <div class="value">{{ $user->email }}</div>

                <div class="label">Mot de passe</div>
                <div class="value">{{ $password }}</div>
            </div>

            <p style="font-size: 14px; color: #6b7280;">
                Nous vous recommandons de changer votre mot de passe dès votre première connexion.
            </p>

            <div style="text-align: center;">
                <a href="https://easy-point.tech/login" class="btn">
                    Se connecter à EasyPoint
                </a>
            </div>

            <p style="margin-top: 24px; color: #6b7280; font-size: 14px;">
                Si vous n'avez pas demandé la création de ce compte, veuillez ignorer cet email.
            </p>
        </div>

        <div class="footer">
            <p>
                &copy; {{ date('Y') }} <a href="{{ config('app.url') }}">EasyPoint</a>.
                Tous droits réservés.
            </p>
            <p style="font-size: 12px; margin-top: 8px;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre.
            </p>
        </div>
    </div>
</body>
</html>
