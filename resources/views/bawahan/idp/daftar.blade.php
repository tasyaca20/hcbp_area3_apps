@extends('layouts.app', ['title' => 'IDP Saya - Bawahan'])

@section('content')
@if(session('success'))
<script>Swal.fire({icon:'success',text:@json(session('success'))});</script>
@endif
<div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
  <div class="absolute inset-0 z-0">
    <div class="w-full h-full" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 px-8 max-w-xl">
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">IDP Saya</h1>
    <p class="text-slate-500 text-[15px]">Kelola rencana pengembangan kompetensi Anda.</p>
  </div>
</div>

<div class="bawahan-section bg-white border border-slate-200 rounded-2xl p-6">
  <div class="mb-6">
    <h1 class="text-xl font-bold">Ketentuan IDP</h1>
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
    @php($idpLocked = $row->rencanaPengembangan->contains('status', 'Disetujui'))
    <form action="{{ route('bawahan.idp.rencana.store', $row) }}" method="POST" class="space-y-6">
      @csrf
      <input type="hidden" name="submit_action" value="simpan" id="submitAction{{ $row->id_daftar_idp }}">
      <div class="mb-4 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
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
        <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table data-table-search="false" data-table-pagination="false" class="w-full text-sm tbl">
            <thead><tr><th>NO</th><th>KOMPETENSI TEKNIS</th><th>10% PEMBELAJARAN</th><th>20% SOCIAL LEARNING</th><th>70% ACTION LEARNING</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
              @foreach($row->rencanaPengembangan->where('status', 'Disetujui') as $rencana)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $rencana->kompetensi->kode_kompetensi }}</strong><br><span class="text-xs text-slate-500">{{ $rencana->kompetensi->nama_kompetensi }}</span></td>
                <td class="whitespace-pre-line">{{ $rencana->pembelajaran_10_persen ?: '-' }}</td>
                <td class="whitespace-pre-line">{{ $rencana->social_learning_20_persen ?: '-' }}</td>
                <td class="whitespace-pre-line">{{ $rencana->action_learning_70_persen ?: '-' }}</td>
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
              <strong>{{ $rencana->kompetensi->kode_kompetensi }} â€” {{ $rencana->kompetensi->nama_kompetensi }}</strong>
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
            <table data-table-search="false" class="w-full mt-1 tbl-plain">
              <tbody class="divide-y divide-slate-100">
                @forelse($kompetensiList->where('jenis', 'umum') as $kom)
                <tr><td class="w-28">{{ $kom->kode_kompetensi }}</td><td >{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="text-slate-400" colspan="2">Tidak ada kompetensi utama</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
          <section class="pt-5">
            <h3 class="font-semibold">Kompetensi Peran</h3>
            <table data-table-search="false" class="w-full mt-1 tbl-plain">
              <tbody class="divide-y divide-slate-100">
                @forelse($kompetensiList->where('jenis', 'peran') as $kom)
                <tr><td class="w-28">{{ $kom->kode_kompetensi }}</td><td >{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="text-slate-400" colspan="2">Tidak ada kompetensi peran</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
          <section>
            <h2 class="font-bold">HARD COMPETENCY</h2>
            <h3 class="font-semibold mt-2">Kompetensi Teknis (Core)</h3>
            <table data-table-search="false" class="w-full mt-1 tbl-plain">
              <tbody class="divide-y divide-slate-100">
                @forelse($kompetensiList->where('jenis', 'teknis_core') as $kom)
                <tr><td class="w-28">{{ $kom->kode_kompetensi }}</td><td >{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="text-slate-400" colspan="2">Tidak ada kompetensi teknis core</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
          <section class="pt-5">
            <h3 class="font-semibold">Kompetensi Teknis (Enabler)</h3>
            <table data-table-search="false" class="w-full mt-1 tbl-plain">
              <tbody class="divide-y divide-slate-100">
                @forelse($kompetensiList->where('jenis', 'teknis_enabler') as $kom)
                <tr><td class="w-28">{{ $kom->kode_kompetensi }}</td><td >{{ $kom->nama_kompetensi }}</td></tr>
                @empty
                <tr><td class="text-slate-400" colspan="2">Tidak ada kompetensi teknis enabler</td></tr>
                @endforelse
              </tbody>
            </table>
          </section>
        </div>
      </div>

      <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-lg font-semibold">Form Usulan IDP</h2>
      </div>

      @if($idpLocked)
        <p class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">IDP telah disetujui atasan dan tidak dapat diubah.</p>
      @endif

      <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table data-table-search="false" data-table-pagination="false" class="w-full text-sm tbl">
          <thead>
            <tr>
              <th>NO</th>
              <th class="w-[220px]">KOMPETENSI TEKNIS</th>
              <th>10% PEMBELAJARAN</th>
              <th>20% SOCIAL LEARNING</th>
              <th>70% ACTION LEARNING</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @for($i = 0; $i < 5; $i++)
            @php($selected = $row->rencanaPengembangan[$i] ?? null)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>
                <select name="kompetensi[{{ $i }}][id_kompetensi]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" {{ $i < 3 ? 'required' : '' }} disabled>
                  <option value="">Pilih kompetensi</option>
                  @foreach($kompetensiTeknis as $kom)
                    <option value="{{ $kom->id_kompetensi }}" @selected($selected?->id_kompetensi === $kom->id_kompetensi)>{{ $kom->kode_kompetensi }} - {{ $kom->nama_kompetensi }}</option>
                  @endforeach
                </select>
              </td>
              <td><textarea name="kompetensi[{{ $i }}][pembelajaran_10_persen]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" disabled>{{ $selected->pembelajaran_10_persen ?? '' }}</textarea></td>
              <td><textarea name="kompetensi[{{ $i }}][social_learning_20_persen]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" disabled>{{ $selected->social_learning_20_persen ?? '' }}</textarea></td>
              <td><textarea name="kompetensi[{{ $i }}][action_learning_70_persen]" class="w-full rounded-lg border-slate-300 text-sm field-idp-{{ $row->id_daftar_idp }}" disabled>{{ $selected->action_learning_70_persen ?? '' }}</textarea></td>
            </tr>
            @endfor
          </tbody>
        </table>
      </div>

      @unless($idpLocked)
         <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:justify-end">
           <button type="button" id="btnEdit{{ $row->id_daftar_idp }}" class="w-full rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-amber-600 sm:w-auto" onclick="enableEdit({{ $row->id_daftar_idp }})">Edit IDP</button>
           <div id="actionButtons{{ $row->id_daftar_idp }}" class="flex flex-col gap-3 sm:flex-row" style="display: none;">
             <button type="button" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold sm:w-auto" onclick="setActionAndSubmit(this, 'simpan')">Simpan</button>
             <button type="button" class="w-full rounded-lg bg-[#31599b] px-4 py-2 text-sm font-semibold text-white sm:w-auto" onclick="setActionAndSubmit(this, 'kirim')">Kirim</button>
           </div>
         </div>
      @endunless
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
