<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Support\ActivityLogFormatter;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = ActivityLog::query()->orderByDesc('created_at');

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('model_id')) {
            $query->where('model_id', $request->model_id);
        }

        if ($request->filled('actor_id')) {
            $query->where('actor_id', $request->actor_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(30)->withQueryString();

        // فلترة "عرض سجل عنصر واحد فقط" (جاية من ضغطة إشعار) بتفعّل عرض مبسّط
        // بدون فلاتر إضافية، بس برضه فيها زر "عرض جميع السجلات" يشيل الفلترة.
        $isSingleRecordView = $request->filled('model_type') && $request->filled('model_id');

        $modelTypes = ActivityLog::select('model_type')->distinct()->pluck('model_type');
        $actors = ActivityLog::whereNotNull('actor_id')
            ->select('actor_id', 'actor_name')
            ->distinct()
            ->orderBy('actor_name')
            ->get();

        return view('activity-log.index', [
            'logs' => $logs,
            'modelTypes' => $modelTypes,
            'actors' => $actors,
            'isSingleRecordView' => $isSingleRecordView,
            'formatter' => new ActivityLogFormatter(),
        ]);
    }
}
