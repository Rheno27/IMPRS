<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\IndikatorRuangan;
use App\Models\IndikatorMutu;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class IndikatorRuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ruangan $ruangan)
    {
        $activeIndikators = IndikatorRuangan::where('id_ruangan', $ruangan->id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu.kategori')
            ->get();

        $allMasterIndikators = IndikatorMutu::orderBy('variabel')->get();

        // Pastikan baris ini ada:
        $allKategoris = Kategori::all();

        return view('superadmin.edit_indikator', [
            'ruangan' => $ruangan,
            'activeIndikators' => $activeIndikators,
            'allMasterIndikators' => $allMasterIndikators,
            'allKategoris' => $allKategoris, // Dan dikirim ke view
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'id_indikator_ruangan_lama' => 'required|exists:indikator_ruangan,id_indikator_ruangan',
            'id_indikator_baru' => 'required|exists:indikator_mutu,id_indikator',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Selalu nonaktifkan indikator yang lama
            IndikatorRuangan::where('id_indikator_ruangan', $request->id_indikator_ruangan_lama)
                ->update(['active' => false]);

            // 2. CEK: Apakah indikator baru ini sudah ada untuk ruangan ini tapi dalam status nonaktif?
            $existingInactive = IndikatorRuangan::where('id_ruangan', $request->id_ruangan)
                ->where('id_indikator', $request->id_indikator_baru)
                ->where('active', false)
                ->first();

            if ($existingInactive) {
                // 3. JIKA ADA: Aktifkan kembali record yang sudah ada (UPDATE)
                $existingInactive->active = true;
                $existingInactive->save();
            } else {
                // 4. JIKA TIDAK ADA: Buat record baru (CREATE)
                IndikatorRuangan::create([
                    'id_ruangan' => $request->id_ruangan,
                    'id_indikator' => $request->id_indikator_baru,
                    'active' => true,
                ]);
            }
        });

        return redirect()->route('superadmin.ruangan.edit_indikator', ['ruangan' => $request->id_ruangan])
            ->with('success', 'Indikator berhasil diganti!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
