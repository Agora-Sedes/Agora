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

        /* ===== PAGE ===== */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        /* ===== BODY (screen) ===== */
        body {
            background: #e6e9ef;
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
            width: 297mm;
            height: 210mm;
            background: #fefcf6;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
            border-radius: 2px;
            /* Padding defines the offset for the inner frame */
            padding: 8mm 14mm;
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

        /* ===== CONTENT TABLE: fills the frame and centers everything ===== */
        .content-table {
            display: table;
            width: 100%;
            height: 100%;
            /* table will fill the entire .border-frame */
            position: relative;
            z-index: 1;  /* above the ::before border */
        }

        .content-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 2mm 0;
        }

        /* ===== TYPOGRAPHY ===== */
        .title {
            font-size: 30px;
            font-weight: 700;
            color: #1a2a4a;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 1cm;
            font-family: 'Georgia', 'Times New Roman', serif;
            border-bottom: 2px solid #d4b87a;
            padding-bottom: 1.5mm;
            display: inline-block;
            padding-left: 14px;
            padding-right: 14px;
        }

        .body-text {
            font-size: 18px;
            line-height: 2.1;
            color: #1e2a3a;
            max-width: 88%;
            margin: 0 auto 3mm auto;
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
            width: 60%;
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
                width: 297mm;
                height: 210mm;
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

            .title {
                font-size: 26px;
                letter-spacing: 2px;
            }
            .body-text {
                font-size: 16.5px;
                line-height: 2;
                max-width: 92%;
            }
            .institution-footer {
                font-size: 13px;
                width: 65%;
            }

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

        /* ===== RESPONSIVE (screen only) ===== */
        @media screen and (max-width: 900px) {
            .certificate {
                width: 100%;
                height: auto;
                min-height: 210mm;
                padding: 5mm;
            }
            .border-frame {
                position: relative;
                top: auto;
                left: auto;
                right: auto;
                bottom: auto;
                width: 100%;
                min-height: calc(100% - 10mm);
                padding: 4mm 5mm;
                border-width: 2px;
                display: flex;
                flex-direction: column;
            }
            .border-frame::before {
                top: 3mm;
                left: 4mm;
                right: 4mm;
                bottom: 3mm;
            }
            .content-table {
                display: flex;
                flex: 1;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .content-cell {
                display: block;
                width: 100%;
            }
            .body-text {
                font-size: 15px;
                max-width: 98%;
                line-height: 1.8;
            }
            .title {
                font-size: 22px;
            }
            .corner {
                width: 12px;
                height: 12px;
            }
            .corner-tl,
            .corner-tr,
            .corner-bl,
            .corner-br {
                border-width: 2px;
            }
            .corner-inner {
                display: none;
            }
            .institution-footer {
                width: 85%;
                font-size: 12px;
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

            <!-- Table for centering -->
            <div class="content-table">
                <div class="content-cell">

                    <h1 class="title">Certificado de Asistencia</h1>

                    <p class="body-text">
                        Se deja constancia para ser presentada a la autoridad que corresponda que
                        <span class="highlight">{{ $attendant->name }}</span>,
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

                    <div class="institution-footer">
                        Instituto de Profesorado Sedes Sapientiae
                        <small>Generado por <a href="https://github.com/Agora-Sedes/Agora">Ágora</a></small>
                    </div>

                </div>
                <!-- /content-cell -->
            </div>
            <!-- /content-table -->

        </div>
        <!-- /border-frame -->

    </div>
    <!-- /certificate -->

</body>

</html>
