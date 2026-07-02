(() => {
  const conferenceId = window.__CONFERENCE_ID__;
  const sessionState = window.__SESSION_STATE__ || 'active';
  const sessionCheckUrl = window.__SESSION_CHECK_URL__ || null;
  let currentVideoId = window.__VIDEO_ID__ || null;
  let ytIframe = null;
  let pendingVolume = 0;
  let iframeReady = false;

  // Si hay una sesión activa en otro dispositivo, el modal bloquea la vista:
  // no arrancamos el video ni el polling hasta que el usuario reemplace la sesión.
  if (sessionState === 'conflict') {
    return;
  }

  function setIframeVolume(iframe, volume) {
    if (!iframe || !iframe.contentWindow) return;
    try {
      const target = iframe.contentWindow;
      target.postMessage(JSON.stringify({ event: 'command', func: 'setVolume', args: [volume] }), '*');
      target.postMessage(JSON.stringify({ event: 'command', func: volume > 0 ? 'unMute' : 'mute' }), '*');
    } catch (_) {}
  }

  function renderIframe(videoId) {
    const frameContainer = document.getElementById('frame-container');
    if (!frameContainer || !videoId) return;

    frameContainer.innerHTML = '';
    const iframe = document.createElement('iframe');
    iframe.id = 'yt-iframe';
    iframe.src = `https://www.youtube.com/embed/${videoId}?controls=0&disablekb=1&modestbranding=1&rel=0&enablejsapi=1&autoplay=1&mute=1&playsinline=1`;
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture; fullscreen');
    iframe.setAttribute('allowfullscreen', '');
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.pointerEvents = 'none';
    frameContainer.appendChild(iframe);
    ytIframe = iframe;

    iframe.addEventListener('load', () => {
      iframeReady = true;
      setIframeVolume(ytIframe, pendingVolume);
    });
  }

  renderIframe(currentVideoId);

  const volSlider = document.getElementById('vol-slider');
  const volIcon = document.getElementById('vol-icon');

  if (volSlider) {
    volSlider.addEventListener('input', (e) => {
      const val = parseInt(e.target.value, 10);
      pendingVolume = val;
      updateVolumeIcon(val);
      if (iframeReady) {
        setIframeVolume(ytIframe, val);
      }
    });
  }

  function updateVolumeIcon(volume) {
    if (!volIcon) return;
    let svg;
    if (volume === 0) {
      svg = `<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><line x1="23" y1="9" x2="17" y2="15" /><line x1="17" y1="9" x2="23" y2="15" />`;
    } else if (volume < 50) {
      svg = `<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" />`;
    } else {
      svg = `<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5" /><path d="M15.54 8.46a5 5 0 0 1 0 7.07" /><path d="M19.07 4.93a10 10 0 0 1 0 14.14" />`;
    }
    volIcon.innerHTML = svg;
  }

  // --- Control de sesión única ---
  // Cada 10s verificamos que nuestra sesión siga siendo la activa. Si fue
  // reemplazada desde otro dispositivo, cortamos el video y avisamos.
  function stopStream() {
    if (ytIframe) {
      ytIframe.remove();
      ytIframe = null;
    }
    const sessionEndedOverlay = document.getElementById('session-ended-overlay');
    if (sessionEndedOverlay) {
      sessionEndedOverlay.classList.remove('hidden');
    }
  }

  function startSessionPolling() {
    // Solo la vista real de un asistente (active) controla la sesión única.
    // El preview del admin (sin token) no hace polling.
    if (sessionState !== 'active' || !sessionCheckUrl) return;

    const intervalId = setInterval(async () => {
      try {
        const res = await fetch(sessionCheckUrl, {
          headers: { 'Accept': 'application/json' },
          credentials: 'same-origin',
          cache: 'no-store',
        });
        const data = await res.json();
        if (!data.active) {
          clearInterval(intervalId);
          stopStream();
        }
      } catch (_) {
        // Error de red puntual: reintentamos en el próximo ciclo.
      }
    }, 10000);
  }

  startSessionPolling();
})();
