<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        // Fetch logs and order by the newest first. 
        // Includes the 'user' relationship to show who did the action.
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('module', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $logs = $query->paginate(15);

        return view('audit-logs.index', compact('logs'));
    }

    /**
     * Clear all audit logs from the database.
     */
    public function clear()
    {
        // Optional Security Check: Uncomment the next line if you only want 'admin' to clear logs
        // if (auth()->user()->role !== 'admin') { abort(403, 'Unauthorized action.'); }

        // Wipe the audit_logs table clean
        DB::table('audit_logs')->truncate();

        return redirect()->route('audit-logs.index')->with('success', 'All system audit logs have been successfully cleared!');
    }
}