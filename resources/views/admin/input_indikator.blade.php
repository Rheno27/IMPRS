@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-green: #337354;
            --dark-green: #2a7f54;
            --light-green-bg: #d6e3dd;
            --dark-text: #2d2d2d;
            --light-text: #9e9e9e;
            --white: #ffffff;
            --background: #fcfcfc;
            --border-color: rgba(51, 115, 84, 0.5);
            --edit-btn-bg: #5f4c14;
            --save-btn-bg: #004e28;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);
        }

        body {
            margin: 0;
            font-family: 'Inter', 'Roboto', sans-serif;
            background-color: var(--background);
            color: var(--dark-text);
        }

        .page-container {
            max-width: 1440px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        button {
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            /* Penyesuaian Ukuran */
            gap: 6px;
            padding: 3px 12px;
            border-radius: 6px;
            color: var(--white);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            line-height: 18px;
        }

        button:hover {
            opacity: 0.9;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* CSS from section:header */
        .site-header {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            justify-content: space-between;
            align-items: center;
            background-color: var(--background);
            border-bottom: 1px solid var(--border-color-semitransparent);
            box-sizing: border-box;
            /* Penyesuaian Ukuran */
            padding: 0 30px;
            height: 60px;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            /* Penyesuaian Ukuran */
            height: 52px;
            width: auto;
            object-fit: contain;
        }

        .user-info {
            display: flex;
            align-items: center;
            /* Penyesuaian Ukuran */
            gap: 15px;
        }

        .user-avatar {
            /* Penyesuaian Ukuran */
            height: 22px;
            width: 22px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            color: var(--primary-color);
            white-space: nowrap;
            /* Penyesuaian Ukuran */
            font-size: 14px;
        }

        .logout-link {
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            /* Penyesuaian Ukuran */
            height: 22px;
            width: 22px;
            border-radius: 6px;
        }

        .logout-link:hover {
            background: rgba(51, 115, 84, 0.1);
        }

        .logout-icon {
            /* Penyesuaian Ukuran */
            height: 21px;
            width: 21px;
        }

        main {
            /* Penyesuaian Ukuran: margin-top = tinggi header */
            margin-top: 60px;
        }

        /* CSS from section:main-content */
        .main-content-section {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            /* Penyesuaian Ukuran */
            padding: 40px 54px;
            gap: 27px;
        }

        .date-picker {
            position: relative;
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--primary-green);
            background: #fff;
            /* Penyesuaian Ukuran */
            gap: 8px;
            padding: 6px 9px;
            border-radius: 8px;
        }

        .date-picker .calendar-btn {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            border-radius: 0 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: inherit;
            line-height: 0;
            /* Penyesuaian Ukuran */
            width: 21px;
            height: 21px;
        }

        .calendar-btn svg {
            /* Penyesuaian Ukuran */
            width: 34px;
            height: 34px;
        }

        .date-text {
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            color: var(--dark-text);
            /* Penyesuaian Ukuran */
            font-size: 15px;
        }

        /* Ukuran pop-up kalender juga disesuaikan */
        .calendar-popup {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #ffffff;
            border: 1px solid var(--primary-green);
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            z-index: 1200;
            /* Penyesuaian Ukuran */
            width: 240px;
            padding: 8px;
        }

        .calendar-popup.hidden {
            display: none;
        }

        .calendar-popup .cal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .calendar-popup .cal-header .month-label {
            font-weight: 600;
            color: var(--primary-green);
        }

        .calendar-popup .cal-nav button {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
        }

        .calendar-popup .weekday-row {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
            font-size: 11px;
            color: var(--light-text);
            text-align: center;
        }

        .calendar-popup .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .calendar-popup .day-btn {
            background: transparent;
            border: none;
            color: var(--dark-text);
            padding: 5px;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            font-size: 13px;
        }

        .calendar-popup .day-btn:hover {
            background: rgba(51, 115, 84, 0.08);
        }

        .calendar-popup .day-btn.selected {
            background: var(--primary-green);
            color: #fff;
        }

        .calendar-popup .cal-nav svg {
            stroke: var(--primary-green);
            width: 16px;
            height: 16px;
        }

        .calendar-popup .cal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 10px;
        }

        .calendar-popup .cancel-btn,
        .calendar-popup .confirm-btn {
            /* Penyesuaian Ukuran */
            min-width: 60px;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .calendar-popup .cancel-btn {
            background: #e0e0e0;
            color: #555;
        }

        .calendar-popup .confirm-btn {
            background: var(--primary-green);
            color: #fff;
        }

        .calendar-popup .cal-header select {
            font-family: 'Roboto', sans-serif;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #f0f0f0;
            color: #333;
            cursor: pointer;
            /* Penyesuaian Ukuran */
            font-size: 14px;
            padding: 4px 8px;
        }

        .calendar-popup .cal-header select:focus {
            border: 2px solid var(--primary-green);
            outline: none;
        }

        .indicator-table-container {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .table-title {
            background-color: var(--primary-green);
            color: var(--white);
            font-weight: 600;
            text-align: center;
            margin: 0;
            /* Penyesuaian Ukuran */
            font-size: 18px;
            padding: 14px 8px;
            border-radius: 15px 15px 0 0;
        }

        .indicator-grid {
            display: grid;
            /* Penyesuaian Ukuran */
            grid-template-columns: 35px 1.5fr 1fr 1fr;
            border: 1px solid var(--primary-green);
            border-top: none;
        }

        .grid-header {
            background-color: var(--light-green-bg);
            border-bottom: 1px solid var(--primary-green);
            border-right: 1px solid var(--primary-green);
            text-align: center;
            font-family: 'Roboto', sans-serif;
            font-weight: 600;
            line-height: 1.4;
            /* Penyesuaian Ukuran */
            padding: 8px 7px;
            font-size: 14px;
        }

        .grid-header:last-child {
            border-right: none;
        }

        .grid-cell {
            background-color: var(--white);
            border-bottom: 1px solid var(--primary-green);
            border-right: 1px solid var(--primary-green);
            display: flex;
            align-items: center;
            /* Penyesuaian Ukuran */
            padding: 9px;
            font-size: 14px;
        }

        .grid-cell:nth-child(4n) {
            border-right: none;
        }

        .indicator-grid>.grid-cell:nth-last-child(-n+4) {
            border-bottom: none;
        }

        .cell-no {
            justify-content: center;
            font-weight: 500;
            /* Penyesuaian Ukuran */
            font-size: 14px;
        }

        .cell-placeholder {
            color: var(--light-text);
        }

        .cell-actions {
            justify-content: center;
            gap: 12px;
        }

        .input-plain {
            width: 100%;
            padding: 4px 6px;
            border: none;
            border-bottom: 1px solid #ccc;
            font-family: inherit;
            background-color: transparent;
            outline: none;
            /* Penyesuaian Ukuran */
            font-size: 14px;
        }

        .input-plain::-webkit-inner-spin-button,
        .input-plain::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Tombol save utama di bawah form */
        .save-btn {
            background-color: var(--save-btn-bg);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            /* Penyesuaian Ukuran */
            font-size: 16px;
            width: 150px;
            height: 40px;
            gap: 8px;
            border-radius: 8px;
        }

        /* ========================================= */
        /* RESPONSIVE CSS - MEDIA QUERIES       */
        /* ========================================= */

        /* Untuk Tablet Landscape (lebar <= 1024px) */
        @media (max-width: 1024px) {
            .main-content-section {
                /* Mengurangi padding agar konten lebih lega */
                padding: 30px 40px;
            }

            .table-title {
                font-size: 17px;
            }

            .grid-header,
            .grid-cell,
            .input-plain {
                font-size: 13px;
                /* Sedikit mengecilkan font tabel */
            }
        }

        /* Untuk Tablet Portrait (lebar <= 768px) */
        @media (max-width: 768px) {
            .site-header {
                padding: 0 15px;
                height: 55px;
            }

            main {
                margin-top: 55px;
            }

            .logo-image {
                height: 45px;
            }

            .user-name {
                display: none;
            }

            .main-content-section {
                padding: 24px;
                /* Konten memenuhi lebar layar */
                align-items: stretch;
            }

            .date-picker {
                /* Pindahkan pemilih tanggal ke kanan */
                align-self: flex-end;
            }

            /* --- Transformasi Tabel Menjadi Kartu --- */
            .indicator-grid {
                display: block;
                /* Hapus layout grid */
                border: none;
            }

            .grid-header {
                display: none;
                /* Sembunyikan header tabel di tampilan ini */
            }

            .grid-cell {
                display: grid;
                /* Buat 2 kolom: satu untuk label, satu untuk input/data */
                grid-template-columns: 150px 1fr;
                border: 1px solid var(--primary-green);
                border-top: none;
                padding: 12px;
                gap: 12px;
                align-items: center;
            }

            /* Beri border atas untuk baris pertama setiap item */
            .grid-cell.cell-no {
                border-top: 1px solid var(--primary-green);
                border-radius: 10px 10px 0 0;
            }

            /* Beri border radius di akhir setiap item kartu dan spasi bawah */
            .grid-cell:nth-child(4n) {
                border-radius: 0 0 10px 10px;
                margin-bottom: 15px;
            }

            /* Gunakan pseudo-element untuk membuat label dari data-label */
            .grid-cell::before {
                content: attr(data-label);
                font-weight: 600;
                padding-right: 10px;
            }

            /* Menambahkan teks label secara dinamis */
            .grid-cell:nth-child(4n-3)::before {
                content: "No.";
            }

            .grid-cell:nth-child(4n-2)::before {
                content: "Variabel Penilaian";
            }

            .grid-cell:nth-child(4n-1)::before {
                content: "Jml. Sesuai Indikator";
            }

            .grid-cell:nth-child(4n)::before {
                content: "Total Pasien";
            }

            .cell-no {
                justify-content: flex-start;
            }
        }

        .alert {
            padding: 15px;
            margin-bottom: 24px;
            /* Samakan dengan 'gap' di main-content-section */
            border: 1px solid transparent;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            width: 100%;
            /* Agar lebarnya sama dengan elemen lain */
            box-sizing: border-box;
            /* Penting agar padding tidak merusak layout */
        }

        .alert-success {
            color: var(--save-btn-bg);
            /* Pakai variabel warna 'save' kamu */
            background-color: #d6e3dd;
            /* Pakai variabel 'light-green-bg' */
            border-color: var(--primary-green);
            /* Pakai variabel 'primary-green' */
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
@endsection

@section('content')

    <main id="main-content" class="main-content-section">
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="date-picker">
            <button type="button" id="calendarTrigger" class="calendar-btn" aria-label="Pilih tanggal">
                <svg width="45" height="45" viewBox="0 0 45 45" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M31.4067 6.675V3.75C31.4067 2.98125 30.7692 2.34375 30.0004 2.34375C29.2317 2.34375 28.5942 2.98125 28.5942 3.75V6.5625H16.4067V3.75C16.4067 2.98125 15.7692 2.34375 15.0004 2.34375C14.2317 2.34375 13.5942 2.98125 13.5942 3.75V6.675C8.53168 7.14375 6.07543 10.1625 5.70043 14.6437C5.66293 15.1875 6.11293 15.6375 6.63793 15.6375H38.3629C38.9067 15.6375 39.3567 15.1687 39.3004 14.6437C38.9254 10.1625 36.4692 7.14375 31.4067 6.675Z"
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
            </button>
            <span id="dateDisplay" class="date-text">--</span>

            <div id="calendarPopup" class="calendar-popup hidden" aria-hidden="true">
                <div class="cal-header">
                    <select id="monthSelect"></select>
                    <select id="yearSelect"></select>
                </div>
                <div class="weekday-row">
                    <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span>
                    <span>Thu</span><span>Fri</span><span>Sat</span>
                </div>
                <div id="calendarGrid" class="calendar-grid"></div>
                <div class="cal-footer">
                    <button id="cancelBtn" class="cancel-btn">Cancel</button>
                    <button id="confirmBtn" class="confirm-btn">Confirm</button>
                </div>
            </div>
        </div>

        <div class="indicator-table-container">
            <h2 class="table-title">Penilaian Indikator Mutu di Ruang Nifas</h2>
            <form method="POST" action="{{ route('admin.input_indikator.store') }}">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <div class="indicator-grid">
                    <!-- Table Header -->
                    <div class="grid-header">No.</div>
                    <div class="grid-header">Variabel Penilaian</div>
                    <div class="grid-header">Jumlah Pasien/Kejadian yang <br>Memenuhi Indikator</div>
                    <div class="grid-header">Total Pasien/Kejadian</div>

                    <!-- Table Data Row (Dinamis) -->
                    @foreach($indikator as $i => $item)
                        <div class="grid-cell cell-no">{{ $i + 1 }}.</div>
                        <div class="grid-cell">{{ $item->variabel ?? $item->standar }}</div>
                        <div class="grid-cell">
                            <input type="number" name="pasien_sesuai[{{ $item->id_indikator }}]" class="input-plain"
                                placeholder="Isi jumlah pasien yang memenuhi indikator"
                                value="{{ isset($mutu[$item->id_indikator]) && $mutu[$item->id_indikator]->pasien_sesuai > 0 ? $mutu[$item->id_indikator]->pasien_sesuai : '' }}">
                        </div>
                        <div class="grid-cell">
                            <input type="number" name="total_pasien[{{ $item->id_indikator }}]" class="input-plain"
                                placeholder="Isi total pasien hari ini"
                                value="{{ isset($mutu[$item->id_indikator]) && $mutu[$item->id_indikator]->total_pasien > 0 ? $mutu[$item->id_indikator]->total_pasien : '' }}">
                        </div>
                    @endforeach
                </div>
                <div style="text-align: right; margin-top: 24px;">
                    <button class="save-btn" type="submit">
                        <span>Simpan</span>
                    </button>
                </div>
            </form>
        </div>

    </main>

@endsection

@push('scripts')
    <script>
        // === Date Picker ===
        const months = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        const calendarTrigger = document.getElementById("calendarTrigger");
        const calendarPopup = document.getElementById("calendarPopup");
        const dateDisplay = document.getElementById("dateDisplay");
        const calendarGrid = document.getElementById("calendarGrid");
        const monthSelect = document.getElementById("monthSelect");
        const yearSelect = document.getElementById("yearSelect");
        const cancelBtn = document.getElementById("cancelBtn");
        const confirmBtn = document.getElementById("confirmBtn");

        // Ambil tanggal dari URL atau gunakan tanggal hari ini
        const urlParams = new URLSearchParams(window.location.search);
        const dateFromUrl = urlParams.get('tanggal');
        let selectedDate = dateFromUrl ? new Date(dateFromUrl + 'T00:00:00') : new Date();


        // isi dropdown bulan & tahun
        months.forEach((m, i) => {
            let opt = document.createElement("option");
            opt.value = i;
            opt.textContent = m;
            monthSelect.appendChild(opt);
        });

        for (let y = 2020; y <= 2030; y++) {
            let opt = document.createElement("option");
            opt.value = y;
            opt.textContent = y;
            yearSelect.appendChild(opt);
        }

        function renderCalendar(date) {
            calendarGrid.innerHTML = "";
            monthSelect.value = date.getMonth();
            yearSelect.value = date.getFullYear();

            let firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
            let daysInMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                let empty = document.createElement("div");
                calendarGrid.appendChild(empty);
            }

            for (let d = 1; d <= daysInMonth; d++) {
                let btn = document.createElement("button");
                btn.classList.add("day-btn");
                btn.textContent = d;
                if (d === date.getDate()) btn.classList.add("selected");
                btn.addEventListener("click", () => {
                    selectedDate = new Date(date.getFullYear(), date.getMonth(), d);
                    renderCalendar(selectedDate);
                });
                calendarGrid.appendChild(btn);
            }
        }

        function formatDateForDisplay(date) {
            return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
        }

        // --- FUNGSI BARU UNTUK FORMAT URL ---
        function formatDateForUrl(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // event handler
        calendarTrigger.addEventListener("click", () => {
            calendarPopup.classList.toggle("hidden");
            renderCalendar(selectedDate);
        });

        monthSelect.addEventListener("change", () => {
            let day = selectedDate.getDate();
            const newDate = new Date(yearSelect.value, monthSelect.value, 1);
            const daysInNewMonth = new Date(newDate.getFullYear(), newDate.getMonth() + 1, 0).getDate();
            if (day > daysInNewMonth) day = daysInNewMonth;

            selectedDate = new Date(yearSelect.value, monthSelect.value, day);
            renderCalendar(selectedDate);
        });
        yearSelect.addEventListener("change", () => {
            let day = selectedDate.getDate();
            const newDate = new Date(yearSelect.value, monthSelect.value, 1);
            const daysInNewMonth = new Date(newDate.getFullYear(), newDate.getMonth() + 1, 0).getDate();
            if (day > daysInNewMonth) day = daysInNewMonth;

            selectedDate = new Date(yearSelect.value, monthSelect.value, day);
            renderCalendar(selectedDate);
        });

        cancelBtn.addEventListener("click", () => {
            calendarPopup.classList.add("hidden");
        });

        // --- BAGIAN YANG DIUBAH ---
        confirmBtn.addEventListener("click", () => {
            const formattedDate = formatDateForUrl(selectedDate);
            // Redirect ke halaman yang sama dengan parameter tanggal yang baru
            window.location.href = `{{ route('admin.input_indikator') }}?tanggal=${formattedDate}`;
        });
        // --- AKHIR BAGIAN YANG DIUBAH ---

        // init
        dateDisplay.textContent = formatDateForDisplay(selectedDate);

        // Validasi sebelum submit (tidak ada perubahan di sini)
        document.querySelector('form').addEventListener('submit', function (e) {
            let valid = true;
            // Cek semua input indikator
            document.querySelectorAll('.indicator-grid input[type="number"]').forEach(function (input) {
                if (input.value === '' || input.value === null) {
                    valid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Semua indikator harus diisi!');
            }
        });
    </script>
@endpush