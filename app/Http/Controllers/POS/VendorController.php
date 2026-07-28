<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POS\Vendor; // ត្រូវប្រាកដថាបងមាន Model នេះ
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * បង្ហាញបញ្ជីអ្នកផ្គត់ផ្គង់ទាំងអស់
     */
    public function index(Request $request)
    {
        $query = Vendor::query();

        // មុខងារស្វែងរកតាមឈ្មោះ លេខទូរស័ព្ទ ឬអ៊ីមែល
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('vendor_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $vendors = $query->orderBy('id', 'desc')->paginate(15);
        return view('POS.Vendors.index', compact('vendors'));
    }

    /**
     * បង្ហាញផ្ទាំងសម្រាប់បញ្ចូលអ្នកផ្គត់ផ្គង់ថ្មី
     */
    public function create()
    {
        return view('POS.Vendors.create');
    }

    /**
     * រក្សាទុកទិន្នន័យចូលក្នុង Database
     */
    public function store(Request $request)
    {
        // ត្រួតពិនិត្យទិន្នន័យ
        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string',
        ]);

        // រក្សាទុក
        Vendor::create([
            'vendor_name' => $request->vendor_name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'address'     => $request->address,
        ]);

        return redirect()->route('vendors.index')->with('success', 'បន្ថែមអ្នកផ្គត់ផ្គង់ថ្មីបានជោគជ័យ!');
    }

    /**
     * បង្ហាញផ្ទាំងកែប្រែទិន្នន័យ (Edit)
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('POS.Vendors.edit', compact('vendor'));
    }

    /**
     * រក្សាទុកការកែប្រែទិន្នន័យ (Update)
     */
    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'phone'       => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'address'     => 'nullable|string',
        ]);

        $vendor->update([
            'vendor_name' => $request->vendor_name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'address'     => $request->address,
        ]);

        return redirect()->route('vendors.index')->with('success', 'កែប្រែព័ត៌មានអ្នកផ្គត់ផ្គង់បានជោគជ័យ!');
    }

    /**
     * លុបទិន្នន័យ (Destroy)
     */
    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'លុបទិន្នន័យអ្នកផ្គត់ផ្គង់បានជោគជ័យ!');
    }

    public function downloadTemplate()
    {
        $fileName = 'Vendor_Import_Template.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // ដាក់ BOM ដើម្បីឱ្យ Excel ស្គាល់អក្សរខ្មែរ (UTF-8) បានល្អ
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
            
            // ឈ្មោះ Column ស្ដង់ដារ (ហាមដូរពេល Import)
            fputcsv($file, ['Vendor Name', 'Phone', 'Email', 'Address']);
            
            // ទិន្នន័យឧទាហរណ៍ (ឱ្យបុគ្គលិកមើលជាគំរូ)
            fputcsv($file, ['MG Auto Supply', '012345678', 'mg@example.com', 'Phnom Penh']);
            fputcsv($file, ['LeapMotor Parts', '098765432', 'parts@leapmotor.com', 'Siem Reap']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ដំណើរការ Import ទិន្នន័យចូល Database
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048'
        ]);

        $file = $request->file('file');
        
        try {
            // អានឯកសារ CSV
            $fileHandle = fopen($file->getRealPath(), 'r');
            
            // រំលងជួរទី១ (ជួរ Header: Vendor Name, Phone, Email, Address)
            fgetcsv($fileHandle); 

            DB::beginTransaction();
            $count = 0;

            while (($row = fgetcsv($fileHandle, 1000, ',')) !== FALSE) {
                // ការពារជួរទទេ (Empty row)
                if(empty(trim($row[0]))) { 
                    continue; 
                }

                // បង្កើតទិន្នន័យថ្មីចូលក្នុង Database
                Vendor::create([
                    'vendor_name' => trim($row[0]), // $row[0] គឺ Column A
                    'phone'       => isset($row[1]) ? trim($row[1]) : null, // $row[1] គឺ Column B
                    'email'       => isset($row[2]) ? trim($row[2]) : null, // $row[2] គឺ Column C
                    'address'     => isset($row[3]) ? trim($row[3]) : null, // $row[3] គឺ Column D
                ]);
                $count++;
            }
            
            fclose($fileHandle);
            DB::commit();

            return redirect()->back()->with('success', "អ្នកបាន Import ទិន្នន័យ Vendor ចំនួន {$count} ក្រុមហ៊ុន ដោយជោគជ័យ!");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'មានបញ្ហាក្នុងការ Import ឯកសារ៖ ' . $e->getMessage());
        }
    }

}

