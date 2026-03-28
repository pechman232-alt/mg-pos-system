<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberCard;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\MemberCardExport;
use App\Imports\MemberCardImport;
use Illuminate\Support\Facades\DB;

class MemberCardController extends Controller
{
    /**
     * 1. Display Member Card Inventory with Left Join
     */
    public function memberCardIndex(Request $request)
    {
        $keyword = $request->input('keyword');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $isSale = $request->input('isSale');

        // FIX: Use leftJoin instead of rightJoin so that unsold cards are NOT hidden.
        $query = MemberCard::query()
            ->leftJoin('sale_reports', 'sale_reports.member_card_id', '=', 'member_cards.id')
            ->select('member_cards.*', 'sale_reports.invoice_date');

        // Filter by Sale Status (0 = Sold, 1 = Available)
        if ($isSale !== null && $isSale !== '') {
            $query->where('member_cards.is_sale', $isSale);
        } 
        
        // Filter by Keyword (Card ID)
        if (!empty($keyword)) {
            $query->where('member_cards.member_card', 'like', '%' . $keyword . '%');
        }

        // Filter by Date (Logic: search based on when the card was sold)
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query->whereBetween('sale_reports.invoice_date', [$dateFrom, $dateTo]);
        } elseif (!empty($dateFrom)) {
            $query->whereDate('sale_reports.invoice_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $query->whereDate('sale_reports.invoice_date', '<=', $dateTo);
        }

        // Paginate and keep filters in URL for page navigation
        $member_cards = $query->latest('member_cards.created_at')
                              ->paginate(15)
                              ->withQueryString();

        $countMember = $member_cards->total();

        return view('MemberCard.memberCard_index', [
            'member_cards' => $member_cards, 
            'countMember'  => $countMember, 
            'date_from'    => $dateFrom, 
            'date_to'      => $dateTo, 
            'isSale'       => $isSale
        ]);
    }

    /**
     * 2. Show Create Form
     */
    public function createMemberCard()
    {
        return view('MemberCard.add_memberCard');
    }

    /**
     * 3. Store New Member Card
     */
    public function storeMemberCard(Request $request)
    {
        $request->validate([
            'member_card' => 'required|string|max:50|unique:member_cards,member_card',
        ], [
            'member_card.required' => 'សូមបញ្ចូលលេខកាតសមាជិក! (Please enter member card)',
            'member_card.unique'   => 'លេខកាតនេះមានរួចហើយក្នុងប្រព័ន្ធ។ (This card already exists)',
        ]);

        $member_card = new MemberCard;
        $member_card->member_card = $request->member_card;
        $member_card->is_sale = 1; // Default to Available
        $member_card->save();

        return redirect('member-cards/create')->with('success', 'Member Card Created Successfully!');
    }

    /**
     * 4. Edit Form
     */
    public function editMemberCard($id)
    {
        try {
            $ID = decrypt($id);
            $member_card = MemberCard::findOrFail($ID);
            return view('MemberCard.edit_memberCard', compact('member_card'));
        } catch (\Exception $e) {
            return redirect('member-cards')->with('error', 'Invalid ID provided.');
        }
    }

    /**
     * 5. Update Member Card
     */
    public function updateMemberCard(Request $request, $id)
    {
        $ID = decrypt($id);
        $member_card = MemberCard::findOrFail($ID);
        
        $request->validate([
            'member_card' => 'required|string|max:50|unique:member_cards,member_card,' . $ID,
        ]);

        $member_card->member_card = $request->member_card;
        $member_card->save();

        // Redirect back to the previous list view URL if provided
        return redirect()->to($request->last_url ?? 'member-cards')->with('info', 'Member Card updated successfully.');
    }

    /**
     * 6. Delete Card
     */
    public function destroyMemberCard($id)
    {
        $ID = decrypt($id);
        $member_card = MemberCard::findOrFail($ID);
        $member_card->delete();

        return redirect('member-cards')->with('error', 'Member Card removed from registry.');
    }

    /**
     * 7. Import Excel
     */
    public function importMemberCard(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return redirect()->back()->with('error', 'សូមជ្រើសរើសហ្វាយ Excel! (Please select Excel file)');
            }

            Excel::import(new MemberCardImport, $request->file('file'));
            
            return redirect('member-cards')->with('success', 'Import successful.');
        } catch (\Exception $th) {
            Log::error('Import Error: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Error during import: ' . $th->getMessage());
        }
    }

    /**
     * 8. Export Excel
     */
    public function exportMemberCard(Request $request)
    {
        try {
            return Excel::download(new MemberCardExport(
                $request->input('isSale'),
                $request->input('date_from'),
                $request->input('date_to')
            ), 'member_card_report_' . now()->format('Y-m-d') . '.xlsx');
        } catch (\Exception $th) {
            Log::error('Export Error: ' . $th->getMessage());
            return redirect()->back()->with('error', 'Could not export report.');
        }
    }
}