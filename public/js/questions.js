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
  async function reorderQuestions(orderedIds) {
    const payload = orderedIds.map((id, i) => ({ id, sort_order: i }));
    await fetchJSON(apiUrl('/reorder'), {
      method: 'POST',
      body: JSON.stringify({ questions: payload }),
    });
  }

  // --- Time formatting ---
  function relativeTime(dateStr) {
    const d = new Date(dateStr);
    const now = new Date();
    const diffMs = now - d;
    const diffMin = Math.floor(diffMs / 60000);
    if (diffMin < 1) return 'ahora';
    if (diffMin < 60) return `hace ${diffMin} min`;
    const diffHr = Math.floor(diffMin / 60);
    if (diffHr < 24) return `hace ${diffHr} h`;
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

  let dragSrcIndex = null;

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
      listEl.innerHTML = '<li class="question-empty">No hay preguntas aun.</li>';
      return;
    }

    const prevScrollTop = listEl.scrollTop;

    listEl.innerHTML = questions.map((q, i) => {
      const isAnswered = q.status === 'answered';
      const statusLabel = isAnswered ? 'Respondida' : 'Pendiente';
      const statusClass = isAnswered ? 'question-status--answered' : 'question-status--pending';
      const pinnedClass = q.is_pinned ? 'question--pinned' : '';
      return `
        <li class="question-admin-item ${statusClass} ${pinnedClass}" draggable="true" data-index="${i}" data-id="${q.id}">
          <div class="question-admin-meta">
            <span class="question-drag-handle" title="Arrastrar para reordenar">⠿</span>
            <span class="question-id">#${q.id}</span>
            <span class="question-status-badge ${statusClass}">${statusLabel}</span>
            <time class="question-time">${relativeTime(q.created_at)}</time>
          </div>
          <p class="question-body">${escapeHtml(q.body)}</p>
          <div class="question-admin-controls">
            <button class="btn btn--sm btn--pin ${q.is_pinned ? 'btn--pinned' : ''}" data-action="pin" data-id="${q.id}" title="${q.is_pinned ? 'Desfijar pregunta' : 'Fijar pregunta'}">
              ${q.is_pinned ? 'Fijada' : 'Fijar'}
            </button>
            ${!isAnswered ? `<button class="btn btn--sm btn--accent" data-action="answer" data-id="${q.id}">Marcar respondida</button>` : ''}
            <button class="btn btn--sm btn--danger" data-action="delete" data-id="${q.id}">Eliminar</button>
          </div>
        </li>
      `;
    }).join('');

    listEl.querySelectorAll('.question-admin-item[draggable]').forEach(item => {
      item.addEventListener('dragstart', handleDragStart);
      item.addEventListener('dragover', handleDragOver);
      item.addEventListener('dragenter', handleDragEnter);
      item.addEventListener('dragleave', handleDragLeave);
      item.addEventListener('drop', handleDrop);
      item.addEventListener('dragend', handleDragEnd);
    });

    listEl.querySelectorAll('[data-action]').forEach(btn => {
      btn.addEventListener('click', handleAdminAction);
    });

    listEl.scrollTop = prevScrollTop;
  }

  function handleDragStart(e) {
    dragSrcIndex = parseInt(e.currentTarget.dataset.index, 10);
    e.currentTarget.classList.add('question--dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', e.currentTarget.dataset.index);
  }

  function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
  }

  function handleDragEnter(e) {
    e.currentTarget.classList.add('question--drag-over');
  }

  function handleDragLeave(e) {
    e.currentTarget.classList.remove('question--drag-over');
  }

  function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('question--drag-over');

    const targetIndex = parseInt(e.currentTarget.dataset.index, 10);
    if (dragSrcIndex === null || dragSrcIndex === targetIndex) {
      dragSrcIndex = null;
      return;
    }

    const listEl = adminPanel.querySelector('.questions-admin-list');
    const items = listEl.querySelectorAll('.question-admin-item');
    const srcItem = items[dragSrcIndex];
    const tgtItem = items[targetIndex];

    if (dragSrcIndex < targetIndex) {
      tgtItem.insertAdjacentElement('afterend', srcItem);
    } else {
      tgtItem.insertAdjacentElement('beforebegin', srcItem);
    }

    const [moved] = questions.splice(dragSrcIndex, 1);
    questions.splice(targetIndex, 0, moved);

    listEl.querySelectorAll('.question-admin-item').forEach((el, i) => {
      el.dataset.index = i;
    });

    dragSrcIndex = null;
    reorderQuestions(questions.map(q => q.id));
  }

  function handleDragEnd(e) {
    e.currentTarget.classList.remove('question--dragging');
    document.querySelectorAll('.question--drag-over').forEach(el => el.classList.remove('question--drag-over'));
    dragSrcIndex = null;
  }

  async function handleAdminAction(e) {
    const btn = e.currentTarget;
    const action = btn.dataset.action;
    const qId = parseInt(btn.dataset.id, 10);

    if (action === 'delete') {
      if (!confirm('Eliminar la pregunta #' + qId + '?')) return;
    }

    btn.disabled = true;

    try {
      if (action === 'answer') {
        await updateQuestion(qId, { status: 'answered' });
      } else if (action === 'delete') {
        await deleteQuestion(qId);
      } else if (action === 'pin') {
        const q = questions.find(x => x.id === qId);
        if (q) {
          await updateQuestion(qId, { is_pinned: !q.is_pinned });
        }
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
    setInterval(() => {
      if (dragSrcIndex !== null) return;
      fetchAllQuestions();
    }, POLL_MS);
    setupAdminRefresh();
  } else {
    setupViewerForm();
  }
})();
