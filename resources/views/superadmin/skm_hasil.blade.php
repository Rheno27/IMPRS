@extends('layouts.app')

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