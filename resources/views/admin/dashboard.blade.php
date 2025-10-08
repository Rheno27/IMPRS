@extends('layouts.app')
@section('styles')
    <style>
        :root {
            --primary-color: #337354;
            --secondary-color: #ffbb00;
            --text-dark: #2d2d2d;
            --text-light: #ffffff;
            --bg-light: #fcfcfc;
            --bg-table-header: #d6e3dd;
            --border-color-light: #77a28d;
            --border-color-dark: #337354;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .site-header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            background-color: var(--text-light);
            border-bottom: 1px solid var(--primary-color);
            padding: 0 40px;
            height: 80px;
            box-sizing: border-box;
        }

        main {
            margin-top: 70px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 70px;
            width: auto;
            object-fit: contain;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-avatar {
            height: 30px;
            width: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            font-size: 18px;
            color: var(--primary-color);
            white-space: nowrap;
        }

        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 30px;
            width: 30px;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .logout-link:hover {
            background: rgba(51, 115, 84, 0.1);
        }

        .logout-icon {
            height: 28px;
            width: 28px;
        }

        /* CSS from section:hero */
        .hero-section {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 200px 40px;
            text-align: center;
            overflow: hidden;

            min-height: 58vh;
            background-image: url('{{ asset('image/background.png') }}');
            background-repeat: no-repeat;
            background-size: cover;
            /* atau contain sesuai kebutuhan */
            background-position: center;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 32px;
            color: var(--primary-color);
        }

        .hero-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 790;
            font-size: 49px;
            /* lebih kecil dari 54px */
            line-height: 55px;
            color: var(--primary-color);
            margin: 0;
            max-width: 1000px;
        }

        .hero-subtitle {
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            font-size: 24px;
            /* lebih kecil dari 24px */
            line-height: 28px;
            color: var(--primary-color);
            margin: 0;
            max-width: 900px;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background-color: var(--secondary-color);
            color: var(--text-dark);
            font-weight: 600;
            font-size: 24px;
            /* lebih kecil dari 28px */
            line-height: 26px;
            padding: 14px 38px;
            border-radius: 40px;
            text-decoration: none;
        }

        .cta-button svg {
            width: 22px;
            height: 22px;
        }

        /* CSS from section:report */
        .report-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 38px;
        }

        .report-controls-top {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
            padding: 30px 48px 0;
            gap: 28px;
        }

        .download-btn,
        .year-selector {
            border: 1px solid var(--border-color-dark);
            border-radius: 12px;
            padding: 9px 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            font-size: 21px;
            color: var(--primary-color);
            background-color: transparent;
            text-decoration: none;
            cursor: pointer;
        }

        .year-picker-wrapper {
            position: relative;
            display: inline-block;
        }

        .year-panel {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 240px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            padding: 12px;
            display: none;
            z-index: 300;
        }

        .year-panel.open {
            display: block;
        }


        .year-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .year-header button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 4px 8px;
        }

        .year-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .year-grid button {
            padding: 8px;
            border: none;
            background: #f7f7f7;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .year-grid button:hover {
            background: #eaeaea;
        }

        .year-grid button.active {
            background: #337354;
            color: #fff;
            font-weight: bold;
        }

        .month-selector {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 48px;
        }

        .arrow-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--primary-color);
        }

        .arrow-btn svg {
            width: 48px;
            height: 48px;
        }

        .months {
            display: flex;
            align-items: center;
            gap: 48px;
        }

        .month-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 24px;
            /* dikecilkan */
            color: var(--primary-color);
            opacity: 0.6;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .month-btn.active {
            background-color: var(--primary-color);
            /* hijau */
            color: white;
            border-radius: 50px;
            padding: 15px 50px;
            opacity: 1;
        }

        .table-wrapper {
            width: calc(100% - 104px);
            margin: 0 52px;
        }

        .report-title {
            text-align: center;
            font-weight: 500;
            font-size: 22px;
            line-height: 28px;
            color: var(--text-dark);
            border: 1px solid var(--border-color-light);
            border-bottom: none;
            border-radius: 20px 20px 0 0;
            background-color: var(--bg-table-header);
        }

        .report-title p {
            margin: 0;
            padding: 10px;
            border-bottom: 1px solid var(--border-color-light);
        }

        .report-title p:last-child {
            border-bottom: none;
        }

        .table-container {
            overflow-x: auto;
        }

        .report-data {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-color-dark);
            margin: 0;
            margin-bottom: 60px;
        }

        .report-data th,
        .report-data td {
            border: 1px solid var(--border-color-dark);
            text-align: center;
            font-size: 14px;
            height: 28px;
        }

        .report-data th {
            background-color: var(--bg-table-header);
            font-weight: 600;
            font-size: 19px;
            padding: 10px 5px;
        }

        .report-data thead tr:first-child th {
            border-bottom: 1px solid var(--border-color-dark);
        }

        .report-data tbody td {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 18px;
            min-width: 28px;
        }

        .report-data .text-left {
            text-align: left;
            padding: 8px 12px;
            font-size: 18px;
        }
    </style>

@endsection

@section('content')
            <section id="hero" class="hero-section">
                <div class="hero-content">
                    <h1 class="hero-title">WEBSITE PELAPORAN INDIKATOR MUTU DAN KESELAMATAN PASIEN</h1>
                    <p class="hero-subtitle">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus a arcu quis nisi sollicitudin imperdiet.
                        Pellentesque volutpat, arcu sagittis pellentesque sollicitudin, urna urna sodales quam, vel ultrices massa
                        dui at ligula. Nullam porta ante ut turpis imperdiet porta ut a augue.
                    </p>
                    <a href="{{ route('admin.input_indikator') }}" class="cta-button">
                        <span>Isi Laporan</span>
                        <svg width="24" height="24" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.7003 31.1333L22.0503 25.7833L25.3336 22.5166C26.7169 21.1333 26.7169 18.8833 25.3336 17.5L16.7003 8.86662C15.5669 7.73329 13.6336 8.54996 13.6336 10.1333V19.4833V29.8666C13.6336 31.4666 15.5669 32.2666 16.7003 31.1333Z"
                                fill="#292D32" />
                        </svg>
                    </a>
                </div>
            </section>

            <section id="report-table" class="report-section">
                <div class="report-controls-top">
                    <a href="#" class="download-btn">
                        <svg width="35" height="35" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="50" height="50" rx="10" fill="#FCFCFC" />
                            <path
                                d="M38 29.3333V35.1111C38 35.8773 37.6956 36.6121 37.1539 37.1539C36.6121 37.6956 35.8773 38 35.1111 38H14.8889C14.1227 38 13.3879 37.6956 12.8461 37.1539C12.3044 36.6121 12 35.8773 12 35.1111V29.3333M17.7778 22.1111L25 29.3333M25 29.3333L32.2222 22.1111M25 29.3333V12"
                                stroke="#DC5E3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Download File</span>
                    </a>
                    <div class="year-picker-wrapper">
                        <div class="year-selector" id="yearBtn">
                            <svg width="35" height="36" viewBox="0 0 45 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M31.4067 7.175V4.25C31.4067 3.48125 30.7692 2.84375 30.0004 2.84375C29.2317 2.84375 28.5942 3.48125 28.5942 4.25V7.0625H16.4067V4.25C16.4067 3.48125 15.7692 2.84375 15.0004 2.84375C14.2317 2.84375 13.5942 3.48125 13.5942 4.25V7.175C8.53168 7.64375 6.07543 10.6625 5.70043 15.1437C5.66293 15.6875 6.11293 16.1375 6.63793 16.1375H38.3629C38.9067 16.1375 39.3567 15.6687 39.3004 15.1437C38.9254 10.6625 36.4692 7.64375 31.4067 7.175Z"
                                    fill="#FFC107" />
                                <path
                                    d="M37.5 18.95C38.5313 18.95 39.375 19.7937 39.375 20.825V32.375C39.375 38 36.5625 41.75 30 41.75H15C8.4375 41.75 5.625 38 5.625 32.375V20.825C5.625 19.7937 6.46875 18.95 7.5 18.95H37.5Z"
                                    fill="#337354" />
                            </svg>
                            <span id="selectedYear">{{ $tahun }}</span>
                        </div>

                        <div class="year-panel" id="yearPanel">
                            <div class="year-header">
                                <button id="prevYears">&lt;</button>
                                <span id="yearRange">2020 - 2029</span>
                                <button id="nextYears">&gt;</button>
                            </div>
                            <div class="year-grid" id="yearGrid"></div>
                        </div>
                    </div>
                </div>

                <div class="month-selector">
                    @php
// Array nama bulan lengkap
$namaBulanLengkap = [1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April", 5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"];

// Ambil bulan aktif dari controller, default ke bulan saat ini jika tidak ada
$bulanAktif = $bulan ?? date('n');

// Tentukan index untuk tombol panah
$prevIndex = ($bulanAktif - 1 < 1) ? 12 : $bulanAktif - 1;
$nextIndex = ($bulanAktif + 1 > 12) ? 1 : $bulanAktif + 1;
                    @endphp

                    {{-- Link panah kiri --}}
                    <a href="{{ route('admin.dashboard', ['bulan' => $prevIndex, 'tahun' => date('Y')]) }}" class="arrow-btn prev"
                        aria-label="Previous month">
                        <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M36 6.5C19.7076 6.5 6.5 19.7076 6.5 36C6.5 52.2924 19.7076 65.5 36 65.5C52.2924 65.5 65.5 52.2924 65.5 36C65.5 19.7076 52.2924 6.5 36 6.5Z"
                                fill="white" stroke="#DC5E3A" />
                            <path
                                d="M23.91 34.4101L32.91 25.4101C33.78 24.5401 35.22 24.5401 36.09 25.4101C36.96 26.2801 36.96 27.7201 36.09 28.5901L30.93 33.7501H46.5C47.73 33.7501 48.75 34.7701 48.75 36.0001C48.75 37.2301 47.73 38.2501 46.5 38.2501H30.93L36.09 43.4101C36.96 44.2801 36.96 45.7201 36.09 46.5901C35.64 47.0401 35.07 47.2501 34.5 47.2501C33.93 47.2501 33.36 47.0401 32.91 46.5901L23.91 37.5901C23.04 36.7201 23.04 35.2801 23.91 34.4101Z"
                                fill="#DC5E3A" />
                        </svg>
                    </a>
                    <div class="months">
                        @php
$displayMonths = [
    $prevIndex => $namaBulanLengkap[$prevIndex],
    $bulanAktif => $namaBulanLengkap[$bulanAktif],
    $nextIndex => $namaBulanLengkap[$nextIndex],
];
                        @endphp

                        @foreach ($displayMonths as $indexBulan => $namaBulanItem)
                            <a href="{{ route('admin.dashboard', ['bulan' => $indexBulan, 'tahun' => date('Y')]) }}"
                                class="month-btn {{ $bulanAktif == $indexBulan ? 'active' : '' }}">
                                {{ $namaBulanItem }}
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.dashboard', ['bulan' => $nextIndex, 'tahun' => date('Y')]) }}" class="arrow-btn next"
                        aria-label="Next month">
                        <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M36 6.5C19.7076 6.5 6.5 19.7076 6.5 36C6.5 52.2924 19.7076 65.5 36 65.5C52.2924 65.5 65.5 52.2924 65.5 36C65.5 19.7076 52.2924 6.5 36 6.5Z"
                                fill="white" stroke="#DC5E3A" />
                            <path
                                d="M48.09 37.5899L39.09 46.5899C38.22 47.4599 36.78 47.4599 35.91 46.5899C35.04 45.7199 35.04 44.2799 35.91 43.4099L41.07 38.2499H25.5C24.27 38.2499 23.25 37.2299 23.25 35.9999C23.25 34.7699 24.27 33.7499 25.5 33.7499H41.07L35.91 28.5899C35.04 27.7199 35.04 26.2799 35.91 25.4099C36.36 24.9599 36.93 24.7499 37.5 24.7499C38.07 24.7499 38.64 24.9599 39.09 25.4099L48.09 34.4099C48.96 35.2799 48.96 36.7199 48.09 37.5899Z"
                                fill="#DC5E3A" />
                        </svg>
                    </a>
                </div>

                <div class="table-wrapper">
                    <div class="report-title">
                        <p>Penilaian Indikator Mutu di Ruang Nifas</p>
                        <p>Bulan {{ $namaBulan[$bulan] ?? '' }}</p>
                        <p>RSD KALISAT</p> 
                    </div>
                    <div class="table-container">
                        <table class="report-data">
                            <thead>
                                <tr>
                                    <th rowspan="2">No.</th>
                                    <th rowspan="2">Variabel Penilaian</th>
                                    <th colspan="{{ $jumlahHari }}">Tanggal</th>
                                    <th rowspan="2">Jumlah</th>
                                    <th rowspan="2">%</th>
                                </tr>
                                <tr>
                                    @for($tgl = 1; $tgl <= $jumlahHari; $tgl++)
                                        <th>{{ $tgl }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($indikatorData as $row)
                                    <tr>
                                        <td rowspan="2">{{ $row['no'] }}.</td>
                                        <td rowspan="2" class="text-left">{{ $row['variabel'] }}</td>
                                        @for($tgl = 1; $tgl <= $jumlahHari; $tgl++)
                                            <td>
                                                {{ isset($row['byTanggal'][$tgl]) ? $row['byTanggal'][$tgl]->pasien_sesuai : '' }}
                                            </td>
                                        @endfor
                                        <td rowspan="2">{{ $row['jumlah_sesuai'] }}</td>
                                        <td rowspan="2">{{ $row['persen'] }}%</td>
                                    </tr>
                                    <tr>
                                        @for($tgl = 1; $tgl <= $jumlahHari; $tgl++)
                                            <td>
                                                {{ isset($row['byTanggal'][$tgl]) ? $row['byTanggal'][$tgl]->total_pasien : '' }}
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
@endsection

@push('scripts')
    <script>
        const selectedYearFromServer = {{ $tahun }};
    </script
>@endpush