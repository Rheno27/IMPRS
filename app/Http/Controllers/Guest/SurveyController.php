<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pertanyaan;
use App\Models\PilihanJawaban;
use App\Models\Ruangan;
use App\Models\BioPasien;
use App\Models\Jawaban;

class SurveyController extends Controller
{
    public function create()
    {
        $pertanyaan = Pertanyaan::where('id_pertanyaan', '!=', 16)
            ->orderBy('urutan', 'asc')
            ->get();

        $pertanyaanKritikSaran = Pertanyaan::find(16);

        $pilihanJawaban = PilihanJawaban::orderBy('id_pilihan', 'asc')
            ->get()
            ->groupBy('id_pertanyaan');

        $ruangan = Ruangan::where('id_ruangan', '!=', 'SP00')->get();

        return view('guest.skm1', compact('pertanyaan', 'pilihanJawaban', 'ruangan', 'pertanyaanKritikSaran'));
    }

    public function store(Request $request)
    {
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

            $pasien = BioPasien::create([
                'id_ruangan' => $request->id_ruangan,
                'no_rm' => $request->no_rm,
                'umur' => $request->umur,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pendidikan' => $request->pendidikan,
                'pekerjaan' => $request->pekerjaan,
            ]);

            $pasienId = $pasien->id_pasien;

            foreach ($request->jawaban as $id_pertanyaan => $id_pilihan) {
                $pilihan = PilihanJawaban::find($id_pilihan);

                if ($pilihan) {
                    Jawaban::create([
                        'tanggal' => Carbon::now(),
                        'id_pasien' => $pasienId,
                        'id_pertanyaan' => $id_pertanyaan,
                        'id_pilihan' => $id_pilihan,
                        'hasil_nilai' => $pilihan->nilai,
                    ]);
                }
            }

            if ($request->filled('kritik_saran')) {
                Jawaban::create([
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