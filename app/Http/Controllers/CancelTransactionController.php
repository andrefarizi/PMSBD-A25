<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CancelTransactionController extends Controller
{
    // Modul 1 (Stored Procedure) - Bagian 1.5: ganti method cancelTransaction() ini dengan kode dari Notion
    public function cancelTransaction(Request $request, $id)
{
    $request->validate([
        'alasan' =>'required|string|max:255'
    ]);

    try {
        DB::beginTransaction();

        DB::table('transactions')
            ->where('id', $id)
            ->update([
                'status' =>'dibatalkan',
                'updated_at' =>now()
            ]);

        $operatorName =Auth::check()
            ?Auth::user()->name
            :'Kasir Toko';

        DB::statement(
            'CALL log_pembatalan_procedure(?, ?, ?)',
            [
                $id,
                $request->alasan,
                $operatorName
            ]
        );

        DB::commit();

        return redirect()->back()
            ->with(
                'success',
                "Pesanan #{$id} berhasil dibatalkan melalui Stored Procedure."
            );

    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()
            ->with(
                'error',
                'Gagal membatalkan pesanan: ' . $e->getMessage()
            );
    }
}
}
