(() => {
  const conferenceId = window.__CONFERENCE_ID__;
  if (!conferenceId) return;

  const isAdmin = !!document.getElementById('questions-admin');
  const POLL_MS = 10000;
  const MAX_LENGTH = 280;

  let questions = [];
  let loading = false;
  let offline = false;

  const panel = document.getElementById('questions-panel');
  const adminPanel = document.getElementById('questions-admin');

  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  let sortableInstance = null;

  function apiUrl(path) {
    return `/api/conferences/${conferenceId}/manage/questions${path}`;
  }

  async function fetchJSON(url, opts = {}) {
    const headers = { Accept: 'application/json' };
    if (opts.body) headers['Content-Type'] = 'application/json';
    if (CSRF_TOKEN && ['POST', 'PATCH', 'DELETE'].includes(opts.method)) {
      headers['X-CSRF-TOKEN'] = CSRF_TOKEN;
    }
    const res = await fetch(url, { ...opts, headers: { ...headers, ...opts.headers } });
    if (!res.ok) {
      const data = await res.json().catch(() => ({}));
      throw new Error(data.message || data.error || 'Error de servidor');
    }
    if (res.status === 204) return null;
    return res.json();
  }

  // --- Viewer: submit question ---
  async function submitQuestion(body) {
    const data = await fetchJSON(apiUrl(''), {
      method: 'POST',
      body: JSON.stringify({ body }),
    });
    return data;
  }

  // --- Admin: fetch all questions ---
  async function fetchAllQuestions() {
    try {
      const data = await fetchJSON(apiUrl(''));
      questions = Array.isArray(data) ? data : data.data ?? [];
      offline = false;
    } catch {
      offline = true;
    }
    renderAdminQuestions();
  }

  // --- Admin: update question ---
  async function updateQuestion(qId, payload) {
    const data = await fetchJSON(apiUrl(`/${qId}`), {
      method: 'PATCH',
      body: JSON.stringify(payload),
    });
    return data;
  }

  // --- Admin: delete question ---
  async function deleteQuestion(qId) {
    await fetchJSON(apiUrl(`/${qId}`), { method: 'DELETE' });
  }

  // --- Admin: reorder questions ---
  async function reorderQuestions(order) {
    await fetchJSON(apiUrl('/reorder'), {
      method: 'POST',
      body: JSON.stringify({ order }),
    });
  }

  // --- Time formatting ---
  function relativeTime(dateStr) {
    const d = new Date(dateStr);
    const now = new Date();
    const diffMs = now - d;
    const diffMin = Math.floor(diffMs / 60000);
    if (diffMin < 1) return 'Recién';
    if (diffMin < 60) return `Hace ${diffMin} min`;
    const diffHr = Math.floor(diffMin / 60);
    if (diffHr < 24) return `Hace ${diffHr} h`;
    return d.toLocaleDateString('es-AR', { day: 'numeric', month: 'short' });
  }

  // ================= VIEWER =================

  function setupViewerForm() {
    if (!panel) return;

    const textarea = panel.querySelector('.questions-textarea');
    const charCount = panel.querySelector('.questions-char-count');
    const submitBtn = panel.querySelector('.questions-submit');
    const form = panel.querySelector('.questions-form');
    const successEl = panel.querySelector('.questions-success');
    const noticeEl = panel.querySelector('.questions-notice');

    if (!textarea || !submitBtn) return;

    textarea.setAttribute('maxlength', MAX_LENGTH);

    textarea.addEventListener('input', () => {
      const len = textarea.value.length;
      if (charCount) charCount.textContent = `${len}/${MAX_LENGTH}`;
      submitBtn.disabled = !textarea.value.trim();
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const body = textarea.value.trim();
      if (!body) return;

      submitBtn.disabled = true;
      submitBtn.textContent = 'Enviando…';
      if (noticeEl) noticeEl.style.display = 'none';
      if (successEl) successEl.style.display = 'none';

      try {
        await submitQuestion(body);
        textarea.value = '';
        if (charCount) charCount.textContent = `0/${MAX_LENGTH}`;
        if (successEl) successEl.style.display = '';
        if (noticeEl) noticeEl.style.display = 'none';
        setTimeout(() => {
          if (successEl) successEl.style.display = 'none';
        }, 3000);
      } catch (err) {
        if (noticeEl) {
          noticeEl.textContent = err.message || 'No se pudo enviar la pregunta';
          noticeEl.className = 'questions-notice questions-notice--error';
          noticeEl.style.display = '';
        }
      }
      submitBtn.textContent = 'Enviar';
      submitBtn.disabled = true;
    });
  }

  // ================= ADMIN =================

  function renderAdminQuestions() {
    if (!adminPanel) return;

    const listEl = adminPanel.querySelector('.questions-admin-list');
    const countEl = adminPanel.querySelector('.questions-count');
    const noticeEl = adminPanel.querySelector('.questions-notice');

    if (countEl) countEl.textContent = questions.length;

    if (offline) {
      if (noticeEl) {
        noticeEl.textContent = 'Servidor no disponible.';
        noticeEl.className = 'questions-notice questions-notice--offline';
        noticeEl.style.display = '';
      }
      return;
    }

    if (noticeEl) noticeEl.style.display = 'none';

    if (!listEl) return;

    if (questions.length === 0) {
      listEl.innerHTML = '<li class="question-empty">No hay preguntas aún.</li>';
      destroySortable();
      return;
    }

    listEl.innerHTML = questions.map(q => {
      const isAnswered = q.status === 'answered';
      const isPinned = q.pinned;
      const statusLabel = isAnswered ? '✔' : '';
      const statusClass = isAnswered ? 'question-status--answered' : 'question-status--pending';
      return `
        <li class="question-admin-item ${statusClass}${isPinned ? ' question-admin-item--pinned' : ''}" data-id="${q.id}">
          <div class="question-admin-meta">
            <span class="question-drag-handle" title="Arrastrar para reordenar">⠿</span>
            <span class="question-status-badge ${statusClass}">${statusLabel}</span>
          </div>
          <p class="question-body">${escapeHtml(q.body)}</p>
          <div class="question-admin-controls">
            <span class="question-time">${relativeTime(q.created_at)}</span>
            ${!isAnswered ? `<button class="btn btn--sm btn--answer" data-action="answer" data-id="${q.id}">✔</button>` : ''}
            <button class="btn btn--sm btn--pin ${isPinned ? 'btn--pinned' : ''}" data-action="pin" data-id="${q.id}">📌</button>
            <button class="btn btn--sm btn--danger" data-action="delete" data-id="${q.id}">✕</button>
          </div>
        </li>
      `;
    }).join('');

    listEl.querySelectorAll('[data-action]').forEach(btn => {
      btn.addEventListener('click', handleAdminAction);
    });

    initSortable();
  }

  function initSortable() {
    const listEl = adminPanel.querySelector('.questions-admin-list');
    if (!listEl || listEl.children.length === 0) return;
    if (listEl.querySelector('.question-empty')) return;

    destroySortable();

    sortableInstance = new Sortable(listEl, {
      handle: '.question-drag-handle',
      animation: 200,
      ghostClass: 'question-ghost',
      onEnd: async (evt) => {
        const items = [...listEl.querySelectorAll('.question-admin-item')];
        const order = items.map(el => parseInt(el.dataset.id, 10));
        try {
          await reorderQuestions(order);
          await fetchAllQuestions();
        } catch (err) {
          const noticeEl = adminPanel.querySelector('.questions-notice');
          if (noticeEl) {
            noticeEl.textContent = 'Error al reordenar: ' + err.message;
            noticeEl.className = 'questions-notice questions-notice--error';
            noticeEl.style.display = '';
          }
          await fetchAllQuestions();
        }
      },
    });
  }

  function destroySortable() {
    if (sortableInstance) {
      sortableInstance.destroy();
      sortableInstance = null;
    }
  }

  async function handleAdminAction(e) {
    const btn = e.currentTarget;
    const action = btn.dataset.action;
    const qId = parseInt(btn.dataset.id, 10);

    if (action === 'delete') {
      if (!confirm('¿Está seguro de que quiere eliminar la pregunta?')) return;
    }

    btn.disabled = true;

    try {
      if (action === 'answer') {
        await updateQuestion(qId, { status: 'answered' });
      } else if (action === 'delete') {
        await deleteQuestion(qId);
      } else if (action === 'pin') {
        const q = questions.find(q => q.id === qId);
        await updateQuestion(qId, { pinned: !q.pinned });
      }
      await fetchAllQuestions();
    } catch (err) {
      const noticeEl = adminPanel.querySelector('.questions-notice');
      if (noticeEl) {
        noticeEl.textContent = err.message;
        noticeEl.className = 'questions-notice questions-notice--error';
        noticeEl.style.display = '';
      }
      btn.disabled = false;
    }
  }

  function setupAdminRefresh() {
    if (!adminPanel) return;

    const refreshBtn = adminPanel.querySelector('.questions-refresh');
    if (refreshBtn) {
      refreshBtn.addEventListener('click', () => {
        refreshBtn.classList.add('auto-refreshing');
        fetchAllQuestions().finally(() => {
          setTimeout(() => refreshBtn.classList.remove('auto-refreshing'), 800);
        });
      });
    }
  }

  // --- Escape HTML ---
  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  // ================= INIT =================

  if (isAdmin) {
    fetchAllQuestions();
    setInterval(fetchAllQuestions, POLL_MS);
    setupAdminRefresh();
  } else {
    setupViewerForm();
  }
})();
