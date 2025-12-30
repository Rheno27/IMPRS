<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\IndikatorRuangan;
use App\Models\IndikatorMutu;
use App\Models\Kategori;
use App\Services\MutuService; 

class IndikatorRuanganController extends Controller
{
    protected $mutuService;

    public function __construct(MutuService $mutuService)
    {
        $this->mutuService = $mutuService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'id_indikator_baru' => 'required|exists:indikator_mutu,id_indikator',
        ]);

        // Panggil Service
        $result = $this->mutuService->assignIndikatorToRuangan(
            $request->id_ruangan,
            $request->id_indikator_baru
        );

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('superadmin.ruangan.edit_indikator', ['ruangan' => $request->id_ruangan])
            ->with('success', $result['message']);
    }

    public function edit(Ruangan $ruangan)
    {
        $rawIndikators = IndikatorRuangan::where('id_ruangan', $ruangan->id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu.kategori')
            ->get();

        $activeIndikators = $rawIndikators->sortBy(function ($item) {
            $namaKategori = $item->indikatorMutu->kategori->kategori ?? '';

            if (stripos($namaKategori, 'Prioritas Unit') !== false) {
                return 1;
            }
            if (stripos($namaKategori, 'Nasional Mutu') !== false) {
                return 2; 
            }
            if (stripos($namaKategori, 'Prioritas RS') !== false) {
                return 3; 
            }

            return 4; 
        })->values(); 

        $allMasterIndikators = IndikatorMutu::orderBy('variabel')->get();
        $allKategoris = Kategori::all();

        return view('superadmin.edit_indikator', [
            'ruangan' => $ruangan,
            'activeIndikators' => $activeIndikators,
            'allMasterIndikators' => $allMasterIndikators,
            'allKategoris' => $allKategoris,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_ruangan' => 'required|exists:ruangan,id_ruangan',
            'id_indikator_ruangan_lama' => 'required|exists:indikator_ruangan,id_indikator_ruangan',
            'id_indikator_baru' => 'required|exists:indikator_mutu,id_indikator',
        ]);

        $this->mutuService->switchIndikatorRuangan(
            $request->id_ruangan,
            $request->id_indikator_ruangan_lama,
            $request->id_indikator_baru
        );

        return redirect()->route('superadmin.ruangan.edit_indikator', ['ruangan' => $request->id_ruangan])
            ->with('success', 'Indikator berhasil diganti!');
    }

    public function deactivate(Request $request)
    {
        $request->validate([
            'id_indikator_ruangan' => 'required|exists:indikator_ruangan,id_indikator_ruangan',
        ]);

        $this->mutuService->deactivateIndikator($request->id_indikator_ruangan);

        return redirect()->back()
            ->with('success', 'Indikator berhasil dinonaktifkan dari ruangan ini.');
    }
}