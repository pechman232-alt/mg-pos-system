<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\UsersModel; 
use Illuminate\Http\Request;
use App\Exports\IssuesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\NewTicketReported; 
use Illuminate\Support\Facades\Notification;
use Exception;
use Illuminate\Support\Facades\DB;

class IssueController extends Controller
{
    /**
     * 1. TICKET DIRECTORY (INDEX)
     */
    public function index()
    {
        $issues = Issue::latest()->get();
        
        $stats = [
            'total'    => $issues->count(),
            'open'     => $issues->where('status', 'Open')->count(),
            'resolved' => $issues->where('status', 'Resolved')->count(),
            'urgent'   => $issues->where('priority', 'Urgent')->count(),
        ];
        
        return view('Issues.issuse_index', compact('issues', 'stats'));
    }

    /**
     * 2. OPEN NEW TICKET (CREATE)
     */
    public function create()
    {
        $categories = ['IT Support', 'Fleet/Vehicle', 'Administration', 'Sales Operations', 'HR/Legal'];
        $departments = ['Showroom Phnom Penh', 'Siem Reap Branch', 'Battambang Branch', 'Service 50M', 'HQ Administration'];
        $staffs = ['Homen Sentonitandijono', 'Ngov ChanRasmey', 'Pum Sorya', 'Sanh Rady', 'Leang Sophea', 'Da Sotheary', 'Chhlim PichPisey', 'Men Rotana', 'Sem SereyYanuth', 'Ke Choraney', 'Duk Ra', 'Vit Sreyna', 'Yem Sokhea', 'Kim SeakLeng', 'Thul Ratha', 'Yann Yorn', 'Rong Kibunnari', 'Poeng Lyly', 'Heng Sophal', 'Che Saory', 'Huo SreyToch', 'Oeng Bunsea', 'Pheng Bunny', 'Oum Kim', 'Sun Pisey', 'Den Chandan', 'Bun Narith', 'Leng Sovannary'];
        
        return view('Issues.add_Issue', compact('categories', 'departments', 'staffs'));
    }

    /**
     * 3. AUTHORIZE & COMMIT TICKET (STORE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'category'    => 'required|string',
            'department'  => 'required|string',
            'reported_by' => 'required|string',
            'priority'    => 'required|in:Low,Medium,High,Urgent',
            'description' => 'required|string|min:10',
        ]);

        DB::beginTransaction();
        try {
            $year = date('Y');
            $count = Issue::whereYear('created_at', $year)->count() + 1;
            $ticketId = "ISS-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);

            $issue = Issue::create([
                'issue_id'    => $ticketId,
                'subject'     => $request->subject,
                'category'    => $request->category,
                'department'  => $request->department,
                'reported_by' => $request->reported_by,
                'priority'    => $request->priority,
                'description' => $request->description,
                'status'      => 'Open',
            ]);
            DB::commit();

            // 🚩 Single Notification Logic
            try {
                $admins = UsersModel::where('role_id', 1)->get(); 
                if($admins->isNotEmpty()) {
                    Notification::send($admins, new NewTicketReported($issue));
                }
            } catch (Exception $e) { \Log::error("Notify Error: " . $e->getMessage()); }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => "Ticket #{$ticketId} បង្កើតជោគជ័យ!"]);
            }
            return redirect()->route('issues.index')->with('issue_success', "Ticket {$ticketId} created!");

        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * 4. SHOW DETAILS
     */
    public function show($id)
    {
        $issue = Issue::findOrFail($id);
        return view('Issues.view_issuse', compact('issue'));
    }

    /**
     * 5. EDIT TICKET
     */
    public function edit($id)
    {
        $issue = Issue::findOrFail($id);
        
        // 🔒 បើ Status គឺ Closed មិនឱ្យចូល Edit ទេ
        if ($issue->status === 'Closed') {
            return redirect()->route('issues.index')->with('error', 'សំបុត្រនេះត្រូវបានបិទ (Closed) មិនអាចកែបានឡើយ!');
        }

        $categories = ['IT Support', 'Fleet/Vehicle', 'Administration', 'Sales Operations', 'HR/Legal'];
        $departments = ['Showroom Phnom Penh', 'Siem Reap Branch', 'Battambang Branch', 'Service 50M', 'HQ Administration'];
        $staffs = ['Homen Sentonitandijono', 'Ngov ChanRasmey', 'Pum Sorya', 'Sanh Rady', 'Leang Sophea', 'Da Sotheary', 'Chhlim PichPisey', 'Men Rotana', 'Sem SereyYanuth', 'Ke Choraney', 'Duk Ra', 'Vit Sreyna', 'Yem Sokhea', 'Kim SeakLeng', 'Thul Ratha', 'Yann Yorn', 'Rong Kibunnari', 'Poeng Lyly', 'Heng Sophal', 'Che Saory', 'Huo SreyToch', 'Oeng Bunsea', 'Pheng Bunny', 'Oum Kim', 'Sun Pisey', 'Den Chandan', 'Bun Narith', 'Leng Sovannary'];
        
        return view('Issues.edit_issuse', compact('issue', 'categories', 'departments', 'staffs'));
    }

    /**
     * 6. UPDATE TICKET (AJAX Support)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status'   => 'required|in:Open,In Progress,Resolved,Closed',
            'priority' => 'required|in:Low,Medium,High,Urgent',
        ]);

        try {
            $issue = Issue::findOrFail($id);
            
            // 🔒 ការពារម្តងទៀត
            if ($issue->status === 'Closed') {
                return $request->ajax() 
                    ? response()->json(['success' => false, 'message' => 'Locked!'], 403) 
                    : back()->with('error', 'Locked!');
            }

            $issue->update($request->all());

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'ទិន្នន័យត្រូវបានធ្វើបច្ចុប្បន្នភាព!']);
            }
            return redirect()->route('issues.index')->with('issue_success', "Update ជោគជ័យ!");

        } catch (Exception $e) {
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * 7. DESTROY TICKET
     */
    public function destroy($id)
    {
        try {
            $issue = Issue::findOrFail($id);
            
            if ($issue->status === 'Closed') {
                return back()->with('error', 'មិនអាចលុបសំបុត្រដែលបានបិទហើយបានទេ!');
            }

            $issue->delete();
            return redirect()->route('issues.index')->with('issue_success', "លុបជោគជ័យ!");
        } catch (Exception $e) {
            return back()->with('error', 'មិនអាចលុបបានទេ!');
        }
    }

    /**
     * 8. EXPORT EXCEL
     */
    public function exportExcel() 
    {
        return Excel::download(new IssuesExport, 'Issues_Report_' . date('Y-m-d') . '.xlsx');
    }
}