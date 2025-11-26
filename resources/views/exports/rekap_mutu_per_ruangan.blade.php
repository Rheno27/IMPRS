<thead>
    <tr>
        <th colspan="{{ $jumlahHari + 3 }}" style="text-align: center; font-weight: bold; font-size: 14px;">
            REKAPITULASI INDIKATOR MUTU - {{ strtoupper($ruangan->nama_ruangan) }}
        </th>
    </tr>
    <tr>
        <th colspan="{{ $jumlahHari + 3 }}" style="text-align: center; font-weight: bold;">
            PERIODE: {{ strtoupper($namaBulan) }} {{ $tahun }}
        </th>
    </tr>
    <tr></tr>

    <tr>
        <th rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">No</th>
        <th rowspan="2" style="border: 1px solid #000000; font-weight: bold; vertical-align: middle; width: 40px;">
            Variabel Penilaian</th>
        <th colspan="{{ $jumlahHari }}" style="border: 1px solid #000000; font-weight: bold; text-align: center;">
            Tanggal</th>

        <th rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">Jumlah
        </th>
        <th rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">%</th>
    </tr>
    <tr>
        @for($i = 1; $i <= $jumlahHari; $i++)
            <th style="border: 1px solid #000000; text-align: center; width: 5px;">{{ $i }}</th>
        @endfor
    </tr>
</thead>

<tbody>
    @foreach($data as $row)
        <tr>
            <td rowspan="2" style="border: 1px solid #000000; vertical-align: middle; text-align: center;">{{ $row['no'] }}
            </td>
            <td rowspan="2" style="border: 1px solid #000000; vertical-align: middle; word-wrap: break-word;">
                {{ $row['variabel'] }}</td>

            @for($i = 1; $i <= $jumlahHari; $i++)
                <td style="border: 1px solid #000000; text-align: center;">
                    {{ $row['harian'][$i]['num'] ?? '' }}
                </td>
            @endfor

            <td rowspan="2" style="border: 1px solid #000000; vertical-align: middle; text-align: center;">
                {{ $row['total_num'] }}</td>
            <td rowspan="2" style="border: 1px solid #000000; vertical-align: middle; text-align: center;">
                {{ $row['persentase'] }}%</td>
        </tr>
        <tr>
            @for($i = 1; $i <= $jumlahHari; $i++)
                <td style="border: 1px solid #000000; text-align: center; background-color: #f0f0f0;">
                    {{ $row['harian'][$i]['denum'] ?? '' }}
                </td>
            @endfor
        </tr>
    @endforeach
</tbody>