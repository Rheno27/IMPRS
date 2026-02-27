<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorMutu;
use App\Models\MutuRuangan;
use App\Models\IndikatorRuangan;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreInputMutuRequest;
use App\Services\MutuService;

class InputIndikatorController extends Controller
{
    protected $mutuService;

    public function __construct(MutuService $mutuService)
    {
        $this->mutuService = $mutuService;
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $indikatorRuanganAktif = IndikatorRuangan::where('id_ruangan', $user->id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu')
            ->get();

        $indikator = $indikatorRuanganAktif->pluck('indikatorMutu')->filter();

        $indikatorRuanganIds = $indikatorRuanganAktif->pluck('id_indikator_ruangan');
        $mutuHariIni = MutuRuangan::where('tanggal', $tanggal)
            ->whereIn('id_indikator_ruangan', $indikatorRuanganIds)
            ->get();

        $mutu = [];
        foreach ($indikatorRuanganAktif as $ir) {
            $record = $mutuHariIni->firstWhere('id_indikator_ruangan', $ir->id_indikator_ruangan);
            if ($record) {
                $mutu[$ir->id_indikator] = $record;
            }
        }
        return view('admin.indikator_mutu.create', compact('indikator', 'mutu', 'tanggal', 'user'));
    }

    public function store(StoreInputMutuRequest $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $updated = $this->mutuService->simpanDataMutu(
            $user,
            $tanggal,
            $request->pasien_sesuai,
            $request->total_pasien
        );

        if ($updated > 0) {
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } else {
            return redirect()->back()->with('info', 'Tidak ada data yang valid untuk disimpan.');
        }
    }
}