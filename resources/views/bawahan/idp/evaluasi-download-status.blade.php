@php($deskripsiField = "deskripsi_realisasi_{$jenis}")
@php($status = $bukti?->status_atasan ?? 'pending')
@php($isLocked = $status === 'setuju')
@if($bukti)
  <div class="space-y-2">
    @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($bukti->file_path))
      <a class="text-xs font-semibold text-blue-600 hover:text-blue-800" href="{{ route('bawahan.idp.evaluasi.download', ['idp' => $row->id_daftar_idp, 'type' => $jenis]) }}">{{ $bukti->original_name ?? 'Download file' }}</a>
    @else
      <span class="text-xs font-semibold text-red-600">File tidak ditemukan, upload ulang</span>
    @endif
    @if($isLocked)
      <p class="whitespace-pre-line text-sm text-slate-600">{{ $evaluasi->$deskripsiField ?: 'Belum ada deskripsi' }}</p>
      <p class="text-xs font-semibold text-green-700">Disetujui atasan — tidak dapat diubah.</p>
    @else
      <form action="{{ route('bawahan.idp.evaluasi.store', $row) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
        @csrf
        <input type="hidden" name="evaluasi_id" value="{{ $evaluasi->id_evaluasi }}">
        <textarea name="{{ $deskripsiField }}" rows="3" maxlength="2000" placeholder="Deskripsi realisasi" class="w-full rounded border-slate-300 text-sm">{{ $evaluasi->$deskripsiField }}</textarea>
        <div class="flex items-center gap-2">
          <input type="file" name="bukti_{{ $jenis }}" accept="application/pdf,.pdf" class="max-w-[160px] text-xs">
          <button type="submit" class="rounded bg-[#31599b] px-3 py-1 text-xs font-semibold text-white hover:bg-[#264178]">Simpan</button>
        </div>
      </form>
    @endif
  </div>
@else
  <form action="{{ route('bawahan.idp.evaluasi.store', $row) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
    @csrf
    <input type="hidden" name="evaluasi_id" value="{{ $evaluasi->id_evaluasi }}">
    <textarea name="{{ $deskripsiField }}" rows="3" maxlength="2000" placeholder="Deskripsi realisasi" class="w-full rounded border-slate-300 text-sm">{{ $evaluasi->$deskripsiField }}</textarea>
    <div class="flex items-center gap-2">
      <input type="file" name="bukti_{{ $jenis }}" accept="application/pdf,.pdf" class="max-w-[160px] text-xs">
      <button type="submit" class="rounded bg-[#31599b] px-3 py-1 text-xs font-semibold text-white hover:bg-[#264178]">Simpan</button>
    </div>
  </form>
@endif
