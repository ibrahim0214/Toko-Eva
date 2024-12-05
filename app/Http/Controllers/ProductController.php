<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
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
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048' // Max size 2MB
        ]);
        
        $imagePath = null;
        if ($request->hasFile('foto')) {
            $imagePath = $request->file('foto')->store('images','public');
        }

        $product = Product::create([
            'nama' => $request->nama,
            'harga' => str_replace(".", "", $request->harga),
            'deskripsi' => $request->deskripsi,
            'foto' =>  $imagePath 
        ]);
        
            if($product){
                return redirect()->route('products.index')->with('success', 'Product added successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to upload image. Please try again.');
            }
        return redirect()->back()->with('error', 'No file selected for upload.');
    }

    public function edit(Product $product) 
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
        ]);

        $product->nama = $request->nama;
        $product->harga = str_replace(".", "", $request->harga);
        $product->deskripsi = $request->deskripsi;

        if ($request->file('foto')) {

            Storage::disk('local')->delete('public/'. $product->foto);
            $foto = $request->file('foto');
            $foto->storeAs('public', $foto->hashName());
            $product->foto = $foto->hashName();
        }

        $product->update();

        return redirect()->route('products.index')->with('success', 'Update Product Success');
    }


    public function destroy(Product $product) 
    {

    }
}