<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;

class SmsLogController extends Controller
{
    public function index()
    {
        $logs = SmsLog::latest()->paginate(20);

        return view('sms-logs.index', compact('logs'));
    }
}
