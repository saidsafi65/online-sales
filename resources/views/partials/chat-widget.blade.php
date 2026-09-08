@if(auth()->check() && auth()->user()->canViewSection('community'))
<div id="chatWidget">
    <button id="cwBubble" class="cw-bubble" type="button" aria-label="مجتمع المعارض" title="مجتمع المعارض">
        <i class="fas fa-comments"></i>
        <span id="cwBubbleBadge" class="cw-bubble-badge"></span>
    </button>

    <div id="cwPanel" class="cw-panel" hidden>
        <div class="cw-header">
            <button id="cwBack" class="cw-icon-btn" type="button" title="رجوع" hidden><i class="fas fa-arrow-right"></i></button>
            <div class="cw-header-title">
                <span id="cwTitle">مجتمع المعارض</span>
                <span id="cwSubtitle" class="cw-header-subtitle" hidden></span>
            </div>
            <a href="{{ route('community.index') }}" class="cw-icon-btn" title="فتح الصفحة الكاملة"><i class="fas fa-up-right-and-down-left-from-center"></i></a>
            <button id="cwClose" class="cw-icon-btn" type="button" title="إغلاق"><i class="fas fa-xmark"></i></button>
        </div>

        <div id="cwListView" class="cw-body">
            <div id="cwPublicRow" class="cw-list-row">
                <span class="cw-room-icon">🌐</span>
                <div class="cw-row-main">
                    <div class="cw-row-name">الغرفة العامة</div>
                    <div class="cw-row-preview" id="cwPublicPreview"></div>
                </div>
                <span id="cwPublicBadge" class="cw-row-badge" hidden></span>
            </div>
            <div class="cw-list-divider">كل المعارض المسجلة</div>
            <div id="cwMembersList"></div>
        </div>

        <div id="cwConvoView" class="cw-body cw-convo" hidden>
            <div id="cwMessages" class="cw-messages"></div>
            <form id="cwForm" class="cw-form">
                @csrf
                <label class="cw-attach-btn" title="إرفاق صورة">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="cwImageInput" accept="image/*" hidden>
                </label>
                <input type="text" id="cwBodyInput" class="cw-text-input" placeholder="اكتب رسالة...">
                <button type="submit" class="cw-send-btn" title="إرسال"><i class="fas fa-paper-plane"></i></button>
            </form>
            <div id="cwImagePreviewName" class="cw-image-preview-name"></div>
        </div>
    </div>

    <div id="cwToastContainer" class="cw-toast-container"></div>
</div>

<style>
    #chatWidget { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 2100; }
    @media (max-width: 576px) { #chatWidget { bottom: 1rem; right: 1rem; } }

    .cw-bubble {
        width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 10px 25px rgba(102, 126, 234, .45);
        color: white; font-size: 1.5rem; display: flex; align-items: center; justify-content: center;
        position: relative; transition: transform .15s;
    }
    .cw-bubble:hover { transform: scale(1.06); }
    .cw-bubble-badge {
        position: absolute; top: -3px; left: -3px; background: #ef4444; color: white;
        font-size: .72rem; font-weight: 900; min-width: 21px; height: 21px; border-radius: 50%;
        display: none; align-items: center; justify-content: center; padding: 0 4px;
        box-shadow: 0 2px 6px rgba(0,0,0,.3); border: 2px solid white;
    }
    .cw-bubble-badge.show { display: flex; }

    .cw-panel {
        position: absolute; bottom: 74px; right: 0; width: 360px; height: 540px;
        max-width: calc(100vw - 2rem); max-height: calc(100vh - 8rem);
        background: white; border-radius: 18px; box-shadow: 0 20px 50px rgba(0,0,0,.25);
        display: flex; flex-direction: column; overflow: hidden;
        animation: cwPanelIn .18s ease-out;
    }
    @keyframes cwPanelIn { from { opacity: 0; transform: translateY(12px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @media (max-width: 576px) {
        .cw-panel {
            position: fixed; inset: 0; width: 100%; height: 100%; max-width: 100%; max-height: 100%;
            border-radius: 0;
        }
    }

    .cw-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;
        padding: .85rem 1rem; display: flex; align-items: center; gap: .6rem; flex-shrink: 0;
    }
    .cw-header-title { flex: 1; min-width: 0; font-weight: 800; font-size: .98rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cw-header-subtitle { display: block; font-size: .72rem; font-weight: 600; opacity: .85; }
    .cw-icon-btn {
        width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(255,255,255,.15);
        color: white; display: flex; align-items: center; justify-content: center; cursor: pointer;
        text-decoration: none; font-size: .85rem; flex-shrink: 0; transition: background .15s;
    }
    .cw-icon-btn:hover { background: rgba(255,255,255,.3); color: white; }

    .cw-body { flex: 1; overflow-y: auto; display: flex; flex-direction: column; min-height: 0; }

    .cw-list-row {
        display: flex; align-items: center; gap: .65rem; padding: .75rem 1rem; cursor: pointer;
        border-bottom: 1px solid #f8fafc; transition: background .12s;
    }
    .cw-list-row:hover { background: #f8fafc; }
    .cw-room-icon { font-size: 1.1rem; width: 20px; text-align: center; flex-shrink: 0; }
    .cw-presence-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; background: #cbd5e1; }
    .cw-presence-dot.online { background: #22c55e; }
    .cw-row-main { flex: 1; min-width: 0; }
    .cw-row-name { font-weight: 700; font-size: .88rem; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cw-row-preview { font-size: .76rem; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 1px; }
    .cw-row-badge {
        background: #ef4444; color: white; font-size: .68rem; font-weight: 900; min-width: 19px; height: 19px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 0 4px; flex-shrink: 0;
    }
    .cw-list-divider { padding: .7rem 1rem .4rem; font-size: .74rem; color: #94a3b8; font-weight: 700; }
    .cw-list-empty { padding: 1.5rem 1rem; color: #94a3b8; font-size: .85rem; text-align: center; }

    .cw-messages { flex: 1; overflow-y: auto; padding: 1.1rem; display: flex; flex-direction: column; gap: .6rem; }
    .cw-msg { align-self: flex-start; max-width: 78%; background: #f1f5f9; color: #1e293b; padding: .55rem .8rem; border-radius: 13px; font-size: .87rem; }
    .cw-msg.me { align-self: flex-end; background: #667eea; color: white; }
    .cw-msg-sender { font-size: .72rem; font-weight: 700; margin-bottom: .2rem; color: #764ba2; }
    .cw-msg img { max-width: 190px; border-radius: 9px; display: block; margin-top: .4rem; }
    .cw-msg-time { font-size: .65rem; opacity: .7; margin-top: .2rem; }

    .cw-form { padding: .75rem; border-top: 1px solid #f1f5f9; display: flex; gap: .5rem; align-items: center; flex-shrink: 0; }
    .cw-attach-btn { cursor: pointer; color: #64748b; display: flex; align-items: center; font-size: 1.05rem; margin: 0; }
    .cw-text-input { flex: 1; border: 2px solid #e2e8f0; border-radius: 10px; padding: .5rem .8rem; font-size: .87rem; min-width: 0; }
    .cw-text-input:focus { outline: none; border-color: #667eea; }
    .cw-send-btn {
        width: 36px; height: 36px; border-radius: 50%; border: none; background: #667eea; color: white;
        display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; font-size: .85rem;
    }
    .cw-send-btn:hover { background: #5a67d8; }
    .cw-image-preview-name { padding: 0 .9rem .5rem; font-size: .76rem; color: #64748b; }

    .cw-toast-container {
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 2099;
        display: flex; flex-direction: column-reverse; gap: .6rem; max-width: 320px; width: calc(100vw - 3rem);
        pointer-events: none;
    }
    @media (max-width: 576px) { .cw-toast-container { bottom: 1rem; right: 1rem; } }
    .cw-toast-container > * { pointer-events: auto; }
    .cw-toast {
        background: white; border-radius: 14px; box-shadow: 0 12px 30px rgba(0,0,0,.18);
        padding: .9rem 1.1rem; cursor: pointer; border-right: 4px solid #667eea; animation: cwToastIn .25s ease-out;
    }
    .cw-toast-title { font-weight: 800; font-size: .88rem; color: #1e293b; margin-bottom: .2rem; display: flex; align-items: center; gap: .4rem; }
    .cw-toast-body { font-size: .83rem; color: #475569; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
    @keyframes cwToastIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
</style>

<script>
(function () {
    const routes = {
        directory: @json(route('community.directory')),
        unreadSummary: @json(route('community.unread-summary')),
        publicMessages: @json(route('community.messages')),
        sendPublicMessage: @json(route('community.messages.send')),
        openConversationTpl: @json(route('community.conversations.show', ['member' => '__ID__'])),
        convoMessagesTpl: @json(route('community.conversations.messages', ['conversation' => '__ID__'])),
        convoSendTpl: @json(route('community.conversations.messages.send', ['conversation' => '__ID__'])),
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const bubble = document.getElementById('cwBubble');
    const bubbleBadge = document.getElementById('cwBubbleBadge');
    const panel = document.getElementById('cwPanel');
    const backBtn = document.getElementById('cwBack');
    const closeBtn = document.getElementById('cwClose');
    const titleEl = document.getElementById('cwTitle');
    const subtitleEl = document.getElementById('cwSubtitle');
    const listView = document.getElementById('cwListView');
    const convoView = document.getElementById('cwConvoView');
    const publicRow = document.getElementById('cwPublicRow');
    const publicBadge = document.getElementById('cwPublicBadge');
    const publicPreview = document.getElementById('cwPublicPreview');
    const membersList = document.getElementById('cwMembersList');
    const messagesBox = document.getElementById('cwMessages');
    const form = document.getElementById('cwForm');
    const bodyInput = document.getElementById('cwBodyInput');
    const imageInput = document.getElementById('cwImageInput');
    const imagePreviewName = document.getElementById('cwImagePreviewName');
    const toastContainer = document.getElementById('cwToastContainer');

    let panelOpen = false;
    let currentRoom = null; // null (list), 'public', or { conversationId, otherId, storeName }
    let pollUrl = null, sendUrl = null, lastId = 0;
    const renderedMessageIds = new Set();
    let isLoadingMessages = false;
    let messagePollTimer = null;
    let latestMembers = [];
    let latestSummary = { public_unread: 0, conversations: [] };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
    function previewText(latest) {
        if (!latest) return '';
        if (latest.is_image && !latest.body) return '📎 صورة';
        return latest.body || '';
    }

    // ---------- Panel open/close ----------

    function openPanel() {
        panelOpen = true;
        panel.hidden = false;
        bubble.querySelector('i').className = 'fas fa-xmark';
        if (currentRoom === null) {
            showListView();
        } else {
            // Reopening into a conversation that was left mid-chat when the panel was closed —
            // resume polling (stopMessagePoll() ran on close, so nothing was ticking).
            loadMessages();
            startMessagePoll();
        }
    }
    function closePanel() {
        panelOpen = false;
        panel.hidden = true;
        bubble.querySelector('i').className = 'fas fa-comments';
        stopMessagePoll();
    }
    bubble.addEventListener('click', function () { panelOpen ? closePanel() : openPanel(); });
    closeBtn.addEventListener('click', closePanel);

    // ---------- List view ----------

    function showListView() {
        currentRoom = null;
        convoView.hidden = true;
        listView.hidden = false;
        backBtn.hidden = true;
        titleEl.textContent = 'مجتمع المعارض';
        subtitleEl.hidden = true;
        stopMessagePoll();
        refreshMembers();
        decorateListBadges();
    }
    backBtn.addEventListener('click', showListView);

    function renderMembers() {
        if (!latestMembers.length) {
            membersList.innerHTML = '<div class="cw-list-empty">لا يوجد معارض ثانية مسجلة بعد</div>';
            return;
        }
        membersList.innerHTML = latestMembers.map(m => `
            <div class="cw-list-row" data-member-id="${m.id}">
                <span class="cw-presence-dot ${m.online ? 'online' : ''}"></span>
                <div class="cw-row-main">
                    <div class="cw-row-name">${esc(m.store_name)}</div>
                    <div class="cw-row-preview" data-preview-for="${m.id}"></div>
                </div>
                <span class="cw-row-badge" data-badge-for="${m.id}" hidden></span>
            </div>
        `).join('');
        membersList.querySelectorAll('[data-member-id]').forEach(row => {
            row.addEventListener('click', function () {
                openMemberConversation(parseInt(row.dataset.memberId, 10), row.querySelector('.cw-row-name').textContent);
            });
        });
        decorateListBadges();
    }

    function decorateListBadges() {
        if (latestSummary.public_unread > 0) {
            publicBadge.textContent = latestSummary.public_unread > 99 ? '99+' : latestSummary.public_unread;
            publicBadge.hidden = false;
            publicPreview.textContent = latestSummary.public_latest ? previewText(latestSummary.public_latest) : '';
        } else {
            publicBadge.hidden = true;
            publicPreview.textContent = '';
        }
        (latestSummary.conversations || []).forEach(function (c) {
            const badge = membersList.querySelector('[data-badge-for="' + c.other_id + '"]');
            const preview = membersList.querySelector('[data-preview-for="' + c.other_id + '"]');
            if (badge) { badge.textContent = c.unread_count > 99 ? '99+' : c.unread_count; badge.hidden = false; }
            if (preview) preview.textContent = previewText(c.latest);
        });
    }

    function refreshMembers() {
        fetch(routes.directory, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => { latestMembers = data.members || []; if (currentRoom === null) renderMembers(); });
    }

    publicRow.addEventListener('click', function () { openRoom('public', { title: 'الغرفة العامة (يشوفها كل المعارض)' }); });

    function openMemberConversation(memberId, storeName) {
        titleEl.textContent = storeName;
        subtitleEl.hidden = true;
        showConvoLoading();
        fetch(routes.openConversationTpl.replace('__ID__', memberId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
            .then(res => res.json())
            .then(data => {
                openRoom('private', {
                    conversationId: data.conversation_id,
                    otherId: data.other.id,
                    title: data.other.store_name,
                });
            });
    }

    // ---------- Conversation view ----------

    function showConvoLoading() {
        listView.hidden = true;
        convoView.hidden = false;
        backBtn.hidden = false;
        messagesBox.innerHTML = '';
    }

    function openRoom(type, meta) {
        renderedMessageIds.clear();
        lastId = 0;
        listView.hidden = true;
        convoView.hidden = false;
        backBtn.hidden = false;
        messagesBox.innerHTML = '';
        titleEl.textContent = meta.title;

        if (type === 'public') {
            currentRoom = 'public';
            pollUrl = routes.publicMessages;
            sendUrl = routes.sendPublicMessage;
            subtitleEl.hidden = true;
        } else {
            currentRoom = { conversationId: meta.conversationId, otherId: meta.otherId };
            pollUrl = routes.convoMessagesTpl.replace('__ID__', meta.conversationId);
            sendUrl = routes.convoSendTpl.replace('__ID__', meta.conversationId);
            const known = latestMembers.find(m => m.id === meta.otherId);
            if (known) {
                subtitleEl.textContent = known.online ? '🟢 متصل الآن' : 'غير متصل حالياً';
                subtitleEl.hidden = false;
            } else {
                subtitleEl.hidden = true;
            }
        }

        if (!panelOpen) openPanel();
        loadMessages();
        startMessagePoll();
    }

    function msgBubble(m) {
        const label = m.is_me ? '' : `<div class="cw-msg-sender">${esc(m.sender_name)}</div>`;
        const image = m.image_url ? `<img src="${m.image_url}">` : '';
        const body = m.body ? `<div>${esc(m.body)}</div>` : '';
        return `<div class="cw-msg ${m.is_me ? 'me' : ''}">${label}${body}${image}<div class="cw-msg-time">${m.created_at}</div></div>`;
    }

    function loadMessages() {
        if (isLoadingMessages || !pollUrl) return;
        isLoadingMessages = true;
        fetch(pollUrl + '?after_id=' + lastId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                if (!data.messages || !data.messages.length) return;
                const isFirstLoad = messagesBox.childElementCount === 0;
                const atBottom = messagesBox.scrollTop + messagesBox.clientHeight >= messagesBox.scrollHeight - 40;
                data.messages.forEach(m => {
                    if (renderedMessageIds.has(m.id)) return;
                    renderedMessageIds.add(m.id);
                    messagesBox.insertAdjacentHTML('beforeend', msgBubble(m));
                    lastId = Math.max(lastId, m.id);
                });
                if (isFirstLoad || atBottom) messagesBox.scrollTop = messagesBox.scrollHeight;
            })
            .finally(() => { isLoadingMessages = false; });
    }

    function startMessagePoll() {
        stopMessagePoll();
        messagePollTimer = setInterval(loadMessages, 4000);
    }
    function stopMessagePoll() {
        if (messagePollTimer) { clearInterval(messagePollTimer); messagePollTimer = null; }
    }

    imageInput.addEventListener('change', function () {
        imagePreviewName.textContent = imageInput.files.length ? '📎 ' + imageInput.files[0].name : '';
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!sendUrl) return;
        if (!bodyInput.value.trim() && !imageInput.files.length) return;

        const data = new FormData();
        data.append('body', bodyInput.value);
        if (imageInput.files.length) data.append('image', imageInput.files[0]);

        fetch(sendUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: data,
        }).then(res => res.json()).then(res => {
            if (res.error) { alert(res.error); return; }
            bodyInput.value = '';
            imageInput.value = '';
            imagePreviewName.textContent = '';
            loadMessages();
        });
    });

    // ---------- Badge + toast polling (always on) ----------

    function isViewingRoom(type, id) {
        if (!panelOpen || currentRoom === null) return false;
        if (type === 'public') return currentRoom === 'public';
        return currentRoom !== 'public' && currentRoom.conversationId === id;
    }

    function showToast(icon, title, body, onClick) {
        const el = document.createElement('div');
        el.className = 'cw-toast';
        el.innerHTML = `
            <div class="cw-toast-title"><span>${icon}</span><span>${esc(title)}</span></div>
            <div class="cw-toast-body">${esc(body)}</div>
        `;
        el.addEventListener('click', onClick);
        toastContainer.appendChild(el);
        setTimeout(function () {
            el.style.transition = 'opacity .3s, transform .3s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            setTimeout(function () { el.remove(); }, 300);
        }, 6000);
    }

    let baseline = null;
    let prevPublic = 0;
    let prevConvUnread = {};

    function pollSummary() {
        fetch(routes.unreadSummary, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                latestSummary = data;
                const total = (data.public_unread || 0) + (data.conversations || []).reduce((s, c) => s + c.unread_count, 0);
                if (total > 0) { bubbleBadge.textContent = total > 99 ? '99+' : total; bubbleBadge.classList.add('show'); }
                else bubbleBadge.classList.remove('show');

                if (currentRoom === null && panelOpen) decorateListBadges();

                const isFirstPoll = baseline === null;
                baseline = true;

                if (!isFirstPoll && data.public_unread > prevPublic && !isViewingRoom('public') && data.public_latest) {
                    showToast('💬', data.public_latest.sender_name + ' (الغرفة العامة)', previewText(data.public_latest),
                        function () { openRoom('public', { title: 'الغرفة العامة (يشوفها كل المعارض)' }); });
                }
                prevPublic = data.public_unread;

                (data.conversations || []).forEach(function (c) {
                    const prev = prevConvUnread[c.conversation_id] || 0;
                    if (!isFirstPoll && c.unread_count > prev && !isViewingRoom('private', c.conversation_id)) {
                        showToast('💬', c.other_store, previewText(c.latest),
                            function () { openRoom('private', { conversationId: c.conversation_id, otherId: c.other_id, title: c.other_store }); });
                    }
                    prevConvUnread[c.conversation_id] = c.unread_count;
                });
            })
            .catch(function () {});
    }

    pollSummary();
    setInterval(pollSummary, 7000);
    setInterval(function () { if (currentRoom === null && panelOpen) refreshMembers(); }, 5000);
})();
</script>
@endif
