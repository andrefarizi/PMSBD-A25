<div class="flex items-center justify-between flex-wrap gap-2">
    <span class="neu-pill px-3.5 py-1.5 rounded-full text-[11px] font-bold text-blue-600 uppercase tracking-wider">{{ $label }}</span>
    @if($status['is_active'])
        <span class="neu-pill px-3 py-1 rounded-full text-[11px] font-bold text-emerald-600 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aktif</span>
    @else
        <span class="neu-pill px-3 py-1 rounded-full text-[11px] font-bold text-rose-500 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Belum Aktif</span>
    @endif
</div>

<h2 class="text-xl font-bold text-slate-800 tracking-tight">{{ $title }}</h2>

<p class="neu-inset px-3.5 py-2.5 text-[11px] leading-relaxed {{ $status['is_active'] ? 'text-emerald-700' : 'text-slate-500' }}">
    {{ $status['message'] }}
</p>
