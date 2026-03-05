@php
$chartId = $chartId ?? ('indikatorLineChart_' . uniqid());
$seriesJson = json_encode($series ?? []);
$chartTitle = $title ?? null;
@endphp

<div class="chart-box"
    style="min-height:220px; background: #fff; border: 1px solid #e6efe9; border-radius: 10px; padding: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.12);">
    <canvas id="{{ $chartId }}" height="{{ $height ?? 220 }}"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const originalSeries = Array.isArray({!! $seriesJson !!}) ? {!! $seriesJson !!} : [];
        const chartSeries = originalSeries.slice();
        const initialCategory = {!! json_encode($selectedKategori ?? 'all') !!};
        const ctx = document.getElementById('{{ $chartId }}');
        const monthLabels = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        function palette(index) {
            const colors = ['#3366CC', '#DC3912', '#FF9900', '#109618', '#990099', '#0099C6', '#DD4477', '#66AA00'];
            return colors[index % colors.length];
        }

        if (!ctx) return;

        function getDatasetsFromSeries(series) {
            return (Array.isArray(series) ? series : []).map((s, idx) => {
                const data = new Array(12).fill(null);
                if (Array.isArray(s.monthly)) {
                    for (let i = 0; i < 12; i++) {
                        const v = s.monthly[i];
                        data[i] = (v === null || v === undefined || v === '') ? null : Number(v);
                    }
                }
                const color = palette(idx);
                return {
                    label: s.label || `Indikator ${idx + 1}`,
                    data: data,
                    borderColor: color,
                    backgroundColor: color,
                    spanGaps: false,
                    tension: 0.2,
                    fill: false,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: color,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                };
            });
        }

        let chartInst = null;
        function renderChart(seriesForRender) {
            const datasets = getDatasetsFromSeries(seriesForRender);
            const config = {
                type: 'line',
                data: { labels: monthLabels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: !!{!! json_encode($chartTitle ?? null) !!},
                            text: {!! json_encode($chartTitle ?? null) !!} || '',
                            align: 'center',
                            font: { size: 16, weight: '600' },
                            padding: { top: 6, bottom: 6 }
                        },
                        legend: { display: false, position: 'bottom', labels: { usePointStyle: true, padding: 12, boxWidth: 10 } },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const v = context.parsed && context.parsed.y;
                                    return (v === null || v === undefined) ? `${context.dataset.label}: -` : `${context.dataset.label}: ${v}%`;
                                }
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
                                callback: function (value) { return value + '%'; }
                            },
                            grid: { color: 'rgba(0,0,0,0.06)' }
                        },
                        x: {
                            grid: { color: 'rgba(0,0,0,0.02)' }
                        }
                    },
                    elements: { line: { spanGaps: false, tension: 0.18 }, point: { pointStyle: 'circle' } },
                    layout: { padding: { top: 8, right: 12, bottom: 10, left: 8 } }
                }
            };

            try {
                if (chartInst) {
                    chartInst.destroy();
                    chartInst = null;
                }
                chartInst = new Chart(ctx.getContext('2d'), config);
                renderHtmlLegend(seriesForRender);
            } catch (e) {
                console.error('Chart render error:', e);
            }
        }

        function renderHtmlLegend(seriesForRender) {
            // remove existing legend container if any
            const containerId = '{{ $chartId }}_legend';
            let container = document.getElementById(containerId);
            if (container) container.remove();

            container = document.createElement('div');
            container.id = containerId;
            container.style.display = 'flex';
            container.style.gap = '12px';
            container.style.marginTop = '10px';
            container.style.flexWrap = 'nowrap';
            container.style.justifyContent = 'space-between';

            const left = document.createElement('ul');
            const right = document.createElement('ul');
            [left, right].forEach(u => {
                u.style.listStyle = 'none';
                u.style.padding = '0';
                u.style.margin = '0';
                u.style.flex = '1 1 50%';
                u.style.minWidth = '260px';
            });

            const total = Array.isArray(seriesForRender) ? seriesForRender.length : 0;
            const half = Math.ceil(total / 2);

            seriesForRender.forEach((s, idx) => {
                const item = document.createElement('li');
                item.style.display = 'flex';
                item.style.alignItems = 'center';
                item.style.marginBottom = '6px';
                item.style.cursor = 'pointer';

                const swatch = document.createElement('span');
                swatch.style.display = 'inline-block';
                swatch.style.width = '12px';
                swatch.style.height = '12px';
                // reuse palette mapping used for chart lines
                swatch.style.background = palette(idx);
                swatch.style.borderRadius = '3px';
                swatch.style.marginRight = '8px';
                swatch.style.border = '1px solid rgba(0,0,0,0.06)';

                const label = document.createElement('span');
                label.textContent = s.label || `Indikator ${idx + 1}`;
                label.style.fontSize = '0.95rem';
                label.style.color = '#1f2937';

                item.appendChild(swatch);
                item.appendChild(label);

                // click toggles dataset visibility in chartInst
                item.addEventListener('click', () => {
                    if (!chartInst || !chartInst.data || !chartInst.data.datasets) return;
                    const ds = chartInst.data.datasets[idx];
                    if (!ds) return;
                    ds.hidden = !ds.hidden;
                    chartInst.update();
                    if (ds.hidden) {
                        swatch.style.opacity = '0.35';
                        label.style.opacity = '0.5';
                        label.style.textDecoration = 'line-through';
                    } else {
                        swatch.style.opacity = '1';
                        label.style.opacity = '1';
                        label.style.textDecoration = 'none';
                    }
                });

                if (idx < half) left.appendChild(item); else right.appendChild(item);
            });

            container.appendChild(left);
            container.appendChild(right);

            // append after chart-box
            const chartBox = document.querySelector('#{{ $chartId }}').closest('.chart-box');
            if (chartBox) chartBox.parentNode.insertBefore(container, chartBox.nextSibling);
        }

        // initial render (all series)
        renderChart(chartSeries);

        // Hook category buttons (buttons should have class .chart-category-btn and data-chart-id matching this chartId)
        const selector = `.chart-category-btn[data-chart-id="{{ $chartId }}"]`;
        document.querySelectorAll(selector).forEach(btn => {
            btn.addEventListener('click', (ev) => {
                ev.preventDefault();
                const category = (btn.dataset.category || '').trim();

                // toggle active styling (use class 'active' to match category-pill)
                document.querySelectorAll(selector).forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                if (!category || category.toLowerCase() === 'all') {
                    renderChart(originalSeries);
                    return;
                }

                const filtered = originalSeries.filter(s => {
                    const k = (s.kategori || '').toString().trim();
                    return k.toLowerCase() === category.toLowerCase();
                });

                renderChart(filtered);
            });
        });

        // apply initial category if provided
        if (initialCategory && initialCategory.toString().toLowerCase() !== 'all') {
            const init = initialCategory.toString();
            const filteredInit = originalSeries.filter(s => (s.kategori || '').toString().trim().toLowerCase() === init.toLowerCase());
            // set active class on matching button if any
            const initBtn = document.querySelector(`${selector}[data-category="${init}"]`);
            if (initBtn) {
                document.querySelectorAll(selector).forEach(b => b.classList.remove('active'));
                initBtn.classList.add('active');
            }
            renderChart(filteredInit);
        }
    })();
</script>