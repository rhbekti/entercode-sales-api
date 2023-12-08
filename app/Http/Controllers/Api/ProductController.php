<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(5);
        
        return new ResponseResource(true, 'List Data Produk',$products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('foto');
        $image->storeAs('public/products', $image->hashName());

        $product = Product::create([
            'foto'     => $image->hashName(),
            'nama'     => $request->nama,
            'harga'   => $request->harga,
            'stok'   => $request->stok,
        ]);

        return new ResponseResource(true,'Data produk berhasil ditambahkan!',$product);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return new ResponseResource(true,'Detail Data Produk',$product);
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(),
        [
            'nama' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::findOrFail($id);

        if ($request->hasFile('foto')) {
            
            $image = $request->file('foto');
            $image->storeAs('public/products', $image->hashName());

            Storage::delete('public/products/'.basename($product->foto));

            $product->update([
                'foto'     => $image->hashName(),
                'nama'     => $request->nama,
                'harga'   => $request->harga,
                'stok'   => $request->stok,
            ]);
        }else{
            $product->update([
                'nama'     => $request->nama,
                'harga'   => $request->harga,
                'stok'   => $request->stok,
            ]);
        }

        return new ResponseResource(true, 'Data produk berhasil diperbarui!', $product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        Storage::delete('public/products/'.basename($product->foto));

        $product->delete();

        return new ResponseResource(true, 'Data Produk Berhasil Dihapus!', null);
    }
}
