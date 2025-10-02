<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\IndikatorRuangan;
use App\Models\MutuRuangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DetailIndikatorController extends Controller
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
    public function show(Request $request, Ruangan $ruangan)
    {
        $bulan = $request->input('bulan', date('n'));
        $tahun = $request->input('tahun', date('Y'));

        // 1. Ambil SEMUA data mutu untuk ruangan dan periode yang dipilih
        $mutu = MutuRuangan::with('indikatorRuangan.indikatorMutu')
            ->whereHas('indikatorRuangan', function ($query) use ($ruangan) {
                $query->where('id_ruangan', $ruangan->id_ruangan);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // 2. Ambil DAFTAR indikator yang seharusnya ada di ruangan tersebut
        $indikators = IndikatorRuangan::where('id_ruangan', $ruangan->id_ruangan)
            ->where('active', true)
            ->with('indikatorMutu')
            ->get();

        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // 3. Proses data seperti di controller admin
        $indikatorData = [];
        foreach ($indikators as $i => $item) {
            // Filter data mutu yang relevan untuk indikator saat ini
            $dataMutu = $mutu->filter(function ($m) use ($item) {
                return $m->id_indikator_ruangan == $item->id_indikator_ruangan;
            });

            // Kelompokkan data berdasarkan tanggal
            $byTanggal = $dataMutu->keyBy(function ($d) {
                return Carbon::parse($d->tanggal)->format('j');
            });

            // Hitung total dan persentase
            $jumlah_total = $dataMutu->sum('total_pasien');
            $jumlah_sesuai = $dataMutu->sum('pasien_sesuai');
            $persen = $jumlah_total > 0 ? round($jumlah_sesuai / $jumlah_total * 100, 2) : 0;

            // Masukkan ke dalam array hasil
            $indikatorData[] = [
                'no' => $i + 1,
                'variabel' => $item->indikatorMutu->variabel,
                'byTanggal' => $byTanggal,
                'jumlah_total' => $jumlah_total,
                'jumlah_sesuai' => $jumlah_sesuai,
                'persen' => $persen,
            ];
        }

        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        // 4. Kirim data yang sudah diolah ke view
        return view('superadmin.detail_indikator', [
            'ruangan' => $ruangan,
            'indikatorData' => $indikatorData, // Ganti 'indikators' menjadi 'indikatorData'
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'jumlahHari' => $jumlahHari
        ]);
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
