<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // បន្ថែមសម្រាប់ការប្រើ Transaction

class ProductController extends Controller
{
    /**
     * បង្ហាញបញ្ជីទំនិញទាំងអស់
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Eager load 'user' ដើម្បីការពារ N+1 query problem
        $query = Product::with('user'); 
    
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
            'description' => 'nullable|string',
            'stock_qty'   => 'nullable|numeric|min:0',
        ]);

        try {
            $product = new Product();
            $product->item_code   = $request->item_code;
            $product->item_name   = $request->item_name;
            $product->description = $request->description;
            $product->category    = $request->category;
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

    /**
     * កែប្រែទិន្នន័យ
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->merge([
            'price' => $request->input('price') ?? $request->input('selling_price')
        ]);

        $request->validate([
            'item_code'   => 'required|unique:products,item_code,' . $product->id,
            'item_name'   => 'required|string|max:255',
            'cost'        => 'required|numeric|min:0',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'stock_qty'   => 'nullable|numeric|min:0',
        ]);

        try {
            $product->item_code   = $request->item_code;
            $product->item_name   = $request->item_name;
            $product->description = $request->description;
            $product->category    = $request->category;
            $product->cost        = $request->cost;
            $product->price       = $request->price;
            $product->stock_qty   = $request->stock_qty ?? 0;
            $product->user_id     = Auth::id(); // កត់ត្រាទុកអ្នកកែចុងក្រោយ
            $product->save(); 

            return redirect()->route('products.index')->with('success', 'កែប្រែព័ត៌មានទំនិញបានជោគជ័យ!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'មិនអាចកែប្រែបានទេ៖ ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // បើបងចង់ការពារកុំឱ្យលុបទំនិញដែលមានក្នុង Stock ច្រើន ឬមានក្នុង Sales
        // បងអាចឆែកលក្ខខណ្ឌនៅទីនេះបន្ថែម
        
        $product->delete();
        return redirect()->route('products.index')->with('success', 'លុបទំនិញបានជោគជ័យ!');
    }
}