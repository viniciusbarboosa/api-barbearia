<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperação de senha</title>
  <style>
    body { background: #fff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color: #2b2430; }
    .container { max-width: 600px; margin: 0 auto; padding: 24px; }
    .brand { display:flex; align-items:center; gap:12px; }
    .brand img { height:48px; }
    .card { background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
    .code { display:inline-block; background:#2b2430; color:#FFDD9E; font-weight:700; padding:10px 18px; border-radius:8px; font-size:20px; letter-spacing:4px; }
    .footer { color:#666; font-size:12px; margin-top:18px }
  </style>
</head>
<body>
  <div class="container">
    <div class="brand">
      <img src="{{ asset('/resources/images/logo.png') }}" alt="logo" />
      <div style="font-weight:700; color:#2b2430">Barbearia.ai</div>
    </div>

    <div class="card">
      <p>Olá {{ $name ?? 'usuário' }},</p>
      <p>Recebemos uma solicitação para recuperar sua senha. Use o código abaixo para confirmar sua identidade:</p>

      <p style="text-align:center; margin:22px 0"><span class="code">{{ $code }}</span></p>

      <p>Este código expira em 15 minutos. Se você não solicitou a recuperação de senha, ignore este e-mail.</p>

      <p class="footer">Atenciosamente,<br/>Equipe Barbearia.ai</p>
    </div>
  </div>
</body>
</html>
