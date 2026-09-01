@extends('layouts.app', ['title' => 'IDP Saya - Bawahan'])

@section('content')
@if(session('success'))
<script>Swal.fire({icon:'success',text:@json(session('success'))});</script>
@endif
<div class="bawahan-section bg-white border border-slate-200 rounded-2xl p-6">
  <div class="mb-6">
    <h1 class="text-xl font-bold">Ketentuan IDP - Bawahan</h1>
    <ol class="list-decimal ml-6 mt-2 text-sm space-y-1 text-slate-700">
      <li>Silahkan memilih 3 s.d 5 kompetensi teknis (core/enabler) yang akan dikembangkan.</li>
      <li>Tuliskan inisiatif / rencana aktivitas yang dalam pelaksanaan pengembangan diri.</li>
      <li>Klik "Simpan" untuk menyimpan data yang telah di-input.</li>
      <li>Klik "Kirim" untuk meminta persetujuan Atasan Langsung.</li>
    </ol>
  </div>

  @forelse($rows as $row)
    @php($kompetensiList = $row->bawahan?->jabatan?->kompetensi ?? collect())
    @php($kompetensiTeknis = $kompetensiList->whereIn('jenis', ['teknis_core', 'teknis_enabler']))
    <form action="{{ route('bawahan.idp.rencana.store', $row) }}" method="POST" class="space-y-6">
      @csrf
      <input type="hidden" name="submit_action" value="simpan" id="submitAction{{ $row->id_daftar_idp }}">
      <div class="grid grid-cols-2 gap-4 text-sm mb-4">
        <div><strong>Nama:</strong> {{ $row->bawahan->nama ?? '-' }}</div>
        <div><strong>Nama Atasan:</strong> {{ $row->atasan->nama ?? '-' }}</div>
        <div><strong>NIP:</strong> {{ $row->bawahan->nip ?? '-' }}</div>
        <div><strong>NIP Atasan:</strong> {{ $row->atasan->nip ?? '-' }}</div>
        <div><strong>Jabatan:</strong> {{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</div>
        <div><strong>Jabatan Atasan:</strong> {{ $row->atasan->jabatan->sebutan_jabatan ?? '-' }}</div>
        <div><strong>Unit Induk:</strong> {{ $row->bawahan->unit_induk ?? '-' }}</div>
        <div><strong>Periode:</strong> {{ $row->periode_idp }}</div>
      </div>

      @if($row->rencanaPengembangan->where('status', 'Disetujui')->isNotEmpty())
      <div class="border border-emerald-200 rounded-xl p-5">
        <h2 class="font-semibold mb-3">Hasil IDP Disetujui Atasan</h2>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50"><tr><th class="px-3 py-3 text-left w-12">NO</th><th class="px-3 py-3 text-left">KOMPETENSI TEKNIS</th><th class="px-3 py-3 text-left">10% PEMBELAJARAN</th><th class="px-3 py-3 text-left">20% SOCIAL LEARNING</th><th class="px-3 py-3 text-left">70% ACTION LEARNING</th></tr></thead>
            <tbody>
              @foreach($row->rencanaPengembangan->where('status', 'Disetujui') as $rencana)
              <tr class="border-t border-slate-200">
                <td class="px-3 py-2">{{ $loop->iteration }}</td>
                <td class="px-3 py-2"><strong>{{ $rencana->kompetensi->kode_kompetensi }}</strong><br><span class="text-xs text-slate-500">{{ $rencana->kompetensi->nama_kompetensi }}</span></td>
                <td class="px-3 py-2 whitespace-pre-line">{{ $rencana->pembelajaran_10_persen ?: '-' }}</td>
                <td class="px-3 py-2 whitespace-pre-line">{{ $rencana->social_learning_20_persen ?: '-' }}</td>
                <td class="px-3 py-2 whitespace-pre-line">{{ $rencana->action_learning_70_persen ?: '-' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif

      @php($revisiItems = $row->rencanaPengembangan->where('direvisi_oleh_atasan', true))
      @if($revisiItems->isNotEmpty())
      <div class="border border-amber-300 bg-amber-50/50 rounded-xl p-5">
        <h2 class="font-semibold text-amber-800 mb-3">IDP Perlu Perbaikan (Direvisi Atasan)</h2>
        <p class="text-xs text-amber-700 mb-3">Atasan telah mengubah/mengoreksi isi dari kolom pembelajaran berikut. Silakan cek dan sesuaikan pada form usulan di bawah:</p>
        <div class="space-y-3">
          @foreach($revisiItems as $rencana)
          <div class="rounded-lg border border-amber-200 bg-white p-3 text-sm">
            <div class="flex justify-between gap-3">
              <strong>{{ $rencana->kompetensi->kode_kompetensi }} — {{ $rencana->kompetensi->nama_kompetensi }}</strong>
              <span class="rounded px-2 py-1 text-xs bg-amber-100 text-amber-700 font-semibold">Status: Revisi</span>
            </div>
            <div class="mt-2 space-y-1 text-xs text-slate-700">
              @if($rencana->pembelajaran_10_persen)
                <div><span class="font-medium text-slate-500">10% Pembelajaran:</span> {{ $rencana->pembelajaran_10_persen }}</div>
              @endif
              @if($rencana->social_learning_20_persen)
                <div><span class="font-medium text-slate-500">20% Social Learning:</span> {{ $rencana->social_learning_20_persen }}</div>
              @endif
              @if($rencana->action_learning_70_persen)
                <div><span class="font-medium text-slate-500">70% Action Learning:</span> {{ $rencana->action_learning_70_persen }}</div>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      <div class="border border-slate-300 rounded-xl p-5">
        <p class="text-sm font-semibold mb-4">Daftar Kebutuhan Kompetensi Jabatan (KKJ) sesuai jenjang jabatan saat ini:</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 text-xs">
          <section>
            <h2 class="font-bold">SOFT COMPETENCY</h2>
            <h3 class="font-semibold mt-2">Kompetensi Utama</h3>
            <table class="w-full border border-slate-400 mt-1">
              <tbody>
                @forelse($kompetensiList->where('jenis', 'umum') as $kom)
                <tr><td class="border border-slate-400 px-2 py-1 w-28">{{ $kom->kode_kompetensi }}</td><td class="border border-slate-400 px-2 py-1">{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="border border-slate-400 px-2 py-1 text-slate-400" colspan="2">Tidak ada kompetensi utama</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
          <section class="pt-5">
            <h3 class="font-semibold">Kompetensi Peran</h3>
            <table class="w-full border border-slate-400 mt-1">
              <tbody>
                @forelse($kompetensiList->where('jenis', 'peran') as $kom)
                <tr><td class="border border-slate-400 px-2 py-1 w-28">{{ $kom->kode_kompetensi }}</td><td class="border border-slate-400 px-2 py-1">{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="border border-slate-400 px-2 py-1 text-slate-400" colspan="2">Tidak ada kompetensi peran</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
          <section>
            <h2 class="font-bold">HARD COMPETENCY</h2>
            <h3 class="font-semibold mt-2">Kompetensi Teknis (Core)</h3>
            <table class="w-full border border-slate-400 mt-1">
              <tbody>
                @forelse($kompetensiList->where('jenis', 'teknis_core') as $kom)
                <tr><td class="border border-slate-400 px-2 py-1 w-28">{{ $kom->kode_kompetensi }}</td><td class="border border-slate-400 px-2 py-1">{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="border border-slate-400 px-2 py-1 text-slate-400" colspan="2">Tidak ada kompetensi teknis core</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
          <section class="pt-5">
            <h3 class="font-semibold">Kompetensi Teknis (Enabler)</h3>
            <table class="w-full border border-slate-400 mt-1">
              <tbody>
                @forelse($kompetensiList->where('jenis', 'teknis_enabler') as $kom)
                <tr><td class="border border-slate-400 px-2 py-1 w-28">{{ $kom->kode_kompetensi }}</td><td class="border border-slate-400 px-2 py-1">{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="border border-slate-400 px-2 py-1 text-slate-400" colspan="2">Tidak ada kompetensi teknis enabler</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
        </div>
      </div>

      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Form Usulan IDP</h2>
      </div>

      <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table data-table-search="false" data-table-pagination="false" class="w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-3 py-3 text-left w-12">NO</th>
              <th class="px-3 py-3 text-left w-[220px]">KOMPETENSI TEKNIS</th>
              <th class="px-3 py-3 text-left">10% PEMBELAJARAN</th>
              <th class="px-3 py-3 text-left">20% SOCIAL LEARNING</th>
              <th class="px-3 py-3 text-left">70% ACTION LEARNING</th>
            </tr>
          </thead>
          <tbody>
            @for($i = 0; $i < 5; $i++)
            @php($selected = $row->rencanaPengembangan[$i] ?? null)
            <tr class="border-t border-slate-200">
              <td class="px-3 py-2">{{ $i + 1 }}</td>
              <td class="px-3 py-2">
                <select name="kompetensi[{{ $i }}][id_kompetensi]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" {{ $i < 3 ? 'required' : '' }} disabled>
                  <option value="">Pilih kompetensi</option>
                  @foreach($kompetensiTeknis as $kom)
                    <option value="{{ $kom->id_kompetensi }}" @selected($selected?->id_kompetensi === $kom->id_kompetensi)>{{ $kom->kode_kompetensi }} - {{ $kom->nama_kompetensi }}</option>
                  @endforeach
                </select>
              </td>
              <td class="px-3 py-2"><textarea name="kompetensi[{{ $i }}][pembelajaran_10_persen]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" disabled>{{ $selected->pembelajaran_10_persen ?? '' }}</textarea></td>
              <td class="px-3 py-2"><textarea name="kompetensi[{{ $i }}][social_learning_20_persen]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" disabled>{{ $selected->social_learning_20_persen ?? '' }}</textarea></td>
              <td class="px-3 py-2"><textarea name="kompetensi[{{ $i }}][action_learning_70_persen]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" disabled>{{ $selected->action_learning_70_persen ?? '' }}</textarea></td>
            </tr>
            @endfor
          </tbody>
        </table>
      </div>

      <div class="flex justify-end gap-3 mt-4">
        <button type="button" id="btnEdit{{ $row->id_daftar_idp }}" class="rounded-lg bg-amber-500 hover:bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition-colors" onclick="enableEdit({{ $row->id_daftar_idp }})">Edit IDP</button>
        <div id="actionButtons{{ $row->id_daftar_idp }}" class="flex gap-3" style="display: none;">
          <button type="button" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold" onclick="setActionAndSubmit(this, 'simpan')">Simpan</button>
          <button type="button" class="rounded-lg bg-[#31599b] px-4 py-2 text-sm font-semibold text-white" onclick="setActionAndSubmit(this, 'kirim')">Kirim</button>
        </div>
      </div>
    </form>
  @empty
    <p class="text-slate-400">Belum ada IDP.</p>
  @endforelse
</div>

<script>
  function enableEdit(id) {
    const fields = document.querySelectorAll('.field-idp-' + id);
    fields.forEach(field => field.removeAttribute('disabled'));
    document.getElementById('btnEdit' + id).style.display = 'none';
    document.getElementById('actionButtons' + id).style.display = 'flex';
  }

  function setActionAndSubmit(button, action) {
    const form = button.closest('form');
    form.querySelector('input[name="submit_action"]').value = action;
    const valid = [...form.querySelectorAll('select[required]')].every((el) => el.value);
    if (!valid) {
      Swal.fire({icon:'warning',text:'Pilih minimal 3 kompetensi teknis.'});
      return;
    }
    Swal.fire({
      icon: 'question',
      text: action === 'kirim' ? 'Kirim ke atasan?' : 'Simpan rencana IDP?'
    }).then((result) => { if (result.isConfirmed) form.submit(); });
  }
</script>
@endsection
