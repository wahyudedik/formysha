<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(): View
    {
        $logs = AuditLog::with(['user', 'tenant'])
            ->latest()
            ->paginate(50);

        return view('super-admin.audit-logs.index', compact('logs'));
    }
}
