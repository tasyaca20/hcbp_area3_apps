<?php

namespace App\Http\Controllers;

use App\Exports\TemplateImportIdpAtasanExport;
use App\Models\EvaluasiIDP;
use App\Models\IDP;
use App\Models\Jabatan;
use App\Models\MonitoringIDP;
use App\Models\Pengguna;
use App\Models\RencanaPengembanganIDP;
use App\Models\CoachingBukti;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;


class IdpController extends Controller
{
    public function daftar()
    {
        $rows = IDP::query()->with(['bawahan.jabatan', 'atasan.jabatan'])->orderBy('id_daftar_idp')->paginate(10);
        $jabatan = Jabatan::orderBy('sebutan_jabatan')->get();
        return view('admin-master.idp.daftar', compact('rows', 'jabatan'));
    }

    public function downloadTemplateImportMaster()
    {
        return Excel::download(new TemplateImportIdpAtasanExport, 'template-import-idp-master.xlsx');
    }

    public function importMaster(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);
        $rows = Excel::toArray(new \stdClass, $request->file('file'))[0] ?? [];
        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($rows) ?? []);
        $requiredHeaders = ['nip', 'nama', 'job_code', 'username', 'password', 'nip_atasan', 'business_area', 'periode_idp'];
        if ($headers !== $requiredHeaders) return back()->with('error', 'Format Excel tidak sesuai. Unduh Template Excel terlebih dahulu.');
        try {
            DB::transaction(function () use ($rows, $headers) {
                foreach ($rows as $index => $row) {
                    if (! array_filter($row, fn ($value) => $value !== null && $value !== '')) continue;
                    $data = array_map(fn ($value) => is_string($value) ? trim($value) : ($value === null ? null : (string) $value), array_combine($headers, array_pad($row, count($headers), null)));
                    $validator = Validator::make($data, ['nip' => ['required', 'string', 'max:50'], 'nama' => ['required', 'string', 'max:150'], 'job_code' => ['required', 'string', 'exists:jabatan,job_code'], 'username' => ['required', 'string', 'max:100'], 'password' => ['required', 'string', 'min:6'], 'nip_atasan' => ['required', 'string', 'max:50'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
                    if ($validator->fails()) throw new \InvalidArgumentException('Baris '.($index + 2).': '.implode(' ', $validator->errors()->all()));
                    $data = $validator->validated();
                    $atasan = Pengguna::where('nip', $data['nip_atasan'])->where('role', 'atasan')->first();
                    if (! $atasan) throw new \InvalidArgumentException('Baris '.($index + 2).': NIP atasan tidak ditemukan.');
                    $bawahan = Pengguna::where('nip', $data['nip'])->orWhere('username', $data['username'])->first();
                    if ($bawahan && ($bawahan->nip !== $data['nip'] || $bawahan->username !== $data['username'])) throw new \InvalidArgumentException('Baris '.($index + 2).': NIP atau username sudah dipakai karyawan lain.');
                    if (! $bawahan) $bawahan = Pengguna::create(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => Jabatan::where('job_code', $data['job_code'])->value('id_jabatan'), 'username' => $data['username'], 'password_hash' => Hash::make($data['password']), 'role' => 'bawahan', 'status_aktif' => true]);
                    IDP::updateOrCreate(['id_bawahan' => $bawahan->id_pengguna, 'id_atasan' => $atasan->id_pengguna, 'periode_idp' => $data['periode_idp']], ['business_area' => $data['business_area']]);
                }
            });
        } catch (\InvalidArgumentException $exception) { return back()->with('error', $exception->getMessage()); }
        return back()->with('success', 'Data bawahan berhasil diimport.');
    }

    public function storeMaster(Request $request)
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:150'], 'nip' => ['required', 'string', 'max:50'], 'id_jabatan' => ['nullable', 'exists:jabatan,id_jabatan'], 'username' => ['required', 'string', 'max:100'], 'password' => ['required', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        $bawahan = Pengguna::where('nip', $data['nip'])->orWhere('username', $data['username'])->first();
        if (! $bawahan) {
            $request->validate(['nip' => ['unique:pengguna,nip'], 'username' => ['unique:pengguna,username']]);
            $bawahan = Pengguna::create(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => $data['id_jabatan'], 'username' => $data['username'], 'password_hash' => Hash::make($data['password']), 'role' => 'bawahan', 'status_aktif' => true]);
        }
        IDP::create(['id_bawahan' => $bawahan->id_pengguna, 'business_area' => $data['business_area'], 'periode_idp' => $data['periode_idp']]);
        return back()->with('success', 'Bawahan berhasil ditambahkan.');
    }

    public function updateMaster(Request $request, IDP $idp)
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:150'], 'nip' => ['required', 'string', 'max:50', 'unique:pengguna,nip,'.$idp->id_bawahan.',id_pengguna'], 'id_jabatan' => ['nullable', 'exists:jabatan,id_jabatan'], 'username' => ['required', 'string', 'max:100', 'unique:pengguna,username,'.$idp->id_bawahan.',id_pengguna'], 'password' => ['nullable', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        $bawahan = Pengguna::findOrFail($idp->id_bawahan);
        $bawahan->fill(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => $data['id_jabatan'], 'username' => $data['username']]);
        if (! empty($data['password'])) $bawahan->password_hash = Hash::make($data['password']);
        $bawahan->save();
        $idp->update(['business_area' => $data['business_area'], 'periode_idp' => $data['periode_idp']]);
        return back()->with('success', 'Bawahan berhasil diperbarui.');
    }

    public function destroyMaster(IDP $idp)
    {
        Pengguna::whereKey($idp->id_bawahan)->delete();
        return back()->with('success', 'Bawahan berhasil dihapus.');
    }

    public function penetapan()
    {
        $rows = IDP::query()->with(['bawahan.jabatan', 'atasan.jabatan', 'rencanaPengembangan' => fn($q) => $q->where('status', 'Disetujui')->with('kompetensi')])->orderBy('id_daftar_idp')->get();
        return view('admin-master.idp.penetapan', compact('rows'));
    }

    public function daftarArea()
    {
        $user = auth()->user();
        $rows = IDP::query()->whereHas('bawahan', fn ($q) => $q->where('unit_induk', $user->unit_induk))->with(['bawahan.jabatan', 'atasan.jabatan'])->orderBy('id_daftar_idp')->get();
        $jabatan = Jabatan::orderBy('sebutan_jabatan')->get();
        return view('admin-area.idp.daftar', compact('rows', 'jabatan'));
    }

    public function downloadTemplateImportArea() { return Excel::download(new TemplateImportIdpAtasanExport, 'template-import-idp-area.xlsx'); }

    public function importArea(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);
        $rows = Excel::toArray(new \stdClass, $request->file('file'))[0] ?? [];
        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($rows) ?? []);
        $requiredHeaders = ['nip', 'nama', 'job_code', 'username', 'password', 'business_area', 'periode_idp'];
        if ($headers !== $requiredHeaders) return back()->with('error', 'Format Excel tidak sesuai. Unduh Template Excel terlebih dahulu.');
        try {
            DB::transaction(function () use ($rows, $headers) {
                foreach ($rows as $index => $row) {
                    if (! array_filter($row, fn ($value) => $value !== null && $value !== '')) continue;
                    $data = array_map(fn ($value) => is_string($value) ? trim($value) : ($value === null ? null : (string) $value), array_combine($headers, array_pad($row, count($headers), null)));
                    $validator = Validator::make($data, ['nip' => ['required', 'string', 'max:50'], 'nama' => ['required', 'string', 'max:150'], 'job_code' => ['required', 'string', 'exists:jabatan,job_code'], 'username' => ['required', 'string', 'max:100'], 'password' => ['required', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
                    if ($validator->fails()) throw new \InvalidArgumentException('Baris '.($index + 2).': '.implode(' ', $validator->errors()->all()));
                    $data = $validator->validated();
                    $bawahan = Pengguna::where('nip', $data['nip'])->orWhere('username', $data['username'])->first();
                    if ($bawahan && ($bawahan->nip !== $data['nip'] || $bawahan->username !== $data['username'])) throw new \InvalidArgumentException('Baris '.($index + 2).': NIP atau username sudah dipakai karyawan lain.');
                    if (! $bawahan) $bawahan = Pengguna::create(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => Jabatan::where('job_code', $data['job_code'])->value('id_jabatan'), 'username' => $data['username'], 'password_hash' => Hash::make($data['password']), 'role' => 'bawahan', 'unit_induk' => auth()->user()->unit_induk, 'status_aktif' => true]);
                    IDP::updateOrCreate(['id_bawahan' => $bawahan->id_pengguna, 'periode_idp' => $data['periode_idp']], ['business_area' => $data['business_area']]);
                }
            });
        } catch (\InvalidArgumentException $exception) { return back()->with('error', $exception->getMessage()); }
        return back()->with('success', 'Data bawahan berhasil diimport.');
    }

    public function storeArea(Request $request)
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:150'], 'nip' => ['required', 'string', 'max:50'], 'id_jabatan' => ['nullable', 'exists:jabatan,id_jabatan'], 'username' => ['required', 'string', 'max:100'], 'password' => ['required', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        $bawahan = Pengguna::where('nip', $data['nip'])->orWhere('username', $data['username'])->first();
        if (! $bawahan) {
            $request->validate(['nip' => ['unique:pengguna,nip'], 'username' => ['unique:pengguna,username']]);
            $bawahan = Pengguna::create(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => $data['id_jabatan'], 'username' => $data['username'], 'password_hash' => Hash::make($data['password']), 'role' => 'bawahan', 'unit_induk' => auth()->user()->unit_induk, 'status_aktif' => true]);
        }
        IDP::create(['id_bawahan' => $bawahan->id_pengguna, 'business_area' => $data['business_area'], 'periode_idp' => $data['periode_idp']]);
        return back()->with('success', 'Bawahan berhasil ditambahkan.');
    }

    public function updateArea(Request $request, IDP $idp)
    {
        $user = auth()->user();
        abort_unless($idp->bawahan && $idp->bawahan->unit_induk === $user->unit_induk, 403);
        $data = $request->validate(['nama' => ['required', 'string', 'max:150'], 'nip' => ['required', 'string', 'max:50', 'unique:pengguna,nip,'.$idp->id_bawahan.',id_pengguna'], 'id_jabatan' => ['nullable', 'exists:jabatan,id_jabatan'], 'username' => ['required', 'string', 'max:100', 'unique:pengguna,username,'.$idp->id_bawahan.',id_pengguna'], 'password' => ['nullable', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        $bawahan = Pengguna::findOrFail($idp->id_bawahan);
        $bawahan->fill(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => $data['id_jabatan'], 'username' => $data['username']]);
        if (! empty($data['password'])) $bawahan->password_hash = Hash::make($data['password']);
        $bawahan->save();
        $idp->update(['business_area' => $data['business_area'], 'periode_idp' => $data['periode_idp']]);
        return back()->with('success', 'Bawahan berhasil diperbarui.');
    }

    public function destroyArea(IDP $idp)
    {
        $user = auth()->user();
        abort_unless($idp->bawahan && $idp->bawahan->unit_induk === $user->unit_induk, 403);
        Pengguna::whereKey($idp->id_bawahan)->delete();
        return back()->with('success', 'Bawahan berhasil dihapus.');
    }

    public function penetapanArea()
    {
        $user = auth()->user();
        $rows = IDP::query()->whereHas('bawahan', fn ($q) => $q->where('unit_induk', $user->unit_induk))->with(['bawahan.jabatan', 'atasan.jabatan', 'rencanaPengembangan' => fn($q) => $q->where('status', 'Disetujui')->with('kompetensi')])->orderBy('id_daftar_idp')->get();
        return view('admin-area.idp.penetapan', compact('rows'));
    }

    public function daftarAtasan()
    {
        $rows = IDP::query()->where('id_atasan', auth()->id())->with(['bawahan.jabatan', 'atasan.jabatan'])->orderBy('id_daftar_idp')->get();
        $jabatan = Jabatan::orderBy('sebutan_jabatan')->get();
        return view('atasan.idp.daftar', compact('rows', 'jabatan'));
    }

    public function daftarBawahan()
    {
        $rows = IDP::query()->where('id_bawahan', auth()->id())->with(['bawahan.jabatan.kompetensi', 'atasan.jabatan', 'monitoring', 'rencanaPengembangan.kompetensi'])->orderBy('id_daftar_idp')->get();
        return view('bawahan.idp.daftar', compact('rows'));
    }

    public function storeRencanaBawahan(Request $request, IDP $idp)
    {
        abort_unless($idp->id_bawahan === auth()->id(), 403);
        $data = $request->validate([
            'submit_action' => ['required', 'in:simpan,kirim'],
            'kompetensi' => ['required', 'array', 'max:5'],
            'kompetensi.*.id_kompetensi' => ['nullable', 'exists:kompetensi,id_kompetensi'],
            'kompetensi.*.pembelajaran_10_persen' => ['nullable', 'string'],
            'kompetensi.*.social_learning_20_persen' => ['nullable', 'string'],
            'kompetensi.*.action_learning_70_persen' => ['nullable', 'string'],
        ]);
        $data['kompetensi'] = array_values(array_filter($data['kompetensi'], fn ($row) => ! empty($row['id_kompetensi'])));
        if (count($data['kompetensi']) < 3) return back()->withErrors(['kompetensi' => 'Pilih minimal 3 kompetensi teknis.'])->withInput();
        if (count(array_unique(array_column($data['kompetensi'], 'id_kompetensi'))) !== count($data['kompetensi'])) return back()->withErrors(['kompetensi' => 'Kompetensi tidak boleh dipilih lebih dari sekali.'])->withInput();
        $allowedIds = auth()->user()->jabatan?->kompetensi()->pluck('kompetensi.id_kompetensi')->all() ?? [];
        foreach ($data['kompetensi'] as $row) {
            abort_unless(in_array((int) $row['id_kompetensi'], $allowedIds, true), 403);
            RencanaPengembanganIDP::updateOrCreate(['id_daftar_idp' => $idp->id_daftar_idp, 'id_kompetensi' => $row['id_kompetensi']], [
                'pembelajaran_10_persen' => $row['pembelajaran_10_persen'] ?? null,
                'social_learning_20_persen' => $row['social_learning_20_persen'] ?? null,
                'action_learning_70_persen' => $row['action_learning_70_persen'] ?? null,
                'status' => $data['submit_action'] === 'kirim' ? 'Diajukan' : 'Draft',
            ]);
        }
        return back()->with('success', $data['submit_action'] === 'kirim' ? 'Rencana IDP dikirim ke atasan.' : 'Rencana IDP disimpan.');
    }

    public function evaluasiBawahan()
    {
        $evaluasi = EvaluasiIDP::whereHas('daftarIdp', fn ($q) => $q->where('id_bawahan', auth()->id()))->with(['daftarIdp.atasan'])->latest('tanggal_evaluasi')->get();
        return view('bawahan.idp.evaluasi', compact('evaluasi'));
    }

    public function pemantauan()
    {
        $rows = IDP::query()->with(['bawahan.jabatan', 'atasan.jabatan', 'monitoring', 'rencanaPengembangan' => fn ($q) => $q->where('status', 'Disetujui')->with('kompetensi')])->orderBy('id_daftar_idp')->get();
        return view('admin-master.idp.pemantauan', compact('rows'));
    }

    public function pemantauanArea()
    {
        $user = auth()->user();
        $rows = IDP::query()->whereHas('bawahan', fn ($q) => $q->where('unit_induk', $user->unit_induk))->with(['bawahan', 'atasan', 'monitoring'])->orderBy('id_daftar_idp')->get();
        return view('admin-area.idp.pemantauan', compact('rows'));
    }

    public function pemantauanCoaching() { return $this->coachingMonitoringView('admin-master.coaching.pemantauan'); }
    public function pemantauanCoachingArea() { return $this->coachingMonitoringView('admin-master.coaching.pemantauan', auth()->user()->unit_induk); }
    public function coachingAtasan() { return $this->coachingView('atasan.coaching.index', 'id_atasan'); }
    public function coachingBawahan() { return $this->coachingView('bawahan.coaching.index', 'id_bawahan'); }

    public function uploadBuktiCoaching(Request $request, IDP $idp)
    {
        abort_unless($idp->id_bawahan === auth()->user()->id_pengguna, 403);

        $validated = $request->validate([
            'bukti_10' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'bukti_20' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'bukti_70' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'plan_id' => ['nullable', 'exists:rencana_pengembangan_idp,id_rencana'],
        ]);

        $planId = $validated['plan_id'] ?? null;
        abort_if($planId && ! RencanaPengembanganIDP::where('id_rencana', $planId)->where('id_daftar_idp', $idp->id_daftar_idp)->exists(), 403);

        $fields = [
            'bukti_10' => 10,
            'bukti_20' => 20,
            'bukti_70' => 70,
        ];

        foreach ($fields as $input => $jenis) {
            if ($request->hasFile($input)) {
                $file = $request->file($input);
                $path = $file->store("coaching-evidence/{$idp->id_daftar_idp}", 'public');
                $originalName = $file->getClientOriginalName();
                
                $query = CoachingBukti::where('id_daftar_idp', $idp->id_daftar_idp)
                    ->where('jenis', $jenis);
                
                if ($planId) {
                    $query->where('id_rencana', $planId);
                } else {
                    $query->whereNull('id_rencana');
                }
                
                $existing = $query->first();
                if ($existing) {
                    Storage::disk('public')->delete($existing->file_path);
                    $existing->delete();
                }
                
                CoachingBukti::create([
                    'id_daftar_idp' => $idp->id_daftar_idp,
                    'id_rencana' => $planId,
                    'jenis' => $jenis,
                    'file_path' => $path,
                    'original_name' => $originalName,
                ]);
            }
        }

        return back()->with('success', 'Bukti coaching berhasil diunggah.');
    }

    private function syncStatusRencanaKeMonitoring(int $idDaftarIdp): void
    {
        $statuses = RencanaPengembanganIDP::where('id_daftar_idp', $idDaftarIdp)->pluck('status');
        $status = $statuses->contains('Revisi') ? 'Revisi' : ($statuses->contains('Diajukan') ? 'Diajukan' : ($statuses->contains('Berjalan') ? 'Berjalan' : ($statuses->contains('Disetujui') ? 'Disetujui' : ($statuses->contains('Selesai') ? 'Selesai' : 'Draft'))));
        DB::table('monitoring_idp')->updateOrInsert(['id_daftar_idp' => $idDaftarIdp], ['status_perencanaan' => $status, 'updated_at' => now()]);
    }

    private function coachingView(string $view, string $userColumn)
    {
        $rows = IDP::query()->where($userColumn, auth()->id())
            ->with([
                'bawahan.jabatan',
                'atasan.jabatan',
                'rencanaPengembangan' => fn ($q) => $q->where('status', 'Disetujui')->with(['kompetensi', 'coachingBukti'])
            ])
            ->orderBy('id_daftar_idp')
            ->get();
        return view($view, compact('rows'));
    }

    private function coachingMonitoringView(string $view, ?string $unitInduk = null)
    {
        $query = IDP::query()->with(['bawahan.jabatan', 'atasan.jabatan', 'monitoring', 'rencanaPengembangan' => fn ($q) => $q->where('status', 'Disetujui')->with('kompetensi')])->orderBy('id_daftar_idp');
        if ($unitInduk) $query->whereHas('bawahan', fn ($q) => $q->where('unit_induk', $unitInduk));
        $summaryRows = (clone $query)->get();
        return view($view, ['rows' => $query->paginate(10), 'summaryRows' => $summaryRows]);
    }

    public function penetapanBawahan()
    {
        $rows = IDP::where('id_bawahan', auth()->id())->with(['rencanaPengembangan' => fn ($q) => $q->where('status', 'Disetujui')])->get();
        return view('bawahan.idp.penetapan', compact('rows'));
    }

    public function penetapanAtasan()
    {
        $rencana = RencanaPengembanganIDP::whereIn('status', ['Diajukan', 'Revisi'])->whereHas('daftarIdp', fn ($q) => $q->where('id_atasan', auth()->id()))->with(['kompetensi', 'daftarIdp.bawahan.jabatan.kompetensi'])->get();
        return view('atasan.idp.penetapan', compact('rencana'));
    }

    public function reviewRencanaAtasan(Request $request, RencanaPengembanganIDP $rencana)
    {
        abort_unless($rencana->daftarIdp()->where('id_atasan', auth()->id())->exists(), 403);
        $data = $request->validate(['status' => ['required', 'in:Disetujui,Revisi'], 'kompetensi_data' => ['nullable', 'string']]);
        $revisedIds = [];
        if (!empty($data['kompetensi_data'])) {
            $kompetensiData = json_decode($data['kompetensi_data'], true);
            if (is_array($kompetensiData)) {
                foreach ($kompetensiData as $idRencana => $values) {
                    $child = RencanaPengembanganIDP::where('id_rencana', $idRencana)->whereHas('daftarIdp', fn ($q) => $q->where('id_atasan', auth()->id()))->first();
                    if ($child) {
                        $originalP10 = $child->pembelajaran_10_persen; $originalS20 = $child->social_learning_20_persen; $originalA70 = $child->action_learning_70_persen;
                        $newP10 = $values['p10'] ?? $child->pembelajaran_10_persen; $newS20 = $values['s20'] ?? $child->social_learning_20_persen; $newA70 = $values['a70'] ?? $child->action_learning_70_persen;
                        $isRevised = ($originalP10 !== $newP10) || ($originalS20 !== $newS20) || ($originalA70 !== $newA70);
                        $child->update(['pembelajaran_10_persen' => $newP10, 'social_learning_20_persen' => $newS20, 'action_learning_70_persen' => $newA70, 'status' => $data['status'], 'direvisi_oleh_atasan' => $isRevised]);
                        $revisedIds[] = $idRencana;
                    }
                }
                RencanaPengembanganIDP::where('id_daftar_idp', $rencana->id_daftar_idp)->whereIn('status', ['Diajukan', 'Revisi'])->whereNotIn('id_rencana', $revisedIds)->update(['status' => $data['status'], 'direvisi_oleh_atasan' => false]);
                $this->syncStatusRencanaKeMonitoring($rencana->id_daftar_idp);
                return back()->with('success', 'Rencana IDP berhasil ditinjau.');
            }
        }
        RencanaPengembanganIDP::where('id_daftar_idp', $rencana->id_daftar_idp)->whereIn('status', ['Diajukan', 'Revisi'])->update(['status' => $data['status'], 'direvisi_oleh_atasan' => false]);
        $this->syncStatusRencanaKeMonitoring($rencana->id_daftar_idp);
        return back()->with('success', 'Rencana IDP berhasil ditinjau.');
    }

    public function pemantauanAtasan()
    {
        $rows = IDP::query()->where('id_atasan', auth()->id())->with(['bawahan.jabatan', 'atasan', 'monitoring', 'rencanaPengembangan' => fn ($query) => $query->whereNot('status', 'Draft')->with('kompetensi')])->orderBy('id_daftar_idp')->get();
        return view('atasan.idp.pemantauan', compact('rows'));
    }

    public function downloadTemplateImportAtasan() { return Excel::download(new TemplateImportIdpAtasanExport, 'template-import-idp-atasan.xlsx'); }

    public function importAtasan(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);
        $rows = Excel::toArray(new \stdClass, $request->file('file'))[0] ?? [];
        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), array_shift($rows) ?? []);
        $requiredHeaders = ['nip', 'nama', 'job_code', 'username', 'password', 'business_area', 'periode_idp'];
        if ($headers !== $requiredHeaders) return back()->with('error', 'Format Excel tidak sesuai. Unduh Template Excel terlebih dahulu.');
        try {
            DB::transaction(function () use ($rows, $headers) {
                foreach ($rows as $index => $row) {
                    if (! array_filter($row, fn ($value) => $value !== null && $value !== '')) continue;
                    $data = array_map(fn ($value) => is_string($value) ? trim($value) : ($value === null ? null : (string) $value), array_combine($headers, array_pad($row, count($headers), null)));
                    $validator = Validator::make($data, ['nip' => ['required', 'string', 'max:50'], 'nama' => ['required', 'string', 'max:150'], 'job_code' => ['required', 'string', 'exists:jabatan,job_code'], 'username' => ['required', 'string', 'max:100'], 'password' => ['required', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
                    if ($validator->fails()) throw new \InvalidArgumentException('Baris '.($index + 2).': '.implode(' ', $validator->errors()->all()));
                    $data = $validator->validated();
                    $bawahan = Pengguna::where('nip', $data['nip'])->orWhere('username', $data['username'])->first();
                    if ($bawahan && ($bawahan->nip !== $data['nip'] || $bawahan->username !== $data['username'])) throw new \InvalidArgumentException('Baris '.($index + 2).': NIP atau username sudah dipakai karyawan lain.');
                    if (! $bawahan) $bawahan = Pengguna::create(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => Jabatan::where('job_code', $data['job_code'])->value('id_jabatan'), 'username' => $data['username'], 'password_hash' => Hash::make($data['password']), 'role' => 'bawahan', 'unit_induk' => auth()->user()->unit_induk, 'status_aktif' => true]);
                    IDP::updateOrCreate(['id_bawahan' => $bawahan->id_pengguna, 'id_atasan' => auth()->id(), 'periode_idp' => $data['periode_idp']], ['business_area' => $data['business_area']]);
                }
            });
        } catch (\InvalidArgumentException $exception) { return back()->with('error', $exception->getMessage()); }
        return back()->with('success', 'Data bawahan berhasil diimport.');
    }

    public function storeAtasan(Request $request)
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:150'], 'nip' => ['required', 'string', 'max:50'], 'id_jabatan' => ['nullable', 'exists:jabatan,id_jabatan'], 'username' => ['required', 'string', 'max:100'], 'password' => ['required', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        $bawahan = Pengguna::where('nip', $data['nip'])->orWhere('username', $data['username'])->first();
        if (! $bawahan) {
            $request->validate(['nip' => ['unique:pengguna,nip'], 'username' => ['unique:pengguna,username']]);
            $bawahan = Pengguna::create(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => $data['id_jabatan'], 'username' => $data['username'], 'password_hash' => Hash::make($data['password']), 'role' => 'bawahan', 'unit_induk' => auth()->user()->unit_induk, 'status_aktif' => true]);
        }
        IDP::create(['id_bawahan' => $bawahan->id_pengguna, 'id_atasan' => auth()->id(), 'business_area' => $data['business_area'], 'periode_idp' => $data['periode_idp']]);
        return back()->with('success', 'Bawahan berhasil ditambahkan.');
    }

    public function updateAtasan(Request $request, IDP $idp)
    {
        abort_unless($idp->id_atasan === auth()->id(), 403);
        $data = $request->validate(['nama' => ['required', 'string', 'max:150'], 'nip' => ['required', 'string', 'max:50', 'unique:pengguna,nip,'.$idp->id_bawahan.',id_pengguna'], 'id_jabatan' => ['nullable', 'exists:jabatan,id_jabatan'], 'username' => ['required', 'string', 'max:100', 'unique:pengguna,username,'.$idp->id_bawahan.',id_pengguna'], 'password' => ['nullable', 'string', 'min:6'], 'business_area' => ['nullable', 'string', 'max:100'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        $bawahan = Pengguna::findOrFail($idp->id_bawahan);
        $bawahan->fill(['nama' => $data['nama'], 'nip' => $data['nip'], 'id_jabatan' => $data['id_jabatan'], 'username' => $data['username']]);
        if (! empty($data['password'])) $bawahan->password_hash = Hash::make($data['password']);
        $bawahan->save();
        $idp->update(['business_area' => $data['business_area'], 'periode_idp' => $data['periode_idp']]);
        return back()->with('success', 'Bawahan berhasil diperbarui.');
    }

    public function destroyAtasan(IDP $idp)
    {
        abort_unless($idp->id_atasan === auth()->id(), 403);
        Pengguna::whereKey($idp->id_bawahan)->delete();
        return back()->with('success', 'Bawahan berhasil dihapus.');
    }

    public function updatePemantauanAtasan(Request $request, IDP $idp)
    {
        abort_unless($idp->id_atasan === auth()->id(), 403);
        $data = $request->validate(['status_perencanaan' => ['required', 'in:Draft,Diajukan,Revisi,Disetujui,Berjalan,Selesai'], 'pembelajaran_10_persen' => ['nullable', 'string'], 'social_learning_20_persen' => ['nullable', 'string'], 'experimental_learning_70_persen' => ['nullable', 'string'], 'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100']]);
        DB::table('monitoring_idp')->updateOrInsert(['id_daftar_idp' => $idp->id_daftar_idp], array_merge($data, ['updated_at' => now()]));
        RencanaPengembanganIDP::where('id_daftar_idp', $idp->id_daftar_idp)->update(['status' => $data['status_perencanaan']]);
        return back()->with('success', 'Pemantauan berhasil diperbarui.');
    }

    public function pemantauanBawahan()
    {
        $rows = IDP::query()->where('id_bawahan', auth()->id())->with(['bawahan', 'atasan', 'monitoring', 'rencanaPengembangan' => fn ($query) => $query->whereNot('status', 'Draft')->with('kompetensi')])->orderBy('id_daftar_idp')->get();
        return view('bawahan.idp.pemantauan', compact('rows'));
    }

    public function updatePemantauan(Request $request, IDP $idp)
    {
        abort_unless($idp->id_bawahan === auth()->id(), 403);
        $data = $request->validate(['status_perencanaan' => ['required', 'in:Belum direncanakan,Menunggu persetujuan,Disetujui'], 'pembelajaran_10_persen' => ['nullable', 'string'], 'social_learning_20_persen' => ['nullable', 'string'], 'experimental_learning_70_persen' => ['nullable', 'string']]);
        DB::table('monitoring_idp')->updateOrInsert(['id_bawahan' => $idp->id_bawahan], $data);
        return back()->with('success', 'Data pemantauan berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama_bawahan' => ['required', 'string', 'max:150'], 'nip_bawahan' => ['required', 'string', 'max:50'], 'jabatan_bawahan' => ['required', 'string', 'max:150'], 'nama_atasan' => ['required', 'string', 'max:150'], 'nip_atasan' => ['required', 'string', 'max:50'], 'jabatan_atasan' => ['required', 'string', 'max:150'], 'business_area' => ['nullable', 'string', 'max:100'], 'unit_induk' => ['required', 'in:UID S2JB,UID LAMPUNG,UIP SUMBAGSEL,UIW BABEL'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        IDP::create($data);
        return back()->with('success', 'Data IDP berhasil ditambahkan.');
    }

    public function update(Request $request, IDP $idp)
    {
        $data = $request->validate(['nama_bawahan' => ['required', 'string', 'max:150'], 'nip_bawahan' => ['required', 'string', 'max:50'], 'jabatan_bawahan' => ['required', 'string', 'max:150'], 'nama_atasan' => ['required', 'string', 'max:150'], 'nip_atasan' => ['required', 'string', 'max:50'], 'jabatan_atasan' => ['required', 'string', 'max:150'], 'business_area' => ['nullable', 'string', 'max:100'], 'unit_induk' => ['required', 'in:UID S2JB,UID LAMPUNG,UIP SUMBAGSEL,UIW BABEL'], 'periode_idp' => ['required', 'in:Batch-1,Batch-2']]);
        $idp->update($data);
        return back()->with('success', 'Data IDP berhasil diperbarui.');
    }

    public function destroy(IDP $idp)
    {
        $idp->delete();
        return back()->with('success', 'Data IDP berhasil dihapus.');
    }

    public function downloadCoachingBukti(IDP $idp, $type, $idRencana = null)
    {
        $user = auth()->user();

        if ($user->role === 'atasan') {
            abort_unless($idp->id_atasan === $user->id_pengguna, 403);
        } elseif ($user->role === 'bawahan') {
            abort_unless($idp->id_bawahan === $user->id_pengguna, 403);
        }

        $query = CoachingBukti::where('id_daftar_idp', $idp->id_daftar_idp)
            ->where('jenis', $type);
        
        if ($idRencana) {
            $query->where('id_rencana', $idRencana);
        }
        
        $coachingBukti = $query->first();
        if (! $coachingBukti) {
            $coachingBukti = CoachingBukti::where('id_daftar_idp', $idp->id_daftar_idp)
                ->where('jenis', $type)
                ->latest('id_coaching_bukti')
                ->first();
        }
        abort_unless($coachingBukti, 404);

        $filePath = $coachingBukti->file_path;
        abort_unless(Storage::disk('public')->exists($filePath), 404);

        $fileName = $coachingBukti->original_name ?? basename($filePath);

        return Storage::disk('public')->download($filePath, $fileName);
    }


}
