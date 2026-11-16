<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Modul 5 (Database Transaction) - Bagian 5.2: ganti method storeTransaction() ini dengan kode dari Notion
   public function storeTransaction(Request $request)
{
    $request->validate([
        'customer_id' =>'required|exists:customers,id',
        'product_id'  =>'required|exists:products,id',
        'qty'         =>'required|integer|min:1',
    ]);

    $product =Product::findOrFail($request->product_id);

    $qty =(int) $request->qty;
    $subtotal =$product->harga *$qty;
    $totalHarga =$subtotal;

    $userId =Auth::id() ??(User::first()->id ??1);

    try {
        DB::beginTransaction();

        // 1. Simpan transaksi utama
        $transactionId =DB::table('transactions')
            ->insertGetId([
                'customer_id' =>$request->customer_id,
                'user_id'     =>$userId,
                'tanggal'     =>now(),
                'total_harga' =>$totalHarga,
                'status'      =>'selesai',
                'created_at'  =>now(),
                'updated_at'  =>now(),
            ]);

        // 2. Simpan detail transaksi
        DB::table('transaction_details')->insert([
            'transaction_id' =>$transactionId,
            'product_id'     =>$product->id,
            'harga'          =>$product->harga,
            'qty'            =>$qty,
            'subtotal'       =>$subtotal,
            'created_at'     =>now(),
            'updated_at'     =>now(),
        ]);

        // 3. Simulasi error untuk menguji ROLLBACK
        if ($request->input('simulate_rollback') =='1') {
            throw new \Exception(
                'Simulasi error: transaksi sengaja dibatalkan.'
            );
        }

        // 4. Simpan seluruh perubahan
        DB::commit();

        return redirect()->back()->with(
            'success',
            "Pesanan #{$transactionId} berhasil disimpan."
        );

    } catch (\Throwable $e) {
        DB::rollBack();

        return redirect()->back()->with(
            'error',
            'Transaksi dibatalkan: ' . $e->getMessage()
        );
    }
}
}
