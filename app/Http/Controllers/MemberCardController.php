<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberCard;

class MemberCardController extends Controller
{
    public function memberCardIndex(){
        $member_cards = MemberCard::all();
        return view('MemberCard.memberCard_index',['member_cards'=>$member_cards]);
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
        return redirect('member-cards')->with('info', 'The Member Card is Updated Succesfully!');
    }

    public function destroyMemberCard($id){
        $ID = decrypt($id);
        $memberCards = MemberCard::where('id' , $ID)->firstorfail()->delete();
        return redirect('member-cards')->with('warning', 'The Member Card is Deleted Succesfully!');
    }
}

