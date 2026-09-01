@php($pageTitle = 'Pemantauan IDP')
@php($activeSection = 'idp')
@php($activePage = 'pemantauan')

@extends('layouts.app', ['title' => $pageTitle])

@section('content')
@if(session('success'))
<script>Swal.fire({icon:'success',text:@json(session('success'))});</script>
@endif
<div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
  <div class="absolute inset-0 z-0">
    <div class="w-full h-full" style="background-image:url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size:cover; background-position:center right;"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/85 to-transparent"></div>
  </div>
  <div class="relative z-10 px-8 max-w-xl">
    <h1 class="text-[27px] font-bold text-[#0a192f] mb-2 leading-tight">Pemantauan IDP</h1>
    <p class="text-slate-500 text-[15px]">Pantau progres rencana pengembangan bawahan Anda.</p>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
    <h2 class="text-lg font-bold">Progres IDP</h2>
    <span class="text-xs text-slate-400">Update progres bawahan</span>
  </div>
  <div class="overflow-x-auto">
    <table data-table-search="false" data-table-pagination="false" class="min-w-[1200px] w-full text-left text-xs">
      <thead class="bg-[#31599b] text-white">
        <tr>
          <th class="px-4 py-4 font-semibold">No.</th>
          <th class="px-4 py-4 font-semibold">Nama Bawahan</th>
          <th class="px-4 py-4 font-semibold">Jabatan</th>
          <th class="px-4 py-4 font-semibold">Status</th>
          <th class="px-4 py-4 font-semibold">Progress</th>
          <th class="px-4 py-4 font-semibold">10% Pembelajaran</th>
          <th class="px-4 py-4 font-semibold">20% Social</th>
          <th class="px-4 py-4 font-semibold">70% Action Learning</th>
          <th class="px-4 py-4 font-semibold">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($rows as $i => $row)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-4">{{ $i + 1 }}</td>
          <td class="px-4 py-4 font-medium">{{ $row->bawahan->nama ?? '-' }}</td>
          <td class="px-4 py-4">{{ $row->bawahan->jabatan->sebutan_jabatan ?? '-' }}</td>
          <td class="px-4 py-4"><span class="px-2 py-1 rounded text-xs font-medium {{ ($row->monitoring->status_perencanaan ?? '') === 'Disetujui' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $row->monitoring->status_perencanaan ?? 'Belum ada' }}</span></td>
          <td class="px-4 py-4">{{ $row->monitoring->progress_percent ?? 0 }}%</td>
          <td class="px-4 py-4">{{ $row->monitoring->pembelajaran_10_persen ?? '-' }}</td>
          <td class="px-4 py-4">{{ $row->monitoring->social_learning_20_persen ?? '-' }}</td>
          <td class="px-4 py-4">{{ $row->monitoring->experimental_learning_70_persen ?? '-' }}</td>
          <td class="px-4 py-4"><button class="text-blue-600 hover:underline" data-id="{{ $row->id_daftar_idp }}" data-status="{{ $row->monitoring->status_perencanaan ?? 'Diajukan' }}" data-progress="{{ $row->monitoring->progress_percent ?? 0 }}" data-pembelajaran="{{ $row->monitoring->pembelajaran_10_persen ?? '' }}" data-social="{{ $row->monitoring->social_learning_20_persen ?? '' }}" data-experimental="{{ $row->monitoring->experimental_learning_70_persen ?? '' }}" onclick="editPemantauan(this)">Update</button></td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada data IDP.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div id="editModal" data-modal class="hidden fixed inset-0 z-[100] items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm">
  <form id="editForm" method="POST" class="my-auto max-h-[calc(100vh-2rem)] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
    @csrf
    @method('PUT')
    <div class="mb-5 flex items-center justify-between">
      <h2 class="text-xl font-bold">Update Pemantauan IDP</h2>
      <button type="button" class="text-2xl text-slate-400" onclick="closePemantauan()">&times;</button>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
      <label class="text-sm font-medium">Status Perencanaan<select id="statusPerencanaan" name="status_perencanaan" required class="mt-1 w-full rounded-lg border-slate-300"><option value="Diajukan">Diajukan</option><option value="Revisi">Revisi</option><option value="Disetujui">Disetujui</option><option value="Berjalan">Berjalan</option><option value="Selesai">Selesai</option></select></label>
      <label class="text-sm font-medium">Progress (%)<input id="progressPercent" name="progress_percent" min="0" max="100" required class="mt-1 w-full rounded-lg border-slate-300" type="number" /></label>
      <label class="text-sm font-medium">10% Pembelajaran<textarea id="pembelajaran" name="pembelajaran_10_persen" class="mt-1 w-full rounded-lg border-slate-300"></textarea></label>
      <label class="text-sm font-medium">20% Social Learning<textarea id="social" name="social_learning_20_persen" class="mt-1 w-full rounded-lg border-slate-300"></textarea></label>
      <label class="text-sm font-medium md:col-span-2">70% Experimental Learning<textarea id="experimental" name="experimental_learning_70_persen" class="mt-1 w-full rounded-lg border-slate-300"></textarea></label>
    </div>
    <div class="mt-6 flex justify-end gap-3"><button type="button" class="px-4 py-2 text-sm font-semibold text-slate-600" onclick="closePemantauan()">Batal</button><button type="submit" class="rounded-lg bg-[#31599b] px-4 py-2 text-sm font-semibold text-white">Simpan</button></div>
  </form>
</div>
<script>
  const editModal = document.querySelector('#editModal');
  const editForm = document.querySelector('#editForm');
  function editPemantauan(button) {
    const data = button.dataset;
    editForm.action = `{{ url('/atasan/idp/pemantauan') }}/${data.id}`;
    document.querySelector('#statusPerencanaan').value = data.status;
    document.querySelector('#progressPercent').value = data.progress;
    document.querySelector('#pembelajaran').value = data.pembelajaran;
    document.querySelector('#social').value = data.social;
    document.querySelector('#experimental').value = data.experimental;
    editModal.classList.replace('hidden', 'flex');
  }
  function closePemantauan() { editModal.classList.replace('flex', 'hidden'); }
</script>
@endsection
