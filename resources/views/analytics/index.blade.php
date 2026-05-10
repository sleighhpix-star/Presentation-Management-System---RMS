@extends('layouts.app')
@section('title','Dashboard – Research PMS')
@section('page-title','Dashboard')

@push('styles')
<style>
.chart-box {
    background: var(--card); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 20px; height: 100%;
    transition: box-shadow var(--t);
}
.chart-box:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
.chart-title {
    font-family: 'Outfit', sans-serif; font-size: 12px; font-weight: 700;
    color: var(--ink); margin-bottom: 16px; display: flex; align-items: center;
    gap: 7px; text-transform: uppercase; letter-spacing: .5px;
}
.chart-title .click-hint {
    margin-left: auto; font-size: 10px; font-weight: 500;
    color: var(--muted); display: flex; align-items: center; gap: 3px;
    opacity: .6; text-transform: none; letter-spacing: 0;
}
canvas { cursor: pointer; }

.sdg-row {
    display: flex; align-items: center; gap: 8px; margin-bottom: 6px;
    cursor: pointer; border-radius: 7px; padding: 4px 6px;
    transition: background var(--t); text-decoration: none;
}
.sdg-row:hover { background: #f0fdfa; }
.sdg-label { font-size: 10px; font-weight: 700; color: var(--text); text-align: right; flex-shrink: 0; width: 64px; font-family:'Outfit',sans-serif; }
.sdg-track { flex: 1; background: var(--border); border-radius: 10px; height: 8px; overflow: hidden; }
.sdg-fill  { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--teal), var(--teal-light)); }
.sdg-cnt   { font-size: 11px; color: var(--muted); width: 20px; text-align: right; flex-shrink: 0; font-weight: 700; }

/* Stat card color variants */
.stat-card.c-teal  .stat-icon { background: #f0fdfa; color: var(--teal); }
.stat-card.c-green .stat-icon { background: #f0fdf4; color: #16a34a; }
.stat-card.c-blue  .stat-icon { background: #eff6ff; color: #2563eb; }
.stat-card.c-amber .stat-icon { background: #fffbeb; color: #d97706; }
.stat-card.c-violet .stat-icon { background: #f5f3ff; color: #7c3aed; }

/* Recent table row hover */
.rec-row { cursor: pointer; transition: background var(--t); }
.rec-row:hover td { background: #f0fdfa !important; }
</style>
@endpush

@section('content')

{{-- ── Filter Bar ─────────────────────────────────────── --}}
<div class="card mb-4" style="border:1px solid var(--border);border-radius:var(--radius);position:sticky;top:60px;z-index:899;box-shadow:0 4px 16px rgba(0,0,0,.1);background:#fff;">
    <div class="card-body py-3 px-4">
        <form method="GET" action="{{ route('dashboard') }}" id="analyticsFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ ($filters['year']??'')==$y?'selected':'' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Quarter</label>
                    <select name="quarter" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($quarters as $q)
                            <option value="{{ $q }}" {{ ($filters['quarter']??'')==$q?'selected':'' }}>{{ $q }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Constituent</label>
                    <select name="constituent" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="__empty__" {{ ($filters['constituent']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                        @foreach(['Alangilan','Lipa','Malvar','Nasugbu','Pablo Borbon'] as $c)
                            <option value="{{ $c }}" {{ ($filters['constituent']??'')==$c?'selected':'' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Classification</label>
                    <select name="classification" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="__empty__" {{ ($filters['classification']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                        @foreach($classifications as $cl)
                            <option value="{{ $cl }}" {{ ($filters['classification']??'')==$cl?'selected':'' }}>{{ $cl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="__empty__" {{ ($filters['status']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ ($filters['status']??'')==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label" style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.4px;">Fund</label>
                    <select name="source_of_fund" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="__empty__" {{ ($filters['source_of_fund']??'')=='__empty__'?'selected':'' }} style="color:#b91c1c;">(Empty)</option>
                        @foreach($funds as $f)
                            <option value="{{ $f }}" {{ ($filters['source_of_fund']??'')==$f?'selected':'' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex align-items-end">
                    @if(array_filter($filters))
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary" style="font-size:12px;">
                            <i class="bi bi-x-circle me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
        {{-- ── Active Filter Pills ─────────────────────────────── --}}
        <div id="pillsRow" style="display:none;align-items:center;gap:8px;flex-wrap:wrap;padding:8px 0 2px;">
            <div id="activePills" style="display:flex;gap:6px;flex-wrap:wrap;"></div>
            <a href="{{ route('dashboard') }}" id="clearFiltersBtn" style="font-size:11px;color:#b91c1c;font-weight:600;text-decoration:none;padding:3px 10px;border:1px solid #fca5a5;border-radius:20px;background:#fef2f2;">
                <i class="bi bi-x-circle me-1"></i>Clear All
            </a>
        </div>
    </div>
</div>

{{-- ── Stat Cards ────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    @php
    // Merge dashboard filters into each card's link so records page shows correctly filtered
    $f = array_filter($filters); // remove empty values
    $cards = [
        ['val'=>$total,       'lbl'=>'Total Records',        'icon'=>'bi-journal-text',      'color'=>'c-teal',   'url'=> route('research.index', $f),                                           'fundKey'=>'__all__'],
        ['val'=>$completed,   'lbl'=>'Completed',            'icon'=>'bi-check-circle-fill', 'color'=>'c-green',  'url'=> route('research.index', array_merge($f, ['status'=>'Completed'])),      'fundKey'=>'Completed'],
        ['val'=>$ongoing,     'lbl'=>'Ongoing',              'icon'=>'bi-arrow-repeat',      'color'=>'c-blue',   'url'=> route('research.index', array_merge($f, ['status'=>'Ongoing'])),        'fundKey'=>'Ongoing'],
        ['val'=>$withPresent, 'lbl'=>'Presentation Support', 'icon'=>'bi-person-video3',     'color'=>'c-violet', 'url'=> route('research.index', array_merge($f, ['presentation_support'=>'Yes'])), 'fundKey'=>null],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="col-6 col-md-4 col-xl">
        <a href="{{ $card['url'] }}" class="stat-card {{ $card['color'] }}" title="View filtered records">
            <div class="stat-icon"><i class="bi {{ $card['icon'] }}"></i></div>
            <div>
                <div class="stat-val" id="{{ $loop->index === 0 ? 'kpiTotal' : ($loop->index === 1 ? 'kpiCompleted' : ($loop->index === 2 ? 'kpiOngoing' : 'kpiPresent')) }}">{{ $card['val'] }}</div>
                <div class="stat-lbl">{{ $card['lbl'] }}</div>
            </div>
        </a>

        </a>
    </div>
    @endforeach
</div>

{{-- ── Funding Source Subcategory ──────────────────── --}}
<div class="card mb-4" style="border:1px solid var(--border);border-radius:var(--radius);">
    <div class="card-body py-3 px-4">
        <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">
            <i class="bi bi-cash-stack me-1" style="color:var(--teal);"></i> Funding Breakdown
        </div>
        <div class="row g-3" id="fundBreakdown">
            @foreach($byFund as $fund)
            @php
                $pct = $total > 0 ? round(($fund->total / $total) * 100) : 0;
                $colors = ['#0d9488','#f59e0b','#6366f1','#ec4899','#3b82f6','#f97316','#10b981','#a855f7'];
                $color = $colors[$loop->index % count($colors)];
            @endphp
            <div class="col-6 col-md-3">
                <a href="{{ route('research.index', array_merge($filters, ['source_of_fund' => $fund->source_of_fund])) }}"
                   style="display:block;text-decoration:none;padding:12px 14px;border-radius:10px;border:1px solid var(--border);background:var(--surface);transition:box-shadow .15s;"
                   onmouseover="this.style.boxShadow='0 2px 12px rgba(0,0,0,.08)'" onmouseout="this.style.boxShadow=''">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <span style="font-size:12px;font-weight:700;color:var(--ink);">{{ $fund->source_of_fund }}</span>
                        <span style="font-size:18px;font-weight:800;color:{{ $color }};">{{ $fund->total }}</span>
                    </div>
                    <div style="background:var(--border);border-radius:10px;height:5px;overflow:hidden;">
                        <div style="width:{{ $pct }}%;height:100%;background:{{ $color }};border-radius:10px;"></div>
                    </div>
                    <div style="font-size:10px;color:var(--text-muted);margin-top:5px;">{{ $pct }}% of total</div>
                </a>
            </div>
            @endforeach
            @if($byFund->isEmpty())
            <div class="col-12">
                <p style="font-size:13px;color:var(--text-muted);margin:0;">No funding data available.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Row 1: Charts ────────────────────────────────── --}}
<div class="row g-3 mb-3" style="align-items:stretch;">
    <div class="col-md-4">
        <div class="chart-box" style="height:auto;">
            <div class="chart-title"><i class="bi bi-pie-chart-fill" style="color:var(--teal);"></i> By Status <span class="click-hint"><i class="bi bi-cursor"></i> clickable</span></div>
            <div style="height:230px;position:relative;">
                <canvas id="statusChart" style="position:absolute;top:0;left:0;width:100%;height:100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-box" style="height:auto;">
            <div class="chart-title"><i class="bi bi-people-fill" style="color:var(--teal);"></i> By Constituent <span class="click-hint"><i class="bi bi-cursor"></i> clickable</span></div>
            <div style="height:230px;position:relative;">
                <canvas id="constituentChart" style="position:absolute;top:0;left:0;width:100%;height:100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-box" style="height:auto;">
            <div class="chart-title"><i class="bi bi-globe2" style="color:var(--teal);"></i> By Classification <span class="click-hint"><i class="bi bi-cursor"></i> clickable</span></div>
            <div style="height:230px;position:relative;">
                <canvas id="classificationChart" style="position:absolute;top:0;left:0;width:100%;height:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 2: Trend + Fund ──────────────────────────── --}}
<div class="row g-3 mb-3" style="align-items:stretch;">
    <div class="col-md-8">
        <div class="chart-box" style="height:auto;">
            <div class="chart-title"><i class="bi bi-graph-up-arrow" style="color:var(--teal);"></i> Submission Trend <span class="click-hint"><i class="bi bi-cursor"></i> clickable</span></div>
            <div style="height:230px;position:relative;">
                <canvas id="trendChart" style="position:absolute;top:0;left:0;width:100%;height:100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-box" style="height:auto;">
            <div class="chart-title"><i class="bi bi-cash-stack" style="color:var(--teal);"></i> Top Funding Sources <span class="click-hint"><i class="bi bi-cursor"></i> clickable</span></div>
            <div style="height:230px;position:relative;">
                <canvas id="fundChart" style="position:absolute;top:0;left:0;width:100%;height:100%;"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 3: College + SDG ─────────────────────────── --}}
<div class="row g-3 mb-3" style="align-items:stretch;">
    <div class="col-md-7">
        <div class="chart-box" style="height:auto;">
            <div class="chart-title"><i class="bi bi-building" style="color:var(--teal);"></i> By College / Campus <span class="click-hint"><i class="bi bi-cursor"></i> clickable</span></div>
            <div style="height:{{ max(230, count($byCollege) * 52) }}px; position:relative; width:100%;">
                <canvas id="collegeChart" style="position:absolute;top:0;left:0;width:100%;height:100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="chart-box" style="height:auto;">
            <div class="chart-title"><i class="bi bi-award-fill" style="color:var(--teal);"></i> SDG Coverage <span class="click-hint"><i class="bi bi-cursor"></i> clickable</span></div>
            <div id="sdgList" style="height:{{ max(230, count($byCollege) * 52) }}px;overflow-y:auto;padding-right:4px;">
                @php $maxSdg = $sdgCounts ? max(array_values($sdgCounts)) : 1; @endphp
                @foreach($sdgCounts as $sdg => $cnt)
                <a href="{{ route('research.index', ['sdg' => $sdg]) }}" class="sdg-row" title="Filter: {{ $sdg }}">
                    <div class="sdg-label">{{ explode(' – ',$sdg)[0] }}</div>
                    <div class="sdg-track"><div class="sdg-fill" style="width:{{ ($cnt/$maxSdg)*100 }}%"></div></div>
                    <div class="sdg-cnt">{{ $cnt }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Records ───────────────────────────────── --}}
<div class="card">
    <div class="card-header py-3 px-4 d-flex justify-content-between align-items-center">
        <span style="display:flex;align-items:center;gap:8px;">
            <i class="bi bi-clock-history" style="color:var(--teal);"></i> Recent Records
        </span>
        <a href="{{ route('research.index') }}" class="btn btn-navy btn-sm px-3">View All →</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table data-table mb-0">
                <thead>
                    <tr><th>Title</th><th>Reporter</th><th>College</th><th>Period</th><th>Status</th><th>SDG</th></tr>
                </thead>
                <tbody id="recentTbody">
                @foreach($recentRecords as $rec)
                <tr class="rec-row" onclick="window.location='{{ route('research.show', $rec) }}'">
                    <td>
                        <a href="{{ route('research.show', $rec) }}" style="color:var(--ink);font-weight:700;text-decoration:none;font-size:13px;">
                            {{ Str::limit($rec->research_title, 60) }}
                        </a>
                    </td>
                    <td style="font-size:12.5px;color:var(--muted);">{{ $rec->reporter }}</td>
                    <td style="font-size:12px;">
                        <a href="{{ route('research.index', ['college_campus'=>$rec->college_campus]) }}" style="color:var(--muted);text-decoration:none;" onclick="event.stopPropagation();">
                            {{ Str::limit($rec->college_campus, 30) }}
                        </a>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <a href="{{ route('research.index', ['year'=>$rec->year,'quarter'=>$rec->quarter]) }}" style="text-decoration:none;">
                            <span class="pill b-proposed" style="font-size:10.5px;">{{ $rec->year }} {{ $rec->quarter }}</span>
                        </a>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <a href="{{ route('research.index', ['status'=>$rec->status]) }}" style="text-decoration:none;">
                            @include('research.partials.status-badge',['status'=>$rec->status])
                        </a>
                    </td>
                    <td>
                        @foreach(array_slice($rec->sdg ?? [], 0, 2) as $s)
                            <a href="{{ route('research.index', ['sdg'=>$s]) }}" class="sdg-chip" style="text-decoration:none;" onclick="event.stopPropagation();">{{ explode(' – ', $s)[0] }}</a>
                        @endforeach
                        @if(count($rec->sdg ?? []) > 2)
                            <span style="font-size:10.5px;color:var(--muted);">+{{ count($rec->sdg)-2 }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const pal      = ['#0d9488','#f59e0b','#8b5cf6','#f43f5e','#3b82f6','#22c55e','#ec4899','#14b8a6','#a78bfa','#fb923c'];
const indexUrl    = '{{ route("research.index") }}';
const dataUrl     = '{{ route("dashboard.data") }}';
const dashboardUrl = '{{ route("dashboard") }}';
let dashFilters = @json($filters);

Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#64748b';

// ── Fade helper ───────────────────────────────────────────
function fadeUpdate(el, fn) {
    el.style.transition = 'opacity .2s';
    el.style.opacity = '0.3';
    setTimeout(() => { fn(); el.style.opacity = '1'; }, 220);
}

// ── Active filter pills ───────────────────────────────────
function renderFilterPills() {
    const container = document.getElementById('activePills');
    if (!container) return;
    const labels = {year:'Year',quarter:'Quarter',constituent:'Constituent',college_campus:'Campus',classification:'Classification',status:'Status',source_of_fund:'Fund'};
    container.innerHTML = '';
    let hasAny = false;
    Object.entries(dashFilters).forEach(([k,v]) => {
        if (!v) return;
        hasAny = true;
        const pill = document.createElement('span');
        pill.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#f0fdfa;border:1px solid #5eead4;color:#0f766e;border-radius:20px;padding:3px 10px 3px 10px;font-size:11px;font-weight:600;cursor:pointer;';
        pill.innerHTML = `<span style="opacity:.6;">${labels[k]||k}:</span> ${v === '__empty__' ? '(Empty)' : v} <span style="font-size:13px;opacity:.5;" onclick="removeFilter('${k}')">&times;</span>`;
        container.appendChild(pill);
    });
    const pillsRow = document.getElementById('pillsRow');
    if (pillsRow) pillsRow.style.display = hasAny ? 'flex' : 'none';
}

function removeFilter(key) {
    delete dashFilters[key];
    fetchAndUpdate();
}

// ── AJAX fetch + update all charts ───────────────────────
function fetchAndUpdate() {
    // Update URL without reload
    const url = new URL(dashboardUrl, window.location.origin);
    Object.entries(dashFilters).forEach(([k,v]) => { if(v) url.searchParams.set(k,v); });
    window.history.replaceState({}, '', url.toString());

    // Update filter form dropdowns
    const form = document.getElementById('analyticsFilterForm');
    if (form) {
        Object.entries(dashFilters).forEach(([k,v]) => {
            const sel = form.querySelector(`[name="${k}"]`);
            if (sel) sel.value = v || '';
        });
        // Clear any key not in dashFilters
        form.querySelectorAll('select').forEach(sel => {
            if (!(sel.name in dashFilters) || !dashFilters[sel.name]) sel.value = '';
        });
    }

    renderFilterPills();

    // Fetch data
    const dataFetchUrl = new URL(dataUrl, window.location.origin);
    Object.entries(dashFilters).forEach(([k,v]) => { if(v) dataFetchUrl.searchParams.set(k,v); });

    fetch(dataFetchUrl.toString(), {headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r => r.json())
        .then(d => {
            updateCharts(d);
            updateKPIs(d);
            updateFundBreakdown(d);
            updateSdg(d.sdgCounts);
            updateRecent(d.recentRecords);
        })
        .catch(err => console.error('Dashboard fetch error:', err));
}

function goFilter(params) {
    Object.entries(params).forEach(([k,v]) => {
        const val = (v === null || v === undefined || v === '') ? '__empty__' : v;
        if (dashFilters[k] === val) { delete dashFilters[k]; }
        else { dashFilters[k] = val; }
    });
    fetchAndUpdate();
}

// ── Update chart data with animation ─────────────────────
function updateChart(chart, labels, data, colors) {
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    if (colors) chart.data.datasets[0].backgroundColor = colors;
    chart.update('active');
}
function getYearBars(items) {
    const totals = {};
    const shown = {};

    items.forEach(x => {
        totals[x.year] = (totals[x.year] || 0) + Number(x.total || 0);
    });

    return items.map(x => {
        if (!shown[x.year]) {
            shown[x.year] = true;
            return totals[x.year]; // Q1-Q4 total per year
        }
        return null; // no repeated bar
    });
}
function updateCharts(d) {
    var statusColorMap = {'completed':'#0d9488','ongoing':'#0f172a','on-going':'#0f172a'};
    const sLabels = Object.keys(d.byStatus);
    const sColors = sLabels.map(l => statusColorMap[l.toLowerCase()] ?? '#94a3b8');
    updateChart(statusChart, sLabels, Object.values(d.byStatus), sColors);

    updateChart(constituentChart, Object.keys(d.byConstituent), Object.values(d.byConstituent), pal);
    updateChart(classificationChart, Object.keys(d.byClassification), Object.values(d.byClassification), ['#0d9488','#f59e0b','#6366f1','#ec4899','#3b82f6','#f97316','#10b981','#a855f7']);

    const tLabels = d.byYearQuarter.map(x => x.year+' '+x.quarter);
    const tData   = d.byYearQuarter.map(x => x.total);
    trendChart.data.labels = tLabels;
    trendChart.data.datasets[0].data = getYearBars(d.byYearQuarter); // yearly total bars
    trendChart.data.datasets[1].data = tData; // quarterly line
    trendChart.update('active');

    const fLabels = d.byFund.map(x => x.source_of_fund);
    const fData   = d.byFund.map(x => x.total);
    updateChart(fundChart, fLabels, fData, pal);

    const cLabels = d.byCollege.map(x => x.constituent);
    const cData   = d.byCollege.map(x => x.total);
    collegeChart.data.labels = cLabels;
    collegeChart.data.datasets[0].data = cData;
    collegeChart.update('active');
}

function animateNum(el, target) {
    const start = parseInt(el.textContent.replace(/\D/g,'')) || 0;
    const dur = 400, fps = 30, steps = dur/fps*1000/1000;
    let i = 0;
    const t = setInterval(() => {
        i++;
        el.textContent = Math.round(start + (target - start) * (i / steps));
        if (i >= steps) { el.textContent = target; clearInterval(t); }
    }, fps);
}

function updateKPIs(d) {
    const ids = {total:'kpiTotal', completed:'kpiCompleted', ongoing:'kpiOngoing', withPresent:'kpiPresent'};
    Object.entries(ids).forEach(([key, id]) => {
        const el = document.getElementById(id);
        if (el) animateNum(el, d[key]);
    });
    // Update KPI links
    const base = new URL(indexUrl, window.location.origin);
    Object.entries(dashFilters).forEach(([k,v]) => { if(v) base.searchParams.set(k,v); });
    const completedUrl = new URL(base); completedUrl.searchParams.set('status','Completed');
    const ongoingUrl   = new URL(base); ongoingUrl.searchParams.set('status','Ongoing');
    const presentUrl   = new URL(base); presentUrl.searchParams.set('presentation_support','Yes');
    document.querySelectorAll('[data-kpi-link]').forEach(a => {
        const type = a.dataset.kpiLink;
        if (type==='total')     a.href = base.toString();
        if (type==='completed') a.href = completedUrl.toString();
        if (type==='ongoing')   a.href = ongoingUrl.toString();
        if (type==='present')   a.href = presentUrl.toString();
    });
}

function updateFundBreakdown(d) {
    const container = document.getElementById('fundBreakdown');
    if (!container) return;
    const total = d.fundTotal.reduce((s,f) => s + f.total, 0) || 1;
    fadeUpdate(container, () => {
        container.innerHTML = d.fundTotal.map((f,i) => {
            const pct = Math.round(f.total/total*100);
            const colors = ['#0d9488','#f59e0b','#6366f1','#ec4899','#3b82f6'];
            const c = colors[i % colors.length];
            return `<div class="col-md-4">
                <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:16px 18px;">
                    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:8px;">
                        <span style="font-size:12.5px;font-weight:600;color:var(--ink);">${f.fund}</span>
                        <span style="font-size:22px;font-weight:800;color:${c};">${f.total}</span>
                    </div>
                    <div style="height:5px;background:var(--border);border-radius:5px;overflow:hidden;">
                        <div style="width:${pct}%;height:100%;background:${c};border-radius:5px;"></div>
                    </div>
                    <div style="font-size:11px;color:var(--muted);margin-top:5px;">${pct}% of total</div>
                </div>
            </div>`;
        }).join('');
    });
}

function updateSdg(sdgCounts) {
    const container = document.getElementById('sdgList');
    if (!container) return;
    const max = sdgCounts && Object.values(sdgCounts).length ? Math.max(...Object.values(sdgCounts)) : 1;
    fadeUpdate(container, () => {
        container.innerHTML = Object.entries(sdgCounts).map(([sdg, cnt]) => {
            const shortLabel = sdg.split(' – ')[0];
            const pct = (cnt/max)*100;
            const base = new URL(indexUrl, window.location.origin);
            Object.entries(dashFilters).forEach(([k,v]) => { if(v) base.searchParams.set(k,v); });
            base.searchParams.set('sdg', sdg);
            return `<a href="${base}" class="sdg-row" title="Filter: ${sdg}">
                <div class="sdg-label">${shortLabel}</div>
                <div class="sdg-track"><div class="sdg-fill" style="width:${pct}%"></div></div>
                <div class="sdg-cnt">${cnt}</div>
            </a>`;
        }).join('');
    });
}

function updateRecent(records) {
    const tbody = document.getElementById('recentTbody');
    if (!tbody) return;
    fadeUpdate(tbody, () => {
        if (!records.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--muted);">No records found</td></tr>';
            return;
        }
        tbody.innerHTML = records.map(r => {
            const sdgs = Array.isArray(r.sdg) ? r.sdg : [];
            const sdgHtml = sdgs.slice(0,2).map(s => {
                const label = s.split(' – ')[0];
                return `<span class="sdg-chip">${label}</span>`;
            }).join('') + (sdgs.length > 2 ? `<span style="font-size:10.5px;color:var(--muted);">+${sdgs.length-2}</span>` : '');
            return `
            <tr class="rec-row" onclick="window.location='{{ route('research.index') }}/${r.id}'" style="cursor:pointer;">
                <td><span style="font-size:12px;font-weight:600;">${r.research_title||'—'}</span></td>
                <td style="font-size:12px;">${r.authors||'—'}</td>
                <td style="font-size:12px;">${r.college_campus||'—'}</td>
                <td><span class="pill b-proposed" style="font-size:10.5px;padding:2px 8px;">${r.year||'—'} ${r.quarter||''}</span></td>
                <td><span class="pill ${r.status?.toLowerCase()==='completed'?'b-completed':'b-ongoing'}" style="font-size:10.5px;">${r.status||'—'}</span></td>
                <td>${sdgHtml||'—'}</td>
            </tr>`;
        }).join('');
    });
}

// Map color by status label name so it works regardless of order or spelling variants
var statusColorMap = {
    'completed':  '#0d9488',
    'ongoing':    '#0f172a',
    'on-going':   '#0f172a',
};
var statusLabels = @json($byStatus->keys());
var statusColors = statusLabels.map(l => statusColorMap[l.toLowerCase()] ?? '#94a3b8');

var statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: statusLabels, datasets: [{ data: @json($byStatus->values()), backgroundColor: statusColors, borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }] },
    options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels:{ padding:16, font:{size:12} } } }, cutout: '68%',
        onClick(e,el) { if(el.length) goFilter({status: statusChart.data.labels[el[0].index]}); } }
});

var constituentChart = new Chart(document.getElementById('constituentChart'), {
    type: 'bar',
    data: { labels: @json($byConstituent->keys()), datasets: [{ data: @json($byConstituent->values()), backgroundColor: pal, borderRadius: 8, borderSkipped: false }] },
    options: { maintainAspectRatio: false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,.04)'}}, x:{grid:{display:false}}},
        onClick(e,el) { if(el.length) goFilter({constituent: constituentChart.data.labels[el[0].index]}); } }
});

var classificationChart = new Chart(document.getElementById('classificationChart'), {
    type: 'doughnut',
    data: { labels: @json($byClassification->keys()), datasets: [{ data: @json($byClassification->values()), backgroundColor: ['#0d9488','#f59e0b','#6366f1','#ec4899','#3b82f6','#f97316','#10b981','#a855f7'], borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }] },
    options: { maintainAspectRatio: false, plugins:{legend:{position:'right',labels:{padding:10,font:{size:11},boxWidth:12,boxHeight:12}}}, cutout:'68%',
        onClick(e,el) { if(el.length) goFilter({classification: classificationChart.data.labels[el[0].index]}); } }
});

var trend = @json($byYearQuarter);

var trendChart = new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: trend.map(d => d.year),
        datasets: [
            {
                type: 'bar',
                label: 'Yearly Total',
                data: getYearBars(trend),
                backgroundColor: 'rgba(13,148,136,.50)',
                borderWidth: 0,
                borderRadius: 10,
                barPercentage: 1.0,
                categoryPercentage: 1.0,
                order: 2
            },
            {
                type: 'line',
                label: 'Submissions',
                data: trend.map(d => d.total),
                borderColor: '#0d9488',
                backgroundColor: 'rgba(13,148,136,.07)',
                borderWidth: 2.5,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 9,
                fill: true,
                tension: .4,
                order: 1
            }
        ]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.dataset.label === 'Yearly Total') {
                            return 'Year Total: ' + context.raw;
                        }
                        return 'Quarter Total: ' + context.raw;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(0,0,0,.04)' }
            },
            x: {
                grid: { display: false }
            }
        },
        onClick(e, el) {
            if (el.length) {
                const index = el[0].index;
                const d = trend[index];
                goFilter({ year: d.year, quarter: d.quarter });
            }
        }
    }
});

var fund = @json($byFund);
var fundChart = new Chart(document.getElementById('fundChart'), {
    type: 'bar',
    data: { labels: fund.map(d=>d.source_of_fund), datasets:[{ data:fund.map(d=>d.total), backgroundColor:pal, borderRadius:8, borderSkipped:false }] },
    options: { maintainAspectRatio: false, indexAxis:'y', plugins:{legend:{display:false}}, scales:{x:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,.04)'}}, y:{grid:{display:false}}},
        onClick(e,el) { if(el.length) goFilter({source_of_fund:fund[el[0].index].source_of_fund}); } }
});

var col = @json($byCollege);
var collegeChart = new Chart(document.getElementById('collegeChart'), {
    type: 'bar',
    data: { labels: col.map(d=>d.constituent), datasets:[{ data:col.map(d=>d.total), backgroundColor:['#0d9488','#14b8a6','#f59e0b','#6366f1','#ec4899','#10b981','#3b82f6','#f97316'], borderRadius:8, borderSkipped:false }] },
    options: { maintainAspectRatio: false, indexAxis:'y', plugins:{legend:{display:false}}, scales:{x:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(0,0,0,.04)'}}, y:{grid:{display:false}}},
        onClick(e,el) { if(el.length) goFilter({constituent:col[el[0].index].constituent}); } }
});

// Wire up filter form selects to AJAX
document.getElementById('analyticsFilterForm').querySelectorAll('select').forEach(sel => {
    sel.addEventListener('change', function() {
        const key = this.name, val = this.value;
        if (val) dashFilters[key] = val;
        else delete dashFilters[key];
        fetchAndUpdate();
    });
});

// Wire up clear button
const clearBtn = document.getElementById('clearFiltersBtn');
if (clearBtn) {
    clearBtn.addEventListener('click', function(e) {
        e.preventDefault();
        dashFilters = {};
        fetchAndUpdate();
    });
}

// Initial render of pills
renderFilterPills();
</script>
@endpush