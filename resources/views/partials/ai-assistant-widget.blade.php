@if(auth()->check() && auth()->user()->canViewSection('ai_assistant'))
<div id="aiwWidget">
    <button id="aiwBubble" class="aiw-bubble" type="button" aria-label="المساعد الذكي" title="المساعد الذكي">
        <i class="fas fa-robot"></i>
    </button>

    <div id="aiwPanel" class="aiw-panel" hidden>
        <div class="aiw-header">
            <div class="aiw-header-title">🤖 المساعد الذكي</div>
            <button id="aiwClear" class="aiw-icon-btn" type="button" title="مسح المحادثة"><i class="fas fa-broom"></i></button>
            <button id="aiwClose" class="aiw-icon-btn" type="button" title="إغلاق"><i class="fas fa-xmark"></i></button>
        </div>

        <div id="aiwMessages" class="aiw-messages">
            <div class="aiw-msg bot">أهلاً! اكتبلي أي سؤال وبساعدك. 🙂</div>
        </div>

        <form id="aiwForm" class="aiw-form">
            <input type="text" id="aiwInput" class="aiw-text-input" placeholder="اكتب رسالة..." autocomplete="off">
            <button type="submit" class="aiw-send-btn" title="إرسال"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<style>
    #aiwWidget { position: fixed; bottom: 1.5rem; left: 1.5rem; z-index: 2100; }
    @media (max-width: 576px) { #aiwWidget { bottom: 1rem; left: 1rem; } }

    .aiw-bubble {
        width: 60px; height: 60px; border-radius: 50%; border: none; cursor: pointer;
        background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
        box-shadow: 0 10px 25px rgba(8, 145, 178, .45);
        color: white; font-size: 1.5rem; display: flex; align-items: center; justify-content: center;
        transition: transform .15s;
    }
    .aiw-bubble:hover { transform: scale(1.06); }

    .aiw-panel {
        position: absolute; bottom: 74px; left: 0; width: 360px; height: 540px;
        max-width: calc(100vw - 2rem); max-height: calc(100vh - 8rem);
        background: white; border-radius: 18px; box-shadow: 0 20px 50px rgba(0,0,0,.25);
        display: flex; flex-direction: column; overflow: hidden;
        animation: aiwPanelIn .18s ease-out;
    }
    @keyframes aiwPanelIn { from { opacity: 0; transform: translateY(12px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @media (max-width: 576px) {
        .aiw-panel { position: fixed; inset: 0; width: 100%; height: 100%; max-width: 100%; max-height: 100%; border-radius: 0; }
    }

    .aiw-header {
        background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: white;
        padding: .85rem 1rem; display: flex; align-items: center; gap: .6rem; flex-shrink: 0;
    }
    .aiw-header-title { flex: 1; font-weight: 800; font-size: .98rem; }
    .aiw-icon-btn {
        width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(255,255,255,.15);
        color: white; display: flex; align-items: center; justify-content: center; cursor: pointer;
        font-size: .85rem; flex-shrink: 0; transition: background .15s;
    }
    .aiw-icon-btn:hover { background: rgba(255,255,255,.3); }

    .aiw-messages { flex: 1; overflow-y: auto; padding: 1.1rem; display: flex; flex-direction: column; gap: .6rem; }
    .aiw-msg { align-self: flex-start; max-width: 82%; background: #f1f5f9; color: #1e293b; padding: .55rem .8rem; border-radius: 13px; font-size: .87rem; white-space: pre-wrap; }
    .aiw-msg.me { align-self: flex-end; background: #0891b2; color: white; }
    .aiw-msg.bot { background: #ecfeff; color: #164e63; }
    .aiw-msg.error { background: #fef2f2; color: #991b1b; }
    .aiw-typing { align-self: flex-start; color: #94a3b8; font-size: .8rem; }

    .aiw-form { padding: .75rem; border-top: 1px solid #f1f5f9; display: flex; gap: .5rem; align-items: center; flex-shrink: 0; }
    .aiw-text-input { flex: 1; border: 2px solid #e2e8f0; border-radius: 10px; padding: .5rem .8rem; font-size: .87rem; min-width: 0; }
    .aiw-text-input:focus { outline: none; border-color: #0891b2; }
    .aiw-send-btn {
        width: 36px; height: 36px; border-radius: 50%; border: none; background: #0891b2; color: white;
        display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; font-size: .85rem;
    }
    .aiw-send-btn:hover { background: #0e7490; }
</style>

<script>
(function () {
    const routes = {
        history: @json(route('ai-assistant.history')),
        send: @json(route('ai-assistant.send')),
        clear: @json(route('ai-assistant.clear')),
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const bubble = document.getElementById('aiwBubble');
    const panel = document.getElementById('aiwPanel');
    const closeBtn = document.getElementById('aiwClose');
    const clearBtn = document.getElementById('aiwClear');
    const messagesBox = document.getElementById('aiwMessages');
    const form = document.getElementById('aiwForm');
    const input = document.getElementById('aiwInput');

    let panelOpen = false;
    let historyLoaded = false;

    function scrollToBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function appendMessage(text, cls) {
        const div = document.createElement('div');
        div.className = 'aiw-msg ' + cls;
        div.textContent = text;
        messagesBox.appendChild(div);
        scrollToBottom();
        return div;
    }

    function loadHistory() {
        if (historyLoaded) return;
        historyLoaded = true;

        fetch(routes.history)
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.messages || data.messages.length === 0) return;
                messagesBox.innerHTML = '';
                data.messages.forEach(m => appendMessage(m.content, m.role === 'user' ? 'me' : 'bot'));
            })
            .catch(() => {});
    }

    bubble.addEventListener('click', function () {
        panelOpen = !panelOpen;
        panel.hidden = !panelOpen;
        if (panelOpen) {
            loadHistory();
            input.focus();
        }
    });

    closeBtn.addEventListener('click', function () {
        panelOpen = false;
        panel.hidden = true;
    });

    clearBtn.addEventListener('click', function () {
        if (!confirm('تأكيد مسح المحادثة؟')) return;
        fetch(routes.clear, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        }).then(() => {
            messagesBox.innerHTML = '<div class="aiw-msg bot">أهلاً! اكتبلي أي سؤال وبساعدك. 🙂</div>';
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;

        appendMessage(text, 'me');
        input.value = '';

        const typingEl = document.createElement('div');
        typingEl.className = 'aiw-typing';
        typingEl.textContent = 'بيكتب...';
        messagesBox.appendChild(typingEl);
        scrollToBottom();

        fetch(routes.send, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ message: text }),
        })
            .then(r => r.json())
            .then(data => {
                typingEl.remove();
                if (data.success) {
                    appendMessage(data.reply, 'bot');
                } else {
                    appendMessage(data.message || 'حدث خطأ', 'error');
                }
            })
            .catch(() => {
                typingEl.remove();
                appendMessage('تعذّر الإرسال، تأكد من الاتصال.', 'error');
            });
    });
})();
</script>
@endif
