@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ── Stat cards ────────────────────────────────────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }
    @media(max-width:900px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    @media(max-width:480px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--white);
        border-radius: 18px;
        padding: 22px 24px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(26,16,64,.06);
        display: flex; flex-direction: column; gap: 10px;
        position: relative; overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 32px rgba(26,16,64,.1); }
    .stat-card::after {
        content: ''; position: absolute;
        bottom: 0; left: 0; right: 0; height: 3px;
    }
    .stat-card.orange::after { background: linear-gradient(90deg, var(--saffron), var(--saffron2)); }
    .stat-card.green::after  { background: linear-gradient(90deg, #22c55e, #86efac); }
    .stat-card.purple::after { background: linear-gradient(90deg, #8b5cf6, #c4b5fd); }
    .stat-card.blue::after   { background: linear-gradient(90deg, #3b82f6, #93c5fd); }

    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
    }
    .stat-card.orange .stat-icon { background: rgba(255,107,0,.12); }
    .stat-card.green  .stat-icon { background: rgba(34,197,94,.12); }
    .stat-card.purple .stat-icon { background: rgba(139,92,246,.12); }
    .stat-card.blue   .stat-icon { background: rgba(59,130,246,.12); }

    .stat-label { font-size: 12px; color: var(--muted); font-weight: 500; letter-spacing: .3px; }
    .stat-value { font-family: 'Baloo 2', cursive; font-size: 36px; font-weight: 800; color: var(--deep); line-height: 1; }

    /* ── Bottom grid ────────────────────────────────────────────────────────── */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media(max-width: 800px) { .bottom-grid { grid-template-columns: 1fr; } }

    .card {
        background: var(--white);
        border-radius: 18px;
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(26,16,64,.06);
    }
    .card-title {
        font-family: 'Baloo 2', cursive;
        font-size: 17px; font-weight: 700; color: var(--deep);
        margin-bottom: 18px;
        display: flex; align-items: center; gap: 8px;
    }

    /* ── City bars ──────────────────────────────────────────────────────────── */
    .city-row { margin-bottom: 14px; }
    .city-meta { display: flex; justify-content: space-between; margin-bottom: 5px; }
    .city-name { font-size: 13px; font-weight: 600; color: var(--deep); }
    .city-count { font-size: 12px; color: var(--muted); }
    .city-bar-bg {
        height: 8px; background: var(--border);
        border-radius: 8px; overflow: hidden;
    }
    .city-bar-fill {
        height: 100%; border-radius: 8px;
        background: linear-gradient(90deg, var(--saffron), var(--saffron2));
        transition: width 1s ease;
    }

    /* ── Recent table ───────────────────────────────────────────────────────── */
    .recent-table { width: 100%; border-collapse: collapse; }
    .recent-table th {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .6px; color: var(--muted);
        padding: 0 0 10px; text-align: left; border-bottom: 1px solid var(--border);
    }
    .recent-table td {
        padding: 10px 0; font-size: 13px; border-bottom: 1px solid var(--border);
        color: var(--deep);
    }
    .recent-table tr:last-child td { border-bottom: none; }
    .city-pill {
        display: inline-block;
        background: rgba(255,107,0,.1);
        color: var(--saffron);
        font-size: 11px; font-weight: 600;
        padding: 2px 8px; border-radius: 20px;
    }
    .view-all {
        display: block; text-align: center; margin-top: 16px;
        font-size: 13px; font-weight: 600; color: var(--saffron);
        text-decoration: none;
    }
    .view-all:hover { text-decoration: underline; }

    /* ── Trend bar chart ────────────────────────────────────────────────────── */
    .trend-chart {
        display: flex; align-items: flex-end; gap: 8px;
        height: 100px; padding-top: 10px;
    }
    .trend-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px; }
    .trend-bar {
        width: 100%; border-radius: 6px 6px 0 0;
        background: linear-gradient(180deg, var(--saffron), var(--saffron2));
        min-height: 4px;
        transition: height 1s ease;
    }
    .trend-label { font-size: 10px; color: var(--muted); white-space: nowrap; }
    .trend-val   { font-size: 11px; font-weight: 600; color: var(--deep); }
</style>
@endpush

@section('content')

<!-- Stat cards -->
<div class="stats-grid">
    <div class="stat-card orange">
        <div class="stat-icon">📋</div>
        <div class="stat-label">Total Registrations</div>
        <div class="stat-value">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">📅</div>
        <div class="stat-label">Registered Today</div>
        <div class="stat-value">{{ $stats['today'] }}</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">📍</div>
        <div class="stat-label">Active Cities</div>
        <div class="stat-value">{{ $stats['cities'] }}</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">🖼️</div>
        <div class="stat-label">Banners Generated</div>
        <div class="stat-value">{{ $stats['banners'] }}</div>
    </div>
</div>

<div class="bottom-grid">

    <!-- City breakdown -->
    <div class="card">
        <div class="card-title">📍 Registrations by City</div>
        @php $maxCity = $cityBreakdown->max('total') ?: 1; @endphp
        @forelse($cityBreakdown as $city)
        <div class="city-row">
            <div class="city-meta">
                <span class="city-name">{{ $city->city }}</span>
                <span class="city-count">{{ $city->total }} registered</span>
            </div>
            <div class="city-bar-bg">
                <div class="city-bar-fill" style="width:{{ round(($city->total / $maxCity) * 100) }}%"></div>
            </div>
        </div>
        @empty
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">No registrations yet</p>
        @endforelse
    </div>

    <!-- Daily trend -->
    <div class="card">
        <div class="card-title">📈 Last 7 Days</div>
        @php $maxTrend = $dailyTrend->max('total') ?: 1; @endphp
        <div class="trend-chart">
            @forelse($dailyTrend as $day)
            <div class="trend-col">
                <div class="trend-val">{{ $day->total }}</div>
                <div class="trend-bar" style="height:{{ max(4, round(($day->total / $maxTrend) * 80)) }}px"></div>
                <div class="trend-label">{{ \Carbon\Carbon::parse($day->date)->format('D') }}</div>
            </div>
            @empty
            <p style="color:var(--muted);font-size:13px;margin:auto;">No data yet</p>
            @endforelse
        </div>
    </div>

    <!-- Recent registrations -->
    <div class="card" style="grid-column: 1 / -1;">
        <div class="card-title">⏱ Recent Registrations</div>
        @if($recentRegistrations->isEmpty())
            <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">No registrations yet</p>
        @else
        <table class="recent-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>City</th>
                    <th>Registered</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentRegistrations as $reg)
                <tr>
                    <td><strong>{{ $reg->full_name }}</strong></td>
                    <td>{{ $reg->mobile }}</td>
                    <td><span class="city-pill">{{ $reg->city }}</span></td>
                    <td>{{ $reg->created_at->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('admin.registrations.show', $reg) }}" style="color:var(--saffron);font-size:13px;font-weight:600;text-decoration:none;">View →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('admin.registrations.index') }}" class="view-all">View all registrations →</a>
        @endif
    </div>

</div>
@endsection
