<table>
    <thead>
        <tr>
            <th colspan="{{ count($listPertanyaan) + 2 }}"
                style="text-align: center; font-weight: bold; font-size: 14px;">
                REKAPITULASI SURVEY KEPUASAN MASYARAKAT
            </th>
        </tr>
        <tr>
            <th colspan="{{ count($listPertanyaan) + 2 }}" style="text-align: center; font-weight: bold;">
                PERIODE: {{ \Carbon\Carbon::create($tahun, $bulan, 1)->isoFormat('MMMM Y') }}
            </th>
        </tr>
        <tr></tr> 

        <tr>
            <th rowspan="2"
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">No
            </th>
            <th colspan="{{ count($listPertanyaan) }}"
                style="border: 1px solid #000000; font-weight: bold; text-align: center;">Nomor Pertanyaan</th>
            <th rowspan="2"
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">
                Rata-rata IKM</th>
        </tr>
        <tr>
            @foreach ($listPertanyaan as $i)
                <th style="border: 1px solid #000000; font-weight: bold; text-align: center;">{{ $loop->iteration }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($dataRekap as $pasien)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>

                @foreach ($listPertanyaan as $idPertanyaan)
                    <td style="border: 1px solid #000000; text-align: center;">
                        {{ $pasien['jawaban'][$idPertanyaan] ?? '-' }}
                    </td>
                @endforeach

                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                    {{ $pasien['total_nilai_ikm'] }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($listPertanyaan) + 2 }}" style="border: 1px solid #000000; text-align: center;">
                    Tidak ada data.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ count($listPertanyaan) + 1 }}"
                style="border: 1px solid #000000; font-weight: bold; text-align: right; padding-right: 10px;">
                Rata-Rata Per Pertanyaan
            </td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: center;">
                @php
                    $rataRataIKMTotal = 0;
                    if (count($dataRekap) > 0) {
                        $totalIKM = array_sum(array_column($dataRekap, 'total_nilai_ikm'));
                        $rataRataIKMTotal = $totalIKM / count($dataRekap);
                    }
                @endphp
                {{ number_format($rataRataIKMTotal, 2) }}
            </td>
        </tr>
    </tfoot>
</table>