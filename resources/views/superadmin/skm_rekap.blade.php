@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-color: #337354;
            --secondary-color: #2a7f54;
            --text-dark: #2d2d2d;
            --text-light: #ffffff;
            --background-light: #fcfcfc;
            --background-medium: rgba(214, 227, 221, 0.5);
            --border-color-light: rgba(51, 115, 84, 0.5);
            --border-medium: #77a28d;
            --border-color-medium: #77a28d;
            --border-color-dark: #337354;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);
            --bg-accent: rgba(214, 227, 221, 0.5);
        }

        html,
        body {
            width: 100%;
            overflow-x: hidden;
            font-size: 0.85rem;
        }

        .page-container,
        .content-inner,
        .table-container,
        .table-wrapper {
            max-width: 100%;
            overflow-x: auto;
            box-sizing: border-box;
        }



        .survey-table {
            max-width: 100%;
            width: 100%;
            table-layout: fixed;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
            background-color: var(--background-light);
            overflow: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            font-family: inherit;
        }

        /* Header */
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
            border-bottom: 1px solid var(--border-color-semitransparent);
            padding: 0 35px;
            height: 70px;
            box-sizing: border-box;
        }

        main {
            margin-top: 65px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-avatar {
            height: 26px;
            width: 26px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            font-size: 16px;
            color: var(--primary-color);
            white-space: nowrap;
        }

        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 26px;
            width: 26px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .logout-link:hover {
            background: rgba(51, 115, 84, 0.1);
        }

        .logout-icon {
            height: 24px;
            width: 24px;
        }

        @media (max-width: 1024px) {
            .site-header {
                flex-direction: column;
                gap: 16px;
                padding: 16px;
            }

            .logo-container {
                padding-left: 0;
            }

            .user-info {
                padding-right: 0;
                width: 100%;
                justify-content: center;
            }

            .brand-name {
                font-size: 24px;
            }

            .user-name {
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .brand-name {
                font-size: 18px;
            }

            .user-name {
                font-size: 16px;
            }

            .logo-graphic {
                width: 55px;
                height: 50px;
                transform: scale(0.75);
                transform-origin: left center;
            }
        }

        .survey-nav-container {
            background-color: var(--bg-accent);
            padding: 100px 0 0;
            text-align: center;
        }

        .survey-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 28px;
            line-height: 44px;
            color: var(--primary-color);
            margin: 0 0 30px 0;
        }

        .survey-tabs {
            display: flex;
            justify-content: center;
            border-bottom: 1px solid var(--border-medium);
        }

        .tab-item {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            font-size: 16px;
            line-height: 24px;
            color: #aaa;
            text-decoration: none;
            padding: 8px 16px;
            text-align: center;
            min-width: 30%;
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
            transition: all 0.2s ease-in-out;
        }

        .tab-item:hover {
            color: var(--primary-color);
        }

        .tab-item.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .survey-title {
                font-size: 26px;
                margin-bottom: 20px;
            }

            .survey-tabs {
                justify-content: flex-start;
                overflow-x: auto;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .survey-tabs::-webkit-scrollbar {
                display: none;
            }

            .tab-item {
                padding: 6px 12px;
                font-size: 14px;
            }
        }

        .survey-recap-section {
            padding: 0 0 60px 0;
        }

        .content-inner {
            padding: 20px 45px;
            max-width: 100%;
            box-sizing: border-box;
            margin: 0 auto;
        }

        .toolbar {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin: 0 0 10px 0;
            padding: 0;
        }

        /* Naikkan posisi toolbar saja */
        .content-inner .toolbar {
            margin-top: -20px;
            /* atur sesuai selera, bisa -10px sampai -25px */
            position: relative;
            z-index: 200;
            /* biar nggak ketiban elemen lain */
            margin-bottom: 20px;
        }

        .toolbar-left,
        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-container {
            width: 100%;
            box-sizing: border-box;
            padding-bottom: 60px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .table-header-group {
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            font-weight: 550;
            line-height: 26px;
            border: 1px solid var(--border-color-medium);
            border-bottom: none;
            border-radius: 16px 16px 0 0;
            overflow: hidden;
        }

        .table-main-title,
        .table-sub-title {
            background-color: #d6e3dd;
            padding: 8px;
            font-size: 16px;
            border-bottom: 1px solid var(--border-color-medium);
        }

        .table-sub-title:last-child {
            border-bottom: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            background: #fff;
            border: 1px solid #d8e8de;
        }

        .btn-calendar {
            border: 1px solid var(--primary-color);
            position: relative;
            background: #fff;
            color: var(--text);
            min-width: 150px;
            justify-content: flex-start;
        }

        .btn-calendar:hover {
            background: #fbfbfb;
        }

        .btn-download {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
        }

        .btn-download:hover {
            background: #f7fbf8;
        }

        .calendar-panel {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            z-index: 150;
            display: none;
            flex-direction: column;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 8px;
            width: 230px;
        }

        .calendar-panel.open {
            display: flex;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .calendar-header button {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            padding: 3px 6px;
        }

        .calendar-months {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }

        .calendar-months button {
            padding: 6px;
            border: none;
            background: #f7f7f7;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .calendar-months button:hover {
            background: #eaeaea;
        }

        .calendar-months button.active {
            background: #337354;
            color: #fff;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #2f6d4e;
            padding: 6px;
            font-size: 14px;
            word-wrap: break-word;
        }

        thead th {
            background-color: #d8e5df;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #eef5f0;
        }

        .survey-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-color-dark);
            border-top: none;
        }

        .survey-table th,
        .survey-table td {
            border: 1px solid var(--border-color-dark);
            text-align: center;
            padding: 4px;
            height: 24px;
        }

        .survey-table thead th {
            font-weight: 600;
            font-size: 16px;
            line-height: 22px;
            height: 40px;
        }

        .survey-table thead tr:last-child th {
            font-size: 12px;
            line-height: 16px;
            height: 24px;
        }

        .survey-table tbody td {
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 14px;
            line-height: 20px;
        }

        .no-col {
            width: 40px;
        }

        .total-col {
            width: 180px;
        }

        .question-col {
            min-width: 950px;
        }
    </style>
@endsection

@section('content')
        <section id="survey-navigation" class="survey-nav-container">
            <h2 class="survey-title">SURVEI KEPUASAN MASYARAKAT</h2>
            <nav class="survey-tabs">
                <a href="{{ route('superadmin.skm_rekap') }}"
                    class="tab-item {{ request()->routeIs('superadmin.skm_rekap') ? 'active' : '' }}">
                    Rekap SKM
                </a>
                <a href="{{ route('superadmin.skm_edit2') }}"
                    class="tab-item {{ request()->routeIs('superadmin.skm_edit2') ? 'active' : '' }}">
                    Edit Pertanyaan
                </a>
                <a href="{{ route('superadmin.skm_hasil') }}"
                    class="tab-item {{ request()->routeIs('superadmin.skm_hasil') ? 'active' : '' }}">
                    Hasil Survei
                </a>
            </nav>
        </section>

        <main class="survey-recap-section">
            <div class="content-inner">
                <div class="toolbar" aria-label="toolbar">
                    <!-- Toolbar kiri -->
                    <div class="toolbar-left">
                        <div style="position:relative;">
                            <button id="calendarBtn" class="btn btn-calendar" aria-haspopup="true" aria-expanded="false"
                                aria-controls="calendarPanel">
                                <svg width="35" height="35" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M31.4057 6.675V3.75C31.4057 2.98125 30.7682 2.34375 29.9995 2.34375C29.2307 2.34375 28.5932 2.98125 28.5932 3.75V6.5625H16.4057V3.75C16.4057 2.98125 15.7682 2.34375 14.9995 2.34375C14.2307 2.34375 13.5932 2.98125 13.5932 3.75V6.675C8.5307 7.14375 6.07445 10.1625 5.69945 14.6437C5.66195 15.1875 6.11195 15.6375 6.63695 15.6375H38.362C38.9057 15.6375 39.3557 15.1687 39.2995 14.6437C38.9245 10.1625 36.4682 7.14375 31.4057 6.675Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M37.5 18.45C38.5313 18.45 39.375 19.2937 39.375 20.325V31.875C39.375 37.5 36.5625 41.25 30 41.25H15C8.4375 41.25 5.625 37.5 5.625 31.875V20.325C5.625 19.2937 6.46875 18.45 7.5 18.45H37.5Z"
                                        fill="#337354" />
                                    <path
                                        d="M15.9375 28.1249C15.45 28.1249 14.9625 27.9186 14.6063 27.5811C14.2688 27.2249 14.0625 26.7374 14.0625 26.2499C14.0625 25.7624 14.2688 25.2749 14.6063 24.9187C15.1313 24.3937 15.9563 24.2249 16.65 24.5249C16.8938 24.6186 17.1 24.7499 17.2687 24.9187C17.6062 25.2749 17.8125 25.7624 17.8125 26.2499C17.8125 26.7374 17.6062 27.2249 17.2687 27.5811C16.9125 27.9186 16.425 28.1249 15.9375 28.1249Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M22.5 28.1249C22.0125 28.1249 21.525 27.9186 21.1688 27.5811C20.8313 27.2249 20.625 26.7374 20.625 26.2499C20.625 25.7624 20.8313 25.2749 21.1688 24.9187C21.3375 24.7499 21.5437 24.6186 21.7875 24.5249C22.4812 24.2249 23.3062 24.3937 23.8312 24.9187C24.1687 25.2749 24.375 25.7624 24.375 26.2499C24.375 26.7374 24.1687 27.2249 23.8312 27.5811C23.7375 27.6561 23.6438 27.7311 23.55 27.8061C23.4375 27.8811 23.325 27.9374 23.2125 27.9749C23.1 28.0312 22.9875 28.0687 22.875 28.0874C22.7438 28.1062 22.6313 28.1249 22.5 28.1249Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M29.0625 28.125C28.575 28.125 28.0875 27.9188 27.7313 27.5813C27.3938 27.225 27.1875 26.7375 27.1875 26.25C27.1875 25.7625 27.3938 25.275 27.7313 24.9188C27.9188 24.75 28.1062 24.6187 28.35 24.525C28.6875 24.375 29.0625 24.3375 29.4375 24.4125C29.55 24.4313 29.6625 24.4687 29.775 24.525C29.8875 24.5625 30 24.6188 30.1125 24.6938C30.2063 24.7688 30.3 24.8438 30.3937 24.9188C30.7312 25.275 30.9375 25.7625 30.9375 26.25C30.9375 26.7375 30.7312 27.225 30.3937 27.5813C30.3 27.6563 30.2063 27.7312 30.1125 27.8062C30 27.8812 29.8875 27.9375 29.775 27.975C29.6625 28.0313 29.55 28.0688 29.4375 28.0875C29.3063 28.1063 29.175 28.125 29.0625 28.125Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M15.9375 34.6875C15.6938 34.6875 15.45 34.6313 15.225 34.5375C14.9812 34.4438 14.7938 34.3125 14.6063 34.1438C14.2688 33.7875 14.0625 33.3 14.0625 32.8125C14.0625 32.325 14.2688 31.8375 14.6063 31.4813C14.7938 31.3125 14.9812 31.1812 15.225 31.0875C15.5625 30.9375 15.9375 30.9 16.3125 30.975C16.425 30.9938 16.5375 31.0312 16.65 31.0875C16.7625 31.125 16.875 31.1813 16.9875 31.2563C17.0813 31.3313 17.175 31.4063 17.2687 31.4813C17.6062 31.8375 17.8125 32.325 17.8125 32.8125C17.8125 33.3 17.6062 33.7875 17.2687 34.1438C17.175 34.2188 17.0813 34.3125 16.9875 34.3687C16.875 34.4437 16.7625 34.5 16.65 34.5375C16.5375 34.5938 16.425 34.6313 16.3125 34.65C16.1813 34.6688 16.0688 34.6875 15.9375 34.6875Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M22.5 34.6875C22.0125 34.6875 21.525 34.4812 21.1688 34.1437C20.8313 33.7875 20.625 33.3 20.625 32.8125C20.625 32.325 20.8313 31.8375 21.1688 31.4813C21.8625 30.7875 23.1375 30.7875 23.8312 31.4813C24.1687 31.8375 24.375 32.325 24.375 32.8125C24.375 33.3 24.1687 33.7875 23.8312 34.1437C23.475 34.4812 22.9875 34.6875 22.5 34.6875Z"
                                        fill="#FFC107" />
                                    <path
                                        d="M29.0625 34.6875C28.575 34.6875 28.0875 34.4812 27.7313 34.1437C27.3938 33.7875 27.1875 33.3 27.1875 32.8125C27.1875 32.325 27.3938 31.8375 27.7313 31.4813C28.425 30.7875 29.7 30.7875 30.3937 31.4813C30.7312 31.8375 30.9375 32.325 30.9375 32.8125C30.9375 33.3 30.7312 33.7875 30.3937 34.1437C30.0375 34.4812 29.55 34.6875 29.0625 34.6875Z"
                                        fill="#FFC107" />
                                </svg>
                                <span id="monthYear">Agustus 2025</span>
                            </button>

                            <!-- Panel kalender -->
                            <div id="calendarPanel" class="calendar-panel" role="dialog" aria-hidden="true">
                                <div class="calendar-header">
                                    <button id="prevYear" aria-label="Tahun Sebelumnya">‹</button>
                                    <span id="calendarYear">2025</span>
                                    <button id="nextYear" aria-label="Tahun Berikutnya">›</button>
                                </div>
                                <div class="calendar-months">
                                    <button>Jan</button><button>Feb</button><button>Mar</button>
                                    <button>Apr</button><button>Mei</button><button>Jun</button>
                                    <button>Jul</button><button class="active">Agu</button><button>Sep</button>
                                    <button>Okt</button><button>Nov</button><button>Des</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toolbar kanan -->
                    <div class="toolbar-right">
                        <button id="downloadBtn" class="btn btn-download" title="Download file">
                            <svg width="22" height="22" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M27 18.3333V24.1111C27 24.8773 26.6956 25.6121 26.1539 26.1539C25.6121 26.6956 24.8773 27 24.1111 27H3.88889C3.12271 27 2.38791 26.6956 1.84614 26.1539C1.30436 25.6121 1 24.8773 1 24.1111V18.3333M6.77778 11.1111L14 18.3333M14 18.3333L21.2222 11.1111M14 18.3333V1"
                                    stroke="#DC5E3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Download File</span>
                        </button>
                    </div>
                </div>


                <div class="table-container">
                    <div class="table-header-group">
                        <div class="table-main-title">REKAPITULASI SURVEY KEPUASAN MASYARAKAT</div>
                        <div class="table-sub-title">{{ \Carbon\Carbon::create()->month($selectedMonth)->isoFormat('MMMM') }}
                            {{ $selectedYear }}
                        </div>
                        <div class="table-sub-title">RSD KALISAT</div>
                    </div>
                    <div class="table-wrapper">
                        <table class="survey-table">
                            <thead>
                                <tr>
                                    {{-- PERBAIKAN 1: Gunakan rowspan="2" agar sel otomatis ke bawah --}}
                                    <th class="no-col" rowspan="2">No.</th>
                                    <th colspan="15" class="question-col">Nomor Pertanyaan</th>
                                    <th class="total-col" rowspan="2">Rata-rata IKM</th>
                                </tr>
                                <tr>
                                    {{-- Baris ini sekarang hanya berisi nomor pertanyaan dan sudah sejajar --}}
                                    @for ($i = 1; $i <= 15; $i++)
                                        <th style="width: 50px;">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataRekap as $pasien)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @for ($i = 1; $i <= 15; $i++)
                                            <td>{{ $pasien['jawaban'][$i] ?? '-' }}</td>
                                        @endfor
                                        <td><strong>{{ $pasien['total_nilai_ikm'] }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="17">Tidak ada data survei untuk periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    {{-- PERBAIKAN 2: Gabungkan sel label "Rata-Rata" dengan sel kosong di akhir --}}
                                    <td style="font-weight: bold;" colspan="16">Rata-Rata Per Pertanyaan</td>

                                    {{-- Kolom terakhir untuk rata-rata dari "Rata-rata IKM" --}}
                                    <td style="font-weight: bold;">
                                        {{-- Hitung rata-rata dari total nilai IKM --}}
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
                    </div>
                </div>
            </div>
        </main>
@endsection

@push('scripts')
    <script>
        (function () {
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const now = new Date();
            let currentYear = now.getFullYear();
            let currentMonth = now.getMonth();

            const monthYearEl = document.getElementById('monthYear');
            const panel = document.getElementById('calendarPanel');
            const btn = document.getElementById('calendarBtn');
            const yearEl = document.getElementById('calendarYear');
            const monthsContainer = document.querySelector('.calendar-months');
            const prevYearBtn = document.getElementById('prevYear');
            const nextYearBtn = document.getElementById('nextYear');

            function renderMonths() {
                yearEl.textContent = currentYear;
                monthsContainer.innerHTML = "";
                monthNames.forEach((m, i) => {
                    const b = document.createElement('button');
                    b.textContent = m;
                    if (i === currentMonth && currentYear === now.getFullYear()) b.classList.add('active');
                    b.addEventListener('click', () => {
                        currentMonth = i;
                        monthYearEl.textContent = monthNames[currentMonth] + " " + currentYear;
                        panel.classList.remove('open');
                    });
                    monthsContainer.appendChild(b);
                });
            }

            prevYearBtn.addEventListener('click', () => {
                currentYear--;
                renderMonths();
            });
            nextYearBtn.addEventListener('click', () => {
                currentYear++;
                renderMonths();
            });

            btn.addEventListener('click', (ev) => {
                ev.stopPropagation();
                panel.classList.toggle('open');
                renderMonths();
            });

            document.addEventListener('click', (e) => {
                if (!panel.contains(e.target) && !btn.contains(e.target)) {
                    panel.classList.remove('open');
                }
            });

            // default init
            monthYearEl.textContent = monthNames[currentMonth] + " " + currentYear;
        })();

    </script>
@endpush