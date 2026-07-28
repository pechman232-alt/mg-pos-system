<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemberCard;
use App\Models\SaleReportModel; // 🌟 ថែម Model នេះសម្រាប់ទាញទិន្នន័យភ្ញៀវពេល Scan
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Exports\MemberCardExport;
use App\Imports\MemberCardImport;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Crypt;
 use Illuminate\Contracts\Encryption\DecryptException;

class MemberCardController extends Controller
{
    /**
     * 1. Display Member Card Inventory (Optimized for Fast Load)
     */
    public function memberCardIndex(Request $request)
    {
        $keyword = $request->input('keyword');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $isSale = $request->input('isSale');

        // 🌟 ប្រើប្រាស់ Subquery ជំនួសឱ្យការ Join (ធ្វើឱ្យការទាញទិន្នន័យលឿនជាងមុន ១០ដង)
        $query = MemberCard::query()
            ->addSelect(['invoice_date' => DB::table('sale_reports')
                ->select('invoice_date')
                ->whereColumn('member_card_id', 'member_cards.id')
                ->limit(1)
            ]);

        // Filter តាមស្ថានភាព (Sale Status)
        if ($isSale !== null && $isSale !== '') {
            $query->where('is_sale', $isSale);
        } 
        
        // Filter តាមលេខកាត (Keyword)
        if (!empty($keyword)) {
            // ប្រើ % នៅខាងចុងលឿនជាងការដាក់ទាំងសងខាង
            $query->where('member_card', 'like', $keyword . '%');
        }

        // Filter តាមកាលបរិច្ឆេទ (ប្រើ whereExists លឿនជាង Join ពេលមានទិន្នន័យធំ)
        if (!empty($dateFrom) || !empty($dateTo)) {
            $query->whereExists(function ($subquery) use ($dateFrom, $dateTo) {
                $subquery->select(DB::raw(1))
                         ->from('sale_reports')
                         ->whereColumn('sale_reports.member_card_id', 'member_cards.id');
                         
                if (!empty($dateFrom) && !empty($dateTo)) {
                    $subquery->whereBetween('invoice_date', [$dateFrom, $dateTo]);
                } elseif (!empty($dateFrom)) {
                    $subquery->whereDate('invoice_date', '>=', $dateFrom);
                } elseif (!empty($dateTo)) {
                    $subquery->whereDate('invoice_date', '<=', $dateTo);
                }
            });
        }

        // ទាញយកទិន្នន័យ
        $member_cards = $query->latest('created_at')
                              ->paginate(50)
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
     * 7. Import Excel (AJAX Support)
     */
    public function importMemberCard(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            if (!$request->hasFile('file')) {
                return response()->json(['errors' => ['file' => ['សូមជ្រើសរើសឯកសារ Excel!']]], 422);
            }

            Excel::import(new MemberCardImport, $request->file('file'));
            
            return response()->json([
                'success' => true,
                'message' => 'នាំចូលទិន្នន័យបានជោគជ័យ!'
            ]);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "ជួរទី " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            return response()->json(['errors' => ['file' => $errors]], 422);
        } catch (\Exception $th) {
            Log::error('Import Error: ' . $th->getMessage());
            return response()->json(['errors' => ['file' => ['បញ្ហាប្រព័ន្ធ: ' . $th->getMessage()]]], 422);
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

    /**
     * 9. Download Excel Template
     */
    public function downloadTemplate()
    {
        $filename = 'member_card_template.xlsx';
        $path = public_path('templates/' . $filename); 
        
        if (file_exists($path)) {
            return response()->download($path);
        }
        
        return redirect()->back()->with('error', 'មិនទាន់មាន File Template ក្នុងប្រព័ន្ធទេ។ សូមដាក់ File ចូល public/templates/ សិន។');
    }
    public function scanDigitalCard($parameter)
    {
        $card_number = trim($parameter);

        // 🌟 ១. ប្រព័ន្ធឆ្លាតវៃ៖ ឆែកមើលថាតើវាជាកូដ Encrypt ថ្មី ឬលេខកាតធម្មតាចាស់?
        // (ជាទូទៅកូដ Encrypt គឺមានប្រវែងវែងជាង ៤០ តួអក្សរ)
        if (strlen($card_number) > 30) {
            try {
                // ព្យាយាម Decrypt កូដសម្ងាត់
                $base64 = str_replace(['-', '_'], ['+', '/'], $card_number);
                $paddedBase64 = str_pad($base64, strlen($base64) % 4 === 0 ? strlen($base64) : strlen($base64) + (4 - (strlen($base64) % 4)), '=', STR_PAD_RIGHT);
                $decrypted = base64_decode($paddedBase64);
                $card_number = Crypt::decryptString($decrypted);
            } catch (\Exception $e) {
                // បើកូដសម្ងាត់ខុស លោត 404
                return abort(404, 'សុំទោស! តំណភ្ជាប់នេះត្រូវបានក្លែងបន្លំ។');
            }
        }

        // 🌟 ២. ទាញយកទិន្នន័យ (ដំណើរការបានទាំង Link ចាស់ និង QR ថ្មី)
        $contract = \App\Models\SaleReportModel::with(['branch', 'logistics']) 
            ->select('sale_reports.*')
            ->join('member_cards', 'member_cards.id', '=', 'sale_reports.member_card_id')
            ->where('member_cards.member_card', $card_number)
            ->first();

        if (!$contract) {
            $checkCard = \App\Models\MemberCard::where('member_card', $card_number)->first();
            if (!$checkCard) {
                return abort(404, 'សុំទោស! កាតនេះមិនមានក្នុងប្រព័ន្ធទេ។');
            }
            return view('SaleReports.not_activated');
        }

        return view('SaleReports.mobile_card', compact('contract'));
    }


    public function storeNewCard(Request $request)
{
    // ឧបមាថា $request->member_card ទទួលបានលេខ 11006486
    $cardNumber = trim($request->member_card);

    // ១. បង្កើតកូដសម្ងាត់ (Safe Base64) ភ្លាមៗ
    $encrypted = Crypt::encryptString($cardNumber);
    $safeEncryptedCode = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($encrypted));

    // ២. បង្កើត Link VIP ពេញលេញ
    $vipLink = url('/vip') . '/' . $safeEncryptedCode;

    // ៣. រក្សាទុកចូលក្នុង Database (ឧបមាថាបងមាន Column `vip_link` ក្នុង Table member_cards)
    $newCard = new MemberCard();
    $newCard->member_card = $cardNumber;
    // បងអាចមាន Field នេះដើម្បីទុក Link ស្រាប់ៗ
    // $newCard->vip_link = $vipLink; 
    $newCard->status = 'In Stock'; 
    $newCard->save();

    return response()->json([
        'success' => true,
        'message' => 'បង្កើតកាតថ្មីជោគជ័យ!',
        'vip_link' => $vipLink // អាចបោះ Link នេះត្រលប់ទៅអោយ Admin ឃើញភ្លាមៗ
    ]);
}
}