@if($bukti)
  <div class="space-y-2">
    @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($bukti->file_path))
      <a class="text-xs font-semibold text-blue-600 hover:text-blue-800" href="{{ route('bawahan.coaching.download', ['idp' => $row->id_daftar_idp, 'type' => $jenis, 'idRencana' => $plan->id_rencana]) }}">{{ $bukti->original_name ?? 'Download file' }}</a>
    @else
      <span class="text-xs font-semibold text-red-600">File tidak ditemukan, upload ulang</span>
    @endif
    <form action="{{ route('bawahan.coaching.bukti', $row) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="plan_id" value="{{ $plan->id_rencana }}">
      <div class="flex items-center gap-2">
        <input type="file" name="bukti_{{ $jenis }}" accept="application/pdf,.pdf" class="max-w-[160px] text-xs">
        <button type="submit" class="rounded bg-[#31599b] px-3 py-1 text-xs font-semibold text-white hover:bg-[#264178]">Ganti</button>
      </div>
    </form>
  </div>
@else
  <form action="{{ route('bawahan.coaching.bukti', $row) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="plan_id" value="{{ $plan->id_rencana }}">
    <div class="flex items-center gap-2">
      <input type="file" name="bukti_{{ $jenis }}" accept="application/pdf,.pdf" required class="max-w-[160px] text-xs">
      <button type="submit" class="rounded bg-[#31599b] px-3 py-1 text-xs font-semibold text-white hover:bg-[#264178]">Upload</button>
    </div>
  </form>
@endif
