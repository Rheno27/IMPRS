<table>
    <thead>
        <tr>
            {{-- Sesuaikan colspan. IMPU = 20 kolom (A-T), INM = 19 kolom (A-S) --}}
            <th colspan="{{ $kategori === 'Indikator Mutu Prioritas Unit (IMPU)' ? '20' : '19' }}"
                style="text-align: center; font-weight: bold; font-size: 14px; height: 30px; vertical-align: middle;">
                HASIL REKAPITULASI {{ strtoupper($kategori) }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ $kategori === 'Indikator Mutu Prioritas Unit (IMPU)' ? '20' : '19' }}"
                style="text-align: center; font-weight: bold; height: 30px; vertical-align: middle;">
                TAHUN: {{ $tahun }}
            </th>
        </tr>
        <tr></tr>

        <tr>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 5px;">
                NO</th>

            {{-- TAMPILKAN KOLOM RUANGAN HANYA JIKA IMPU --}}
            @if($kategori === 'Indikator Mutu Prioritas Unit (IMPU)')
                <th
                    style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 20px;">
                    RUANGAN
                </th>
            @endif

            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 40px;">
                JUDUL INDIKATOR
            </th>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px;">
                STANDAR
            </th>

            @foreach(['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'] as $bln)
                <th
                    style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px;">
                    {{ $bln }}
                </th>
            @endforeach

            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px;">
                TW 1</th>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px;">
                TW 2</th>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px;">
                TW 3</th>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 10px;">
                TW 4</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp

        {{-- === LOGIKA TAMPILAN 1: IMPU (Grouping by Ruangan) === --}}
        @if($kategori === 'Indikator Mutu Prioritas Unit (IMPU)')
            @foreach($data as $namaRuangan => $items)
                @foreach($items as $index => $item)
                    <tr>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $no++ }}</td>

                        {{-- Merge Cell Ruangan --}}
                        @if($index === 0)
                            <td rowspan="{{ count($items) }}"
                                style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">
                                {{ $namaRuangan }}
                            </td>
                        @endif

                        <td style="border: 1px solid #000000; vertical-align: top; word-wrap: break-word;">{{ $item->judul }}</td>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $item->standar }}</td>

                        {{-- Data Bulan 1-12 --}}
                        @for($b = 1; $b <= 12; $b++)
                            <td style="border: 1px solid #000000; text-align: center;">
                                {{ $item->data_bulan[$b] !== null ? $item->data_bulan[$b] . '%' : '' }}
                            </td>
                        @endfor

                        {{-- Data Triwulan --}}
                        @for($q = 1; $q <= 4; $q++)
                            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f0f0f0;">
                                {{ $item->data_tw[$q] !== null ? $item->data_tw[$q] . '%' : '' }}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            @endforeach

            {{-- === LOGIKA TAMPILAN 2: INM & IMPRS (List Biasa Tanpa Ruangan) === --}}
        @else
            @foreach($data as $item)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $no++ }}</td>

                    {{-- Kolom Ruangan TIDAK ADA DI SINI --}}

                    <td style="border: 1px solid #000000; vertical-align: top; word-wrap: break-word;">{{ $item->judul }}</td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">{{ $item->standar }}</td>

                    {{-- Data Bulan 1-12 --}}
                    @for($b = 1; $b <= 12; $b++)
                        <td style="border: 1px solid #000000; text-align: center;">
                            {{ $item->data_bulan[$b] !== null ? $item->data_bulan[$b] . '%' : '' }}
                        </td>
                    @endfor

                    {{-- Data Triwulan --}}
                    @for($q = 1; $q <= 4; $q++)
                        <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #f0f0f0;">
                            {{ $item->data_tw[$q] !== null ? $item->data_tw[$q] . '%' : '' }}
                        </td>
                    @endfor
                </tr>
            @endforeach
        @endif
    </tbody>
</table>