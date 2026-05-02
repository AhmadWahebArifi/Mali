<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'subject'])->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // Get unique values for filters
        $actions = AuditLog::distinct()->pluck('action');
        $subjectTypes = AuditLog::distinct()->pluck('subject_type')->filter();
        $users = \App\Models\User::orderBy('first_name')->get();

        return view('audit-logs.index', compact('logs', 'actions', 'subjectTypes', 'users'));
    }

    public function show($id)
    {
        $log = AuditLog::with(['user', 'subject'])->findOrFail($id);
        return view('audit-logs.show', compact('log'));
    }
}
