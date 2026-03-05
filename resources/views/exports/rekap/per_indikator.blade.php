<table>
    <thead>
        <tr>
            {{-- Header Judul --}}
            <th colspan="{{ $kategori === 'Indikator Mutu Prioritas Unit' ? '20' : '19' }}"
                style="text-align: center; font-weight: bold; font-size: 14px; height: 30px; vertical-align: middle;">
                HASIL REKAPITULASI {{ strtoupper($kategori) }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ $kategori === 'Indikator Mutu Prioritas Unit' ? '20' : '19' }}"
                style="text-align: center; font-weight: bold; height: 30px; vertical-align: middle;">
                TAHUN: {{ $tahun }}
            </th>
        </tr>
        <tr></tr>

        <tr>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 5px;">
                NO
            </th>

            @if($kategori === 'Indikator Mutu Prioritas Unit')
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
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 15px;">
                STANDAR
            </th>

            {{-- Loop Nama Bulan --}}
            @foreach(['JANUARI', 'FEBRUARI', 'MARET', 'APRIL', 'MEI', 'JUNI', 'JULI', 'AGUSTUS', 'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'] as $bln)
                <th
                    style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 12px;">
                    {{ $bln }}
                </th>
            @endforeach

            {{-- Kolom Triwulan --}}
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 12px; background-color: #FFF2CC;">
                TW 1
            </th>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 12px; background-color: #FCE4D6;">
                TW 2
            </th>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 12px; background-color: #EAD1DC;">
                TW 3
            </th>
            <th
                style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle; width: 12px; background-color: #F4CCCC;">
                TW 4
            </th>
        </tr>
    </thead>
    <tbody>
        {{-- Inisialisasi Nomor --}}
        @php $no = 1; @endphp

        @if($kategori === 'Indikator Mutu Prioritas Unit')

            @foreach($data as $namaRuangan => $items)
                @foreach($items as $index => $item)
                    <tr>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">
                            {{ $no++ }}
                        </td>

                        @if($index === 0)
                            <td rowspan="{{ count($items) }}"
                                style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">
                                {{ $namaRuangan }}
                            </td>
                        @endif

                        <td style="border: 1px solid #000000; vertical-align: top; word-wrap: break-word;">
                            {{ $item->judul }}
                        </td>
                        <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">
                            {{ $item->standar }}
                        </td>

                        @for($b = 1; $b <= 12; $b++)
                            <td style="border: 1px solid #000000; text-align: center;">
                                {{ $item->data_bulan[$b] !== null ? $item->data_bulan[$b] . '%' : '' }}
                            </td>
                        @endfor

                        @for($q = 1; $q <= 4; $q++)
                            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                                {{ $item->data_tw[$q] !== null ? $item->data_tw[$q] . '%' : '' }}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            @endforeach

        @else
            @foreach($data as $item)
                <tr>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">
                        {{ $no++ }}
                    </td>

                    <td style="border: 1px solid #000000; vertical-align: top; word-wrap: break-word;">
                        {{ $item->judul }}
                    </td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: top;">
                        {{ $item->standar }}
                    </td>

                    @for($b = 1; $b <= 12; $b++)
                        <td style="border: 1px solid #000000; text-align: center;">
                            {{ $item->data_bulan[$b] !== null ? $item->data_bulan[$b] . '%' : '' }}
                        </td>
                    @endfor

                    @for($q = 1; $q <= 4; $q++)
                        <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                            {{ $item->data_tw[$q] !== null ? $item->data_tw[$q] . '%' : '' }}
                        </td>
                    @endfor
                </tr>
            @endforeach
        @endif
    </tbody>
</table>