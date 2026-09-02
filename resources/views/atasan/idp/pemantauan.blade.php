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
  <div class="p-6 space-y-5">
    @forelse($rows as $row)
      @if($row->rencanaPengembangan->isNotEmpty())
      <div class="border border-slate-200 rounded-xl p-5">
        <div class="mb-3 flex items-center justify-between"><h3 class="font-semibold">{{ $row->bawahan->nama ?? '-' }} <span class="font-normal text-slate-500">({{ $row->bawahan->nip ?? '-' }})</span></h3><button class="text-sm font-semibold text-blue-600 hover:underline" data-id="{{ $row->id_daftar_idp }}" data-status="{{ $row->monitoring->status_perencanaan ?? 'Diajukan' }}" data-progress="{{ $row->monitoring->progress_percent ?? 0 }}" data-pembelajaran="{{ $row->monitoring->pembelajaran_10_persen ?? '' }}" data-social="{{ $row->monitoring->social_learning_20_persen ?? '' }}" data-experimental="{{ $row->monitoring->experimental_learning_70_persen ?? '' }}" onclick="editPemantauan(this)">Update</button></div>
        <div class="overflow-x-auto">
          <table class="min-w-[900px] w-full text-sm">
            <thead class="bg-slate-50"><tr><th class="px-3 py-3 text-left w-12">NO</th><th class="px-3 py-3 text-left w-[220px]">KOMPETENSI TEKNIS</th><th class="px-3 py-3 text-left">10% PEMBELAJARAN</th><th class="px-3 py-3 text-left">20% SOCIAL LEARNING</th><th class="px-3 py-3 text-left">70% ACTION LEARNING</th><th class="px-3 py-3 text-left">STATUS</th></tr></thead>
            <tbody>
              @foreach($row->rencanaPengembangan as $rencana)
              <tr class="border-t border-slate-200"><td class="px-3 py-2">{{ $loop->iteration }}</td><td class="px-3 py-2"><strong>{{ $rencana->kompetensi->kode_kompetensi }}</strong><br><span class="text-xs text-slate-500">{{ $rencana->kompetensi->nama_kompetensi }}</span></td><td class="px-3 py-2 whitespace-pre-line">{{ $rencana->pembelajaran_10_persen ?: '-' }}</td><td class="px-3 py-2 whitespace-pre-line">{{ $rencana->social_learning_20_persen ?: '-' }}</td><td class="px-3 py-2 whitespace-pre-line">{{ $rencana->action_learning_70_persen ?: '-' }}</td><td class="px-3 py-2"><span class="rounded bg-green-100 px-2 py-1 text-xs font-medium text-green-700">{{ $rencana->status }}</span></td></tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif
    @empty
      <p class="py-4 text-center text-slate-400">Belum ada data IDP.</p>
    @endforelse
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
      <label class="text-sm font-medium">Status Perencanaan<select id="statusPerencanaan" name="status_perencanaan" required class="mt-1 w-full rounded-lg border-slate-300"><option value="Draft">Draft</option><option value="Diajukan">Diajukan</option><option value="Revisi">Revisi</option><option value="Disetujui">Disetujui</option><option value="Berjalan">Berjalan</option><option value="Selesai">Selesai</option></select></label>
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
