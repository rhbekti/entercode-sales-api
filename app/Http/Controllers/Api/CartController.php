<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::latest()->paginate(5);

        return new ResponseResource(true,'List Data Keranjang',$carts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'produk_id' => 'required|numeric',
            'pembeli_id' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $cart = Cart::create([
            'produk_id' => $request->produk_id,
            'pembeli_id' => $request->pembeli_id
        ]);

        return new ResponseResource(true,'Produk berhasil ditambah ke keranjang',$cart);
    }

    public function show($id)
    {
        $cart = Cart::findOrFail($id);

        return new ResponseResource(true,'Detail data keranjang',$cart);
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(),[
            'produk_id' => 'required|numeric',
            'pembeli_id' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $cart = Cart::findOrFail($id);

        $cart->update([
            'produk_id' => $request->produk_id,
            'pembeli_id' => $request->pembeli_id
        ]);

        return new ResponseResource(true,'Data keranjang berhasil diperbarui',$cart);
    }

    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);

        $cart->delete();

        return new ResponseResource(true,'Data keranjang berhasil dihapus',null);
    }
}
