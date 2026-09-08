<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMember;
use App\Models\ChatPrivateMessage;
use App\Models\ChatPublicMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    const ONLINE_THRESHOLD_SECONDS = 20;

    public function index()
    {
        $me = $this->currentMember();

        return view('community.index', [
            'me' => $me,
            'members' => $this->membersWithPresence($me),
            'activeConversation' => null,
            'activeOther' => null,
        ]);
    }

    public function conversation(ChatMember $member)
    {
        $me = $this->currentMember();
        $member->loadMissing('tenant');

        if ($member->id === $me->id) {
            abort(404);
        }

        [$oneId, $twoId] = $me->id < $member->id ? [$me->id, $member->id] : [$member->id, $me->id];

        $conversation = ChatConversation::on('central')->firstOrCreate([
            'member_one_id' => $oneId,
            'member_two_id' => $twoId,
        ]);

        return view('community.index', [
            'me' => $me,
            'members' => $this->membersWithPresence($me),
            'activeConversation' => $conversation,
            'activeOther' => $member,
        ]);
    }

    public function directory()
    {
        $me = $this->currentMember();

        return response()->json(['members' => $this->membersWithPresence($me)]);
    }

    /**
     * Cross-page unread summary, polled globally (see resources/views/partials/chat-toast.blade.php)
     * to drive the site-wide toast notification independently of whether the community page is open.
     */
    public function unreadSummary()
    {
        $me = $this->currentMember();

        $newPublicMessages = ChatPublicMessage::on('central')
            ->with('member.tenant')
            ->where('id', '>', $me->last_read_public_message_id ?? 0)
            ->where('chat_member_id', '!=', $me->id);

        $publicUnread = (clone $newPublicMessages)->count();
        $latestPublic = $publicUnread > 0 ? $newPublicMessages->orderByDesc('id')->first() : null;

        $conversations = ChatConversation::on('central')
            ->where(function ($q) use ($me) {
                $q->where('member_one_id', $me->id)->orWhere('member_two_id', $me->id);
            })
            ->with(['memberOne.tenant', 'memberTwo.tenant'])
            ->get()
            ->map(function (ChatConversation $c) use ($me) {
                $unreadCount = ChatPrivateMessage::on('central')
                    ->where('conversation_id', $c->id)
                    ->where('sender_member_id', '!=', $me->id)
                    ->whereNull('read_at')
                    ->count();

                if ($unreadCount === 0) {
                    return null;
                }

                $other = $c->otherMember($me);
                $latest = ChatPrivateMessage::on('central')
                    ->with('sender')
                    ->where('conversation_id', $c->id)
                    ->orderByDesc('id')
                    ->first();

                return [
                    'conversation_id' => $c->id,
                    'other_id' => $other->id,
                    'other_name' => $other->name,
                    'other_store' => $other->tenant->name,
                    'unread_count' => $unreadCount,
                    'latest' => $latest ? [
                        'sender_name' => $latest->sender->name,
                        'body' => $latest->body,
                        'is_image' => (bool) $latest->image,
                        'created_at' => $latest->created_at->format('Y-m-d H:i'),
                    ] : null,
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'public_unread' => $publicUnread,
            'public_latest' => $latestPublic ? [
                'sender_name' => $latestPublic->member->name,
                'store_name' => $latestPublic->member->tenant->name,
                'body' => $latestPublic->body,
                'is_image' => (bool) $latestPublic->image,
                'created_at' => $latestPublic->created_at->format('Y-m-d H:i'),
            ] : null,
            'conversations' => $conversations,
        ]);
    }

    public function publicMessages(Request $request)
    {
        $me = $this->currentMember();

        $afterId = $request->integer('after_id', 0);
        $query = ChatPublicMessage::on('central')->with('member.tenant');
        $messages = $afterId > 0
            ? $query->where('id', '>', $afterId)->orderBy('id')->limit(200)->get()
            : $query->orderByDesc('id')->limit(50)->get()->sortBy('id')->values();

        $maxId = ChatPublicMessage::on('central')->max('id');
        if ($maxId) {
            $me->update(['last_read_public_message_id' => max($me->last_read_public_message_id ?? 0, $maxId)]);
        }

        return response()->json(['messages' => $messages->map(fn ($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'image_url' => $m->image ? asset('storage/'.$m->image) : null,
            'sender_name' => $m->member->name,
            'store_name' => $m->member->tenant->name,
            'is_me' => $m->chat_member_id === $me->id,
            'created_at' => $m->created_at->format('Y-m-d H:i'),
        ])->values()]);
    }

    public function sendPublicMessage(Request $request)
    {
        $me = $this->currentMember();
        $body = $this->validateAndStoreMessage($request);

        ChatPublicMessage::on('central')->create([
            'chat_member_id' => $me->id,
            'body' => $body['body'],
            'image' => $body['image'],
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function privateMessages(ChatConversation $conversation, Request $request)
    {
        $me = $this->currentMember();
        if (! $conversation->hasParticipant($me)) {
            abort(403);
        }

        $afterId = $request->integer('after_id', 0);
        $query = ChatPrivateMessage::on('central')->with('sender')->where('conversation_id', $conversation->id);
        $messages = $afterId > 0
            ? $query->where('id', '>', $afterId)->orderBy('id')->limit(200)->get()
            : $query->orderByDesc('id')->limit(50)->get()->sortBy('id')->values();

        ChatPrivateMessage::on('central')
            ->where('conversation_id', $conversation->id)
            ->where('sender_member_id', '!=', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages->map(fn ($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'image_url' => $m->image ? asset('storage/'.$m->image) : null,
            'sender_name' => $m->sender->name,
            'is_me' => $m->sender_member_id === $me->id,
            'created_at' => $m->created_at->format('Y-m-d H:i'),
        ])->values()]);
    }

    public function sendPrivateMessage(ChatConversation $conversation, Request $request)
    {
        $me = $this->currentMember();
        if (! $conversation->hasParticipant($me)) {
            abort(403);
        }

        $body = $this->validateAndStoreMessage($request);

        ChatPrivateMessage::on('central')->create([
            'conversation_id' => $conversation->id,
            'sender_member_id' => $me->id,
            'body' => $body['body'],
            'image' => $body['image'],
        ]);

        $conversation->touch();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Resolves (and refreshes) the ChatMember row representing the current TENANT
     * (store) — not the individual staff account. The community chat is store-to-store,
     * so every staff member at the same store shares one ChatMember identity; whichever
     * of them is active most recently just updates local_user_id/last_seen_public_at.
     */
    private function currentMember(): ChatMember
    {
        if (! app()->bound('currentTenant')) {
            abort(500, 'تعذّر تحديد المعرض الحالي');
        }

        $tenant = app('currentTenant');

        return ChatMember::on('central')->updateOrCreate(
            ['tenant_id' => $tenant->id],
            ['name' => $tenant->name, 'local_user_id' => auth()->id(), 'last_seen_public_at' => now()]
        );
    }

    /**
     * Every registered chat member (i.e. every store admin who has opened the
     * community feature at least once) except the current one, flagged online
     * if they've been active anywhere in the community feature recently, sorted
     * online-first then alphabetically.
     */
    private function membersWithPresence(ChatMember $me)
    {
        $onlineCutoff = now()->subSeconds(self::ONLINE_THRESHOLD_SECONDS);

        return ChatMember::on('central')
            ->with('tenant')
            ->where('id', '!=', $me->id)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'store_name' => $m->tenant->name,
                'online' => (bool) ($m->last_seen_public_at && $m->last_seen_public_at->greaterThan($onlineCutoff)),
            ])
            ->sortBy([
                ['online', 'desc'],
                ['name', 'asc'],
            ])
            ->values();
    }

    private function validateAndStoreMessage(Request $request): array
    {
        // Validated manually (instead of $request->validate()) so every failure — including
        // Laravel's own rule failures, not just the hand-rolled "empty message" case — returns
        // the same {error: ...} JSON shape the frontend already understands. $request->validate()
        // throws ValidationException, which the app's catch-all JSON exception renderer doesn't
        // special-case (no getStatusCode()), so it used to fall through as a bare 500 that the
        // frontend silently swallowed — the compose box would clear as if the send had succeeded.
        $validator = Validator::make($request->all(), [
            'body' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'body.max' => 'الرسالة طويلة كتير (الحد الأقصى 2000 حرف)',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'صيغة الصورة غير مدعومة (jpeg, png, jpg, webp فقط)',
            'image.max' => 'حجم الصورة كبير كتير (الحد الأقصى 2 ميجا)',
        ]);

        if ($validator->fails()) {
            abort(response()->json(['error' => $validator->errors()->first()], 422));
        }

        $validated = $validator->validated();

        if (empty($validated['body']) && ! $request->hasFile('image')) {
            abort(response()->json(['error' => 'اكتب رسالة أو أرفق صورة'], 422));
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = Str::random(20).'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('chat', $imageName, 'public');
            $imagePath = 'chat/'.$imageName;
        }

        return ['body' => $validated['body'] ?? null, 'image' => $imagePath];
    }
}
