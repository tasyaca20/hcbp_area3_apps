<?php

namespace App\Http\Controllers;

use App\Models\IDP;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class IdpController extends Controller
{
    // ... existing controller methods ...

    public function updatePemantauanAtasan(Request $request, IDP $idp)
    {
        abort_unless($idp->id_atasan === auth()->id(), 403);

        $data = $request->validate([
            'status_perencanaan' => ['required', 'in:Diajukan,Revisi,Disetujui,Berjalan,Selesai'],
            'pembelajaran_10_persen' => ['nullable', 'string'],
            'social_learning_20_persen' => ['nullable', 'string'],
            'experimental_learning_70_persen' => ['nullable', 'string'],
            'progress_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        DB::table('monitoring_idp')->updateOrInsert(
            ['id_daftar_idp' => $idp->id_daftar_idp],
            array_merge($data, ['updated_at' => now()])
        );

        return back()->with('success', 'Pemantauan berhasil diperbarui.');
    }

    // Existing methods below remain unchanged.
}
