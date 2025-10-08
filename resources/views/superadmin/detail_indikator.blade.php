@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --green: #337354;
            --green-dark: #004e28;
            --accent: #DC5E3A;
            --edit-bg: #ffe6e6;
            --bg: #fcfcfc;
            --text-light: #ffff;
            --text: #2d2d2d;
            --panel-shadow: 0 8px 20px rgba(10, 13, 18, 0.08);
            --border-color-light: #77a28d;
            --bg-table-header: #d6e3dd;
            --border-color-light: #77a28d;
            --border-color-dark: #337354;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .site-header {
            display: flex;
            position: fixed;
            top: 0;
            /* penting biar nempel di atas */
            left: 0;
            width: 100%;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            background-color: var(--text-light);
            border-bottom: 1px solid var(--border-color-semitransparent);
            padding: 0 40px;
            height: 80px;
            /* lebih proporsional, jangan terlalu tinggi */
            box-sizing: border-box;
            /* biar padding gak nambah tinggi */
        }

        /* Logo */
        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 70px;
            width: auto;
            object-fit: contain;
        }

        /* User info */
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
            /* sebanding dengan tinggi avatar/logo */
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

        @media (max-width: 1024px) {
            .site-header {
                padding: 0 24px;
                height: 100px;
            }

            .brand-name {
                font-size: 24px;
            }

            .username {
                font-size: 20px;
            }

            .user-icon,
            .logout-icon {
                width: 32px;
                height: 32px;
            }
        }

        @media (max-width: 768px) {
            .site-header {
                flex-direction: column;
                height: auto;
                padding: 20px;
                gap: 20px;
            }

            .user-profile {
                width: 100%;
                justify-content: flex-end;
            }
        }


        /* toolbar */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 52px;
            gap: 16px;
            padding-top: 130px;
        }

        .toolbar-left {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .toolbar-right {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* common button */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            background: #fff;
            border: 1px solid #d8e8de;
        }

        /* Calendar button */
        .btn-calendar {
            border: 1px solid var(--green);
            position: relative;
            /* panel absolute relatif ke wrapper */
            background: #fff;
            color: var(--text);
            min-width: 160px;
            justify-content: flex-start;
        }

        .btn-calendar:hover {
            background: #fbfbfb;
        }

        /* download button (green border + green text) */
        .btn-download {
            border: 1px solid var(--green);
            color: var(--green);
            background: #fff;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 18px;
        }

        .btn-download:hover {
            background: #f7fbf8;
        }

        /* edit button on the right */
        .btn-edit {
            background: var(--edit-bg);
            border: 1px solid rgba(141, 10, 0, 0.08);
            color: #8D0A00;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .btn-edit:hover {
            background: #ffd6d6;
        }

        .calendar-panel {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            z-index: 150;
            display: none;
            flex-direction: column;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 10px;
            width: 250px;
        }

        .calendar-panel.open {
            display: flex;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .calendar-header button {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            padding: 4px 8px;
        }

        .calendar-months {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .calendar-months button {
            padding: 8px;
            border: none;
            background: #f7f7f7;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .calendar-months button:hover {
            background: #eaeaea;
        }

        .calendar-months button.active {
            background: #337354;
            color: #fff;
            font-weight: bold;
        }

        /* small responsive tweak */
        @media (max-width:680px) {
            .toolbar {
                padding: 16px;
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .toolbar-left {
                order: 1
            }

            .toolbar-right {
                order: 2;
                justify-content: flex-end;
            }

            .calendar-panel {
                top: calc(100% + 6px);
                left: 0;
                right: auto;
            }
        }

        .table-wrapper {
            width: calc(100% - 104px);
            margin: 0 52px;
        }

        .report-title {
            text-align: center;
            font-weight: 550;
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
    <div class="toolbar" aria-label="toolbar">
        <div class="toolbar-left">
            <div style="position:relative;">
                <button id="calendarBtn" class="btn btn-calendar" aria-haspopup="true" aria-expanded="false"
                    aria-controls="calendarPanel">
                    <svg width="33" height="33" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M31.4067 6.675V3.75C31.4067 2.98125 30.7692 2.34375 30.0004 2.34375C29.2317 2.34375 28.5942 2.98125 28.5942 3.75V6.5625H16.4067V3.75C16.4067 2.98125 15.7692 2.34375 15.0004 2.34375C14.2317 2.34375 13.5942 2.98125 13.5942 3.75V6.675C8.53168 7.14375 6.07543 10.1625 5.70043 14.6437C5.66293 15.1875 6.11293 15.6375 6.63793 15.6375H38.3629C38.9067 15.6375 39.3567 15.1687 39.3004 14.6437C38.9254 10.1625 36.4692 7.14375 31.4067 6.675Z"
                            fill="#FFC107" />
                        <path
                            d="M37.5 18.45C38.5312 18.45 39.375 19.2937 39.375 20.325V31.875C39.375 37.5 36.5625 41.25 30 41.25H15C8.4375 41.25 5.625 37.5 5.625 31.875V20.325C5.625 19.2937 6.46875 18.45 7.5 18.45H37.5Z"
                            fill="#337354" />
                        <path
                            d="M15.9375 28.1249C15.45 28.1249 14.9625 27.9186 14.6063 27.5811C14.2688 27.2249 14.0625 26.7374 14.0625 26.2499C14.0625 25.7624 14.2688 25.2749 14.6063 24.9187C15.1313 24.3937 15.9563 24.2249 16.65 24.5249C16.8938 24.6186 17.1 24.7499 17.2687 24.9187C17.6062 25.2749 17.8125 25.7624 17.8125 26.2499C17.8125 26.7374 17.6062 27.2249 17.2687 27.5811C16.9125 27.9186 16.425 28.1249 15.9375 28.1249Z"
                            fill="#FFC107" />
                        <path
                            d="M22.5 28.1249C22.0125 28.1249 21.525 27.9186 21.1688 27.5811C20.8313 27.2249 20.625 26.7374 20.625 26.2499C20.625 25.7624 20.8313 25.2749 21.1688 24.9187C21.3375 24.7499 21.5437 24.6186 21.7875 24.5249C22.4812 24.2249 23.3062 24.3937 23.8312 24.9187C24.1687 25.2749 24.375 25.7624 24.375 26.2499C24.375 26.7374 24.1687 27.2249 23.8312 27.5811C23.7375 27.6561 23.6438 27.7311 23.55 27.8061C23.4375 27.8811 23.325 27.9374 23.2125 27.9749C23.1 28.0312 22.9875 28.0687 22.875 28.0874C22.7438 28.1062 22.6312 28.1249 22.5 28.1249Z"
                            fill="#FFC107" />
                        <path
                            d="M29.0625 28.125C28.575 28.125 28.0875 27.9188 27.7313 27.5813C27.3938 27.225 27.1875 26.7375 27.1875 26.25C27.1875 25.7625 27.3938 25.275 27.7313 24.9188C27.9188 24.75 28.1062 24.6187 28.35 24.525C28.6875 24.375 29.0625 24.3375 29.4375 24.4125C29.55 24.4313 29.6625 24.4687 29.775 24.525C29.8875 24.5625 30 24.6188 30.1125 24.6938C30.2063 24.7688 30.3 24.8438 30.3937 24.9188C30.7312 25.275 30.9375 25.7625 30.9375 26.25C30.9375 26.7375 30.7312 27.225 30.3937 27.5813C30.3 27.6563 30.2063 27.7312 30.1125 27.8062C30 27.8812 29.8875 27.9375 29.775 27.975C29.6625 28.0313 29.55 28.0688 29.4375 28.0875C29.3063 28.1063 29.175 28.125 29.0625 28.125Z"
                            fill="#FFC107" />
                        <path
                            d="M15.9375 34.6875C15.6938 34.6875 15.45 34.6313 15.225 34.5375C14.9812 34.4438 14.7938 34.3125 14.6063 34.1438C14.2688 33.7875 14.0625 33.3 14.0625 32.8125C14.0625 32.325 14.2688 31.8375 14.6063 31.4813C14.7938 31.3125 14.9812 31.1812 15.225 31.0875C15.5625 30.9375 15.9375 30.9 16.3125 30.975C16.425 30.9938 16.5375 31.0312 16.65 31.0875C16.7625 31.125 16.875 31.1813 16.9875 31.2563C17.0813 31.3313 17.175 31.4063 17.2687 31.4813C17.6062 31.8375 17.8125 32.325 17.8125 32.8125C17.8125 33.3 17.6062 33.7875 17.2687 34.1438C17.175 34.2188 17.0813 34.3125 16.9875 34.3687C16.875 34.4437 16.7625 34.5 16.65 34.5375C16.5375 34.5938 16.425 34.6313 16.3125 34.65C16.1813 34.6688 16.0687 34.6875 15.9375 34.6875Z"
                            fill="#FFC107" />
                        <path
                            d="M22.5 34.6875C22.0125 34.6875 21.525 34.4812 21.1688 34.1437C20.8313 33.7875 20.625 33.3 20.625 32.8125C20.625 32.325 20.8313 31.8375 21.1688 31.4813C21.8625 30.7875 23.1375 30.7875 23.8312 31.4813C24.1687 31.8375 24.375 32.325 24.375 32.8125C24.375 33.3 24.1687 33.7875 23.8312 34.1437C23.475 34.4812 22.9875 34.6875 22.5 34.6875Z"
                            fill="#FFC107" />
                        <path
                            d="M29.0625 34.6875C28.575 34.6875 28.0875 34.4812 27.7313 34.1437C27.3938 33.7875 27.1875 33.3 27.1875 32.8125C27.1875 32.325 27.3938 31.8375 27.7313 31.4813C28.425 30.7875 29.7 30.7875 30.3937 31.4813C30.7312 31.8375 30.9375 32.325 30.9375 32.8125C30.9375 33.3 30.7312 33.7875 30.3937 34.1437C30.0375 34.4812 29.55 34.6875 29.0625 34.6875Z"
                            fill="#FFC107" />
                    </svg>
                    <span id="monthYear" aria-live="polite">Agustus 2025</span>
                </button>

                <div id="calendarPanel" class="calendar-panel" role="dialog" aria-hidden="true">
                    <div class="calendar-header">
                        <button id="prevYear" aria-label="Tahun Sebelumnya">‹</button>
                        <span id="calendarYear"></span>
                        <button id="nextYear" aria-label="Tahun Berikutnya">›</button>
                    </div>
                    <div class="calendar-months"></div>
                </div>

            </div>
            <button id="downloadBtn" class="btn btn-download" title="Download file">
                <svg width="22" height="22" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M27 18.3333V24.1111C27 24.8773 26.6956 25.6121 26.1539 26.1539C25.6121 26.6956 24.8773 27 24.1111 27H3.88889C3.12271 27 2.38791 26.6956 1.84614 26.1539C1.30436 25.6121 1 24.8773 1 24.1111V18.3333M6.77778 11.1111L14 18.3333M14 18.3333L21.2222 11.1111M14 18.3333V1"
                        stroke="#DC5E3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Download File</span>
            </button>
        </div>

        <div class="toolbar-right">
            <button class="btn-edit" 
                    onclick="window.location.href='{{ route('superadmin.ruangan.edit_indikator', ['ruangan' => $ruangan]) }}'" 
                    id="editIndicatorBtn"
                    title="Edit indikator ruangan">
                <svg width="35" height="35" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- ... kode SVG Anda tidak perlu diubah ... --}}
                    <path
                        d="M39.375 41.25H5.625C4.85625 41.25 4.21875 40.6125 4.21875 39.8438C4.21875 39.075 4.85625 38.4375 5.625 38.4375H39.375C40.1437 38.4375 40.7812 39.075 40.7812 39.8438C40.7812 40.6125 40.1437 41.25 39.375 41.25Z"
                        fill="#8D0A00" />
                    <path
                        d="M35.6623 6.52511C32.0248 2.88761 28.4623 2.79386 24.7311 6.52511L22.4623 8.79386C22.2748 8.98136 22.1998 9.28136 22.2748 9.54386C23.6998 14.5126 27.6748 18.4876 32.6436 19.9126C32.7186 19.9314 32.7936 19.9501 32.8686 19.9501C33.0748 19.9501 33.2623 19.8751 33.4123 19.7251L35.6623 17.4564C37.5186 15.6189 38.4186 13.8376 38.4186 12.0376C38.4373 10.1814 37.5373 8.38136 35.6623 6.52511Z"
                        fill="#8D0A00" />
                    <path
                        d="M29.2686 21.6187C28.7249 21.3562 28.1999 21.0937 27.6936 20.7937C27.2811 20.55 26.8874 20.2875 26.4936 20.0062C26.1749 19.8 25.7999 19.5 25.4436 19.2C25.4061 19.1812 25.2749 19.0687 25.1249 18.9187C24.5061 18.3937 23.8124 17.7188 23.1936 16.9688C23.1374 16.9313 23.0436 16.8 22.9124 16.6312C22.7249 16.4062 22.4061 16.0312 22.1249 15.6C21.8999 15.3187 21.6374 14.9062 21.3936 14.4937C21.0936 13.9875 20.8311 13.4812 20.5686 12.9562C20.3061 12.3937 20.0999 11.85 19.9124 11.3438L8.13737 23.1187C7.89362 23.3625 7.66862 23.8312 7.61237 24.15L6.59987 31.3312C6.41237 32.6062 6.76862 33.8062 7.55612 34.6125C8.23112 35.2687 9.16861 35.625 10.1811 35.625C10.4061 35.625 10.6311 35.6062 10.8561 35.5687L18.0561 34.5563C18.3936 34.5 18.8624 34.275 19.0874 34.0312L30.8624 22.2562C30.3374 22.0687 29.8311 21.8625 29.2686 21.6187Z"
                        fill="#F44336" />
                </svg>
                <span>Edit Indikator Ruangan</span>
            </button>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="report-title">
            <p>Penilaian Indikator Mutu di Ruang {{ $ruangan->nama_ruangan }}</p>
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

                            {{-- Baris untuk Numerator (Pasien Sesuai) --}}
                            @for($tgl = 1; $tgl <= $jumlahHari; $tgl++)
                                <td>
                                    {{ isset($row['byTanggal'][$tgl]) ? $row['byTanggal'][$tgl]->pasien_sesuai : '' }}
                                </td>
                            @endfor

                            <td rowspan="2">{{ $row['jumlah_sesuai'] }}</td>
                            <td rowspan="2">{{ $row['persen'] }}%</td>
                        </tr>
                        <tr>
                            {{-- Baris untuk Denominator (Total Pasien) --}}
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
@endsection

@push('scripts')
    <script>
        (function () {
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

            // 1. Inisialisasi state dari data PHP yang dikirim controller, bukan tanggal hari ini
            let currentYear = {{ $tahun }};
            // Bulan dari PHP adalah 1-12, sedangkan di JavaScript index-nya 0-11
            let currentMonthIndex = {{ $bulan }} - 1;

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
                monthNames.forEach((m, index) => {
                    const b = document.createElement('button');
                    b.textContent = m;

                    // Tandai bulan yang sedang aktif
                    if (index === currentMonthIndex) {
                        b.classList.add('active');
                    }

                    // 2. BAGIAN UTAMA YANG DIPERBAIKI:
                    // Tambahkan event listener yang akan me-reload halaman dengan parameter baru.
                    b.addEventListener('click', () => {
                        const selectedMonth = index + 1; // Konversi ke format 1-12 untuk URL
                        const baseUrl = "{{ route('superadmin.ruangan.detail', ['ruangan' => $ruangan]) }}";

                        // Arahkan browser ke URL baru dengan parameter bulan dan tahun yang dipilih
                        window.location.href = `${baseUrl}?bulan=${selectedMonth}&tahun=${currentYear}`;
                    });
                    monthsContainer.appendChild(b);
                });
            }

            // Navigasi tahun hanya mengubah tampilan panel, tidak perlu me-reload halaman
            prevYearBtn.addEventListener('click', () => {
                currentYear--;
                renderMonths();
            });
            nextYearBtn.addEventListener('click', () => {
                currentYear++;
                renderMonths();
            });

            // Buka/tutup panel
            btn.addEventListener('click', (ev) => {
                ev.stopPropagation();
                panel.classList.toggle('open');
                // Render pilihan bulan hanya saat panel dibuka agar efisien
                if (panel.classList.contains('open')) {
                    renderMonths();
                }
            });

            // Tutup panel jika klik di area lain
            document.addEventListener('click', (e) => {
                if (!panel.contains(e.target) && !btn.contains(e.target)) {
                    panel.classList.remove('open');
                }
            });

            // 3. Inisialisasi teks pada tombol utama saat halaman pertama kali dimuat
            monthYearEl.textContent = monthNames[currentMonthIndex] + " " + currentYear;
        })();

    </script>
@endpush