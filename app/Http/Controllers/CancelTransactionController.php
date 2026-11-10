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
        return redirect()->back()->with('error', 'Bagian 1 belum dikerjakan: cancelTransaction() masih placeholder.');
    }
}
