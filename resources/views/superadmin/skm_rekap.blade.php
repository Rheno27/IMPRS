@extends('layouts.app')

@section('styles')
    <style>
        /* --- 2. CSS KHUSUS TABEL SKM (Mengikuti Gaya Detail) --- */
        * --- CSS TABEL SKM --- */
        .skm-table-wrapper {
            overflow-x: auto;
            width: 100%;
            padding-bottom: 5px; 
        }

        .data-table.skm-table {
            width: 100%; 
            border-collapse: collapse;
        }

        .data-table.skm-table th, 
        .data-table.skm-table td {
            white-space: nowrap; 
            padding: 12px 8px; 

            vertical-align: middle;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .data-table.skm-table th {
            background-color: var(--bg-table-header);
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
        {{-- Header Navigasi (Kop) --}}
        @include('superadmin.partials.skm_nav')

        <div class="page-container" style="padding-bottom: 50px;">
            {{-- Toolbar Container --}}
            <div class="toolbar-container">
                <div class="toolbar-left">
                    <div style="position:relative;">
                        <button id="calendarBtn" class="btn-control" aria-haspopup="true" aria-expanded="false"
                            style="min-width: 180px; justify-content: center;">
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
                            <span id="monthYear"
                                style="color: var(--primary-color); font-weight: 600;">{{ $selectedYear }}</span>
                        </button>

                        <div id="calendarPanel" class="calendar-popup" style="display: none; width: 280px; left: 0;">
                            <div class="cal-header">
                                <button id="prevYear" class="arrow-btn">‹</button>
                                <span id="calendarYear" style="font-weight: bold;"></span>
                                <button id="nextYear" class="arrow-btn">›</button>
                            </div>
                            <div class="calendar-months"
                                style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; margin-top:10px;"></div>
                        </div>
                    </div>
                </div>

                <div class="toolbar-right">
                    <form action="{{ route('superadmin.skm.download') }}" method="GET" style="display: inline;">
                        <input type="hidden" name="month" value="{{ $selectedMonth }}">
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        <button type="submit" class="btn-control">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC5E3A" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span style="color: var(--primary-color);">Download File</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Report Table Container --}}
            <div class="report-container" style="padding-top: 10px;">
                <div class="report-table-wrapper">
                    <div class="report-header-block">
                        <h3>REKAPITULASI SURVEY KEPUASAN MASYARAKAT<br>
                            <span style="font-size: 0.8em; opacity: 0.9;">
                                {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->isoFormat('MMMM') }}
                                {{ $selectedYear }} - RSD KALISAT
                            </span>
                        </h3>
                    </div>

                    {{-- Wrapper Scroll --}}
                    <div class="skm-table-wrapper">
                        <table class="data-table skm-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 50px;">No.</th>
                                    <th colspan="{{ count($listPertanyaan) }}">Nomor Pertanyaan</th>
                                    <th rowspan="2" style="min-width: 120px;">Rata-rata IKM</th>
                                </tr>
                                <tr>
                                    @foreach ($listPertanyaan as $id)
                                        <th>{{ $loop->iteration }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataRekap as $pasien)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @foreach ($listPertanyaan as $idPertanyaan)
                                            <td>{{ $pasien['jawaban'][$idPertanyaan] ?? '-' }}</td>
                                        @endforeach
                                        <td style="font-weight: bold;">{{ $pasien['total_nilai_ikm'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($listPertanyaan) + 2 }}">Tidak ada data survei untuk periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="font-weight: bold; text-align: right; padding-right: 15px;"
                                        colspan="{{ count($listPertanyaan) + 1 }}">Rata-Rata Total</td>
                                    <td style="font-weight: bold; background-color: #eaf5f0;">
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
        </div>
@endsection

@push('scripts')
    {{-- Script JS Kalender tetap sama --}}
    <script>
        (function () {
            const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Juli", "Ags", "Sep", "Okt", "Nov", "Des"];
            let currentYear = {{ $selectedYear }};
            let currentMonthIndex = {{ $selectedMonth }} - 1;

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
                    b.style.padding = "8px";
                    b.style.borderRadius = "6px";
                    b.style.border = "none";
                    b.style.cursor = "pointer";
                    b.style.background = (index === currentMonthIndex && currentYear == {{ $selectedYear }}) ? "var(--primary-color)" : "#f7f7f7";
                    b.style.color = (index === currentMonthIndex && currentYear == {{ $selectedYear }}) ? "#fff" : "inherit";
                    b.style.fontWeight = (index === currentMonthIndex && currentYear == {{ $selectedYear }}) ? "bold" : "normal";

                    b.addEventListener('click', () => {
                        const selectedMonth = index + 1;
                        const baseUrl = "{{ route('superadmin.skm.rekap') }}";
                        window.location.href = `${baseUrl}?month=${selectedMonth}&year=${currentYear}`;
                    });
                    monthsContainer.appendChild(b);
                });
            }

            prevYearBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); currentYear--; renderMonths(); });
            nextYearBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); currentYear++; renderMonths(); });

            btn.addEventListener('click', (ev) => {
                ev.stopPropagation();
                panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
                if (panel.style.display === 'block') renderMonths();
            });

            document.addEventListener('click', (e) => {
                if (!panel.contains(e.target) && !btn.contains(e.target)) {
                    panel.style.display = 'none';
                }
            });

            monthYearEl.textContent = monthNames[currentMonthIndex] + " " + currentYear;
        })();
    </script>
@endpush