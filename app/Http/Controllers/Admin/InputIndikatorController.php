<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorMutu;
use App\Models\MutuRuangan;
use App\Models\IndikatorRuangan;
use Illuminate\Support\Facades\Auth;

class InputIndikatorController extends Controller
{
    public function create(Request $request) 
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // 1. Ambil semua indikator yang aktif untuk ruangan user saat ini
        $indikatorRuanganAktif = IndikatorRuangan::where('id_ruangan', $user->id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu') 
            ->get();

        // 2. Ambil model IndikatorMutu dari koleksi di atas
        $indikator = $indikatorRuanganAktif->pluck('indikatorMutu')->filter();

        // 3. Ambil data mutu yang sudah diinput untuk tanggal yang dipilih
        $indikatorRuanganIds = $indikatorRuanganAktif->pluck('id_indikator_ruangan');
        $mutuHariIni = MutuRuangan::where('tanggal', $tanggal)
            ->whereIn('id_indikator_ruangan', $indikatorRuanganIds)
            ->get();

        // 4. Susun data mutu agar mudah diakses di view dengan key id_indikator
        $mutu = [];
        foreach ($indikatorRuanganAktif as $ir) {
            $record = $mutuHariIni->firstWhere('id_indikator_ruangan', $ir->id_indikator_ruangan);
            if ($record) {
                $mutu[$ir->id_indikator] = $record;
            }
        }
        return view('admin.input_indikator', compact('indikator', 'mutu', 'tanggal', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user(); 
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $updated = 0;
        $errors = [];

        foreach ($request->pasien_sesuai as $id_indikator => $ps) {
            $total = $request->total_pasien[$id_indikator] ?? 0;
            $sesuai = $ps ?? 0;

            // 1. Cek Negatif
            if ($total < 0 || $sesuai < 0) {
                return redirect()->back()->with('error', 'Gagal: Input tidak boleh angka negatif.');
            }

            // 2. Cek Logika (Sesuai > Total)
            if ($sesuai > $total) {
                return redirect()->back()->with('error', 'Gagal: Jumlah pasien sesuai tidak boleh lebih besar dari total pasien.');
            }

            $indikatorRuangan = IndikatorRuangan::where('id_ruangan', $user->id_ruangan)
                ->where('id_indikator', $id_indikator)
                ->first();

            if (!$indikatorRuangan) {
                continue;
            }

            MutuRuangan::updateOrCreate(
                [
                    'tanggal' => $tanggal,
                    'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan,
                ],
                [
                    'total_pasien' => $total,
                    'pasien_sesuai' => $sesuai,
                ]
            );
            $updated++;
        }

        if ($updated > 0) {
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } else {
            return redirect()->back()->with('info', 'Tidak ada data yang diubah atau dikirim.');
        }
    }
}
