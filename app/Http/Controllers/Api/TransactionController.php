<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailNotify;
use App\Models\User;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::latest()->paginate(5);

        return new ResponseResource(true,'List Data Transaksi',$transactions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'order_id'      => 'required',
            'produk_id'     => 'required',
            'pembeli_id'    => 'required',
            'total_harga'   => 'required|numeric',
            'status'        => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(),422);
        }

        $transaction = Transaction::create([
            'order_id' => $request->order_id,
            'produk_id' => $request->produk_id,
            'pembeli_id' => $request->pembeli_id,
            'total_harga' => $request->total_harga,
            'status' => $request->status
        ]);

        $pembeli = User::findOrFail($request->pembeli_id);

        Mail::to($pembeli->email)->send(new MailNotify($pembeli->name,$request->order_id,$request->status));

        return new ResponseResource(true,'Data transaksi berhasil ditambahkan!',$transaction);
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        return new ResponseResource(true,'Detail Data Transaksi',$transaction);
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(),[
            'order_id'      => 'required',
            'produk_id'     => 'required',
            'pembeli_id'    => 'required',
            'total_harga'   => 'required|numeric',
            'status'        => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors(),422);
        }

        $transaction = Transaction::findOrFail($id);

        $transaction->update([
            'order_id' => $request->order_id,
            'produk_id' => $request->produk_id,
            'pembeli_id' => $request->pembeli_id,
            'total_harga' => $request->total_harga,
            'status' => $request->status
        ]);

        $pembeli = User::findOrFail($request->pembeli_id);
        Mail::to($pembeli->email)->send(new MailNotify($pembeli->name,$request->order_id,$request->status));

        return new ResponseResource(true,'Data transaksi berhasil diperbarui!',$transaction);
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction->delete();

        return new ResponseResource(true, 'Data transaksi berhasil dihapus!', null);
    }
}
