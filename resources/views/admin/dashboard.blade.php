@extends('layouts.app')
@section('styles')
    <style>
        .hero-section {
            background-image: url('{{ asset('image/background.png') }}');
        }

        /* Tambahan agar area kontrol rapi tanpa class report-section */
        .dashboard-controls {
            padding: 20px 36px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            align-items: center;
        }
    </style>
@endsection

@section('content')
    {{-- 1. HERO SECTION (TETAP) --}}
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

    {{-- 2. WRAPPER UNTUK KONTROL (Download, Tahun, Bulan) --}}
    {{-- Kita lepas dari 'report-section' agar tidak kena efek flex-center yang bikin gepeng --}}
    <div class="dashboard-controls">

        {{-- Baris Tombol Download & Tahun --}}
        <div class="report-controls-top" style="justify-content: flex-end; padding: 0; width: 100%; max-width: 1440px;">
            <button type="button" class="download-btn"
                onclick="document.getElementById('downloadModal').style.display='block'">
                <svg width="35" height="35" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="50" height="50" rx="10" fill="#FCFCFC" />
                    <path
                        d="M38 29.3333V35.1111C38 35.8773 37.6956 36.6121 37.1539 37.1539C36.6121 37.6956 35.8773 38 35.1111 38H14.8889C14.1227 38 13.3879 37.6956 12.8461 37.1539C12.3044 36.6121 12 35.8773 12 35.1111V29.3333M17.7778 22.1111L25 29.3333M25 29.3333L32.2222 22.1111M25 29.3333V12"
                        stroke="#DC5E3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Download File</span>
            </button>

            <div id="downloadModal" class="modal-overlay">
                <div class="modal-box">
                    <h3 class="modal-title">Pilih Periode Laporan</h3>

                    <form action="{{ route('admin.download_rekap') }}" method="GET">
                        <label class="form-label-sm">Bulan:</label>
                        <select name="bulan" class="form-select-full">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>

                        <label class="form-label-sm">Tahun:</label>
                        <select name="tahun" class="form-select-full">
                            @foreach(range(date('Y') - 2, date('Y') + 2) as $y)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>

                        <div class="modal-actions">
                            <button type="button" class="btn-secondary"
                                onclick="document.getElementById('downloadModal').style.display='none'">
                                Batal
                            </button>
                            <button type="submit" class="btn-primary">
                                Download
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="year-picker-wrapper">
                <div class="year-selector" id="yearBtn">
                    <svg width="35" height="36" viewBox="0 0 45 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M31.4067 7.175V4.25C31.4067 3.48125 30.7692 2.84375 30.0004 2.84375C29.2317 2.84375 28.5942 3.48125 28.5942 4.25V7.0625H16.4067V4.25C16.4067 3.48125 15.7692 2.84375 15.0004 2.84375C14.2317 2.84375 13.5942 3.48125 13.5942 4.25V7.175C8.53168 7.64375 6.07543 10.6625 5.70043 15.1437C5.66293 15.6875 6.11293 16.1375 6.63793 16.1375H38.3629C38.9067 16.1375 39.3567 15.6687 39.3004 15.1437C38.9254 10.6625 36.4692 7.64375 31.4067 7.175Z"
                            fill="#FFC107" />
                        <path
                            d="M37.5 18.95C38.5313 18.95 39.375 19.7937 39.375 20.825V32.375C39.375 38 36.5625 41.75 30 41.75H15C8.4375 41.75 5.625 38 5.625 32.375V20.825C5.625 19.7937 6.46875 18.95 7.5 18.95H37.5Z"
                            fill="#337354" />
                        <path
                            d="M15.9375 28.6249C15.45 28.6249 14.9625 28.4186 14.6063 28.0811C14.2688 27.7249 14.0625 27.2374 14.0625 26.7499C14.0625 26.2624 14.2688 25.7749 14.6063 25.4187C15.1313 24.8937 15.9563 24.7249 16.65 25.0249C16.8938 25.1186 17.1 25.2499 17.2687 25.4187C17.6062 25.7749 17.8125 26.2624 17.8125 26.7499C17.8125 27.2374 17.6062 27.7249 17.2687 28.0811C16.9125 28.4186 16.425 28.6249 15.9375 28.6249Z"
                            fill="#FFC107" />
                        <path
                            d="M22.5 28.6249C22.0125 28.6249 21.525 28.4186 21.1688 28.0811C20.8313 27.7249 20.625 27.2374 20.625 26.7499C20.625 26.2624 20.8313 25.7749 21.1688 25.4187C21.3375 25.2499 21.5437 25.1186 21.7875 25.0249C22.4812 24.7249 23.3062 24.8937 23.8312 25.4187C24.1687 25.7749 24.375 26.2624 24.375 26.7499C24.375 27.2374 24.1687 27.7249 23.8312 28.0811C23.7375 28.1561 23.6438 28.2311 23.55 28.3061C23.4375 28.3811 23.325 28.4374 23.2125 28.4749C23.1 28.5312 22.9875 28.5687 22.875 28.5874C22.7438 28.6062 22.6313 28.6249 22.5 28.6249Z"
                            fill="#FFC107" />
                        <path
                            d="M29.0625 28.625C28.575 28.625 28.0875 28.4188 27.7313 28.0813C27.3938 27.725 27.1875 27.2375 27.1875 26.75C27.1875 26.2625 27.3938 25.775 27.7313 25.4188C27.9188 25.25 28.1062 25.1187 28.35 25.025C28.6875 24.875 29.0625 24.8375 29.4375 24.9125C29.55 24.9313 29.6625 24.9687 29.775 25.025C29.8875 25.0625 30 25.1188 30.1125 25.1938C30.2063 25.2688 30.3 25.3438 30.3937 25.4188C30.7312 25.775 30.9375 26.2625 30.9375 26.75C30.9375 27.2375 30.7312 27.725 30.3937 28.0813C30.3 28.1563 30.2063 28.2312 30.1125 28.3062C30 28.3812 29.8875 28.4375 29.775 28.475C29.6625 28.5313 29.55 28.5688 29.4375 28.5875C29.3063 28.6063 29.175 28.625 29.0625 28.625Z"
                            fill="#FFC107" />
                        <path
                            d="M15.9375 35.1875C15.6938 35.1875 15.45 35.1313 15.225 35.0375C14.9812 34.9438 14.7938 34.8125 14.6063 34.6438C14.2688 34.2875 14.0625 33.8 14.0625 33.3125C14.0625 32.825 14.2688 32.3375 14.6063 31.9813C14.7938 31.8125 14.9812 31.6812 15.225 31.5875C15.5625 31.4375 15.9375 31.4 16.3125 31.475C16.425 31.4938 16.5375 31.5312 16.65 31.5875C16.7625 31.625 16.875 31.6813 16.9875 31.7563C17.0813 31.8313 17.175 31.9063 17.2687 31.9813C17.6062 32.3375 17.8125 32.825 17.8125 33.3125C17.8125 33.8 17.6062 34.2875 17.2687 34.6438C17.175 34.7188 17.0813 34.8125 16.9875 34.8687C16.875 34.9437 16.7625 35 16.65 35.0375C16.5375 35.0938 16.425 35.1313 16.3125 35.15C16.1813 35.1688 16.0688 35.1875 15.9375 35.1875Z"
                            fill="#FFC107" />
                        <path
                            d="M22.5 35.1875C22.0125 35.1875 21.525 34.9812 21.1688 34.6437C20.8313 34.2875 20.625 33.8 20.625 33.3125C20.625 32.825 20.8313 32.3375 21.1688 31.9813C21.8625 31.2875 23.1375 31.2875 23.8312 31.9813C24.1687 32.3375 24.375 32.825 24.375 33.3125C24.375 33.8 24.1687 34.2875 23.8312 34.6437C23.475 34.9812 22.9875 35.1875 22.5 35.1875Z"
                            fill="#FFC107" />
                        <path
                            d="M29.0625 35.1875C28.575 35.1875 28.0875 34.9812 27.7313 34.6437C27.3938 34.2875 27.1875 33.8 27.1875 33.3125C27.1875 32.825 27.3938 32.3375 27.7313 31.9813C28.425 31.2875 29.7 31.2875 30.3937 31.9813C30.7312 32.3375 30.9375 32.825 30.9375 33.3125C30.9375 33.8 30.7312 34.2875 30.3937 34.6437C30.0375 34.9812 29.55 35.1875 29.0625 35.1875Z"
                            fill="#FFC107" />
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

        {{-- Baris Selector Bulan --}}
        <div class="month-selector">
            @php
                $namaBulanLengkap = [1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April", 5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"];
                $bulanAktif = $bulan ?? date('n');
                $prevIndex = ($bulanAktif - 1 < 1) ? 12 : $bulanAktif - 1;
                $nextIndex = ($bulanAktif + 1 > 12) ? 1 : $bulanAktif + 1;
            @endphp

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
                @foreach ([$prevIndex => $namaBulanLengkap[$prevIndex], $bulanAktif => $namaBulanLengkap[$bulanAktif], $nextIndex => $namaBulanLengkap[$nextIndex]] as $indexBulan => $namaBulanItem)
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
    </div>

    {{-- 3. BAGIAN TABEL (STRUKTUR SAMA PERSIS DENGAN SUPERADMIN) --}}
    {{-- Menggunakan class .report-container (block element) menggantikan .report-section (flex element) --}}
    <div class="report-container">
        <div class="report-table-wrapper">
            {{-- Header Block Hijau --}}
            <div class="report-header-block">
                <h3>Penilaian Indikator Mutu di Ruang {{ $user->nama_ruangan }}<br>
                    <span style="font-size: 0.8em; opacity: 0.9;">Bulan {{ $namaBulan[$bulan] ?? '' }} - RSD KALISAT</span>
                </h3>
            </div>

            {{-- Tabel Data --}}
            <div style="overflow-x: auto;">
                {{-- TIDAK PERLU min-width, karena parent .report-container mengizinkan overflow --}}
                <table class="data-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 50px;">No.</th>
                            <th rowspan="2">Variabel Penilaian</th>
                            <th colspan="{{ $jumlahHari }}">Tanggal</th>
                            <th rowspan="2" style="width: 80px;">Jumlah</th>
                            <th rowspan="2" style="width: 60px;">%</th>
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
                                <td rowspan="2" class="text-start">{{ $row['variabel'] }}</td>

                                {{-- Numerator --}}
                                @for($tgl = 1; $tgl <= $jumlahHari; $tgl++)
                                    <td>{{ isset($row['byTanggal'][$tgl]) ? $row['byTanggal'][$tgl]->pasien_sesuai : '' }}</td>
                                @endfor
                                <td rowspan="2">{{ $row['jumlah_sesuai'] }}</td>
                                <td rowspan="2">{{ $row['persen'] }}%</td>
                            </tr>
                            <tr>
                                {{-- Denominator --}}
                                @for($tgl = 1; $tgl <= $jumlahHari; $tgl++)
                                    <td>{{ isset($row['byTanggal'][$tgl]) ? $row['byTanggal'][$tgl]->total_pasien : '' }}</td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const selectedYearFromServer = {{ $tahun }};
    </script>
@endpush