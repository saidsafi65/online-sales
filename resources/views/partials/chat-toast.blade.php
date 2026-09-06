@if(auth()->check() && auth()->user()->canViewSection('community'))
<div id="chatToastContainer" style="position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 2100; display: flex; flex-direction: column-reverse; gap: 0.6rem; max-width: 320px; width: calc(100vw - 3rem);"></div>

<script>
(function () {
    const unreadUrl = @json(route('community.unread-summary'));
    const communityUrl = @json(route('community.index'));
    const container = document.getElementById('chatToastContainer');

    let baseline = null; // null = haven't polled yet this page load, don't toast on the first result
    let prevPublic = 0;
    let prevConvUnread = {};

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    function onCommunityPage(conversationOtherId) {
        if (!window.location.pathname.startsWith('/community')) return false;
        if (conversationOtherId == null) return true; // public room toast, any community page counts as "already there"
        return window.location.pathname === '/community/conversations/' + conversationOtherId;
    }

    function showToast(title, body, url) {
        const el = document.createElement('div');
        el.style.cssText = 'background:white; border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,.18); padding:0.9rem 1.1rem; cursor:pointer; border-right:4px solid #667eea; animation:chatToastIn .25s ease-out;';
        el.innerHTML = `
            <div style="font-weight:800; font-size:0.88rem; color:#1e293b; margin-bottom:0.2rem; display:flex; align-items:center; gap:0.4rem;">
                <span>💬</span><span>${esc(title)}</span>
            </div>
            <div style="font-size:0.83rem; color:#475569; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">${esc(body)}</div>
        `;
        el.addEventListener('click', function () {
            window.location.href = url;
        });
        container.appendChild(el);

        setTimeout(function () {
            el.style.transition = 'opacity .3s, transform .3s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            setTimeout(function () { el.remove(); }, 300);
        }, 6000);
    }

    function previewText(latest) {
        if (!latest) return '';
        if (latest.is_image && !latest.body) return '📎 صورة';
        return latest.body || '';
    }

    function poll() {
        fetch(unreadUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                const isFirstPoll = baseline === null;
                baseline = true;

                if (!isFirstPoll && data.public_unread > prevPublic && !onCommunityPage(null) && data.public_latest) {
                    showToast(
                        data.public_latest.sender_name + ' — ' + data.public_latest.store_name + ' (الغرفة العامة)',
                        previewText(data.public_latest),
                        communityUrl
                    );
                }
                prevPublic = data.public_unread;

                (data.conversations || []).forEach(function (c) {
                    const prev = prevConvUnread[c.conversation_id] || 0;
                    if (!isFirstPoll && c.unread_count > prev && !onCommunityPage(c.other_id)) {
                        showToast(
                            c.latest.sender_name + ' — ' + c.other_store,
                            previewText(c.latest),
                            '/community/conversations/' + c.other_id
                        );
                    }
                    prevConvUnread[c.conversation_id] = c.unread_count;
                });
            })
            .catch(function () {});
    }

    poll();
    setInterval(poll, 7000);
})();
</script>

<style>
@keyframes chatToastIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>
@endif
