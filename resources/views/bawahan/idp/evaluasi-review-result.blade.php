@foreach([10, 20, 70] as $jenis)
  @php($bukti = $evaluasi->bukti->where('jenis', $jenis)->first())
  <div class="mb-3 last:mb-0"><span class="font-semibold">{{ $jenis }}%:</span>
    @if(! $bukti)
      <span class="text-xs text-slate-500">Belum ada bukti</span>
    @elseif(($bukti->status_atasan ?? 'pending') === 'setuju')
      <span class="rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Disetujui</span>
    @elseif(($bukti->status_atasan ?? 'pending') === 'revisi')
      <div class="mt-1 rounded border border-amber-200 bg-amber-50 p-2"><span class="text-xs font-semibold text-amber-700">Perlu revisi</span><p class="mt-1 text-xs font-semibold text-slate-700">Instruksi atasan:</p><p class="whitespace-pre-line text-xs text-slate-600">{{ $bukti->catatan_revisi }}</p></div>
    @else
      <span class="text-xs text-slate-500">Menunggu tinjauan</span>
    @endif
  </div>
@endforeach
