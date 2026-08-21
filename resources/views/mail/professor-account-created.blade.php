<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <title>Compte professeur</title>
</head>

<body
    style="
        margin:0;
        padding:0;
        background:#eef2f7;
        font-family:Arial,Helvetica,sans-serif;
        color:#172033;
    "
>
<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    border="0"
    style="padding:30px 12px;background:#eef2f7;"
>
    <tr>
        <td align="center">
            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    max-width:650px;
                    overflow:hidden;
                    border-radius:22px;
                    background:#ffffff;
                    box-shadow:
                        0 18px 55px rgba(15,23,42,.12);
                "
            >
                <tr>
                    <td
                        style="
                            height:6px;
                            background:
                                linear-gradient(
                                    90deg,
                                    #2563eb,
                                    #7c3aed,
                                    #f59e0b
                                );
                        "
                    ></td>
                </tr>

                <tr>
                    <td
                        align="center"
                        style="
                            padding:31px 38px 25px;
                            background:#081426;
                        "
                    >
                        <img
                            src="{{ asset(
                                'images/logoSmartSchool.jpg'
                            ) }}"
                            width="76"
                            height="76"
                            alt="Smart School Academy"
                            style="
                                display:block;
                                margin:0 auto 14px;
                                border-radius:17px;
                                background:#ffffff;
                                object-fit:contain;
                            "
                        >

                        <div
                            style="
                                color:#ffffff;
                                font-size:21px;
                                font-weight:800;
                            "
                        >
                            Smart School Academy
                        </div>

                        <div
                            style="
                                margin-top:6px;
                                color:#93a4bb;
                                font-size:11px;
                                letter-spacing:1.4px;
                                text-transform:uppercase;
                            "
                        >
                            Espace professeur
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:35px 40px 18px;">
                        <h1
                            style="
                                margin:0 0 14px;
                                color:#172033;
                                font-size:25px;
                                line-height:1.3;
                            "
                        >
                            Votre compte professeur est prêt
                        </h1>

                        <p
                            style="
                                margin:0 0 12px;
                                color:#526078;
                                font-size:15px;
                                line-height:1.75;
                            "
                        >
                            Bonjour
                            <strong>
                                {{ $professor->name }}
                            </strong>,
                        </p>

                        <p
                            style="
                                margin:0;
                                color:#526078;
                                font-size:15px;
                                line-height:1.75;
                            "
                        >
                            L’administration a créé votre
                            compte professeur. Utilisez les
                            informations temporaires ci-dessous
                            pour votre première connexion.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:8px 40px 22px;">
                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                border:1px solid #e4e9f1;
                                border-radius:15px;
                                background:#f8fafc;
                            "
                        >
                            <tr>
                                <td style="padding:20px;">
                                    <div
                                        style="
                                            margin-bottom:7px;
                                            color:#64748b;
                                            font-size:11px;
                                            font-weight:700;
                                            letter-spacing:.7px;
                                            text-transform:uppercase;
                                        "
                                    >
                                        Adresse e-mail
                                    </div>

                                    <div
                                        style="
                                            margin-bottom:18px;
                                            color:#172033;
                                            font-size:16px;
                                            font-weight:800;
                                            word-break:break-word;
                                        "
                                    >
                                        {{ $professor->email }}
                                    </div>

                                    <div
                                        style="
                                            margin-bottom:7px;
                                            color:#64748b;
                                            font-size:11px;
                                            font-weight:700;
                                            letter-spacing:.7px;
                                            text-transform:uppercase;
                                        "
                                    >
                                        Mot de passe temporaire
                                    </div>

                                    <div
                                        style="
                                            padding:13px 15px;
                                            border:1px dashed #a5b4fc;
                                            border-radius:10px;
                                            color:#3730a3;
                                            background:#eef2ff;
                                            font-family:
                                                Consolas,
                                                Monaco,
                                                monospace;
                                            font-size:18px;
                                            font-weight:900;
                                            letter-spacing:1px;
                                            text-align:center;
                                        "
                                    >
                                        {{ $temporaryPassword }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td
                        align="center"
                        style="padding:3px 40px 28px;"
                    >
                        <a
                            href="{{ $loginUrl }}"
                            style="
                                display:inline-block;
                                padding:15px 28px;
                                border-radius:12px;
                                color:#ffffff;
                                background:
                                    linear-gradient(
                                        135deg,
                                        #2563eb,
                                        #7c3aed
                                    );
                                box-shadow:
                                    0 10px 24px
                                    rgba(79,70,229,.25);
                                font-size:15px;
                                font-weight:800;
                                text-decoration:none;
                            "
                        >
                            Se connecter à mon espace
                        </a>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 40px 25px;">
                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                border:1px solid #fde68a;
                                border-radius:13px;
                                background:#fffbeb;
                            "
                        >
                            <tr>
                                <td
                                    style="
                                        padding:15px 17px;
                                        color:#92400e;
                                        font-size:12px;
                                        line-height:1.65;
                                    "
                                >
                                    <strong>
                                        Sécurité obligatoire
                                    </strong>
                                    <br>
                                    Le mot de passe temporaire
                                    expire le {{ $expiresAt }}.
                                    Après votre première connexion,
                                    vous devrez choisir immédiatement
                                    un nouveau mot de passe.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td
                        align="center"
                        style="
                            padding:22px 38px;
                            border-top:1px solid #e8edf3;
                            background:#f8fafc;
                        "
                    >
                        <p
                            style="
                                margin:0;
                                color:#94a3b8;
                                font-size:11px;
                                line-height:1.6;
                            "
                        >
                            © {{ date('Y') }}
                            Smart School Academy
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
