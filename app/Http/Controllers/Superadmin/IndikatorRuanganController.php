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
        $request->validate([
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'id_indikator_baru' => 'required|exists:indikator_mutu,id_indikator',
        ]);

        // Cek dulu apakah indikator ini sudah pernah ditugaskan ke ruangan ini
        $indikator = IndikatorRuangan::where('id_ruangan', $request->id_ruangan)
            ->where('id_indikator', $request->id_indikator_baru)
            ->first();

        if ($indikator) {
            // Jika sudah ada, cek apakah sudah aktif
            if ($indikator->active) {
                return redirect()->back()
                    ->with('error', 'Indikator ini sudah aktif di ruangan ini.');
            } else {
                // Jika tidak aktif, aktifkan kembali
                $indikator->active = true;
                $indikator->save();
            }
        } else {
            // Jika belum ada sama sekali, buat data baru
            IndikatorRuangan::create([
                'id_ruangan' => $request->id_ruangan,
                'id_indikator' => $request->id_indikator_baru,
                'active' => true,
            ]);
        }

        return redirect()->route('superadmin.ruangan.edit_indikator', ['ruangan' => $request->id_ruangan])
            ->with('success', 'Indikator baru berhasil ditambahkan ke ruangan.');
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

    public function deactivate(Request $request)
    {
        $request->validate([
            'id_indikator_ruangan' => 'required|exists:indikator_ruangan,id_indikator_ruangan',
        ]);

        $indikatorRuangan = IndikatorRuangan::findOrFail($request->id_indikator_ruangan);

        // Cek apakah indikator ini memang milik ruangan yang sedang diedit (optional tapi bagus untuk keamanan)
        // if ($indikatorRuangan->id_ruangan != $request->id_ruangan) { ... }

        $indikatorRuangan->active = false;
        $indikatorRuangan->save();

        return redirect()->back()
            ->with('success', 'Indikator berhasil dinonaktifkan dari ruangan ini.');
    }
}
