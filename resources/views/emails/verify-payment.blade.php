<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verificá tu pago</title>
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">

    <h1>Verificá tu pago en efectivo</h1>

    <p>
        Mostrá este código QR a quien recibe el pago al ingresar a la jornada:
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <img
            src="data:image/png;base64,{{ $qrCode }}"
            alt="QR de verificación de pago"
            width="250"
            height="250"
        >
    </div>

</body>
</html>
