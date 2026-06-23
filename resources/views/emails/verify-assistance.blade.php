<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verificá tu asistencia</title>
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">

    <h1>Tu asistencia a la jornada está confirmada</h1>

    <p>
        Mostrá este código QR al ingresar a la jornada:
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <img
            src="data:image/png;base64,{{ $qrCode }}"
            alt="QR de verificación"
            width="250"
            height="250"
        >
    </div>

    <p>
        Cantidad de personas: {{ $peopleAmount }}
    </p>

    <p>
        Total pagado: ${{ number_format($moneyPaid, 0, ',', '.') }}
    </p>

</body>
</html>
