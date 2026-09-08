@php($deskripsiField = "deskripsi_realisasi_{$jenis}")
@if($bukti)
  <a class="text-xs font-semibold text-blue-600 hover:text-blue-800" href="{{ route('atasan.coaching.download', ['idp' => $row->id_daftar_idp, 'type' => $jenis, 'idRencana' => $plan->id_rencana]) }}">{{ $bukti->original_name ?? 'Download file' }}</a>
@endif
<p class="mt-2 whitespace-pre-line text-xs text-slate-600">{{ $plan->$deskripsiField ?: 'Belum ada deskripsi' }}</p>
