@extends('layouts.app')

@section('title', 'Escanear QR')

@section('header_action')
    <a href="{{ route('intranet.conferences.dashboard', [ 'id' => $conferenceId ]) }}" class="header-action">← Panel</a>
@endsection

@section('css')
<style>
    .qr-flow {
        display: grid;
        gap: 24px;
        align-items: start;
    }

    .qr-card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 24px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.08);
        padding: 24px;
    }

    .qr-stage {
        min-height: 460px;
    }

    .qr-stage__title {
        margin: 0 0 12px;
        font-size: 1.5rem;
    }

    .qr-camera {
        width: 100%;
        aspect-ratio: 1 / 1;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        display: grid;
        place-items: center;
        position: relative;
    }

    .qr-camera video, .qr-camera canvas {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #eef2ff;
        color: #3730a3;
        font-size: 0.9rem;
        margin-bottom: 16px;
    }

    .qr-hidden {
        display: none !important;
    }

    .qr-person {
        display: grid;
        gap: 10px;
        padding: 18px;
        border-radius: 18px;
        background: #f8fafc;
        margin: 16px 0 24px;
    }

    .qr-person strong {
        display: block;
        font-size: 1.1rem;
    }

    .qr-actions {
        display: grid;
        gap: 12px;
    }

    @media (min-width: 860px) {
        .qr-flow {
            grid-template-columns: 1.05fr 0.95fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container section">
    <h2 class="page-title">Escanear QR</h2>

    <div class="qr-flow">
        <section class="qr-card qr-stage" id="scanStage">
            <div class="qr-badge">Escanea el QR con la cámara del dispositivo</div>
            <h3 class="qr-stage__title">Lector QR</h3>
            <p id="scanHelp">Apuntá la cámara al código QR del asistente. Si el navegador no puede abrir la cámara, revisá los permisos del dispositivo.</p>

            <div class="qr-camera" id="cameraWrap">
                <video id="cameraVideo" playsinline muted></video>
                <canvas id="cameraCanvas" class="qr-hidden"></canvas>
            </div>

            <p id="scanStatus" style="margin-top: 16px;">Esperando un código...</p>
            <form id="manualScanForm" style="display: flex; gap: 12px; margin-top: 16px;">
                <input id="manualCode" class="input" type="text" placeholder="Pegar código o URL del QR" style="flex: 1;">
                <button class="btn btn--primary" type="submit">Buscar</button>
            </form>
        </section>

        <section class="qr-card qr-stage qr-hidden" id="verifyStage">
            <div class="qr-badge" style="background: #ecfeff; color: #0f766e;">Verificá los datos</div>
            <h3 class="qr-stage__title">Persona detectada</h3>
            <p>Confirmá que la persona y los datos coinciden antes de registrar la asistencia.</p>

            <div class="qr-person" id="attendantData"></div>

            <div class="qr-actions">
                <button type="button" class="btn" id="cancelScanBtn">Cancelar</button>
                <button type="button" class="btn btn--primary" id="confirmScanBtn">Correcto</button>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const lookupUrl = @json(route('intranet.conferences.qr-scan.lookup', ['id' => $conferenceId]));
    const confirmUrl = @json(route('intranet.conferences.qr-scan.confirm', ['id' => $conferenceId]));
    const scanStage = document.getElementById('scanStage');
    const verifyStage = document.getElementById('verifyStage');
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const canvasContext = canvas.getContext('2d');
    const scanStatus = document.getElementById('scanStatus');
    const attendantData = document.getElementById('attendantData');
    const confirmBtn = document.getElementById('confirmScanBtn');
    const cancelBtn = document.getElementById('cancelScanBtn');
    const manualForm = document.getElementById('manualScanForm');
    const manualCode = document.getElementById('manualCode');
    let stream = null;
    let activeAttendant = null;
    let scanning = false;
    let detector = null;

    function showScanStage() {
        verifyStage.classList.add('qr-hidden');
        scanStage.classList.remove('qr-hidden');
        activeAttendant = null;
        scanStatus.textContent = 'Esperando un código...';
    }

    function showVerifyStage(attendant) {
        activeAttendant = attendant;
        scanStage.classList.add('qr-hidden');
        verifyStage.classList.remove('qr-hidden');
        attendantData.innerHTML = `
            <strong>${escapeHtml(attendant.full_name)}</strong>
            <span>DNI: ${escapeHtml(attendant.government_id)}</span>
            <span>Email: ${escapeHtml(attendant.email)}</span>
            <span>Teléfono: ${escapeHtml(attendant.phone_number)}</span>
            <span>Estado actual: ${attendant.was_present ? 'Confirmado' : 'Pendiente'}</span>
        `;
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function lookupCode(code) {
        scanStatus.textContent = 'Verificando código...';
        const response = await fetch(`${lookupUrl}?code=${encodeURIComponent(code)}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (!response.ok || !data.found) {
            throw new Error(data.message || 'No se pudo verificar el QR.');
        }

        showVerifyStage(data.attendant);
    }

    async function confirmAttendance() {
        if (!activeAttendant) {
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Guardando...';

        try {
            const response = await fetch(confirmUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ attendant_id: activeAttendant.id }),
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'No se pudo confirmar la asistencia.');
            }

            scanStatus.textContent = data.message || 'Asistencia confirmada.';
            showScanStage();
        } catch (error) {
            scanStatus.textContent = error.message;
            showScanStage();
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Correcto';
        }
    }

    async function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            scanStatus.textContent = 'Tu navegador no soporta cámara.';
            return;
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: { ideal: 'environment' } },
                audio: false
            });

            video.srcObject = stream;
            await video.play();
            scanStatus.textContent = 'Escaneando...';
            scanning = true;
            detectLoop();
        } catch (error) {
            scanStatus.textContent = 'No se pudo acceder a la cámara. Revisá los permisos.';
        }
    }

    async function detectLoop() {
        if (!scanning) {
            return;
        }

        if (!video.videoWidth || !video.videoHeight) {
            requestAnimationFrame(detectLoop);
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);

        try {
            if ('BarcodeDetector' in window) {
                detector ??= new BarcodeDetector({ formats: ['qr_code'] });
                const barcodes = await detector.detect(canvas);
                if (barcodes.length > 0) {
                    scanning = false;
                    try {
                        await lookupCode(barcodes[0].rawValue);
                    } catch (error) {
                        scanStatus.textContent = error.message;
                        scanning = true;
                        requestAnimationFrame(detectLoop);
                    }
                    return;
                }
            } else {
                scanStatus.textContent = 'Tu navegador no tiene lector QR nativo. Usá el campo manual o probá desde Chrome/Edge.';
            }
        } catch (error) {
            scanStatus.textContent = 'No se pudo leer el QR. Probá de nuevo.';
            scanning = true;
        }

        requestAnimationFrame(detectLoop);
    }

    manualForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const code = manualCode.value.trim();
        if (!code) {
            return;
        }

        scanning = false;
        try {
            await lookupCode(code);
        } catch (error) {
            scanStatus.textContent = error.message;
            scanning = true;
            requestAnimationFrame(detectLoop);
        }
    });

    cancelBtn.addEventListener('click', async () => {
        try {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        } finally {
            stream = null;
            scanning = false;
            manualCode.value = '';
            showScanStage();
            await startCamera();
        }
    });

    confirmBtn.addEventListener('click', confirmAttendance);

    showScanStage();
    startCamera();
})();
</script>
@endpush
