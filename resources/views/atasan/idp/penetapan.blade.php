@extends('layouts.app', ['title' => 'Penetapan IDP - Atasan'])

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
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Penetapan IDP</h1>
    <p class="text-slate-500 text-[15px]">Tinjau dan setujui usulan IDP bawahan.</p>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="px-6 py-5 border-b border-slate-200">
    <h1 class="text-xl font-bold">Penetapan IDP</h1>
    <p class="text-sm text-slate-500 mt-1">Usulan kompetensi bawahan yang menunggu persetujuan.</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[760px] text-sm">
      <thead class="bg-slate-50 text-slate-700">
        <tr>
          <th class="px-4 py-3 text-left">No</th>
          <th class="px-4 py-3 text-left">Bawahan</th>
          <th class="px-4 py-3 text-left">NIP</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($rencana->groupBy('id_daftar_idp') as $idDaftar => $group)
          @php
            $first = $group->first();
            $komp = $first->daftarIdp->bawahan?->jabatan?->kompetensi ?? collect();
            $groupData = $group->map(fn ($item) => [
              'id' => $item->id_rencana,
              'kode' => $item->kompetensi->kode_kompetensi,
              'kompetensi' => $item->kompetensi->nama_kompetensi,
              'p10' => $item->pembelajaran_10_persen,
              's20' => $item->social_learning_20_persen,
              'a70' => $item->action_learning_70_persen,
              'feedback' => $item->feedback_atasan,
            ])->values();
            $kompData = [
              'umum' => $komp->where('jenis', 'umum')->map(fn ($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi])->values(),
              'peran' => $komp->where('jenis', 'peran')->map(fn ($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi])->values(),
              'teknis_core' => $komp->where('jenis', 'teknis_core')->map(fn ($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi])->values(),
              'teknis_enabler' => $komp->where('jenis', 'teknis_enabler')->map(fn ($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi])->values(),
            ];
          @endphp
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 align-top">{{ $loop->iteration }}</td>
            <td class="px-4 py-3 font-medium align-top">{{ $first->daftarIdp->bawahan->nama ?? '-' }}</td>
            <td class="px-4 py-3 align-top">{{ $first->daftarIdp->bawahan->nip ?? '-' }}</td>
            <td class="px-4 py-3"><span class="rounded px-2 py-1 text-xs font-medium {{ $first->status === 'Diajukan' ? 'bg-yellow-100 text-yellow-700' : 'bg-amber-100 text-amber-700' }}">{{ $first->status }}</span></td>
            <td class="px-4 py-3 text-center">
              <button type="button" class="rounded-lg border border-[#31599b] px-3 py-1.5 text-xs font-semibold text-[#31599b] hover:bg-blue-50" data-group='@json($groupData)' data-komp='@json($kompData)' onclick="openReview(this)">Review</button>
            </td>
          </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">Tidak ada usulan yang menunggu persetujuan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div id="reviewModal" data-modal class="hidden fixed inset-0 z-[100] items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm">
  <form id="reviewForm" method="POST" class="my-auto max-h-[calc(100vh-2rem)] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white shadow-xl">
    @csrf
    @method('PUT')
    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
      <div><h2 class="text-lg font-bold">Review Usulan IDP</h2><p id="reviewEmployee" class="text-sm text-slate-500"></p></div>
      <button type="button" class="text-2xl text-slate-400" onclick="closeReview()">&times;</button>
    </div>
    <div class="p-6 space-y-5">
      <div class="border border-slate-200 rounded-xl p-5">
        <p class="text-sm font-semibold mb-4">Daftar Kebutuhan Kompetensi Jabatan (KKJ) sesuai jenjang jabatan saat ini:</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 text-xs">
          <section>
            <h2 class="font-bold">SOFT COMPETENCY</h2>
            <h3 class="font-semibold mt-2">Kompetensi Utama</h3>
            <table class="w-full border border-slate-400 mt-1"><tbody id="reviewSoftMain"></tbody></table>
          </section>
          <section class="pt-5"><h3 class="font-semibold">Kompetensi Peran</h3><table class="w-full border border-slate-400 mt-1"><tbody id="reviewSoftRole"></tbody></table></section>
          <section><h2 class="font-bold">HARD COMPETENCY</h2><h3 class="font-semibold mt-2">Kompetensi Teknis (Core)</h3><table class="w-full border border-slate-400 mt-1"><tbody id="reviewHardCore"></tbody></table></section>
          <section class="pt-5"><h3 class="font-semibold">Kompetensi Teknis (Enabler)</h3><table class="w-full border border-slate-400 mt-1"><tbody id="reviewHardEnabler"></tbody></table></section>
        </div>
      </div>
      <div class="overflow-x-auto border border-slate-200 rounded-xl">
        <table class="w-full text-sm">
          <thead class="bg-slate-50"><tr><th class="px-3 py-3 text-left w-12">NO</th><th class="px-3 py-3 text-left w-[220px]">KOMPETENSI TEKNIS</th><th class="px-3 py-3 text-left">10% PEMBELAJARAN</th><th class="px-3 py-3 text-left">20% SOCIAL LEARNING</th><th class="px-3 py-3 text-left">70% ACTION LEARNING</th></tr></thead>
          <tbody id="reviewPlanRows"></tbody>
        </table>
      </div>
      <input id="reviewStatus" type="hidden" name="status" value="" />
      <input type="hidden" name="kompetensi_data" id="kompetensiData" value="" />
    </div>
    <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
      <button type="button" class="px-4 py-2 text-sm font-semibold text-slate-600" onclick="closeReview()">Batal</button>
      <button type="button" id="btnEdit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white" onclick="toggleEditMode()">Edit</button>
      <button type="button" id="btnSimpan" class="hidden rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white" onclick="submitReview('Revisi')">Simpan</button>
      <button type="button" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white" onclick="submitReview('Disetujui')">Setujui</button>
    </div>
  </form>
</div>

<script>
  const reviewModal = document.querySelector('#reviewModal');
  const reviewForm = document.querySelector('#reviewForm');
  let editMode = false;
  let currentItems = [];

  function renderRows(items, editable) {
    return items.map((item, index) => {
      if (editable) {
        return `<tr class="border-t border-slate-200">
          <td class="px-3 py-2">${index + 1}</td>
          <td class="px-3 py-2"><strong>${item.kode}</strong><br><span class="text-xs text-slate-500">${item.kompetensi}</span></td>
          <td class="px-3 py-2"><textarea name="p10_${item.id}" class="w-full rounded-lg border-slate-300 text-sm bg-amber-50" rows="2">${item.p10 || ''}</textarea></td>
          <td class="px-3 py-2"><textarea name="s20_${item.id}" class="w-full rounded-lg border-slate-300 text-sm bg-amber-50" rows="2">${item.s20 || ''}</textarea></td>
          <td class="px-3 py-2"><textarea name="a70_${item.id}" class="w-full rounded-lg border-slate-300 text-sm bg-amber-50" rows="2">${item.a70 || ''}</textarea></td>
        </tr>`;
      }
      return `<tr class="border-t border-slate-200">
        <td class="px-3 py-2">${index + 1}</td>
        <td class="px-3 py-2"><strong>${item.kode}</strong><br><span class="text-xs text-slate-500">${item.kompetensi}</span></td>
        <td class="px-3 py-2 whitespace-pre-line">${item.p10 || '-'}</td>
        <td class="px-3 py-2 whitespace-pre-line">${item.s20 || '-'}</td>
        <td class="px-3 py-2 whitespace-pre-line">${item.a70 || '-'}</td>
      </tr>`;
    }).join('');
  }

  function renderCompetencyTable(items) {
    return items.length ? items.map((item) => `<tr><td class="border border-slate-400 px-2 py-1 w-28">${item.kode}</td><td class="border border-slate-400 px-2 py-1">${item.kompetensi}</td></tr>`).join('') : '<tr><td class="border border-slate-400 px-2 py-1 text-slate-400" colspan="2">Tidak ada data</td></tr>';
  }

  function openReview(button) {
    currentItems = JSON.parse(button.dataset.group || '[]');
    const komp = JSON.parse(button.dataset.komp || '{}');
    editMode = false;
    reviewForm.action = `{{ url('/atasan/idp/penetapan') }}/${currentItems[0].id}`;
    document.querySelector('#reviewEmployee').textContent = button.closest('tr').children[1].textContent;
    document.querySelector('#reviewPlanRows').innerHTML = renderRows(currentItems, false);
    document.querySelector('#reviewSoftMain').innerHTML = renderCompetencyTable(komp.umum || []);
    document.querySelector('#reviewSoftRole').innerHTML = renderCompetencyTable(komp.peran || []);
    document.querySelector('#reviewHardCore').innerHTML = renderCompetencyTable(komp.teknis_core || []);
    document.querySelector('#reviewHardEnabler').innerHTML = renderCompetencyTable(komp.teknis_enabler || []);
    document.querySelector('#btnEdit').classList.remove('hidden');
    document.querySelector('#btnSimpan').classList.add('hidden');
    reviewModal.classList.replace('hidden', 'flex');
  }

  function closeReview() {
    editMode = false;
    reviewModal.classList.replace('flex', 'hidden');
  }

  function toggleEditMode() {
    editMode = !editMode;
    document.querySelector('#reviewPlanRows').innerHTML = renderRows(currentItems, editMode);
    document.querySelector('#btnEdit').classList.toggle('hidden');
    document.querySelector('#btnSimpan').classList.toggle('hidden');
  }

  function submitReview(status) {
    document.querySelector('#reviewStatus').value = status;
    
    if (editMode) {
      const kompetensiData = {};
      currentItems.forEach((item) => {
        const p10El = document.querySelector(`[name="p10_${item.id}"]`);
        const s20El = document.querySelector(`[name="s20_${item.id}"]`);
        const a70El = document.querySelector(`[name="a70_${item.id}"]`);
        if (p10El || s20El || a70El) {
          kompetensiData[item.id] = {
            p10: p10El ? p10El.value : item.p10,
            s20: s20El ? s20El.value : item.s20,
            a70: a70El ? a70El.value : item.a70,
          };
        }
      });
      document.querySelector('#kompetensiData').value = JSON.stringify(kompetensiData);
    }
    
    Swal.fire({icon:'question',text:status === 'Disetujui' ? 'Setujui usulan ini?' : 'Simpan perubahan?',showCancelButton:true,confirmButtonText:'Ya',cancelButtonText:'Batal'}).then((result) => { if (result.isConfirmed) reviewForm.submit(); });
  }
</script>
@endsection
