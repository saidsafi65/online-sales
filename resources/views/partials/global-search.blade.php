<div id="gsearchOverlay" class="gsearch-overlay" hidden>
    <div class="gsearch-box">
        <div class="gsearch-input-row">
            <i class="fas fa-magnifying-glass gsearch-icon"></i>
            <input type="text" id="gsearchInput" class="gsearch-input" placeholder="ابحث عن منتج، عميل، فاتورة، صيانة..." autocomplete="off">
            <span class="gsearch-hint">Esc</span>
        </div>
        <div id="gsearchResults" class="gsearch-results">
            <div class="gsearch-empty">اكتب حرفين على الأقل للبحث</div>
        </div>
    </div>
</div>

<style>
    .gsearch-overlay {
        position: fixed; inset: 0; background: rgba(15, 23, 42, .55); z-index: 3000;
        display: flex; align-items: flex-start; justify-content: center; padding-top: 10vh;
    }
    .gsearch-box {
        width: 560px; max-width: calc(100vw - 2rem); max-height: 70vh; background: white;
        border-radius: 16px; box-shadow: 0 25px 60px rgba(0,0,0,.35); overflow: hidden;
        display: flex; flex-direction: column; animation: gsearchIn .15s ease-out;
    }
    @keyframes gsearchIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .gsearch-input-row { display: flex; align-items: center; gap: .7rem; padding: 1rem 1.2rem; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; }
    .gsearch-icon { color: #94a3b8; font-size: 1.05rem; }
    .gsearch-input { flex: 1; border: none; outline: none; font-size: 1.05rem; background: transparent; }
    .gsearch-hint { font-size: .72rem; color: #94a3b8; border: 1px solid #e2e8f0; border-radius: 5px; padding: .1rem .4rem; }
    .gsearch-results { flex: 1; overflow-y: auto; padding: .5rem 0; }
    .gsearch-empty { padding: 2.5rem 1rem; text-align: center; color: #94a3b8; font-size: .9rem; }
    .gsearch-group-label { padding: .6rem 1.2rem .3rem; font-size: .74rem; font-weight: 800; color: #94a3b8; }
    .gsearch-item {
        display: flex; align-items: center; gap: .75rem; padding: .65rem 1.2rem; text-decoration: none;
        color: #1e293b; cursor: pointer;
    }
    .gsearch-item:hover, .gsearch-item.active { background: #f8fafc; }
    .gsearch-item-icon {
        width: 34px; height: 34px; border-radius: 9px; background: var(--primary-color, #dc2626); opacity: .12;
        flex-shrink: 0;
    }
    .gsearch-item-icon-wrap {
        width: 34px; height: 34px; border-radius: 9px; background: rgba(220,38,38,.1); color: var(--primary-color, #dc2626);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: .9rem;
    }
    .gsearch-item-title { font-weight: 700; font-size: .9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .gsearch-item-subtitle { font-size: .78rem; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
</style>

<script>
(function () {
    const searchUrl = @json(route('search'));
    const overlay = document.getElementById('gsearchOverlay');
    const input = document.getElementById('gsearchInput');
    const resultsBox = document.getElementById('gsearchResults');
    const triggerBtn = document.getElementById('gsearchTriggerBtn');

    let debounceTimer = null;
    let activeIndex = -1;
    let flatUrls = [];

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    function open() {
        overlay.hidden = false;
        input.value = '';
        input.focus();
        resultsBox.innerHTML = '<div class="gsearch-empty">اكتب حرفين على الأقل للبحث</div>';
        activeIndex = -1;
        flatUrls = [];
    }
    function close() {
        overlay.hidden = true;
    }

    triggerBtn.addEventListener('click', open);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) close(); });

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            overlay.hidden ? open() : close();
            return;
        }
        if (overlay.hidden) return;
        if (e.key === 'Escape') { close(); return; }
        if (e.key === 'ArrowDown') { e.preventDefault(); moveActive(1); return; }
        if (e.key === 'ArrowUp') { e.preventDefault(); moveActive(-1); return; }
        if (e.key === 'Enter' && activeIndex >= 0 && flatUrls[activeIndex]) {
            window.location.href = flatUrls[activeIndex];
        }
    });

    function moveActive(delta) {
        const items = resultsBox.querySelectorAll('.gsearch-item');
        if (!items.length) return;
        activeIndex = (activeIndex + delta + items.length) % items.length;
        items.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
        items[activeIndex].scrollIntoView({ block: 'nearest' });
    }

    function render(groups) {
        flatUrls = [];
        const keys = Object.keys(groups || {});
        if (!keys.length) {
            resultsBox.innerHTML = '<div class="gsearch-empty">لا توجد نتائج</div>';
            return;
        }

        let html = '';
        keys.forEach(function (key) {
            const group = groups[key];
            html += `<div class="gsearch-group-label">${esc(group.label)}</div>`;
            group.items.forEach(function (item) {
                flatUrls.push(item.url);
                html += `
                    <a href="${item.url}" class="gsearch-item">
                        <span class="gsearch-item-icon-wrap"><i class="fas ${group.icon}"></i></span>
                        <div style="min-width:0;">
                            <div class="gsearch-item-title">${esc(item.title)}</div>
                            <div class="gsearch-item-subtitle">${esc(item.subtitle)}</div>
                        </div>
                    </a>
                `;
            });
        });
        resultsBox.innerHTML = html;
        activeIndex = -1;
    }

    input.addEventListener('input', function () {
        const q = input.value.trim();
        clearTimeout(debounceTimer);

        if (q.length < 2) {
            resultsBox.innerHTML = '<div class="gsearch-empty">اكتب حرفين على الأقل للبحث</div>';
            flatUrls = [];
            return;
        }

        debounceTimer = setTimeout(function () {
            fetch(searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => render(data.results))
                .catch(function () {});
        }, 300);
    });
})();
</script>
