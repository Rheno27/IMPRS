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
