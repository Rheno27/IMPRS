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

        /* --- 2. CSS HASIL SURVEI (LAYOUT VERTIKAL) --- */
        .page-container {
            padding-bottom: 60px;
        }

        .section-title-banner {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 600;
            font-size: 20px;
            text-align: center;
            padding: 15px;
            margin: 0 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .cards-wrapper {
            display: flex;             
            flex-direction: column;    
            gap: 24px;                 
            padding: 0 20px 40px;
            max-width: 1000px;         
            margin: 0 auto;            
        }

        .data-card {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px; 
            display: flex;
            flex-direction: column;
            gap: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            width: 100%; 
            box-sizing: border-box;
        }

        .card-header {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
        }

        .card-title {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-dark);
            margin: 0;
        }

        .card-answer-count {
            font-size: 12px;
            color: #666;
            background: #f4f4f4;
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* --- DATA LIST (Text Only) --- */
        .data-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 250px;
            overflow: hidden;
        }
        
        .data-list.expanded {
            overflow-y: auto;
            max-height: 400px;
        }

        .data-list-item {
            background-color: var(--bg-table-header);
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 14px;
            color: var(--text-dark);
        }

        .view-more-link {
            display: block;
            margin-top: 5px;
            color: var(--primary-color);
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
        }

        /* --- 3. CHART LAYOUT  --- */
        .chart-card-body {
            display: flex;
            flex-direction: row; 
            gap: 60px; 
            align-items: center;
            justify-content: flex-start; 
            padding: 10px 20px;
        }

        .chart-container {
            width: 220px; 
            height: 220px;
            flex-shrink: 0; 
            position: relative;
        }

        /* Container Legend/Poin */
        .legend-wrapper {
            flex-grow: 1; 
            display: flex;
            justify-content: flex-start; 
        }

        .legend-grid, .legend-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 14px;
            width: 100%;
        }
        
        .legend-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr); 
            gap: 10px 30px; 
        }

        .legend-item {
            display: flex;
            align-items: flex-start; 
            gap: 12px;
            line-height: 1.4;
        }

        .legend-swatch {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            flex-shrink: 0;
            margin-top: 2px; 
        }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 768px) {
            .chart-card-body {
                flex-direction: column; 
                gap: 30px;
                text-align: left;
                align-items: center;
            }
            
            .legend-grid {
                grid-template-columns: 1fr; 
            }
            
            .chart-card-body {
                padding: 0;
            }
        }
    </style>
@endsection

@section('content')
    @include('superadmin.partials.skm_nav')

    <div class="page-container">
        <section id="data-responden">
            <h2 class="section-title-banner">Data Responden</h2>
            <div class="cards-wrapper">
                {{-- CARD NO RM --}}
                <article class="data-card">
                    <header class="card-header">
                        <h3 class="card-title">No RM</h3>
                        <span class="card-answer-count">{{ $totalResponden }} Responden</span>
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
                            <a class="view-more-link" data-limit="10">Lihat Selengkapnya</a>
                        @endif
                    </div>
                </article>

                {{-- CARD UMUR --}}
                <article class="data-card">
                    <header class="card-header">
                        <h3 class="card-title">Umur</h3>
                        <span class="card-answer-count">{{ $totalResponden }} Responden</span>
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
                            <a class="view-more-link" data-limit="5">Lihat Selengkapnya</a>
                        @endif
                    </div>
                </article>

                {{-- CARD GRAFIK (Looping) --}}
                @foreach([
                    ['id'=>'namaRuanganChart', 'title'=>'Nama Ruangan', 'chartData'=>$ruanganChart, 'type'=>'grid'],
                    ['id'=>'jenisKelaminChart', 'title'=>'Jenis Kelamin', 'chartData'=>$jenisKelaminChart, 'type'=>'col'],
                    ['id'=>'pendidikanTerakhirChart', 'title'=>'Pendidikan Terakhir', 'chartData'=>$pendidikanChart, 'type'=>'grid'],
                    ['id'=>'pekerjaanChart', 'title'=>'Pekerjaan', 'chartData'=>$pekerjaanChart, 'type'=>'grid']
                ] as $chartItem)
                    <article class="data-card">
                        <header class="card-header">
                            <h3 class="card-title">{{ $chartItem['title'] }}</h3>
                            <span class="card-answer-count">{{ $totalResponden }} Responden</span>
                        </header>
                        
                        {{-- BODY CHART CUSTOM LAYOUT --}}
                        <div class="chart-card-body">
                            {{-- KIRI: CHART --}}
                            <div class="chart-container">
                                <canvas id="{{ $chartItem['id'] }}"></canvas>
                            </div>

                            {{-- LEGEND --}}
                            <div class="legend-wrapper">
                                <div class="{{ $chartItem['type'] == 'grid' ? 'legend-grid' : 'legend-column' }}">
                                    @foreach($chartItem['chartData']['labels'] as $index => $label)
                                        @php 
                                            $colors = ['#99fdff','#ff7ba1','#f44336','#77a28d','#074679','#337354','#ffbb00','#dc5e3a','#2196f3','#4caf50'];
                                            $color = $colors[$index % count($colors)];
                                        @endphp
                                        <div class="legend-item">
                                            <span class="legend-swatch" style="background-color: {{ $color }};"></span>
                                            <span>{{ $label }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Section 2: Hasil Survei --}}
        <section id="hasil-survei">
            <h2 class="section-title-banner">Hasil Survei</h2>
            <div class="cards-wrapper">
                @foreach($allSurveyCharts as $item)
                    <article class="data-card">
                        <header class="card-header">
                            <h3 class="card-title">Q{{ $loop->iteration }}: {{ Str::limit($item['pertanyaan_text'], 100) }}</h3>
                            <span class="card-answer-count">{{ $totalResponden }} Jawaban</span>
                        </header>
                        
                        <div class="chart-card-body">
                            {{-- KIRI: CHART --}}
                            <div class="chart-container">
                                <canvas id="surveyChart{{ $item['id_pertanyaan'] }}"></canvas>
                            </div>
                            
                            {{-- TENGAH/KANAN: LEGEND --}}
                            <div class="legend-wrapper">
                                <div class="legend-column">
                                    @foreach($item['chart']['labels'] as $index => $label)
                                        @php 
                                            $colors = ['#074679', '#4caf50', '#ba51a3', '#ce8172'];
                                            $color = $colors[$index % 4];
                                        @endphp
                                        <div class="legend-item">
                                            <span class="legend-swatch" style="background-color: {{ $color }};"></span>
                                            <span>{{ $label }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach

                {{-- Kritik Saran --}}
                <article class="data-card">
                    <header class="card-header">
                        <h3 class="card-title">Kritik & Saran</h3>
                        <span class="card-answer-count">{{ $listKritikSaran->count() }} Masukan</span>
                    </header>
                    <div class="card-body">
                        <ul class="data-list">
                            @forelse($listKritikSaran as $saran)
                                <li class="data-list-item">{{ $saran }}</li>
                            @empty
                                <li class="data-list-item">Belum ada kritik/saran.</li>
                            @endforelse
                        </ul>
                        @if($listKritikSaran->count() > 10)
                            <a class="view-more-link" data-limit="10">Lihat Selengkapnya</a>
                        @endif
                    </div>
                </article>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Setup Warna Chart
        const palette = ['#99fdff','#ff7ba1','#f44336','#77a28d','#074679','#337354','#ffbb00','#dc5e3a','#2196f3','#4caf50'];
        const surveyPalette = ['#074679', '#4caf50', '#ba51a3', '#ce8172'];

        function renderPie(id, labels, data, colors) {
            const ctx = document.getElementById(id);
            if(ctx) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: colors,
                            borderWidth: 1, borderColor: '#fff'
                        }]
                    },
                    options: { 
                        plugins: { 
                            legend: { display: false }, 
                            tooltip: { callbacks: { label: c => `${c.parsed} orang` } } 
                        }, 
                        maintainAspectRatio: false 
                    }
                });
            }
        }

        renderPie('namaRuanganChart', @json($ruanganChart['labels']), @json($ruanganChart['data']), palette);
        renderPie('jenisKelaminChart', @json($jenisKelaminChart['labels']), @json($jenisKelaminChart['data']), [palette[4], palette[9]]);
        renderPie('pendidikanTerakhirChart', @json($pendidikanChart['labels']), @json($pendidikanChart['data']), palette);
        renderPie('pekerjaanChart', @json($pekerjaanChart['labels']), @json($pekerjaanChart['data']), palette);

        const surveyCharts = @json($allSurveyCharts);
        surveyCharts.forEach(item => {
            renderPie(`surveyChart${item.id_pertanyaan}`, item.chart.labels, item.chart.data, surveyPalette);
        });

        document.querySelectorAll('.view-more-link').forEach(btn => {
            const list = btn.parentElement.querySelector('.data-list');
            const limit = parseInt(btn.dataset.limit);
            const items = list.querySelectorAll('li');
            
            items.forEach((li, idx) => { if(idx >= limit) li.style.display = 'none'; });

            btn.addEventListener('click', () => {
                const isExpanded = list.classList.toggle('expanded');
                items.forEach((li, idx) => {
                    if(isExpanded) li.style.display = 'block';
                    else if(idx >= limit) li.style.display = 'none';
                });
                btn.textContent = isExpanded ? 'Sembunyikan' : 'Lihat Selengkapnya';
            });
        });
    </script>
@endpush