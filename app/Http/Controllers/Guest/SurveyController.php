<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SurveyController extends Controller
{
    public function create()
    {
        // 1. Ambil semua pertanyaan (kecuali ID 16/Kritik Saran) & URUTKAN BERDASARKAN 'urutan'
        $pertanyaan = DB::table('pertanyaan')
            ->where('id_pertanyaan', '!=', 16)
            ->orderBy('urutan', 'asc')
            ->get();

        // 2. Ambil pertanyaan spesifik untuk kritik dan saran (ID 16)
        $pertanyaanKritikSaran = DB::table('pertanyaan')->where('id_pertanyaan', 16)->first();

        // 3. Ambil semua pilihan jawaban dan kelompokkan berdasarkan id_pertanyaan
        $pilihanJawaban = DB::table('pilihan_jawaban')
            ->orderBy('id_pilihan', 'asc')
            ->get()
            ->groupBy('id_pertanyaan');

        // 4. Ambil daftar ruangan, kecuali Super Admin
        $ruangan = DB::table('ruangan')->where('id_ruangan', '!=', 'SP00')->get();

        // 5. Kirim semua data ke view
        return view('guest.skm1', compact('pertanyaan', 'pilihanJawaban', 'ruangan', 'pertanyaanKritikSaran'));
    }

    public function store(Request $request)
    {
        // Validasi data responden
        $request->validate([
            'id_ruangan' => 'required',
            'no_rm' => 'required|numeric',
            'umur' => 'required|integer',
            'jenis_kelamin' => 'required|string|max:50',
            'pendidikan' => 'required|string|max:50',
            'pekerjaan' => 'required|string|max:100',
            'jawaban' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan bio pasien dan dapatkan ID nya
            $pasienId = DB::table('bio_pasien')->insertGetId([
                'id_ruangan' => $request->id_ruangan,
                'no_rm' => $request->no_rm,
                'umur' => $request->umur,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pendidikan' => $request->pendidikan,
                'pekerjaan' => $request->pekerjaan,
            ]);

            // 2. Loop dan simpan setiap jawaban pilihan ganda
            foreach ($request->jawaban as $id_pertanyaan => $id_pilihan) {
                $pilihan = DB::table('pilihan_jawaban')->where('id_pilihan', $id_pilihan)->first();
                if ($pilihan) {
                    DB::table('jawaban')->insert([
                        'tanggal' => Carbon::now(),
                        'id_pasien' => $pasienId,
                        'id_pertanyaan' => $id_pertanyaan,
                        'id_pilihan' => $id_pilihan,
                        'hasil_nilai' => $pilihan->nilai,
                    ]);
                }
            }

            // 3. Simpan jawaban untuk kritik dan saran (jika ada)
            if ($request->filled('kritik_saran')) {
                DB::table('jawaban')->insert([
                    'tanggal' => Carbon::now(),
                    'id_pasien' => $pasienId,
                    'id_pertanyaan' => 16, 
                    'id_pilihan' => null,
                    'hasil_nilai' => $request->kritik_saran,
                ]);
            }
        });

        return redirect()->route('guest.survei-done')->with('success', 'Terima kasih telah mengisi survei!');
    }
}
