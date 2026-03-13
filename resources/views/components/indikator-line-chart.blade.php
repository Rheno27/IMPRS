@php
$chartId = $chartId ?? ('indikatorLineChart_' . uniqid());
$seriesJson = json_encode($series ?? []);
$hasTitle = !empty($title);
$jsonTitle = json_encode($title ?? '');
$chartHeight = $height ?? 300; 
@endphp

<style>
    .custom-chart-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #eef2f7;
    }

    .legend-grid-wrapper {
        margin-top: 15px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        column-gap: 20px;
        row-gap: 8px;
        border-top: 1px dashed #edf2f7;
        padding: 15px 10px 15px 5px;
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .legend-grid-wrapper::-webkit-scrollbar {
        width: 5px;
    }

    .legend-grid-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .legend-grid-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
        transition: background 0.3s;
    }

    .legend-grid-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .custom-legend-item {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .custom-legend-item:hover {
        background: #f8fafc;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        margin-right: 10px;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .legend-text {
        font-size: 12px;
        color: #475569;
        line-height: 1.4;
    }

    .custom-legend-item.is-hidden {
        opacity: 0.3;
    }

    .custom-legend-item.is-hidden .legend-text {
        text-decoration: line-through;
    }
</style>

<div class="custom-chart-card">
    <div style="height: {{ $chartHeight }}px; width: 100%; position: relative;">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
    <div id="{{ $chartId }}_legend" class="legend-grid-wrapper"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const ctx = document.getElementById('{{ $chartId }}');
        if (!ctx) return;

        const originalSeries = {!! $seriesJson !!};
        const monthLabels = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];

        function palette(idx) {
            const colors = ['#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395'];
            return colors[idx % colors.length];
        }

        let chartInst = null;

        function renderChart(dataToRender) {
            const datasets = dataToRender.map((s, idx) => {
                const data = new Array(12).fill(null);
                if (Array.isArray(s.monthly)) {
                    s.monthly.forEach((v, i) => {
                        if (i < 12) data[i] = (v === null || v === undefined || v === '') ? null : Number(v);
                    });
                }
                const color = palette(idx);
                return {
                    label: s.label || `Indikator ${idx + 1}`,
                    data: data,
                    borderColor: color,
                    backgroundColor: color,
                    tension: 0.2,
                    pointRadius: 3,
                    pointBackgroundColor: color,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 1.5,
                    spanGaps: false,
                    clip: false
                };
            });

            if (chartInst) chartInst.destroy();

            chartInst = new Chart(ctx, {
                type: 'line',
                data: { labels: monthLabels, datasets: datasets },
                options: {
                    devicePixelRatio: 3,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: {!! $hasTitle ? 'true' : 'false' !!},
                            text: {!! $jsonTitle !!},
                            font: { size: 14, weight: '600' },
                            padding: { bottom: 10 }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => ` ${context.dataset.label}: ${context.parsed.y}%`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            ticks: {
                                stepSize: 10,
                                maxTicksLimit: 11,
                                autoSkip: false,
                                callback: v => v + '%'
                            },
                            grid: { color: 'rgba(0,0,0,0.06)' }
                        },
                        x: { grid: { display: false } }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 20,
                            top: 0,
                            bottom: 0
                        }
                    }
                }
            });
            renderCustomLegend(dataToRender);
        }

        function renderCustomLegend(seriesForLegend) {
            const container = document.getElementById('{{ $chartId }}_legend');
            if (!container) return;
            container.innerHTML = '';
            seriesForLegend.forEach((s, idx) => {
                const item = document.createElement('div');
                item.className = 'custom-legend-item';
                const color = palette(idx);
                item.innerHTML = `<span class="legend-dot" style="background: ${color}"></span><span class="legend-text">${s.label || 'Indikator ' + (idx + 1)}</span>`;
                item.onclick = () => {
                    const isVisible = chartInst.isDatasetVisible(idx);
                    chartInst.setDatasetVisibility(idx, !isVisible);
                    chartInst.update();
                    item.classList.toggle('is-hidden', isVisible);
                };
                container.appendChild(item);
            });
        }

        renderChart(originalSeries);

        const selector = `.chart-category-btn[data-chart-id="{{ $chartId }}"]`;
        document.querySelectorAll(selector).forEach(btn => {
            btn.addEventListener('click', (ev) => {
                ev.preventDefault();

                const categoryRaw = (btn.dataset.category || '').trim();
                const catLower = categoryRaw.toLowerCase();

                document.querySelectorAll(selector).forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const titleElement = document.getElementById('dynamic-category-name');
                if (titleElement) {
                    if (!btn.classList.contains('nav-arrow')) {
                        titleElement.innerText = btn.innerText;
                    } else {
                        titleElement.innerText = (catLower === 'all') ? "REKAP" : categoryRaw.toUpperCase();
                    }
                }

                if (!categoryRaw || catLower === 'all') {
                    renderChart(originalSeries);
                } else {
                    const filtered = originalSeries.filter(s =>
                        (s.kategori || '').toString().toLowerCase() === catLower
                    );
                    renderChart(filtered);
                }
            });
        });
    })();
</script>