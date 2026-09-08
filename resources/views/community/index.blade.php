@extends('layout.app')

@section('title', 'مجتمع المعارض')

@push('styles')
<style>
    @media (max-width: 768px) {
        .community-layout {
            flex-direction: column !important;
            height: auto !important;
            min-height: 0 !important;
        }
        .community-sidebar {
            width: 100% !important;
            max-height: 220px;
        }
        .community-chat {
            height: 65vh;
            min-height: 420px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h1 style="font-size: 2rem; font-weight: 900; margin-bottom: 1.5rem; color: #1e293b;">🗨️ مجتمع المعارض</h1>

    <div class="community-layout" style="display: flex; gap: 1.5rem; height: 70vh; min-height: 500px;">
        <!-- الشريط الجانبي -->
        <div class="community-sidebar" style="width: 280px; flex-shrink: 0; background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); display: flex; flex-direction: column; overflow: hidden;">
            <a href="{{ route('community.index') }}" style="display: block; padding: 1rem; text-decoration: none; font-weight: 700; color: {{ $activeConversation ? '#1e293b' : 'white' }}; background: {{ $activeConversation ? 'transparent' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }};">
                🌐 الغرفة العامة
            </a>

            <div style="padding: 0.75rem 1rem; font-size: 0.8rem; color: #94a3b8; font-weight: 700; border-top: 1px solid #f1f5f9;">
                كل المعارض المسجلة
            </div>

            <div id="membersList" style="flex: 1; overflow-y: auto;">
                @forelse($members as $m)
                    <a href="{{ route('community.conversations.show', $m['id']) }}" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; text-decoration: none; color: {{ $activeOther && $activeOther->id === $m['id'] ? 'white' : '#1e293b' }}; background: {{ $activeOther && $activeOther->id === $m['id'] ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'transparent' }}; border-bottom: 1px solid #f8fafc;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: {{ $m['online'] ? '#3b82f6' : '#cbd5e1' }};"></span>
                        <div style="min-width: 0;">
                            <div style="font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $m['store_name'] }}</div>
                        </div>
                    </a>
                @empty
                    <div style="padding: 1rem; color: #94a3b8; font-size: 0.85rem;">لا يوجد معارض ثانية مسجلة بعد</div>
                @endforelse
            </div>
        </div>

        <!-- نافذة المحادثة -->
        <div class="community-chat" style="flex: 1; background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); display: flex; flex-direction: column; overflow: hidden;">
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b;">
                @if($activeOther)
                    {{ $activeOther->tenant->name }}
                @else
                    الغرفة العامة (يشوفها كل المعارض)
                @endif
            </div>

            <div id="messagesBox" style="flex: 1; overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;"></div>

            <form id="messageForm" style="padding: 1rem; border-top: 1px solid #f1f5f9; display: flex; gap: 0.75rem; align-items: center;">
                @csrf
                <label for="imageInput" style="cursor: pointer; color: #64748b; margin: 0;">
                    <i class="fas fa-paperclip" style="font-size: 1.2rem;"></i>
                </label>
                <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
                <input type="text" id="bodyInput" name="body" placeholder="اكتب رسالة..." style="flex: 1; border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.6rem 1rem;">
                <button type="submit" class="btn btn-primary" style="border-radius: 10px; font-weight: 600;">إرسال</button>
            </form>
            <div id="imagePreviewName" style="padding: 0 1rem 0.5rem; font-size: 0.8rem; color: #64748b;"></div>
        </div>
    </div>
</div>

<script>
(function () {
    const config = {
        pollUrl: @json($activeConversation ? route('community.conversations.messages', $activeConversation) : route('community.messages')),
        sendUrl: @json($activeConversation ? route('community.conversations.messages.send', $activeConversation) : route('community.messages.send')),
        directoryUrl: @json(route('community.directory')),
        activeOtherId: @json($activeOther?->id),
        conversationUrlTemplate: @json(route('community.conversations.show', ['member' => '__ID__'])),
    };

    const messagesBox = document.getElementById('messagesBox');
    const form = document.getElementById('messageForm');
    const bodyInput = document.getElementById('bodyInput');
    const imageInput = document.getElementById('imageInput');
    const imagePreviewName = document.getElementById('imagePreviewName');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let lastId = 0;

    imageInput.addEventListener('change', function () {
        imagePreviewName.textContent = imageInput.files.length ? '📎 ' + imageInput.files[0].name : '';
    });

    function bubble(m) {
        const align = m.is_me ? 'flex-end' : 'flex-start';
        const bg = m.is_me ? '#667eea' : '#f1f5f9';
        const color = m.is_me ? 'white' : '#1e293b';
        // sender_name صار دايماً اسم المعرض نفسه (المحادثة بين معارض مش حسابات)، فما في داعي نكرره مع store_name
        const label = m.is_me ? '' : `<div style="font-size: 0.75rem; font-weight: 700; margin-bottom: 0.25rem; color: #764ba2;">${m.sender_name}</div>`;
        const image = m.image_url ? `<img src="${m.image_url}" style="max-width: 220px; border-radius: 10px; display: block; margin-top: ${m.body ? '0.5rem' : '0'};">` : '';
        const body = m.body ? `<div>${m.body.replace(/</g, '&lt;')}</div>` : '';

        return `
            <div style="align-self: ${align}; max-width: 70%; background: ${bg}; color: ${color}; padding: 0.6rem 0.9rem; border-radius: 14px;">
                ${label}
                ${body}
                ${image}
                <div style="font-size: 0.7rem; opacity: 0.7; margin-top: 0.25rem;">${m.created_at}</div>
            </div>
        `;
    }

    let isLoadingMessages = false;
    const renderedMessageIds = new Set();

    function loadMessages() {
        if (isLoadingMessages) return;
        isLoadingMessages = true;

        fetch(config.pollUrl + '?after_id=' + lastId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                if (!data.messages || data.messages.length === 0) return;
                const isFirstLoad = messagesBox.childElementCount === 0;
                const atBottom = messagesBox.scrollTop + messagesBox.clientHeight >= messagesBox.scrollHeight - 40;

                data.messages.forEach(m => {
                    if (renderedMessageIds.has(m.id)) return;
                    renderedMessageIds.add(m.id);
                    messagesBox.insertAdjacentHTML('beforeend', bubble(m));
                    lastId = Math.max(lastId, m.id);
                });

                if (isFirstLoad || atBottom) {
                    messagesBox.scrollTop = messagesBox.scrollHeight;
                }
            })
            .finally(() => { isLoadingMessages = false; });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!bodyInput.value.trim() && !imageInput.files.length) return;

        const data = new FormData();
        data.append('body', bodyInput.value);
        if (imageInput.files.length) data.append('image', imageInput.files[0]);

        fetch(config.sendUrl, {
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

    const membersList = document.getElementById('membersList');

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    function renderMembers(members) {
        if (!members.length) {
            membersList.innerHTML = '<div style="padding: 1rem; color: #94a3b8; font-size: 0.85rem;">لا يوجد معارض ثانية مسجلة بعد</div>';
            return;
        }

        membersList.innerHTML = members.map(m => {
            const active = config.activeOtherId === m.id;
            const color = active ? 'white' : '#1e293b';
            const bg = active ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'transparent';
            const dot = m.online ? '#3b82f6' : '#cbd5e1';

            return `
                <a href="${config.conversationUrlTemplate.replace('__ID__', m.id)}" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; text-decoration: none; color: ${color}; background: ${bg}; border-bottom: 1px solid #f8fafc;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: ${dot};"></span>
                    <div style="min-width: 0;">
                        <div style="font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${esc(m.store_name)}</div>
                    </div>
                </a>
            `;
        }).join('');
    }

    function refreshMembers() {
        fetch(config.directoryUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => renderMembers(data.members || []));
    }

    loadMessages();
    setInterval(loadMessages, 4000);
    setInterval(refreshMembers, 5000);
})();
</script>
@endsection
