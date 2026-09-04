@php($deskripsiField = "deskripsi_realisasi_{$jenis}")
@if($bukti)
  <div class="space-y-2">
    @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($bukti->file_path))
      <a class="text-xs font-semibold text-blue-600 hover:text-blue-800" href="{{ route('bawahan.coaching.download', ['idp' => $row->id_daftar_idp, 'type' => $jenis, 'idRencana' => $plan->id_rencana]) }}">{{ $bukti->original_name ?? 'Download file' }}</a>
    @else
      <span class="text-xs font-semibold text-red-600">File tidak ditemukan, upload ulang</span>
    @endif
    <form action="{{ route('bawahan.coaching.bukti', $row) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
      @csrf
      <input type="hidden" name="plan_id" value="{{ $plan->id_rencana }}">
      <textarea name="{{ $deskripsiField }}" rows="3" maxlength="2000" placeholder="Deskripsi realisasi" class="w-full rounded border-slate-300 text-sm">{{ $plan->$deskripsiField }}</textarea>
      <div class="flex items-center gap-2">
        <input type="file" name="bukti_{{ $jenis }}" accept="application/pdf,.pdf" class="max-w-[160px] text-xs">
        <button type="submit" class="rounded bg-[#31599b] px-3 py-1 text-xs font-semibold text-white hover:bg-[#264178]">Simpan</button>
      </div>
    </form>
  </div>
@else
  <form action="{{ route('bawahan.coaching.bukti', $row) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
    @csrf
    <input type="hidden" name="plan_id" value="{{ $plan->id_rencana }}">
    <textarea name="{{ $deskripsiField }}" rows="3" maxlength="2000" placeholder="Deskripsi realisasi" class="w-full rounded border-slate-300 text-sm">{{ $plan->$deskripsiField }}</textarea>
    <div class="flex items-center gap-2">
      <input type="file" name="bukti_{{ $jenis }}" accept="application/pdf,.pdf" class="max-w-[160px] text-xs">
      <button type="submit" class="rounded bg-[#31599b] px-3 py-1 text-xs font-semibold text-white hover:bg-[#264178]">Simpan</button>
    </div>
  </form>
@endif
