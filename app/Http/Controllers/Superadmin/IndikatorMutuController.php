<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndikatorMutuController extends Controller
{
    public function create(Request $request)
    {
        $kategoris = DB::table('kategori')->orderBy('id_kategori')->get();

        $limit = $request->input('limit', 10);

        $indikators = DB::table('indikator_mutu as im')
            ->join('kategori as k', 'im.id_kategori', '=', 'k.id_kategori')
            ->select('im.id_indikator', 'im.variabel', 'im.standar', 'k.kategori', 'im.id_kategori')
            ->orderBy('k.id_kategori')
            ->orderBy('im.id_indikator')
            ->when($request->input('search'), function ($query, $search) {
                return $query->where('im.variabel', 'like', "%{$search}%")
                            ->orWhere('k.kategori', 'like', "%{$search}%");
            })
            ->paginate($limit)
            ->withQueryString(); 

        return view('superadmin.create_indikator', compact('kategoris', 'indikators'));
    }

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

    public function update(Request $request, string $id)
    {
        // 1. Validasi input 
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

    public function destroy(string $id)
    {
        $isInUse = DB::table('indikator_ruangan')->where('id_indikator', $id)->exists();

        if ($isInUse) {
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Indikator ini tidak dapat dihapus karena sedang digunakan oleh satu atau lebih ruangan.');
        }

        try {
            DB::table('indikator_mutu')->where('id_indikator', $id)->delete();

            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('success', 'Indikator mutu berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
