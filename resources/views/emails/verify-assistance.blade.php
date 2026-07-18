<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verificá tu asistencia</title>
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">

    <h1>Tu asistencia a la jornada está confirmada</h1>

    <p>
        Mostrá este código QR al ingresar a la jornada si vas en persona:
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <img
            src="data:image/png;base64,{{ $qrCode }}"
            alt="QR de verificación"
            width="250"
            height="250"
        >
    </div>

    @if ($isOnline)
        <div style="margin: 30px 0; padding: 20px; background: #f4f4f4; border-radius: 8px;">
            <p style="margin: 0 0 8px;">
                Como te inscribiste en modalidad <strong>virtual</strong>, también tenés la opción de seguir la jornada en vivo desde este enlace:
            </p>
            <p style="margin: 0;">
                <a href="{{ $conferenceUrl }}">{{ $conferenceUrl }}</a>
            </p>
        </div>
    @endif

</body>
</html>
