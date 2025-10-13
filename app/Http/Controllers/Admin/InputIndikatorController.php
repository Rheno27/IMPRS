<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorMutu;
use App\Models\MutuRuangan;
use App\Models\IndikatorRuangan;
use Illuminate\Support\Facades\Session;

class InputIndikatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Menampilkan form untuk membuat resource baru.
     * Logika diubah total untuk mengambil data dari relasi yang benar.
     */
    public function create(Request $request) // Tambahkan Request $request di sini
    {
        $user = Session::get('user');
        if (!$user)
            return redirect('/login');
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
        return view('admin.input_indikator', compact('indikator', 'mutu', 'tanggal'));
    }

    /**
     * Menyimpan resource baru ke dalam storage.
     * Logika diubah total untuk menyimpan dengan foreign key yang benar.
     */
    public function store(Request $request)
    {
        $user = Session::get('user');
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $updated = 0;

        foreach ($request->pasien_sesuai as $id_indikator => $ps) {

            // 1. Cari record 'indikator_ruangan' untuk mendapatkan Primary Key
            $indikatorRuangan = IndikatorRuangan::where('id_ruangan', $user->id_ruangan)
                ->where('id_indikator', $id_indikator)
                ->first();
            if (!$indikatorRuangan) {
                continue;
            }

            $dataToStore = [
                'total_pasien' => $request->total_pasien[$id_indikator] ?? 0,
                'pasien_sesuai' => $ps ?? 0,
            ];

            // 2. Lakukan update atau create menggunakan kunci yang benar
            MutuRuangan::updateOrCreate(
                [
                    'tanggal' => $tanggal,
                    'id_indikator_ruangan' => $indikatorRuangan->id_indikator_ruangan, // Kunci yang benar
                ],
                $dataToStore
            );
            $updated++;
        }

        if ($updated > 0) {
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } else {
            return redirect()->back()->with('info', 'Tidak ada data yang diubah atau dikirim.');
        }
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
