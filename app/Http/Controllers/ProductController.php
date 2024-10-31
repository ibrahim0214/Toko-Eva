<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index() 
    {
        $products = Product::paginate(10);

       return view('products.index', compact('products'));
    }

    public function create() 
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'harga' => 'required | numeric',
            'foto' => 'required | image | mimes:jpeg,png,jpg'
            
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        };


        $foto = $request->file('foto');
        $foto->storeAs('public', $foto->hashName());
        

        Product::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'foto' => $foto->hashName()
        ]);

        return redirect()->route('products.index')->with('success', 'Add Product Success');
            
    }


    public function edit(Product $product) 
    {
        return view('products.edit', compact('product'));
    }
}
