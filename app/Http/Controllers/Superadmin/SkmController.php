<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\RekapSkmExport;
use App\Services\SkmService;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Ruangan;
use App\Models\PilihanJawaban;
use App\Models\Jawaban;
use App\Models\Pertanyaan;
use App\Models\BioPasien;

class SkmController extends Controller
{
    protected $skmService;

    public function __construct(SkmService $skmService)
    {
        $this->skmService = $skmService;
    }

    public function index(Request $request)
    {
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedRuangan = $request->input('ruangan');

        $listRuangan = Ruangan::select('id_ruangan', 'nama_ruangan')
            ->where('nama_ruangan', '!=', 'Super Admin')
            ->get();

        $listPertanyaan = PilihanJawaban::join('pertanyaan', 'pilihan_jawaban.id_pertanyaan', '=', 'pertanyaan.id_pertanyaan')
            ->select('pilihan_jawaban.id_pertanyaan', 'pertanyaan.urutan')
            ->distinct()
            ->orderBy('pertanyaan.urutan', 'asc')
            ->get()
            ->pluck('id_pertanyaan');

        $queryJawaban = Jawaban::join('bio_pasien', 'jawaban.id_pasien', '=', 'bio_pasien.id_pasien')
            ->leftJoin('pilihan_jawaban', 'jawaban.id_pilihan', '=', 'pilihan_jawaban.id_pilihan')
            ->select(
                'bio_pasien.id_pasien',
                'bio_pasien.no_rm',
                'jawaban.id_pertanyaan',
                'pilihan_jawaban.nilai'
            )
            ->whereYear('jawaban.tanggal', $selectedYear)
            ->whereMonth('jawaban.tanggal', $selectedMonth)
            ->whereIn('jawaban.id_pertanyaan', $listPertanyaan);

        if ($selectedRuangan) {
            $queryJawaban->where('bio_pasien.id_ruangan', $selectedRuangan);
        }

        $jawabanPasien = $queryJawaban->get();

        $dataRekap = [];
        $rataRataKolom = [];
        foreach ($listPertanyaan as $id) {
            $rataRataKolom[$id] = ['total' => 0, 'count' => 0];
        }

        foreach ($jawabanPasien as $jawaban) {
            if (!isset($dataRekap[$jawaban->id_pasien])) {
                $dataRekap[$jawaban->id_pasien] = [
                    'no_rm' => $jawaban->no_rm,
                    'jawaban' => [],
                    'total_nilai_ikm' => 0
                ];
            }

            $dataRekap[$jawaban->id_pasien]['jawaban'][$jawaban->id_pertanyaan] = $jawaban->nilai;

            if (isset($jawaban->nilai) && isset($rataRataKolom[$jawaban->id_pertanyaan])) {
                $rataRataKolom[$jawaban->id_pertanyaan]['total'] += $jawaban->nilai;
                $rataRataKolom[$jawaban->id_pertanyaan]['count']++;
            }
        }

        foreach ($dataRekap as $id_pasien => $data) {
            $dataRekap[$id_pasien]['total_nilai_ikm'] = array_sum($data['jawaban']);
        }

        $finalRataRataKolom = [];
        foreach ($listPertanyaan as $id) {
            if ($rataRataKolom[$id]['count'] > 0) {
                $finalRataRataKolom[$id] = $rataRataKolom[$id]['total'] / $rataRataKolom[$id]['count'];
            } else {
                $finalRataRataKolom[$id] = 0;
            }
        }

        return view('superadmin.skm.rekap', [
            'dataRekap' => $dataRekap,
            'rataRataKolom' => $finalRataRataKolom,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'listPertanyaan' => $listPertanyaan,
            'listRuangan' => $listRuangan,
            'selectedRuangan' => $selectedRuangan
        ]);
    }

    public function destroyPertanyaan($id)
    {
        try {
            $this->skmService->deleteSinglePertanyaan($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Pertanyaan berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            $status = str_contains($e->getMessage(), 'GAGAL') ? 400 : 500;

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $status);
        }
    }

    public function editPertanyaan()
    {
        $dataPertanyaan = Pertanyaan::orderBy('urutan', 'asc')
            ->orderBy('id_pertanyaan', 'asc')
            ->get();

        $dataPilihan = PilihanJawaban::orderBy('id_pilihan', 'asc')
            ->get()
            ->groupBy('id_pertanyaan');

        $surveyData = $dataPertanyaan->map(function ($pertanyaan) use ($dataPilihan) {
            $pertanyaan->pilihan = $dataPilihan->get($pertanyaan->id_pertanyaan, collect());

            if ($pertanyaan->id_pertanyaan == 16 || $pertanyaan->pilihan->isEmpty()) {
                $pertanyaan->tipe_pertanyaan = 'Isian Teks';
            } else {
                $pertanyaan->tipe_pertanyaan = 'Pilihan Ganda';
            }
            return $pertanyaan;
        });

        return view('superadmin.skm.edit', compact('surveyData'));
    }

    public function updatePertanyaan(Request $request)
    {
        $submittedQuestions = $request->input('questions', []);

        try {
            $this->skmService->syncPertanyaan($submittedQuestions);

            return redirect()->route('superadmin.skm.edit2')
                ->with('success', 'Struktur pertanyaan berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.skm.edit2')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function hasil(Request $request)
    {
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedRuangan = $request->input('ruangan');

        $listRuangan = Ruangan::select('id_ruangan', 'nama_ruangan')
            ->where('nama_ruangan', '!=', 'Super Admin')
            ->get();

        $queryResponden = Jawaban::join('bio_pasien', 'jawaban.id_pasien', '=', 'bio_pasien.id_pasien')
            ->whereYear('jawaban.tanggal', $selectedYear)
            ->whereMonth('jawaban.tanggal', $selectedMonth);

        if ($selectedRuangan) {
            $queryResponden->where('bio_pasien.id_ruangan', $selectedRuangan);
        }

        $respondenIds = $queryResponden->distinct()->pluck('jawaban.id_pasien');
        $totalResponden = $respondenIds->count();

        $bioResponden = BioPasien::whereIn('id_pasien', $respondenIds)->get();

        $idsPilihanGanda = PilihanJawaban::distinct()
            ->pluck('id_pertanyaan');

        $listKritikSaran = Jawaban::whereIn('id_pasien', $respondenIds)
            ->whereNotIn('id_pertanyaan', $idsPilihanGanda)
            ->whereNotNull('hasil_nilai')
            ->pluck('hasil_nilai');

        // Data List Pendukung
        $listNoRm = $bioResponden->pluck('no_rm');
        $listUmur = $bioResponden->pluck('umur');

        $ruanganData = BioPasien::join('ruangan as r', 'bio_pasien.id_ruangan', '=', 'r.id_ruangan')
            ->whereIn('bio_pasien.id_pasien', $respondenIds)
            ->select('r.nama_ruangan', DB::raw('count(*) as total'))
            ->groupBy('r.nama_ruangan')
            ->pluck('total', 'nama_ruangan');
        $ruanganChart = ['labels' => $ruanganData->keys(), 'data' => $ruanganData->values()];

        // Chart Jenis Kelamin
        $jenisKelaminData = $bioResponden->countBy('jenis_kelamin');
        $jenisKelaminChart = ['labels' => $jenisKelaminData->keys(), 'data' => $jenisKelaminData->values()];

        // Chart Pendidikan
        $pendidikanData = $bioResponden->countBy('pendidikan');
        $pendidikanChart = ['labels' => $pendidikanData->keys(), 'data' => $pendidikanData->values()];

        // Chart Pekerjaan
        $pekerjaanData = $bioResponden->countBy('pekerjaan');
        $pekerjaanChart = ['labels' => $pekerjaanData->keys(), 'data' => $pekerjaanData->values()];

        $pertanyaanSurvei = Pertanyaan::whereIn('id_pertanyaan', $idsPilihanGanda)
            ->orderBy('urutan', 'asc')
            ->get();

        $allSurveyCharts = [];

        foreach ($pertanyaanSurvei as $pertanyaan) {
            $data = Jawaban::from('jawaban as j')
                ->join('pilihan_jawaban as pj', 'j.id_pilihan', '=', 'pj.id_pilihan')
                ->where('j.id_pertanyaan', $pertanyaan->id_pertanyaan)
                ->whereIn('j.id_pasien', $respondenIds)
                ->select('pj.pilihan', 'pj.nilai', DB::raw('count(*) as total'))
                ->groupBy('pj.pilihan', 'pj.nilai')
                ->orderBy('pj.nilai')
                ->pluck('total', 'pilihan');

            $allSurveyCharts[] = [
                'id_pertanyaan' => $pertanyaan->id_pertanyaan,
                'pertanyaan_text' => $pertanyaan->pertanyaan,
                'chart' => [
                    'labels' => $data->keys(),
                    'data' => $data->values()
                ]
            ];
        }

        return view('superadmin.skm.results', compact(
            'totalResponden',
            'listNoRm',
            'listUmur',
            'listKritikSaran',
            'ruanganChart',
            'jenisKelaminChart',
            'pendidikanChart',
            'pekerjaanChart',
            'allSurveyCharts',
            'selectedYear',
            'selectedMonth',
            'selectedRuangan',
            'listRuangan'
        ));
    }

    public function downloadRekap(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric',
            'year' => 'required|numeric',
        ]);

        $bulan = $request->month;
        $tahun = $request->year;
        $ruanganId = $request->ruangan;

        $namaFile = 'Rekap_SKM_' . $bulan . '-' . $tahun;
        if ($ruanganId) {
            $namaRuangan = Ruangan::where('id_ruangan', $ruanganId)->value('nama_ruangan');
            $namaFile .= '_' . str_replace(' ', '_', $namaRuangan);
        }
        $namaFile .= '.xlsx';

        return Excel::download(new RekapSkmExport($bulan, $tahun, $ruanganId), $namaFile);
    }
}