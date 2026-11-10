@extends('layouts.app')

@section('title', 'TokoKita - Praktikum MSBD')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="neu-card p-6 sm:p-7">
            <div class="space-y-5">
                @include('partials.module-status', ['label' => '1. Transaksi (Commit / Rollback)', 'title' => 'Buat Pesanan', 'status' => $moduleStatus[1]])

                <form action="{{ route('transactions.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Pelanggan</label>
                        <select name="customer_id" required class="w-full text-xs font-medium text-slate-700 neu-input-inset rounded-xl p-3 outline-none border-0">
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Produk</label>
                            <select name="product_id" required class="w-full text-xs font-medium text-slate-700 neu-input-inset rounded-xl p-3 outline-none border-0">
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }} (Rp {{ number_format($p->harga, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Jumlah</label>
                            <input type="number" name="qty" value="1" min="1" max="10" required class="w-full text-xs font-semibold text-slate-700 neu-input-inset rounded-xl p-3 outline-none border-0 text-center">
                        </div>
                    </div>
                    <label class="neu-inset p-3.5 rounded-xl flex items-start space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="simulate_rollback" value="1" class="rounded border-slate-300 text-blue-500 focus:ring-0 h-4 w-4 mt-0.5 neu-pill">
                        <div>
                            <span class="text-xs font-bold text-slate-700 block">Uji Rollback</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5">Paksa error agar data tidak tersimpan.</span>
                        </div>
                    </label>
                    <button type="submit" class="w-full neu-btn-primary text-white text-xs font-bold py-3.5 px-4 rounded-xl">
                        Proses Pesanan
                    </button>
                </form>
            </div>
        </div>

        <div class="neu-card p-6 sm:p-7">
            <div class="space-y-5">
                @include('partials.module-status', ['label' => '2. Stored Procedure', 'title' => 'Batalkan Pesanan', 'status' => $moduleStatus[2]])

                @if($activeTransactions->count() > 0)
                    <form id="cancelForm" method="POST" onsubmit="this.action='/transactions/' + document.getElementById('cancel_id').value + '/cancel';" class="space-y-3.5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Pesanan</label>
                            <select id="cancel_id" required class="w-full text-xs font-medium text-slate-700 neu-input-inset rounded-xl p-3 outline-none border-0">
                                @foreach($activeTransactions as $at)
                                    <option value="{{ $at->id }}">ORD-{{ str_pad($at->id, 3, '0', STR_PAD_LEFT) }} · {{ $at->customer->nama ?? '-' }} · Rp {{ number_format($at->total_harga, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">Alasan</label>
                            <input type="text" name="alasan" required placeholder="Contoh: stok habis" class="w-full text-xs font-medium text-slate-700 neu-input-inset rounded-xl p-3 outline-none border-0">
                        </div>
                        <button type="submit" class="w-full neu-btn-danger text-rose-600 text-xs font-bold py-3 px-4 rounded-xl">
                            Batalkan Pesanan
                        </button>
                    </form>
                @else
                    <p class="neu-inset p-4 text-center text-xs text-slate-400">Tidak ada pesanan aktif.</p>
                @endif

                <div>
                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block mb-2">log_pembatalan</span>
                    <div class="space-y-2 max-h-24 overflow-y-auto pr-1">
                        @forelse($cancellationLogs as $cl)
                            <div class="neu-inset p-2.5 text-xs flex justify-between items-center gap-2">
                                <span class="text-slate-700"><strong class="font-mono text-slate-900">ORD-{{ str_pad($cl->transaksi_id, 3, '0', STR_PAD_LEFT) }}</strong>: {{ $cl->alasan }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $cl->dibatalkan_oleh }}</span>
                            </div>
                        @empty
                            <p class="neu-inset p-3 text-center text-[11px] text-slate-400 italic">Belum ada data.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="neu-card p-6 sm:p-7">
            <div class="space-y-5">
                @include('partials.module-status', ['label' => '3. Database View', 'title' => 'Transaksi Sukses', 'status' => $moduleStatus[3]])

                <div class="neu-inset p-1 overflow-x-auto">
                    <table class="min-w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="text-slate-500 border-b border-slate-300/50">
                                <th class="px-3.5 py-3 font-bold">Order</th>
                                <th class="px-3.5 py-3 font-bold">Pelanggan</th>
                                <th class="px-3.5 py-3 font-bold">Kasir</th>
                                <th class="px-3.5 py-3 font-bold">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/50 text-slate-700">
                            @forelse($viewTransactions as $vt)
                                <tr>
                                    <td class="px-3.5 py-2.5 font-mono font-bold text-slate-900">ORD-{{ str_pad($vt->transaksi_id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-3.5 py-2.5 font-medium">{{ $vt->nama_customer }}</td>
                                    <td class="px-3.5 py-2.5 text-slate-500">{{ $vt->nama_kasir }}</td>
                                    <td class="px-3.5 py-2.5 font-bold text-slate-900">Rp {{ number_format($vt->total_harga, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3.5 py-6 text-center text-slate-400">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="neu-card p-6 sm:p-7">
            <div class="space-y-5">
                @include('partials.module-status', ['label' => '4. Database Function', 'title' => 'Omzet Penjualan', 'status' => $moduleStatus[4]])

                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['Hari Ini',   'calculate_today_revenue()', $stats['todayRevenue'], $stats['todayTransactions']],
                        ['Bulan Ini',  'calculate_month_revenue()', $stats['monthRevenue'], $stats['monthTransactions']],
                        ['Total',      'calculate_total_revenue()', $stats['totalRevenue'], null],
                        ['Kasir Ini',  'calculate_user_revenue()',  $stats['userRevenue'],  null],
                    ] as [$name, $fn, $value, $count])
                        <div class="neu-card p-4">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $name }}</span>
                            <span class="text-[10px] text-blue-500 font-mono">{{ $fn }}</span>
                            <span class="text-lg font-extrabold text-slate-800 block mt-2">Rp {{ number_format($value, 0, ',', '.') }}</span>
                            @if(!is_null($count))
                                <span class="text-[10px] text-slate-500">{{ $count }} pesanan</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="neu-card p-6 sm:p-7">
            <div class="space-y-5">
                @include('partials.module-status', ['label' => '5. Database Trigger', 'title' => 'Log Aktivitas', 'status' => $moduleStatus[5]])

                <div class="space-y-2.5 max-h-52 overflow-y-auto pr-1">
                    @forelse($triggerLogs as $tl)
                        <div class="neu-inset p-3 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-blue-600 font-mono text-[10px] neu-pill px-2 py-0.5 rounded">{{ $tl->aksi }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $tl->created_at }}</span>
                            </div>
                            <p class="text-slate-700 font-medium">{{ $tl->keterangan }}</p>
                        </div>
                    @empty
                        <p class="neu-inset p-4 text-center text-xs text-slate-400">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="neu-card p-6 sm:p-7">
            <div class="space-y-5">
                @include('partials.module-status', ['label' => '6. User Management & Grant', 'title' => 'Hak Akses Database', 'status' => $moduleStatus[6]])

                <div class="space-y-3 text-[11px]">
                    <div class="neu-inset p-3.5 flex items-center justify-between gap-2">
                        <div>
                            <span class="font-bold text-slate-800 block">Kasir</span>
                            <span class="text-slate-600">SELECT, INSERT, UPDATE · tanpa DELETE</span>
                        </div>
                        <span class="neu-pill px-2 py-0.5 rounded font-mono font-bold text-[10px] text-blue-600">mysql_kasir</span>
                    </div>
                    <div class="neu-inset p-3.5 flex items-center justify-between gap-2">
                        <div>
                            <span class="font-bold text-slate-800 block">Admin</span>
                            <span class="text-slate-600">ALL PRIVILEGES</span>
                        </div>
                        <span class="neu-pill px-2 py-0.5 rounded font-mono font-bold text-[10px] text-indigo-600">mysql_admin</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
