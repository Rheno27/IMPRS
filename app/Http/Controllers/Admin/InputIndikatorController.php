<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorMutu;
use App\Models\MutuRuangan;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Session::get('user');
        if (!$user)
            return redirect('/login');

        // Ambil semua id_indikator yang sudah pernah diinput di mutu_ruangan untuk ruangan ini
        $indikatorIds = MutuRuangan::where('id_ruangan', $user->id_ruangan)
            ->pluck('id_indikator')
            ->unique();

        // Ambil data indikator yang hanya ada di mutu_ruangan ruangan ini
        $indikator = IndikatorMutu::whereIn('id_indikator', $indikatorIds)->get();

        // Ambil data mutu_ruangan terakhir per indikator untuk ruangan ini (misal hari ini)
        $tanggal = date('Y-m-d');
        $mutu = MutuRuangan::where('id_ruangan', $user->id_ruangan)
            ->whereIn('id_indikator', $indikatorIds)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('id_indikator');

        return view('admin.input_indikator', compact('indikator', 'mutu', 'tanggal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Session::get('user');
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $updated = 0;

        foreach ($request->pasien_sesuai as $id_indikator => $ps) {
            $data = [
                'total_pasien' => $request->total_pasien[$id_indikator] ?? 0,
                'pasien_sesuai' => $ps ?? 0,
            ];

            // Ambil data lama
            $existing = MutuRuangan::where('tanggal', $tanggal)
                ->where('id_ruangan', $user->id_ruangan)
                ->where('id_indikator', $id_indikator)
                ->first();

            // Cek apakah data berubah atau belum ada data
            if (
                !$existing ||
                $existing->total_pasien != $data['total_pasien'] ||
                $existing->pasien_sesuai != $data['pasien_sesuai']
            ) {
                MutuRuangan::updateOrCreate(
                    [
                        'tanggal' => $tanggal,
                        'id_ruangan' => $user->id_ruangan,
                        'id_indikator' => $id_indikator,
                    ],
                    $data
                );
                $updated++;
            }
        }

        if ($updated > 0) {
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } else {
            return redirect()->back()->with('info', 'Tidak ada data yang diubah.');
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
