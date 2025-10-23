<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberCard;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\MemberCardExport;
use App\Imports\MemberCardImport;

class MemberCardController extends Controller
{
    public function memberCardIndex(Request $request){

        $keyword = $request->input('keyword');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $isSale = $request->input('isSale');

        $member_cards = MemberCard::query()
        ->leftJoin('sale_reports', 'sale_reports.member_card_id', '=', 'member_cards.id')
        ->select('member_cards.*', 'sale_reports.invoice_date');

        if (!is_null($isSale) && $isSale !== '') {
            $member_cards->where('member_cards.is_sale', $isSale);
        } 
        
        if (!empty($keyword)) {
            $member_cards->where('member_cards.member_card', 'like', '%' . $keyword . '%');
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $member_cards->whereBetween('sale_reports.invoice_date', [$dateFrom, $dateTo]);
        } elseif (!empty($dateFrom)) {
            $member_cards->whereDate('sale_reports.invoice_date', '>=', $dateFrom);
        } elseif (!empty($dateTo)) {
            $member_cards->whereDate('sale_reports.invoice_date', '<=', $dateTo);
        }

        $member_cards = $member_cards->paginate(10);
        $countMember = $member_cards->total();

        return view('MemberCard.memberCard_index',[
            'member_cards'=>$member_cards, 
            'countMember'=>$countMember, 
            'date_from'=>$dateFrom, 
            'date_to'=>$dateTo, 
            'isSale'=>$isSale
        ]);
    }

    public function createMemberCard(){
        return view('MemberCard.add_memberCard');
    }

    public function storeMemberCard(Request $request){
        $request->validate([
            'member_card' => 'required|string|max:50|unique:member_cards,member_card',
        ],
        [
            'member_card' => 'Please Enter Member Card!',
            'member_card.unique' => 'the member card is already exists.'
        ]);
        $member_card = new MemberCard;
        $member_card->member_card = $request->member_card;
        $member_card->save();
        return redirect('member-cards/create')->with('success', 'The Member Card is Created Succesfully!');

    }

    public function editMemberCard($id){
        $ID = decrypt($id);
        $member_card = MemberCard::where('id', $ID)->first();
        return view('MemberCard.edit_memberCard',['member_card'=>$member_card]);
    }

    public function updateMemberCard(Request $request, $id){
        $ID = decrypt($id);
        $member_card = MemberCard::where('id', $ID)->firstorfail();
        $member_card->member_card = $request->member_card;
        $member_card->update();
        return redirect()->to($request->last_url)->with('info','The Member Card is Created Succesfully!');

    }

    public function destroyMemberCard($id){
        $ID = decrypt($id);
        $memberCards = MemberCard::where('id' , $ID)->firstorfail()->delete();
        return redirect('member-cards')->with('error', 'The Member Card is Deleted Succesfully!');
    }

    public function importMemberCard(Request $memberCardFileRequest ){
        try {
            if (!$memberCardFileRequest->hasFile('file')) {
                return redirect()->back()->with('error', 'Please Import File Excel!');
            }
            Excel::import(new MemberCardImport, $memberCardFileRequest->file('file'));
            return redirect('member-cards')->with('success', value: 'Member Card imported successfully.');
        } catch (\Exception $th){
            Log::info($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function exportMemberCard(Request $request){
        try {
            return Excel::download(new MemberCardExport(
                $request->input('isSale'),
                $request->input('date_from'),
                $request->input('date_to'),
            ), 'member_card_report.xlsx');
        } catch (\Exception $th){
            Log::info($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}

