{{-- Tidak ada perubahan yang diperlukan di file ini. --}}
<div class="space-y-3">
    <div>
        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block">Uji Hak Akses</span>
        <span class="text-[11px] text-slate-500">Query dijalankan ke MySQL lalu di-rollback.</span>
    </div>

    @unless($privilegeIntegrated)
        <p class="neu-inset p-3 rounded-xl border border-amber-400/60 text-[11px] text-amber-800">
            Belum terintegrasi. Masih login ke MySQL sebagai <strong>{{ \App\Services\ModuleStatusService::mysqlUser() }}</strong>,
            seharusnya sebagai <strong>{{ strtolower($roleLabel) }}</strong>. Kerjakan Bagian 6.
        </p>
    @endunless

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach($privItems as $priv)
            @php
                [$badge, $color] = !$privilegeIntegrated
                    ? ['Belum', 'text-amber-500']
                    : ($priv['expected'] ? ['Boleh', 'text-emerald-500'] : ['Ditolak', 'text-rose-500']);
            @endphp
            <form action="{{ route('test.privilege', $priv['action']) }}" method="POST">
                @csrf
                <button type="submit" class="neu-pill w-full p-3 rounded-xl hover:bg-slate-50 text-left transition cursor-pointer flex flex-col justify-between h-full group">
                    <div class="flex items-center justify-between mb-1 gap-1">
                        <span class="font-bold text-xs text-slate-800 group-hover:text-indigo-600">{{ $priv['label'] }}</span>
                        <span class="{{ $color }} text-xs font-bold">{{ $badge }}</span>
                    </div>
                    <span class="text-[10px] text-slate-500 leading-tight">{{ $priv['desc'] }}</span>
                </button>
            </form>
        @endforeach
    </div>

    @if(session('privilege_test_result'))
        @php
            $testRes = session('privilege_test_result');
            $state = $testRes['status'] ?? ($testRes['allowed'] ? 'success' : 'danger');
            $theme = [
                'success' => ['border' => 'border-emerald-400/50', 'text' => 'text-emerald-800', 'pill' => 'text-emerald-600', 'label' => 'DIIZINKAN'],
                'danger'  => ['border' => 'border-rose-400/50',    'text' => 'text-rose-800',    'pill' => 'text-rose-600',    'label' => 'DITOLAK'],
                'pending' => ['border' => 'border-amber-400/60',   'text' => 'text-amber-800',   'pill' => 'text-amber-600',   'label' => 'BELUM'],
            ][$state] ?? ['border' => 'border-rose-400/50', 'text' => 'text-rose-800', 'pill' => 'text-rose-600', 'label' => 'DITOLAK'];
        @endphp
        <div class="neu-inset p-4 rounded-xl border {{ $theme['border'] }} space-y-2">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <span class="text-xs font-bold text-slate-800">{{ $testRes['action'] }}</span>
                <span class="text-[10px] font-extrabold {{ $theme['pill'] }}">{{ $theme['label'] }}</span>
            </div>
            <p class="text-xs {{ $theme['text'] }} leading-relaxed">{{ $testRes['message'] }}</p>
            <p class="text-[10px] font-mono text-slate-400">
                @if(!empty($testRes['sql'])) {{ $testRes['sql'] }} · @endif
                user {{ $testRes['mysql_user'] ?? '-' }}
            </p>
        </div>
    @endif
</div>
