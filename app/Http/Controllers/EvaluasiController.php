<?php

namespace App\Http\Controllers;

use App\Models\Evaluasi;
use App\Models\EvaluasiBukti;
use App\Models\IDP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvaluasiController extends Controller
{
    public function indexBawahan()
    {
        $rows = IDP::query()->where('id_bawahan', auth()->id())
            ->with([
                'bawahan.jabatan',
                'atasan.jabatan',
                'rencanaPengembangan' => fn ($q) => $q->where('status', 'Disetujui')->with(['kompetensi', 'coachingBukti']),
                'evaluasi.kompetensi',
                'evaluasi.bukti',
            ])
            ->orderBy('id_daftar_idp')
            ->get();

        $this->ensureEvaluasiCreated($rows->filter(fn ($row) => $this->coachingDisetujui($row)));

        $rows = IDP::query()->where('id_bawahan', auth()->id())
            ->with([
                'bawahan.jabatan',
                'atasan.jabatan',
                'rencanaPengembangan' => fn ($q) => $q->where('status', 'Disetujui')->with(['kompetensi', 'coachingBukti']),
                'evaluasi.kompetensi',
                'evaluasi.bukti',
            ])
            ->orderBy('id_daftar_idp')
            ->get();

        $rows->each(fn ($row) => $row->setAttribute('coaching_disetujui', $this->coachingDisetujui($row)));

        return view('bawahan.idp.evaluasi', compact('rows'));
    }

    public function indexAtasan()
    {
        $rows = IDP::query()->where('id_atasan', auth()->id())
            ->with([
                'bawahan.jabatan',
                'atasan.jabatan',
                'rencanaPengembangan' => fn ($q) => $q->where('status', 'Disetujui')->with('coachingBukti'),
                'evaluasi.kompetensi',
                'evaluasi.bukti',
            ])
            ->orderBy('id_daftar_idp')
            ->get();

        $rows->each(fn ($row) => $row->setAttribute('coaching_disetujui', $this->coachingDisetujui($row)));

        return view('atasan.idp.evaluasi', compact('rows'));
    }

    public function store(Request $request, IDP $idp)
    {
        abort_unless($idp->id_bawahan === auth()->user()->id_pengguna, 403);

        $validated = $request->validate([
            'bukti_10' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'bukti_20' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'bukti_70' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'deskripsi_realisasi_10' => ['nullable', 'string', 'max:2000'],
            'deskripsi_realisasi_20' => ['nullable', 'string', 'max:2000'],
            'deskripsi_realisasi_70' => ['nullable', 'string', 'max:2000'],
            'evaluasi_id' => ['nullable', 'exists:evaluasi,id_evaluasi'],
        ]);

        $evalId = $validated['evaluasi_id'] ?? null;
        abort_unless($evalId && Evaluasi::whereKey($evalId)->where('id_daftar_idp', $idp->id_daftar_idp)->exists(), 422);
        abort_unless($this->coachingDisetujui($idp), 422, 'Evaluasi belum tersedia. Setujui seluruh bukti coaching 10%, 20%, dan 70% untuk setiap kompetensi.');

        $evaluasi = Evaluasi::findOrFail($evalId);
        $lockedJenis = [];

        foreach ([10, 20, 70] as $jenis) {
            $field = "deskripsi_realisasi_{$jenis}";
            $existingBukti = EvaluasiBukti::where('id_evaluasi', $evalId)
                ->where('jenis', $jenis)
                ->first();
            $isApproved = $existingBukti && ($existingBukti->status_atasan ?? 'pending') === 'setuju';
            if ($isApproved) {
                $lockedJenis[] = $jenis;
                continue;
            }
            if ($request->has($field) && $evaluasi->$field !== $validated[$field]) {
                $evaluasi->update([$field => $validated[$field]]);
                EvaluasiBukti::where('id_evaluasi', $evalId)
                    ->where('jenis', $jenis)
                    ->update(['status_atasan' => 'pending', 'catatan_revisi' => null]);
            }
        }

        $fields = [
            'bukti_10' => 10,
            'bukti_20' => 20,
            'bukti_70' => 70,
        ];

        foreach ($fields as $input => $jenis) {
            if ($request->hasFile($input)) {
                $existingBukti = EvaluasiBukti::where('id_evaluasi', $evalId)
                    ->where('jenis', $jenis)
                    ->first();
                if ($existingBukti && ($existingBukti->status_atasan ?? 'pending') === 'setuju') {
                    $lockedJenis[] = $jenis;
                    continue;
                }
                $file = $request->file($input);
                $path = $file->store("evaluasi/{$idp->id_daftar_idp}", 'public');
                $originalName = $file->getClientOriginalName();

                if ($existingBukti) {
                    Storage::disk('public')->delete($existingBukti->file_path);
                    $existingBukti->delete();
                }

                EvaluasiBukti::create([
                    'id_daftar_idp' => $idp->id_daftar_idp,
                    'id_evaluasi' => $evalId,
                    'jenis' => $jenis,
                    'file_path' => $path,
                    'original_name' => $originalName,
                ]);
            }
        }

        $message = 'Realisasi evaluasi berhasil disimpan.';
        if (! empty($lockedJenis)) {
            $daftar = implode('%, ', array_unique($lockedJenis)) . '%';
            $message = "Sebagian realisasi tidak disimpan (sudah disetujui atasan): {$daftar}.";
        }

        return back()->with('success', $message);
    }

    public function reviewAtasan(Request $request, IDP $idp, EvaluasiBukti $bukti)
    {
        abort_unless($idp->id_atasan === auth()->id() && $bukti->id_daftar_idp === $idp->id_daftar_idp, 403);
        abort_unless($this->coachingDisetujui($idp), 422, 'Evaluasi belum tersedia. Setujui seluruh bukti coaching 10%, 20%, dan 70% untuk setiap kompetensi.');
        $data = $request->validate([
            'status_atasan' => ['required', 'in:setuju,revisi'],
            'catatan_revisi' => ['required_if:status_atasan,revisi', 'nullable', 'string', 'max:2000'],
        ]);

        $bukti->update([
            'status_atasan' => $data['status_atasan'],
            'catatan_revisi' => $data['status_atasan'] === 'revisi' ? $data['catatan_revisi'] : null,
        ]);

        return back()->with('success', 'Status evaluasi berhasil diperbarui.');
    }

    public function storeNilai(Request $request, IDP $idp)
    {
        abort_unless($idp->id_atasan === auth()->id(), 403);

        $data = $request->validate([
            'skor' => ['required', 'integer', 'in:0,1,2'],
            'feedback_evaluasi' => ['nullable', 'string', 'max:2000'],
        ]);

        abort_unless($this->coachingDisetujui($idp), 422, 'Evaluasi belum tersedia. Setujui seluruh bukti coaching 10%, 20%, dan 70% untuk setiap kompetensi.');

        $jenisWajib = [10, 20, 70];
        $adaKompetensi = Evaluasi::where('id_daftar_idp', $idp->id_daftar_idp)->exists();
        $semuaDisetujui = ! Evaluasi::where('id_daftar_idp', $idp->id_daftar_idp)
            ->where(function ($query) use ($jenisWajib) {
                foreach ($jenisWajib as $jenis) {
                    $query->orWhereDoesntHave('bukti', fn ($bukti) => $bukti
                        ->where('jenis', $jenis)
                        ->where('status_atasan', 'setuju'));
                }
            })
            ->exists();
        abort_unless($adaKompetensi && $semuaDisetujui, 422, 'Penilaian hanya bisa dilakukan jika bukti 10%, 20%, dan 70% setiap kompetensi sudah disetujui.');

        Evaluasi::where('id_daftar_idp', $idp->id_daftar_idp)->update([
            'skor' => $data['skor'],
            'feedback_evaluasi' => $data['feedback_evaluasi'],
            'tanggal_penilaian' => now(),
        ]);

        return back()->with('success', 'Nilai evaluasi berhasil disimpan.');
    }

    public function download(IDP $idp, $type)
    {
        $user = auth()->user();

        if ($user->role === 'atasan') {
            abort_unless($idp->id_atasan === $user->id_pengguna, 403);
        } elseif ($user->role === 'bawahan') {
            abort_unless($idp->id_bawahan === $user->id_pengguna, 403);
        }

        abort_unless(in_array((int) $type, [10, 20, 70], true), 404);

        $bukti = EvaluasiBukti::where('id_daftar_idp', $idp->id_daftar_idp)
            ->where('jenis', $type)
            ->latest('id_evaluasi_bukti')
            ->first();

        abort_unless($bukti, 404);

        $filePath = $bukti->file_path;
        abort_unless(Storage::disk('public')->exists($filePath), 404);

        $fileName = $bukti->original_name ?? basename($filePath);

        return Storage::disk('public')->download($filePath, $fileName);
    }

    private function coachingDisetujui(IDP $idp): bool
    {
        $rencana = $idp->rencanaPengembangan()
            ->where('status', 'Disetujui')
            ->with('coachingBukti')
            ->get();

        return $rencana->isNotEmpty() && $rencana->every(
            fn ($item) => collect([10, 20, 70])->every(
                fn ($jenis) => $item->coachingBukti->contains(
                    fn ($bukti) => (int) $bukti->jenis === $jenis && $bukti->status_atasan === 'setuju'
                )
            )
        );
    }

    private function ensureEvaluasiCreated($rows): void
    {
        foreach ($rows as $row) {
            foreach ($row->rencanaPengembangan as $plan) {
                $exists = Evaluasi::where('id_daftar_idp', $row->id_daftar_idp)
                    ->where('id_rencana_asal', $plan->id_rencana)
                    ->exists();
                if (! $exists) {
                    Evaluasi::create([
                        'id_daftar_idp' => $row->id_daftar_idp,
                        'id_rencana_asal' => $plan->id_rencana,
                        'id_kompetensi' => $plan->id_kompetensi,
                        'pembelajaran_10_persen' => $plan->pembelajaran_10_persen,
                        'social_learning_20_persen' => $plan->social_learning_20_persen,
                        'action_learning_70_persen' => $plan->action_learning_70_persen,
                    ]);
                }
            }
        }
    }
}
