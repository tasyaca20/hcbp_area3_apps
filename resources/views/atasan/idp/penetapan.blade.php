@extends('layouts.app', ['title' => 'Penetapan IDP - Atasan'])

@section('content')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                text: @json(session('success'))
            });
        </script>
    @endif
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 h-[220px] flex items-center">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full"
                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCjz6BR9t1xAm_4yTRtjUFCuOHqIkeE5EKFN9IU8hG8k_u3B7psxquSS6Suk591TY9q_Y27C-c3_8Z_HW-o9T4mcs-BSs8NcCHUUxWxaIBP5fVyRf74hO-H-p4ikb76omJMmeg7ue3VDxPp1T-0X1MDCB76X944-1OQyLGojPYj3yPCpw1wjdFvEbWlt83s8VQfKiYaIR8j8f1RfVnP8NMKIKcRo4783TuNbljKJLkY62hs9DzkeU81TZVA3E9SyXdF3Ck'); background-size: cover; background-position: center right;">
            </div>
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
        <div class="m-4 flex justify-end">
            <form method="GET" data-search class="flex items-center gap-2"><input name="search"
                    value="{{ request('search') }}" placeholder="Cari semua data..."
                    class="w-52 rounded-lg border-slate-300 text-sm"><button
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full min-w-[760px] text-sm tbl">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bawahan</th>
                        <th>NIP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rencana->groupBy('id_daftar_idp') as $idDaftar => $group)
                        @php
                            $first = $group->first();
                            $komp = $first->daftarIdp->bawahan?->jabatan?->kompetensi ?? collect();
                            $groupData = $group
                                ->map(
                                    fn($item) => [
                                        'id' => $item->id_rencana,
                                        'kode' => $item->kompetensi->kode_kompetensi,
                                        'kompetensi' => $item->kompetensi->nama_kompetensi,
                                        'p10' => $item->pembelajaran_10_persen,
                                        's20' => $item->social_learning_20_persen,
                                        'a70' => $item->action_learning_70_persen,
                                        'feedback' => $item->feedback_atasan,
                                    ],
                                )
                                ->values();
                            $kompData = [
                                'umum' => $komp
                                    ->where('jenis', 'umum')
                                    ->map(
                                        fn($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi],
                                    )
                                    ->values(),
                                'peran' => $komp
                                    ->where('jenis', 'peran')
                                    ->map(
                                        fn($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi],
                                    )
                                    ->values(),
                                'teknis_core' => $komp
                                    ->where('jenis', 'teknis_core')
                                    ->map(
                                        fn($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi],
                                    )
                                    ->values(),
                                'teknis_enabler' => $komp
                                    ->where('jenis', 'teknis_enabler')
                                    ->map(
                                        fn($k) => ['kode' => $k->kode_kompetensi, 'kompetensi' => $k->nama_kompetensi],
                                    )
                                    ->values(),
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-medium">{{ $first->daftarIdp->bawahan->nama ?? '-' }}</td>
                            <td>{{ $first->daftarIdp->bawahan->nip ?? '-' }}</td>
                            <td><span
                                    class="rounded px-2 py-1 text-xs font-medium {{ $first->status === 'Diajukan' ? 'bg-yellow-100 text-yellow-700' : 'bg-amber-100 text-amber-700' }}">{{ $first->status }}</span>
                            </td>
                            <td>
                                <button type="button"
                                    class="rounded-lg border border-[#31599b] px-3 py-1.5 text-xs font-semibold text-[#31599b] hover:bg-blue-50"
                                    data-group='@json($groupData)' data-komp='@json($kompData)'
                                    onclick="openReview(this)">Review</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-slate-400">Tidak ada usulan yang menunggu persetujuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-1">
            @include('components.pagination', ['paginator' => $rencana])
        </div>
    </div>

    <div id="reviewModal" data-modal
        class="hidden fixed inset-0 z-[100] items-center justify-center overflow-y-auto bg-slate-900/50 p-4 backdrop-blur-sm">
        <form id="reviewForm" method="POST"
            class="my-auto max-h-[calc(100vh-2rem)] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white shadow-xl">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-bold">Review Usulan IDP</h2>
                    <p id="reviewEmployee" class="text-sm text-slate-500"></p>
                </div>
                <button type="button" class="text-2xl text-slate-400" onclick="closeReview()">&times;</button>
            </div>
            <div class="p-6 space-y-5">
                <div class="border border-slate-200 rounded-xl p-5">
                    <p class="text-sm font-semibold mb-4">Daftar Kebutuhan Kompetensi Jabatan (KKJ) sesuai jenjang jabatan
                        saat ini:</p>
                    <div class="grid grid-cols-1 gap-4 text-xs md:grid-cols-2 md:gap-5">
                        <section class="flex min-w-0 flex-col">
                            <h2 class="mb-2 font-bold text-slate-800">SOFT COMPETENCY</h2>
                            <h3 class="mb-1 font-semibold text-slate-700">Kompetensi Utama</h3>
                            <div class="overflow-hidden rounded-lg border border-slate-300">
                                <table class="w-full table-fixed tbl-kkj">
                                    <colgroup>
                                        <col class="w-[7.5rem]">
                                        <col>
                                    </colgroup>
                                    <tbody id="reviewSoftMain" class="divide-y divide-slate-200"></tbody>
                                </table>
                            </div>
                        </section>
                        <section class="flex min-w-0 flex-col">
                            <h2 class="mb-2 hidden font-bold text-slate-800 md:block" aria-hidden="true">&nbsp;</h2>
                            <h3 class="mb-1 font-semibold text-slate-700">Kompetensi Peran</h3>
                            <div class="overflow-hidden rounded-lg border border-slate-300">
                                <table class="w-full table-fixed tbl-kkj">
                                    <colgroup>
                                        <col class="w-[7.5rem]">
                                        <col>
                                    </colgroup>
                                    <tbody id="reviewSoftRole" class="divide-y divide-slate-200"></tbody>
                                </table>
                            </div>
                        </section>
                        <section class="flex min-w-0 flex-col">
                            <h2 class="mb-2 font-bold text-slate-800">HARD COMPETENCY</h2>
                            <h3 class="mb-1 font-semibold text-slate-700">Kompetensi Teknis (Core)</h3>
                            <div class="overflow-hidden rounded-lg border border-slate-300">
                                <table class="w-full table-fixed tbl-kkj">
                                    <colgroup>
                                        <col class="w-[7.5rem]">
                                        <col>
                                    </colgroup>
                                    <tbody id="reviewHardCore" class="divide-y divide-slate-200"></tbody>
                                </table>
                            </div>
                        </section>
                        <section class="flex min-w-0 flex-col">
                            <h2 class="mb-2 hidden font-bold text-slate-800 md:block" aria-hidden="true">&nbsp;</h2>
                            <h3 class="mb-1 font-semibold text-slate-700">Kompetensi Teknis (Enabler)</h3>
                            <div class="overflow-hidden rounded-lg border border-slate-300">
                                <table class="w-full table-fixed tbl-kkj">
                                    <colgroup>
                                        <col class="w-[7.5rem]">
                                        <col>
                                    </colgroup>
                                    <tbody id="reviewHardEnabler" class="divide-y divide-slate-200"></tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
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
                        <tbody id="reviewPlanRows" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
                <input id="reviewStatus" type="hidden" name="status" value="" />
                <input type="hidden" name="kompetensi_data" id="kompetensiData" value="" />
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button type="button" class="px-4 py-2 text-sm font-semibold text-slate-600"
                    onclick="closeReview()">Batal</button>
                <button type="button" id="btnEdit"
                    class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white"
                    onclick="toggleEditMode()">Edit</button>
                <button type="button" id="btnSimpan"
                    class="hidden rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white"
                    onclick="submitReview('Revisi')">Simpan</button>
                <button type="button" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white"
                    onclick="submitReview('Disetujui')">Setujui</button>
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
                    return `<tr>
          <td>${index + 1}</td>
          <td><strong>${item.kode}</strong><br><span class="text-xs text-slate-500">${item.kompetensi}</span></td>
          <td><textarea name="p10_${item.id}" class="w-full rounded-lg border-slate-300 text-sm bg-amber-50" rows="2">${item.p10 || ''}</textarea></td>
          <td><textarea name="s20_${item.id}" class="w-full rounded-lg border-slate-300 text-sm bg-amber-50" rows="2">${item.s20 || ''}</textarea></td>
          <td><textarea name="a70_${item.id}" class="w-full rounded-lg border-slate-300 text-sm bg-amber-50" rows="2">${item.a70 || ''}</textarea></td>
        </tr>`;
                }
                return `<tr>
        <td>${index + 1}</td>
        <td><strong>${item.kode}</strong><br><span class="text-xs text-slate-500">${item.kompetensi}</span></td>
        <td class="whitespace-pre-line">${item.p10 || '-'}</td>
        <td class="whitespace-pre-line">${item.s20 || '-'}</td>
        <td class="whitespace-pre-line">${item.a70 || '-'}</td>
      </tr>`;
            }).join('');
        }

        function renderCompetencyTable(items) {
            return items.length ?
                items.map((item) =>
                    `<tr class="align-top"><td class="break-words px-2.5 py-2 font-mono text-[11px] leading-snug text-slate-600">${item.kode}</td><td class="break-words px-2.5 py-2 leading-relaxed text-slate-800">${item.kompetensi || '-'}</td></tr>`
                ).join('') :
                '<tr><td class="px-2.5 py-2 text-slate-400" colspan="2">Tidak ada data</td></tr>';
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

            Swal.fire({
                icon: 'question',
                text: status === 'Disetujui' ? 'Setujui usulan ini?' : 'Simpan perubahan?',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) reviewForm.submit();
            });
        }
    </script>
@endsection
