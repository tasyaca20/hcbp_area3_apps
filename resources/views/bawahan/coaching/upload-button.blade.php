@if($bukti)
<a class="mb-2 block text-xs text-blue-600 hover:text-blue-800" href="{{ route('bawahan.coaching.download', ['idp' => $row->id_daftar_idp, 'type' => $jenis, 'idRencana' => $plan->id_rencana]) }}">Download: {{ $bukti->original_name ?? 'file.pdf' }}</a>
@endif
<form action="{{ route('bawahan.coaching.bukti', $row) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
  @csrf
  <input type="hidden" name="plan_id" value="{{ $plan->id_rencana }}">
  <input type="file" name="bukti_{{ $jenis }}" accept="application/pdf,.pdf" required class="block max-w-[180px] text-xs">
  <button type="submit" class="rounded bg-[#31599b] px-3 py-1 text-xs font-semibold text-white hover:bg-[#264178]">{{ $bukti ? 'Ganti File' : 'Upload File' }}</button>
</form>