<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndikatorMutu;
use App\Models\Kategori;
use App\Models\IndikatorRuangan;

class IndikatorMutuController extends Controller
{
    public function create(Request $request)
    {
        $kategoris = Kategori::orderBy('id_kategori')->get();

        $limit = $request->input('limit', 10);

        $indikators = IndikatorMutu::with('kategori')
            ->join('kategori', 'indikator_mutu.id_kategori', '=', 'kategori.id_kategori')
            ->select('indikator_mutu.*')
            ->orderBy('kategori.id_kategori')
            ->orderBy('indikator_mutu.id_indikator')
            ->when($request->input('search'), function ($query, $search) {
                return $query->where('indikator_mutu.variabel', 'like', "%{$search}%")
                    ->orWhere('kategori.kategori', 'like', "%{$search}%");
            })
            ->paginate($limit)
            ->withQueryString();

        return view('superadmin.indikator.create', compact('kategoris', 'indikators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'variabel' => 'required|string',
            'standar' => 'required|string|max:255',
        ]);

        IndikatorMutu::create([
            'id_kategori' => $request->id_kategori,
            'variabel' => $request->variabel,
            'standar' => $request->standar,
        ]);

        return redirect()->route('superadmin.indikator_mutu.create')
            ->with('success', 'Indikator mutu baru berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'variabel' => 'required|string',
            'standar' => 'required|string|max:255',
        ]);

        $indikator = IndikatorMutu::find($id);

        if (!$indikator) {
            return redirect()->route('superadmin.indikator_mutu.create')->with('error', 'Data indikator tidak ditemukan.');
        }

        try {
            $indikator->update([
                'id_kategori' => $request->id_kategori,
                'variabel' => $request->variabel,
                'standar' => $request->standar,
            ]);

            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('success', 'Indikator mutu berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $isInUse = IndikatorRuangan::where('id_indikator', $id)->exists();

        if ($isInUse) {
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Indikator ini tidak dapat dihapus karena sedang digunakan oleh satu atau lebih ruangan.');
        }

        try {
            IndikatorMutu::destroy($id);

            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('success', 'Indikator mutu berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->route('superadmin.indikator_mutu.create')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}