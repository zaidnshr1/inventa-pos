<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::with(['user', 'outlet'])
            ->latest('id')
            ->paginate(20);

        return view('activity_logs.index', compact('logs'));
    }
}
