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

    .qr-btn--danger {
        background: #dc2626;
        color: #fff;
    }
    .qr-btn--danger:hover {
        background: #b91c1c;
    }

    .qr-btn--success {
        background: #16a34a;
        color: #fff;
    }
    .qr-btn--success:hover {
        background: #15803d;
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
            <div class="qr-badge" id="verifyBadge" style="background: #ecfeff; color: #0f766e;">Verificá los datos</div>
            <h3 class="qr-stage__title">Persona detectada</h3>
            <p id="verifyDescription">Confirmá que la persona pertenece a esta conferencia.</p>
            <p class="qr-status" id="verifyError" style="color: #dc2626; font-weight: 600; display: none;"></p>
            <p class="qr-status" id="verifyPaymentInfo" style="font-weight: 600; display: none;"></p>
            <p class="qr-status" id="verifyConferenceInfo" style="display: none;"></p>

            <div class="qr-person" id="attendantData"></div>

            <div class="qr-actions" id="verifyActions">
                <button type="button" class="btn" id="cancelScanBtn">Cancelar</button>
                <button type="button" class="btn btn--primary" id="confirmScanBtn">Datos correctos</button>
            </div>
        </section>

        <section class="qr-card qr-stage qr-hidden" id="successStage">
            <div class="qr-badge" id="successBadge" style="background: #ecfdf3; color: #166534;">Operación exitosa</div>
            <h3 class="qr-stage__title" id="successTitle">Completado</h3>
            <p id="successMessage">La transacción se completó correctamente.</p>

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
    const markPaidUrl = @json(route('intranet.conferences.qr-scan.mark-paid', ['id' => $conferenceId]));
    const csrfToken = @json(csrf_token());

    const scanStage = document.getElementById('scanStage');
    const verifyStage = document.getElementById('verifyStage');
    const successStage = document.getElementById('successStage');
    const scanStatus = document.getElementById('scanStatus');
    const attendantData = document.getElementById('attendantData');
    const successData = document.getElementById('successData');
    const successMessage = document.getElementById('successMessage');
    const successBadge = document.getElementById('successBadge');
    const successTitle = document.getElementById('successTitle');
    const confirmBtn = document.getElementById('confirmScanBtn');
    const cancelBtn = document.getElementById('cancelScanBtn');
    const backToScanBtn = document.getElementById('backToScanBtn');
    const verifyError = document.getElementById('verifyError');
    const verifyBadge = document.getElementById('verifyBadge');
    const verifyDescription = document.getElementById('verifyDescription');
    const verifyPaymentInfo = document.getElementById('verifyPaymentInfo');
    const verifyConferenceInfo = document.getElementById('verifyConferenceInfo');
    const verifyActions = document.getElementById('verifyActions');
    const manualForm = document.getElementById('manualScanForm');
    const manualCode = document.getElementById('manualCode');

    let activeAttendant = null;
    let activeConference = null;
    let activeIsPaid = false;
    let activeIsEventDay = false;
    let html5QrCode = null;
    let scanningActive = false;

    function formatDateTime(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        return d.toLocaleDateString('es-AR', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function formatDate(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        return d.toLocaleDateString('es-AR', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });
    }

    function formatTime(iso) {
        if (!iso) return '';
        const d = new Date(iso);
        return d.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });
    }

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
        `;
    }

    function renderSuccess(personData) {
        successData.innerHTML = `
            <strong>${escapeHtml(personData.full_name)}</strong>
            <span>DNI: ${escapeHtml(personData.government_id)}</span>
        `;
    }

    function renderPaidState() {
        verifyBadge.style.background = '#ecfdf3';
        verifyBadge.style.color = '#166534';
        verifyBadge.textContent = '✔ Pagado';

        const conf = activeConference;
        if (conf && conf.starts_at) {
            verifyConferenceInfo.style.display = 'block';
            verifyConferenceInfo.innerHTML = `
                <strong>Presentarse:</strong> ${escapeHtml(formatDate(conf.starts_at))} a las <strong>${escapeHtml(formatTime(conf.starts_at))}</strong>
                ${conf.ends_at ? `<br><span style="color: #6b7280; font-size: 0.9rem;">Hasta: ${escapeHtml(formatTime(conf.ends_at))}</span>` : ''}
            `;
        } else {
            verifyConferenceInfo.style.display = 'none';
        }

        verifyPaymentInfo.style.display = 'none';

        if (activeAttendant.was_present) {
            confirmBtn.textContent = '✔ Ya presente';
            confirmBtn.disabled = true;
            confirmBtn.className = 'btn qr-btn--success';
            cancelBtn.textContent = 'Volver a escanear';
        } else if (activeIsEventDay) {
            confirmBtn.textContent = 'Marcar como presente';
            confirmBtn.disabled = false;
            confirmBtn.className = 'btn qr-btn--success';
            cancelBtn.textContent = 'Cancelar';
        } else {
            confirmBtn.textContent = 'Marcar como presente';
            confirmBtn.disabled = false;
            confirmBtn.className = 'btn btn--primary';
            cancelBtn.textContent = 'Cancelar';
        }

        verifyError.style.display = 'none';
    }

    function renderUnpaidState() {
        verifyBadge.style.background = '#fef2f2';
        verifyBadge.style.color = '#991b1b';
        verifyBadge.textContent = '✖ Pago pendiente';

        verifyPaymentInfo.style.display = 'block';
        verifyPaymentInfo.style.color = '#991b1b';
        verifyPaymentInfo.textContent = 'La persona aún no pagó su inscripción.';
        verifyPaymentInfo.style.fontWeight = '600';

        verifyConferenceInfo.style.display = 'none';

        confirmBtn.textContent = 'Marcar como pagado';
        confirmBtn.disabled = false;
        confirmBtn.className = 'btn qr-btn--success';
        cancelBtn.textContent = 'Cancelar';

        verifyError.style.display = 'none';
    }

    async function lookupCode(code) {
        scanStatus.textContent = 'Verificando código...';
        const response = await fetch(`${lookupUrl}?code=${encodeURIComponent(code)}`, {
            headers: { 'Accept': 'application/json' }
        });
        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json')
            ? await response.json()
            : { message: await response.text() };

        if (!response.ok || !data.found) {
            throw new Error(data.message || 'No se pudo verificar el QR.');
        }

        activeAttendant = data.attendant;
        activeConference = data.conference;
        activeIsPaid = data.is_paid;
        activeIsEventDay = data.is_event_day;

        renderAttendant(activeAttendant);

        if (!data.belongs_to_conference) {
            verifyBadge.style.background = '#fef2f2';
            verifyBadge.style.color = '#991b1b';
            verifyBadge.textContent = 'Error';
            verifyDescription.textContent = 'Esta persona no pertenece a esta conferencia.';
            verifyError.textContent = 'No se puede registrar la asistencia.';
            verifyError.style.display = 'block';
            verifyPaymentInfo.style.display = 'none';
            verifyConferenceInfo.style.display = 'none';
            confirmBtn.style.display = 'none';
            cancelBtn.textContent = 'Volver a escanear';
        } else {
            verifyDescription.textContent = 'Revisá los datos de la persona.';
            confirmBtn.style.display = '';

            if (activeIsPaid) {
                renderPaidState();
            } else {
                renderUnpaidState();
            }
        }

        showOnly(verifyStage);
    }

    async function markAsPaid() {
        if (!activeAttendant) return;

        const confirmed = confirm('¿Está seguro que la persona pagó?');
        if (!confirmed) return;

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Guardando...';

        try {
            const response = await fetch(markPaidUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ attendant_id: activeAttendant.id }),
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'No se pudo confirmar el pago.');
            }

            activeAttendant.is_draft = false;
            activeIsPaid = true;
            renderPaidState();
        } catch (error) {
            scanStatus.textContent = error.message;
            showOnly(scanStage);
            await startScanner();
        } finally {
            confirmBtn.disabled = false;
        }
    }

    async function markAsPresent() {
        if (!activeAttendant) return;

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Guardando...';

        try {
            const response = await fetch(confirmUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ attendant_id: activeAttendant.id }),
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'No se pudo confirmar la asistencia.');
            }

            successBadge.style.background = '#ecfdf3';
            successBadge.style.color = '#166534';
            successBadge.textContent = '✔ Asistencia registrada';
            successTitle.textContent = '¡Presente!';
            successMessage.textContent = result.message || 'La persona ha sido marcada como presente.';
            renderSuccess(activeAttendant);
            showOnly(successStage);
            await stopScanner();
        } catch (error) {
            scanStatus.textContent = error.message;
            showOnly(scanStage);
            await startScanner();
        } finally {
            confirmBtn.disabled = false;
        }
    }

    async function stopScanner() {
        scanningActive = false;
        if (html5QrCode) {
            try { await html5QrCode.stop(); } catch (error) {}
            try { await html5QrCode.clear(); } catch (error) {}
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

        if (scanningActive) return;

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
                    if (!scanningActive) return;
                    scanningActive = false;
                    try {
                        await stopScanner();
                        await lookupCode(decodedText);
                    } catch (error) {
                        scanStatus.textContent = error.message;
                        console.error('Error al procesar el QR escaneado:', error);
                        await startScanner();
                    }
                },
                () => { scanStatus.textContent = 'Buscando QR...'; }
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
        if (!code) return;

        try {
            await stopScanner();
            await lookupCode(code);
        } catch (error) {
            scanStatus.textContent = error.message;
            console.error('Error al buscar el QR manualmente:', error);
            await startScanner();
        }
    });

    function resetVerifyStage() {
        verifyBadge.style.background = '#ecfeff';
        verifyBadge.style.color = '#0f766e';
        verifyBadge.textContent = 'Verificá los datos';
        verifyDescription.textContent = 'Confirmá que la persona pertenece a esta conferencia.';
        verifyError.style.display = 'none';
        verifyPaymentInfo.style.display = 'none';
        verifyConferenceInfo.style.display = 'none';
        confirmBtn.style.display = '';
        confirmBtn.disabled = false;
        confirmBtn.className = 'btn btn--primary';
        confirmBtn.textContent = 'Datos correctos';
        cancelBtn.textContent = 'Cancelar';
    }

    confirmBtn.addEventListener('click', () => {
        if (!activeAttendant) return;

        if (!activeAttendant.belongs_to_conference) return;

        if (!activeIsPaid) {
            markAsPaid();
        } else if (!activeAttendant.was_present) {
            markAsPresent();
        }
    });

    cancelBtn.addEventListener('click', async () => {
        activeAttendant = null;
        activeConference = null;
        manualCode.value = '';
        resetVerifyStage();
        showOnly(scanStage);
        scanStatus.textContent = 'Vuelve a escanear cuando quieras.';
        await startScanner();
    });

    backToScanBtn.addEventListener('click', async () => {
        activeAttendant = null;
        activeConference = null;
        manualCode.value = '';
        resetVerifyStage();
        showOnly(scanStage);
        scanStatus.textContent = 'Esperando permiso de cámara...';
        await startScanner();
    });

    showOnly(scanStage);
    startScanner();
})();
</script>
@endpush
