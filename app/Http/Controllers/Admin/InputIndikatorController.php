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

        // --- BAGIAN YANG DIUBAH ---
        // Ambil tanggal dari request URL, jika tidak ada, gunakan tanggal hari ini.
        // Contoh URL: /admin/input-indikator/create?tanggal=2025-10-05
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        // --- AKHIR BAGIAN YANG DIUBAH ---

        // 1. Ambil semua indikator yang aktif untuk ruangan user saat ini
        $indikatorRuanganAktif = IndikatorRuangan::where('id_ruangan', $user->id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu') // Eager load relasi ke IndikatorMutu untuk efisiensi
            ->get();

        // 2. Ambil model IndikatorMutu dari koleksi di atas
        $indikator = $indikatorRuanganAktif->pluck('indikatorMutu')->filter();

        // 3. Ambil data mutu yang sudah diinput untuk tanggal yang dipilih
        $indikatorRuanganIds = $indikatorRuanganAktif->pluck('id_indikator_ruangan');

        // Gunakan variabel $tanggal yang sudah dinamis
        $mutuHariIni = MutuRuangan::where('tanggal', $tanggal)
            ->whereIn('id_indikator_ruangan', $indikatorRuanganIds)
            ->get();

        // 4. Susun data mutu agar mudah diakses di view dengan key id_indikator
        $mutu = [];
        foreach ($indikatorRuanganAktif as $ir) {
            $record = $mutuHariIni->firstWhere('id_indikator_ruangan', $ir->id_indikator_ruangan);
            if ($record) {
                // Gunakan id_indikator sebagai key agar view tidak perlu diubah
                $mutu[$ir->id_indikator] = $record;
            }
        }

        // Pastikan $tanggal dikirim ke view agar form tahu tanggal yang aktif
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

            // Lanjutkan hanya jika indikator tersebut memang milik ruangan ini
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

            // (Opsional) Jika Anda ingin tetap menghitung perubahan, logikanya bisa disederhanakan
            // atau disesuaikan seperti ini, namun updateOrCreate sudah cukup andal.
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
