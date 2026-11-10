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
        return redirect()->back()->with('error', 'Bagian 5 belum dikerjakan: storeTransaction() masih placeholder.');
    }
}
