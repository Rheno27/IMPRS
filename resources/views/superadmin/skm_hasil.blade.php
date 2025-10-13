@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --bg-main: #fcfcfc;
            --bg-accent: rgba(214, 227, 221, 0.5);
            --primary-green: #337354;
            --primary-green-dark: #2a7f54;
            --primary-green-light: #77a28d;
            --text-light: #ffffff;
            --text-dark: #2d2d2d;
            --text-primary: #337354;
            --border-light: rgba(51, 115, 84, 0.5);
            --border-medium: #77a28d;
            --border-dark: #337354;
            --list-item-bg: #d6e3dd;
            --border-color-semitransparent: rgba(51, 115, 84, 0.5);

            /* Chart Colors */
            --chart-color-1: #99fdff;
            --chart-color-2: #ff7ba1;
            --chart-color-3: #f44336;
            --chart-color-4: #77a28d;
            --chart-color-5: #074679;
            --chart-color-6: #337354;
            --chart-color-7: #ffbb00;
            --chart-color-8: #dc5e3a;
            --chart-color-9: #2196f3;
            --chart-color-10: #4caf50;
            --chart-color-11: #c2c2c2;
            --chart-color-12: #960808;
            --chart-color-13: #667080;
            --chart-color-14: #f4a28c;
            --chart-color-15: #ba51a3;
            --chart-color-16: #36dae2;
            --chart-color-17: #ff0909;
            --chart-color-18: #24285b;
            --chart-color-19: #ce8172;
            --chart-color-20: #6f145b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
        }

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: var(--bg-main);
            overflow: hidden;
        }

        .main-content {
            padding: 0 36px;
            display: flex;
            flex-direction: column;
            gap: 40px;
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

        /* Logo */
        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-image {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        /* User info */
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

        /* Survey Nav */
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
            color: var(--primary-green);
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
            min-width: 32%;
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
            transition: all 0.2s ease-in-out;
        }

        .tab-item:hover {
            color: var(--primary-green);
        }

        .tab-item.active {
            color: var(--primary-green);
            border-bottom-color: var(--primary-green);
            font-weight: 700;
        }

        /* Section Styling */
        .survey-section {
            padding-top: 48px;
            padding-bottom: 48px;
            padding-left: 36px;
            padding-right: 36px;
            box-sizing: border-box;
        }

        .section-title-banner {
            background-color: var(--primary-green);
            color: var(--text-light);
            font-family: 'Roboto', sans-serif;
            font-weight: 550;
            font-size: 20px;
            line-height: 20px;
            text-align: center;
            padding: 14px;
            margin: 0 0 18px 0;
            border-radius: 14px;
        }

        /* Data Cards */
        .cards-wrapper {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .data-card {
            background-color: #fff;
            border: 1px solid var(--border-dark);
            border-radius: 18px;
            padding: 20px 30px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            border-bottom: 1px solid var(--border-medium);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
        }

        .card-title {
            font-family: 'Roboto', sans-serif;
            font-weight: 550;
            font-size: 16px;
            color: var(--text-dark);
            margin: 0;
        }

        .card-answer-count {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
        }

        /* Card Body */
        .card-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .data-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 180px;
            overflow: hidden;
        }

        .data-list.expanded {
            overflow-y: auto;
            /* aktifkan scroll */
            max-height: 200px;
            /* tinggi area scroll */
        }

        .data-list-item {
            background-color: var(--list-item-bg);
            border-radius: 8px;
            padding: 8px 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
        }

        .view-more-link {
            display: inline-block;
            margin-top: 8px;
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
        }

        .view-more-link:hover {
            text-decoration: underline;
        }

        /* Chart Cards */
        .chart-card-body {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 32px;
            flex-wrap: wrap;
        }

        .pie-chart {
            width: 200px !important;
            height: 200px !important;
            max-width: 100%;
            flex-shrink: 0;
            background: transparent;
        }

        /* Legend */
        .legend-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
            justify-content: center;
            flex-grow: 1;
            margin-top: 40px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
            white-space: nowrap;
        }

        .legend-swatch {
            width: 36px;
            height: 12px;
            border-radius: 20px;
        }

        .legend-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(150px, 1fr));
            gap: 10px 20px;
            flex-grow: 1;
            margin-top: 40px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .site-header {
                flex-direction: column;
                gap: 16px;
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 0 24px;
                gap: 28px;
            }

            .survey-title {
                font-size: 24px;
                margin-bottom: 24px;
            }

            .tab-item {
                padding: 6px 12px;
                font-size: 14px;
            }

            .data-card {
                padding: 16px 20px;
            }

            .card-title {
                font-size: 16px;
            }

            .card-answer-count {
                font-size: 12px;
            }

            .data-list-item {
                font-size: 12px;
                padding: 6px 10px;
            }

            .pie-chart {
                width: 150px !important;
                height: 150px !important;
            }

            .legend-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .legend-item {
                justify-content: center;
            }
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

    <section id="data-responden" class="survey-section">
        <h2 class="section-title-banner">Data Responden</h2>
        <div class="cards-wrapper">
            {{-- CARD NO RM --}}
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">No RM</h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body">
                    <ul class="data-list">
                        @forelse($listNoRm as $noRm)
                            <li class="data-list-item">{{ $noRm }}</li>
                        @empty
                            <li class="data-list-item">Belum ada data.</li>
                        @endforelse
                    </ul>
                    @if($listNoRm->count() > 10)
                        <a class="view-more-link">Lihat Selengkapnya</a>
                    @endif
                </div>
            </article>

            {{-- CARD UMUR --}}
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Umur</h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body">
                    <ul class="data-list">
                        @forelse($listUmur as $umur)
                            <li class="data-list-item">{{ $umur }}</li>
                        @empty
                            <li class="data-list-item">Belum ada data.</li>
                        @endforelse
                    </ul>
                    @if($listUmur->count() > 5)
                        <a class="view-more-link">Lihat Selengkapnya</a>
                    @endif
                </div>
            </article>

            {{-- CARD NAMA RUANGAN --}}
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Nama Ruangan</h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body chart-card-body">
                    <div class="chart-container">
                        <canvas id="namaRuanganChart" class="pie-chart"></canvas>
                    </div>
                    <div class="legend-grid">
                        @foreach($ruanganChart['labels'] as $label)
                            <div class="legend-item"><span class="legend-swatch"
                                    style="background-color: var(--chart-color-{{$loop->iteration}});"></span>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>
            </article>

            {{-- CARD JENIS KELAMIN --}}
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Jenis Kelamin</h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body chart-card-body">
                    <div class="chart-container">
                        <canvas id="jenisKelaminChart" class="pie-chart"></canvas>
                    </div>
                    <div class="legend-column">
                        @foreach($jenisKelaminChart['labels'] as $label)
                            <div class="legend-item"><span class="legend-swatch"
                                    style="background-color: var(--chart-color-{{$loop->iteration * 5}});"></span>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>
            </article>
            {{-- CARD PENDIDIKAN TERAKHIR --}}
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Pendidikan Terakhir</h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body chart-card-body">
                    <div class="chart-container">
                        <canvas id="pendidikanTerakhirChart" class="pie-chart"></canvas>
                    </div>
                    <div class="legend-grid">
                        @foreach($pendidikanChart['labels'] as $label)
                            <div class="legend-item"><span class="legend-swatch"
                                    style="background-color: var(--chart-color-{{$loop->iteration}});"></span>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>
            </article>
            {{-- CARD PEKERJAAN --}}
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Pekerjaan</h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body chart-card-body">
                    <div class="chart-container">
                        <canvas id="pekerjaanChart" class="pie-chart"></canvas>
                    </div>
                    <div class="legend-grid">
                        @foreach($pekerjaanChart['labels'] as $label)
                            <div class="legend-item"><span class="legend-swatch"
                                    style="background-color: var(--chart-color-{{$loop->iteration}});"></span>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section id="pelayanan-publik" class="survey-section">
        <h2 class="section-title-banner">Pelayanan Publik</h2>
        <div class="cards-wrapper">
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Pertanyaan 1 Pelayanan Publik</h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body chart-card-body">
                    <div class="chart-container">
                        <canvas id="pelayananPublik1Chart" class="pie-chart"></canvas>
                    </div>
                    <div class="legend-column">
                        @foreach($pelayananChart['labels'] as $label)
                            <div class="legend-item"><span class="legend-swatch"
                                    style="background-color: var(--chart-color-{{$loop->iteration * 5}});"></span>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section id="keselamatan-pasien" class="survey-section">
        <h2 class="section-title-banner">Keselamatan Pasien</h2>
        <div class="cards-wrapper">
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Pertanyaan 1 Keselamatan Pasien </h3>
                    <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                </header>
                <div class="card-body chart-card-body">
                    <div class="chart-container">
                        <canvas id="keselamatanPasien1Chart" class="pie-chart"></canvas>
                    </div>
                    <div class="legend-column">
                        @foreach($keselamatanChart['labels'] as $label)
                            <div class="legend-item"><span class="legend-swatch"
                                    style="background-color: var(--chart-color-{{$loop->iteration * 5}});"></span>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>
            </article>
            <article class="data-card">
                <header class="card-header">
                    <h3 class="card-title">Silahkan berikan kritik dan saran</h3>
                    <span class="card-answer-count">{{ $listKritikSaran->count() }} Jawaban</span>
                </header>
                <div class="card-body">
                    <ul class="data-list">
                        @forelse($listKritikSaran as $saran)
                            <li class="data-list-item">{{ $saran }}</li>
                        @empty
                            <li class="data-list-item">Belum ada data.</li>
                        @endforelse
                    </ul>
                    @if($listKritikSaran->count() > 10)
                        <a class="view-more-link">Lihat Selengkapnya</a>
                    @endif
                </div>
            </article>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        // ================================
        // === Ambil warna dari CSS var ===
        // ================================
        function getCSSVar(name) {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        }

        const chartColors = [
            getCSSVar('--chart-color-1'),
            getCSSVar('--chart-color-2'),
            getCSSVar('--chart-color-3'),
            getCSSVar('--chart-color-4'),
            getCSSVar('--chart-color-5'),
            getCSSVar('--chart-color-6'),
            getCSSVar('--chart-color-7'),
            getCSSVar('--chart-color-8'),
            getCSSVar('--chart-color-9'),
            getCSSVar('--chart-color-10'),
            getCSSVar('--chart-color-11'),
            getCSSVar('--chart-color-12'),
            getCSSVar('--chart-color-13'),
            getCSSVar('--chart-color-14'),
            getCSSVar('--chart-color-15'),
            getCSSVar('--chart-color-16'),
            getCSSVar('--chart-color-17'),
            getCSSVar('--chart-color-18'),
            getCSSVar('--chart-color-19'),
            getCSSVar('--chart-color-20')
        ];

        // ====================
        // === Nama Ruangan ===
        // ====================
        const namaRuanganCtx = document.getElementById('namaRuanganChart');
            new Chart(namaRuanganCtx, {
                type: 'pie',
                data: {
                    labels: @json($ruanganChart['labels']),
                    datasets: [{
                        data: @json($ruanganChart['data']),
                        backgroundColor: chartColors.slice(0, {{ count($ruanganChart['data']) }}),
                        borderWidth: 1, borderColor: '#fff'
                    }]
                },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => `${ctx.parsed} pasien` }
                    }
                }
            }
        });

        // ======================
        // === Jenis Kelamin ====
        // ======================
        const jenisKelaminCtx = document.getElementById('jenisKelaminChart');
            new Chart(jenisKelaminCtx, {
                type: 'pie',
                data: {
                    labels: @json($jenisKelaminChart['labels']),
                    datasets: [{
                        data: @json($jenisKelaminChart['data']),
                        backgroundColor: [chartColors[4], chartColors[14]],
                        borderWidth: 1, borderColor: '#fff'
                    }]
                },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => `${ctx.parsed} pasien` }
                    }
                }
            }
        });

        // =============================
        // === Pendidikan Terakhir =====
        // =============================
        const pendidikanCtx = document.getElementById('pendidikanTerakhirChart');
        new Chart(pendidikanCtx, {
            type: 'pie',
            data: {
                labels: @json($pendidikanChart['labels']),
                datasets: [{
                    data: @json($pendidikanChart['data']),
                    backgroundColor: chartColors.slice(0, {{ count($pendidikanChart['data']) }}),
                    borderWidth: 1, borderColor: '#fff'
                }]
            },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => `${ctx.parsed} pasien` }
                    }
                }
            }
        });

        // =================
        // === Pekerjaan ===
        // =================
        const pekerjaanCtx = document.getElementById('pekerjaanChart');
            new Chart(pekerjaanCtx, {
                type: 'pie',
                data: {
                    labels: @json($pekerjaanChart['labels']),
                    datasets: [{
                        data: @json($pekerjaanChart['data']),
                        backgroundColor: chartColors.slice(8, 8 + {{ count($pekerjaanChart['data']) }}),
                        borderWidth: 1, borderColor: '#fff'
                    }]
                },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => `${ctx.parsed} pasien` }
                    }
                }
            }
        });

        // ============================
        // === Pelayanan Publik 1 =====
        // ============================
        const pelayananPublik2Ctx = document.getElementById('pelayananPublik1Chart');
            new Chart(pelayananPublik2Ctx, {
                type: 'pie',
                data: {
                    labels: @json($pelayananChart['labels']),
                    datasets: [{
                        data: @json($pelayananChart['data']),
                        backgroundColor: chartColors.slice(0, {{ count($pelayananChart['data']) }}),
                        borderWidth: 1, borderColor: '#fff'
                    }]
                },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => `${ctx.parsed} pasien` }
                    }
                }
            }
        });
        // ===============================
        // === Keselamatan Pasien 1 ======
        // ===============================
        const keselamatanPasien1Ctx = document.getElementById('keselamatanPasien1Chart');
            new Chart(keselamatanPasien1Ctx, {
                type: 'pie',
                data: {
                    labels: @json($keselamatanChart['labels']),
                    datasets: [{
                        data: @json($keselamatanChart['data']),
                        backgroundColor: [chartColors[5], chartColors[2]],
                        borderWidth: 1, borderColor: '#fff'
                    }]
                },
            options: {
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => `${ctx.parsed} pasien` }
                    }
                }
            }
        });

        document.querySelectorAll('.data-card').forEach(card => {
            const list = card.querySelector('.data-list');
            const button = card.querySelector('.view-more-link');

            // Hanya jalankan jika card punya .data-list dan .view-more-link
            if (!list || !button) return;

            const items = Array.from(list.querySelectorAll('.data-list-item'));
            const visibleCount = 3;
            let expanded = false;

            // tampilkan hanya 3 item awal
            items.forEach((item, index) => {
                if (index >= visibleCount) {
                    item.style.display = 'none';
                }
            });

            // pasang event listener
            button.addEventListener('click', () => {
                expanded = !expanded;

                if (expanded) {
                    // tampilkan semua item
                    items.forEach(item => item.style.display = 'list-item');
                    list.style.maxHeight = '200px'; // aktifkan scroll internal
                    list.style.overflowY = 'auto';
                    button.textContent = 'Sembunyikan';
                } else {
                    // sembunyikan lagi ke 3 item awal
                    items.forEach((item, index) => {
                        item.style.display = index < visibleCount ? 'list-item' : 'none';
                    });
                    list.scrollTop = 0;
                    list.style.overflowY = 'hidden';
                    button.textContent = 'Lihat Selengkapnya';
                }
            });
        });

    </script>
@endpush