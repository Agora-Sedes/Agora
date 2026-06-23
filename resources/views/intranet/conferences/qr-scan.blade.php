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

    .qr-camera {
        width: 100%;
        min-height: 320px;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        display: grid;
        place-items: center;
        position: relative;
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

    .qr-hidden {
        display: none !important;
    }

    .qr-status {
        margin-top: 16px;
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
            <div class="qr-badge">Permiso de cámara y escaneo</div>
            <h3 class="qr-stage__title">Lector QR</h3>
            <p>El navegador pedirá permiso para usar la cámara. Apuntá al QR del asistente para buscarlo en la conferencia actual.</p>

            <div class="qr-camera" id="qrReader"></div>

            <p class="qr-status" id="scanStatus">Esperando permiso de cámara...</p>

            <form id="manualScanForm" style="display: flex; gap: 12px; margin-top: 16px;">
                <input id="manualCode" class="input" type="text" placeholder="Pegar código o URL del QR" style="flex: 1;">
                <button class="btn btn--primary" type="submit">Buscar</button>
            </form>
        </section>

        <section class="qr-card qr-stage qr-hidden" id="verifyStage">
            <div class="qr-badge" style="background: #ecfeff; color: #0f766e;">Verificá los datos</div>
            <h3 class="qr-stage__title">Persona detectada</h3>
            <p>Confirmá que la persona pertenece a esta conferencia antes de registrar la asistencia.</p>

            <div class="qr-person" id="attendantData"></div>

            <div class="qr-actions">
                <button type="button" class="btn" id="cancelScanBtn">Cancelar inscripción</button>
                <button type="button" class="btn btn--primary" id="confirmScanBtn">Datos correctos</button>
            </div>
        </section>

        <section class="qr-card qr-stage qr-hidden" id="successStage">
            <div class="qr-badge" style="background: #ecfdf3; color: #166534;">Persona aceptada</div>
            <h3 class="qr-stage__title">Asistencia registrada</h3>
            <p id="successMessage">La transacción en la base de datos se completó correctamente.</p>

            <div class="qr-person" id="successData"></div>

            <div class="qr-actions">
                <button type="button" class="btn btn--primary" id="backToScanBtn">Volver a escanear</button>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
(function () {
    const lookupUrl = @json(route('intranet.conferences.qr-scan.lookup', ['id' => $conferenceId]));
    const confirmUrl = @json(route('intranet.conferences.qr-scan.confirm', ['id' => $conferenceId]));
    const scanStage = document.getElementById('scanStage');
    const verifyStage = document.getElementById('verifyStage');
    const successStage = document.getElementById('successStage');
    const scanStatus = document.getElementById('scanStatus');
    const attendantData = document.getElementById('attendantData');
    const successData = document.getElementById('successData');
    const successMessage = document.getElementById('successMessage');
    const confirmBtn = document.getElementById('confirmScanBtn');
    const cancelBtn = document.getElementById('cancelScanBtn');
    const backToScanBtn = document.getElementById('backToScanBtn');
    const manualForm = document.getElementById('manualScanForm');
    const manualCode = document.getElementById('manualCode');
    const readerElement = document.getElementById('qrReader');
    let activeAttendant = null;
    let html5QrCode = null;
    let scanningActive = false;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showOnly(stage) {
        scanStage.classList.add('qr-hidden');
        verifyStage.classList.add('qr-hidden');
        successStage.classList.add('qr-hidden');
        stage.classList.remove('qr-hidden');
    }

    function renderAttendant(attendant) {
        attendantData.innerHTML = `
            <strong>${escapeHtml(attendant.full_name)}</strong>
            <span>DNI: ${escapeHtml(attendant.government_id)}</span>
            <span>Email: ${escapeHtml(attendant.email)}</span>
            <span>Teléfono: ${escapeHtml(attendant.phone_number)}</span>
            <span>Conferencia ID: ${escapeHtml(attendant.conference_id)}</span>
            <span>Estado actual: ${attendant.was_present ? 'Confirmado' : 'Pendiente'}</span>
        `;
    }

    function renderSuccess(attendant) {
        successData.innerHTML = `
            <strong>${escapeHtml(attendant.full_name)}</strong>
            <span>La base de datos fue actualizada para esta conferencia.</span>
            <span>DNI: ${escapeHtml(attendant.government_id)}</span>
        `;
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

        activeAttendant = data.attendant;
        renderAttendant(activeAttendant);
        showOnly(verifyStage);
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

            successMessage.textContent = data.message || 'La transacción en la base de datos se completó correctamente.';
            renderSuccess(activeAttendant);
            showOnly(successStage);
            await stopScanner();
        } catch (error) {
            scanStatus.textContent = error.message;
            showOnly(scanStage);
            await startScanner();
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Datos correctos';
        }
    }

    async function stopScanner() {
        scanningActive = false;
        if (html5QrCode) {
            try {
                await html5QrCode.stop();
            } catch (error) {
                // Si ya está detenido, no hacemos nada.
            }
            try {
                await html5QrCode.clear();
            } catch (error) {
                // Sin-op
            }
        }
    }

    async function startScanner() {
        if (typeof Html5Qrcode === 'undefined') {
            scanStatus.textContent = 'No se pudo cargar la librería de cámara.';
            return;
        }

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('qrReader');
        }

        if (scanningActive) {
            return;
        }

        scanningActive = true;
        scanStatus.textContent = 'Pidiendo permiso de cámara...';

        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                async (decodedText) => {
                    if (!scanningActive) {
                        return;
                    }

                    scanningActive = false;
                    try {
                        await stopScanner();
                        await lookupCode(decodedText);
                    } catch (error) {
                        scanStatus.textContent = error.message;
                        await startScanner();
                    }
                },
                (errorMessage) => {
                    scanStatus.textContent = 'Buscando QR...';
                }
            );

            scanStatus.textContent = 'Cámara activa. Buscando QR...';
        } catch (error) {
            scanningActive = false;
            scanStatus.textContent = 'No se pudo acceder a la cámara. Revisá permisos y navegador.';
        }
    }

    manualForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const code = manualCode.value.trim();
        if (!code) {
            return;
        }

        try {
            await stopScanner();
            await lookupCode(code);
        } catch (error) {
            scanStatus.textContent = error.message;
            await startScanner();
        }
    });

    cancelBtn.addEventListener('click', async () => {
        activeAttendant = null;
        manualCode.value = '';
        showOnly(scanStage);
        scanStatus.textContent = 'Vuelve a escanear cuando quieras.';
        await startScanner();
    });

    confirmBtn.addEventListener('click', confirmAttendance);

    backToScanBtn.addEventListener('click', async () => {
        activeAttendant = null;
        manualCode.value = '';
        showOnly(scanStage);
        scanStatus.textContent = 'Esperando permiso de cámara...';
        await startScanner();
    });

    showOnly(scanStage);
    startScanner();
})();
</script>
@endpush
