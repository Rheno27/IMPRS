<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndikatorMutuController extends Controller
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
        // 1. Ambil semua kategori untuk dropdown di modal
        $kategoris = DB::table('kategori')->orderBy('id_kategori')->get();

        // 2. Ambil semua indikator mutu yang ada, join dengan kategori,
        //    lalu kelompokkan berdasarkan nama kategori untuk ditampilkan di tabel.
        $indikators = DB::table('indikator_mutu as im')
            ->join('kategori as k', 'im.id_kategori', '=', 'k.id_kategori')
            // PASTIKAN im.id_kategori di-select untuk logika di view
            ->select('im.id_indikator', 'im.variabel', 'im.standar', 'k.kategori', 'im.id_kategori')
            ->orderBy('k.id_kategori')
            ->orderBy('im.id_indikator')
            ->get()
            ->groupBy('kategori'); // Mengelompokkan hasil query berdasarkan nama kategori

        return view('superadmin.create_indikator', compact('kategoris', 'indikators'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 3. Validasi input dari form modal
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'variabel' => 'required|string',
            'standar' => 'required|string|max:255',
        ]);

        // 4. Simpan data baru ke tabel indikator_mutu
        DB::table('indikator_mutu')->insert([
            'id_kategori' => $request->id_kategori,
            'variabel' => $request->variabel,
            'standar' => $request->standar,
        ]);

        // 5. Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('superadmin.indikator_mutu.create')
            ->with('success', 'Indikator mutu baru berhasil ditambahkan.');
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
        // 1. Validasi input (sama seperti 'store')
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'variabel' => 'required|string',
            'standar' => 'required|string|max:255',
        ]);

        // 2. Cari data yang akan diupdate
        $indikator = DB::table('indikator_mutu')->where('id_indikator', $id);

        if (!$indikator->first()) {
            return redirect()->route('superadmin.indikator_mutu.create')->with('error', 'Data indikator tidak ditemukan.');
        }

        // 3. Lakukan update data
        try {
            $indikator->update([
                'id_kategori' => $request->id_kategori,
                'variabel' => $request->variabel,
                'standar' => $request->standar,
            ]);

            // 4. Redirect kembali dengan pesan sukses
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('success', 'Indikator mutu berhasil diperbarui.');

        } catch (\Exception $e) {
            // 5. Tangkap jika ada error
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 1. Cek apakah indikator ini sedang digunakan di tabel 'indikator_ruangan'
        $isInUse = DB::table('indikator_ruangan')->where('id_indikator', $id)->exists();

        if ($isInUse) {
            // 2. JIKA DIPAKAI: Jangan hapus. Kembalikan dengan pesan error.
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Indikator ini tidak dapat dihapus karena sedang digunakan oleh satu atau lebih ruangan.');
        }

        // 3. JIKA TIDAK DIPAKAI (aman): Lanjutkan proses hapus
        try {
            DB::table('indikator_mutu')->where('id_indikator', $id)->delete();

            // 4. Kembalikan dengan pesan sukses
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('success', 'Indikator mutu berhasil dihapus.');

        } catch (\Exception $e) {
            // 5. Tangkap jika ada error database lain
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
