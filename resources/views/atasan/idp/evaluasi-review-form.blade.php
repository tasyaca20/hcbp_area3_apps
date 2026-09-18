@php($status = $bukti->status_atasan ?? 'pending')
@if($status === 'pending')
  <div class="mt-1 space-y-2">
    <form action="{{ route('atasan.idp.evaluasi.review', ['idp' => $row, 'bukti' => $bukti]) }}" method="POST" class="space-y-2">
      @csrf
      @method('PUT')
      <input type="hidden" name="status_atasan" value="revisi">
      <textarea name="catatan_revisi" rows="2" maxlength="2000" required placeholder="Instruksi revisi untuk bawahan" class="w-full min-w-0 rounded border border-slate-300 px-2 py-1 text-xs">{{ old('catatan_revisi') }}</textarea>
      @error('catatan_revisi')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
      <div class="flex gap-2"><button class="rounded bg-amber-500 px-2 py-1 text-xs font-semibold text-white hover:bg-amber-600">Kirim Revisi</button><button type="submit" form="setuju-{{ $bukti->id_evaluasi_bukti }}" class="rounded bg-green-600 px-2 py-1 text-xs font-semibold text-white hover:bg-green-700">Setuju</button></div>
    </form>
    <form id="setuju-{{ $bukti->id_evaluasi_bukti }}" action="{{ route('atasan.idp.evaluasi.review', ['idp' => $row, 'bukti' => $bukti]) }}" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" name="status_atasan" value="setuju">
    </form>
  </div>
@elseif($status === 'setuju')
  <span class="inline-block rounded bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Disetujui</span>
@else
  <span class="inline-block rounded bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">Perlu revisi</span>
  <p class="mt-1 whitespace-pre-line text-xs text-slate-600">{{ $bukti->catatan_revisi }}</p>
@endif
