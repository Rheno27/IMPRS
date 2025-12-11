@extends('layouts.app')

@section('styles')
    <style>
        /* --- 1. CSS KHUSUS NAVIGASI (KOP) --- */
        .survey-nav-container {
            background-color: rgba(214, 227, 221, 0.5);
            padding: 100px 0 0;
            text-align: center;
            margin-bottom: 30px;
        }

        .survey-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: var(--primary-color);
            margin: 0 0 30px 0;
            text-transform: uppercase;
        }

        .survey-tabs {
            display: flex;
            justify-content: center;
            border-bottom: 1px solid #77a28d;
        }

        .tab-item {
            font-weight: 600;
            font-size: 16px;
            color: #aaa;
            text-decoration: none;
            padding: 12px 24px;
            text-align: center;
            min-width: 200px;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .tab-item:hover {
            color: var(--primary-color);
        }

        .tab-item.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            font-weight: 700;
        }

        /* --- 2. CSS KHUSUS TABEL SKM (Mengikuti Gaya Detail) --- */
        /* Kita hapus min-width paksaan, biarkan natural */

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

    {{-- Gunakan struktur page-container biasa (Block Element), bukan Flex-Center --}}
    <div class="page-container" style="padding-bottom: 50px;">

        {{-- Toolbar Container (Calendar & Download) --}}
        {{-- Struktur ini sama dengan .dashboard-controls di dashboard --}}
        <div class="toolbar-container">
            <div class="toolbar-left">
                <div style="position:relative;">
                    <button id="calendarBtn" class="btn-control" aria-haspopup="true" aria-expanded="false"
                        style="min-width: 180px; justify-content: center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"></rect>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"></line>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></line>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"></line>
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
        {{-- Kita gunakan struktur .report-container yang bersifat block (melebar penuh) --}}
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
                                {{-- Kita hapus width manual, biarkan padding & text menentukan lebar --}}
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