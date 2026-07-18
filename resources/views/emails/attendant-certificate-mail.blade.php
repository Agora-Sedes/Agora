<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de asistencia</title>

    <style>
        /* ===== RESET ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #qr-code {
            float: right;
            margin: 1em;
            margin-right: 5em;
        }

        .spacing {
            padding: 0.5em;
        }

        .big-spacing {
            margin-top: 5em;
            height: 4em;
        }

        /* ===== PAGE ===== */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        /* ===== BODY (screen) ===== */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Times New Roman', 'Georgia', 'Cambria', serif;
            padding: 20px;
        }

        /* ===== CERTIFICATE: fixed size ===== */
        .certificate {
            position: relative;
            background: #fefcf6;
            border-radius: 2px;
            width: 380mm;
            height: 220mm;
        }

        /* ===== INNER FRAME: absolutely positioned to fill the padded area ===== */
        .border-frame {
            position: absolute;
            top: 8mm;
            left: 14mm;
            right: 14mm;
            bottom: 8mm;
            border: 2.5px solid #b4944b;
            background: #fefcf6;
            /* This will be a block container; we'll put a table inside for centering */
        }

        /* ===== INNER BORDER (double‑frame effect) ===== */
        .border-frame::before {
            content: '';
            position: absolute;
            top: 4mm;
            left: 6mm;
            right: 6mm;
            bottom: 4mm;
            border: 1.5px solid #d4b87a;
            pointer-events: none;
            /* ensure it draws behind content but above the background */
            z-index: 0;
        }

        /* ===== CORNERS (outer) ===== */
        .corner {
            position: absolute;
            width: 18px;
            height: 18px;
            border-color: #b4944b;
            border-style: solid;
            border-width: 0;
            pointer-events: none;
            z-index: 2;
        }
        .corner-tl { top: 3mm; left: 5mm; border-top-width: 3px; border-left-width: 3px; }
        .corner-tr { top: 3mm; right: 5mm; border-top-width: 3px; border-right-width: 3px; }
        .corner-bl { bottom: 3mm; left: 5mm; border-bottom-width: 3px; border-left-width: 3px; }
        .corner-br { bottom: 3mm; right: 5mm; border-bottom-width: 3px; border-right-width: 3px; }

        /* ===== CORNERS (inner) ===== */
        .corner-inner {
            position: absolute;
            width: 10px;
            height: 10px;
            border-color: #d4b87a;
            border-style: solid;
            border-width: 0;
            pointer-events: none;
            z-index: 2;
        }
        .corner-inner-tl { top: 6mm; left: 8mm; border-top-width: 2px; border-left-width: 2px; }
        .corner-inner-tr { top: 6mm; right: 8mm; border-top-width: 2px; border-right-width: 2px; }
        .corner-inner-bl { bottom: 6mm; left: 8mm; border-bottom-width: 2px; border-left-width: 2px; }
        .corner-inner-br { bottom: 6mm; right: 8mm; border-bottom-width: 2px; border-right-width: 2px; }

        .content {
            width: 100%;
            height: 100%;
            /* table will fill the entire .border-frame */
            position: absolute;
            z-index: 1;  /* above the ::before border */
        }

        /* ===== TYPOGRAPHY ===== */
        .title {
            font-size: 30pt;
            font-weight: 700;
            color: #1a2a4a;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 1cm;
            font-family: 'Georgia', 'Times New Roman', serif;
            border-bottom: 2px solid #d4b87a;
            padding-bottom: 1.5mm;
            display: inline-block;
            text-align: center;
            width: 18em;
        }

        .title-container {
            padding-left: 20em;
            padding-right: 20em;
        }

        .body-text {
            font-size: 16pt;
            line-height: 2.1;
            color: #1e2a3a;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
            font-family: 'Georgia', 'Times New Roman', serif;
            text-align: justify;
        }

        .body-text .highlight {
            color: #1a2a4a;
            font-weight: 700;
            border-bottom: 1px solid #d4b87a;
            padding-bottom: 1px;
        }

        .institution-footer {
            margin-top: 1cm;
            font-size: 15px;
            color: #1a2a4a;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            border-top: 1.5px solid #d4b87a;
            padding-top: 2.5mm;
            text-align: center;
            width: 62%;
            margin-left: auto;
            margin-right: auto;
        }

        .institution-footer small {
            display: block;
            font-weight: 400;
            font-size: 10px;
            color: #6a7a9a;
            letter-spacing: 2px;
            margin-top: 1mm;
            text-transform: none;
            text-align: center;
        }

        a {
            color: #b4944b;
            text-decoration: none;
        }

        /* ===== PRINT OVERRIDES ===== */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                display: block;
                min-height: auto;
            }

            .certificate {
                box-shadow: none;
                border-radius: 0;
                padding: 8mm 14mm;
                margin: 0;
            }

            .border-frame {
                top: 8mm;
                left: 14mm;
                right: 14mm;
                bottom: 8mm;
                border-width: 2px;
            }

            .border-frame::before {
                top: 3.5mm;
                left: 5mm;
                right: 5mm;
                bottom: 3.5mm;
                border-width: 1px;
            }

            .corner {
                width: 14px;
                height: 14px;
            }
            .corner-tl,
            .corner-tr,
            .corner-bl,
            .corner-br {
                border-width: 2.5px;
            }
            .corner-tl { top: 2.5mm; left: 4mm; }
            .corner-tr { top: 2.5mm; right: 4mm; }
            .corner-bl { bottom: 2.5mm; left: 4mm; }
            .corner-br { bottom: 2.5mm; right: 4mm; }

            .corner-inner {
                width: 8px;
                height: 8px;
            }
            .corner-inner-tl { top: 5mm; left: 7mm; }
            .corner-inner-tr { top: 5mm; right: 7mm; }
            .corner-inner-bl { bottom: 5mm; left: 7mm; }
            .corner-inner-br { bottom: 5mm; right: 7mm; }

            /* Force colour printing */
            .corner,
            .corner-inner,
            .border-frame,
            .border-frame::before,
            .title,
            .body-text .highlight,
            .institution-footer {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

    <div class="certificate">

        <!-- Inner frame -->
        <div class="border-frame">

            <!-- Outer corners -->
            <span class="corner corner-tl"></span>
            <span class="corner corner-tr"></span>
            <span class="corner corner-bl"></span>
            <span class="corner corner-br"></span>

            <!-- Inner corners -->
            <span class="corner-inner corner-inner-tl"></span>
            <span class="corner-inner corner-inner-tr"></span>
            <span class="corner-inner corner-inner-bl"></span>
            <span class="corner-inner corner-inner-br"></span>

            <div class="content">
                <div class="big-spacing"></div>

                <div class="title-container">
                    <h1 class="title">Certificado de Asistencia</h1>
                </div>

                <div class="spacing"></div>

                <p class="body-text">
                    Se deja constancia para ser presentada a la autoridad que corresponda que
                    <span class="highlight">{{ $attendant->full_name }}</span>,
                    N° DNI <span class="highlight">{{ $attendant->government_id }}</span>,
                    asistió a la jornada titulada
                    <span class="highlight">“{{ $conference->title }}”</span>
                    en la institución
                    <span class="highlight">“Instituto de Profesorado Sedes Sapientiae”</span>,
                    en los días
                    <span class="highlight">
                        {{ \Carbon\Carbon::parse($conference->starts_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                        a
                        {{ \Carbon\Carbon::parse($conference->ends_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                    </span>.
                </p>

                <div class="spacing"></div>

                <div class="institution-footer">
                    Instituto de Profesorado Sedes Sapientiae
                    <small>Generado por <a href="https://github.com/Agora-Sedes/Agora">Ágora</a></small>
                </div>

                <img
                    id="qr-code"
                    src="data:image/png;base64,{{ $qrCode }}"
                    alt="QR de verificación"
                    width="150"
                    height="150"
                >
                <div class="spacing"></div>
            </div>
        <!-- /content -->

        </div>
        <!-- /border-frame -->

    </div>
    <!-- /certificate -->

</body>

</html>
