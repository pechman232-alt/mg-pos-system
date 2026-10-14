<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * បង្ហាញបញ្ជីទំនិញទាំងអស់
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // ✅ កែសម្រួលទីនេះ៖ ប្រើ 'branch' ឱ្យត្រូវនឹង Relationship ក្នុង Model Product របស់បង
        $query = Product::with(['user', 'branch']); 
    
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
    
        $products = $query->orderBy('id', 'desc')->paginate(20);
    
        // បញ្ជូនទិន្នន័យទៅ View
        return view('POS.Products.index', compact('products'));
    }

    public function create()
    {
        return view('POS.Products.create');
    }

    /**
     * រក្សាទុកទិន្នន័យ
     */
    public function store(Request $request)
    {
        // សម្រួលតម្លៃ price ឱ្យត្រូវជាមួយ validation
        $request->merge([
            'price' => $request->input('price') ?? $request->input('selling_price')
        ]);

        $request->validate([
            'item_code'   => 'required|unique:products,item_code',
            'item_name'   => 'required|string|max:255',
            'cost'        => 'required|numeric|min:0',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:255',
            'location_id' => 'required', // Validate location_id (សាខា)
            'description' => 'nullable|string',
            'stock_qty'   => 'nullable|numeric|min:0',
        ]);

        try {
            $product = new Product();
            $product->item_code   = $request->item_code;
            $product->item_name   = $request->item_name;
            $product->description = $request->description;
            $product->category    = $request->category;
            $product->location_id = $request->location_id; 
            $product->cost        = $request->cost;
            $product->price       = $request->price;
            $product->stock_qty   = $request->stock_qty ?? 0;
            $product->user_id     = Auth::id(); 
            $product->save();

            return redirect()->route('products.index')->with('success', 'បន្ថែមទំនិញថ្មីបានជោគជ័យ!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'មានបញ្ហាបច្ចេកទេស៖ ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('POS.Products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        // សម្រួលតម្លៃ price ឱ្យត្រូវជាមួយ validation
        $request->merge([
            'price' => $request->input('price') ?? $request->input('selling_price')
        ]);

        $request->validate([
            'item_code'   => 'required|unique:products,item_code,' . $id,
            'item_name'   => 'required|string|max:255',
            'cost'        => 'required|numeric|min:0',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:255',
            'location_id' => 'required',
            'description' => 'nullable|string',
            'stock_qty'   => 'nullable|numeric|min:0',
        ]);

        try {
            $product = Product::findOrFail($id);
            $product->item_code   = $request->item_code;
            $product->item_name   = $request->item_name;
            $product->description = $request->description;
            $product->category    = $request->category;
            $product->location_id = $request->location_id;
            $product->cost        = $request->cost;
            $product->price       = $request->price;
            $product->stock_qty   = $request->stock_qty ?? 0;
            $product->save();

            return redirect()->route('products.index')->with('success', 'កែប្រែទិន្នន័យទំនិញបានជោគជ័យ!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'មានបញ្ហាបច្ចេកទេស៖ ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // អាចដាក់លក្ខខណ្ឌបន្ថែម បើមិនចង់ឱ្យលុបទំនិញដែលមានសកម្មភាពហើយ
        $product->delete();
        
        return redirect()->route('products.index')->with('success', 'លុបទំនិញបានជោគជ័យ!');
    }
}