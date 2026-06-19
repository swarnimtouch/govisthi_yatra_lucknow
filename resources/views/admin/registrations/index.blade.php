@extends('layouts.admin')
@use(Illuminate\Support\Facades\Storage)
@section('page-title', 'All Registrations')

@push('styles')
    <style>
        /* ── Filter bar ─────────────────────────────────────────────────────────── */
        .filter-card {
            background: var(--white);
            border-radius: 16px;
            padding: 20px 24px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(26,16,64,.05);
            margin-bottom: 20px;
        }
        .filter-row {
            display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 140px; }
        .filter-group label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
        .filter-group input,
        .filter-group select {
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: 9px; font-size: 13px;
            font-family: 'Poppins', sans-serif;
            background: var(--cream); color: var(--deep);
            outline: none; transition: border .2s;
        }
        .filter-group input:focus,
        .filter-group select:focus { border-color: var(--saffron); background: #fff; }

        .btn-filter, .btn-reset {
            padding: 9px 18px; border-radius: 9px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            border: none; transition: all .2s;
        }
        .btn-filter { background: var(--saffron); color: #fff; }
        .btn-filter:hover { background: #E55F00; }
        .btn-reset { background: var(--cream); color: var(--muted); border: 1.5px solid var(--border); }
        .btn-reset:hover { border-color: var(--saffron); color: var(--saffron); }

        /* ── Table card ─────────────────────────────────────────────────────────── */
        .table-card {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(26,16,64,.05);
            overflow: hidden;
        }
        .table-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
        }
        .table-title { font-family:'Baloo 2',cursive; font-size:17px; font-weight:700; color:var(--deep); }
        .table-count { font-size:12px; color:var(--muted); }
        .btn-export {
            padding: 8px 16px;
            background: #dcfce7; color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 8px; font-size: 12px; font-weight: 600;
            text-decoration: none; font-family:'Poppins',sans-serif;
            transition: all .2s;
        }
        .btn-export:hover { background: #bbf7d0; }

        /* ── Data table ─────────────────────────────────────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .6px; color: var(--muted);
            padding: 12px 20px; text-align: left;
            background: var(--cream);
            border-bottom: 1px solid var(--border);
        }
        .data-table td {
            padding: 13px 20px; font-size: 13px;
            border-bottom: 1px solid var(--border);
            color: var(--deep); vertical-align: middle;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tbody tr { transition: background .15s; }
        .data-table tbody tr:hover { background: var(--cream); }

        .avatar-cell { display: flex; align-items: center; gap: 10px; }
        .reg-avatar {
            width: 36px; height: 36px; border-radius: 8px;
            object-fit: cover; border: 1.5px solid var(--border);
            flex-shrink: 0;
        }
        .reg-avatar-placeholder {
            width: 36px; height: 36px; border-radius: 8px;
            background: linear-gradient(135deg, var(--saffron), var(--saffron2));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 14px; font-weight: 700;
            flex-shrink: 0;
        }
        .reg-name  { font-weight: 600; }
        .reg-email { font-size: 12px; color: var(--muted); }

        .city-pill {
            display: inline-block;
            background: rgba(255,107,0,.1); color: var(--saffron);
            font-size: 11px; font-weight: 600;
            padding: 3px 10px; border-radius: 20px;
        }
        .banner-badge {
            display: inline-block;
            background: #dcfce7; color: #166534;
            font-size: 11px; font-weight: 600;
            padding: 3px 10px; border-radius: 20px;
        }
        .no-banner-badge {
            display: inline-block;
            background: var(--cream); color: var(--muted);
            font-size: 11px; font-weight: 600;
            padding: 3px 10px; border-radius: 20px;
        }

        .btn-view {
            padding: 6px 12px;
            background: rgba(255,107,0,.1); color: var(--saffron);
            border: 1px solid rgba(255,107,0,.2);
            border-radius: 7px; font-size: 12px; font-weight: 600;
            text-decoration: none; transition: all .2s;
        }
        .btn-view:hover { background: var(--saffron); color: #fff; }

        /* ── Pagination ─────────────────────────────────────────────────────────── */
        .pagination-wrap {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
        }
        .pagination-info { font-size: 12px; color: var(--muted); }
        .pagination { display: flex; gap: 4px; list-style: none; }
        .pagination .page-item .page-link {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 8px;
            border: 1.5px solid var(--border);
            font-size: 13px; font-weight: 600; color: var(--deep);
            text-decoration: none; transition: all .2s;
            background: var(--white);
        }
        .pagination .page-item.active .page-link {
            background: var(--saffron); color: #fff; border-color: var(--saffron);
        }
        .pagination .page-item .page-link:hover {
            border-color: var(--saffron); color: var(--saffron);
        }
        .pagination .page-item.disabled .page-link {
            opacity: .4; pointer-events: none;
        }

        /* ── Empty state ────────────────────────────────────────────────────────── */
        .empty-state {
            text-align: center; padding: 60px 20px;
            color: var(--muted);
        }
        .empty-state .icon { font-size: 48px; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }

        @media(max-width:700px) {
            .data-table thead { display: none; }
            .data-table td { display: block; padding: 6px 16px; border: none; }
            .data-table td::before { content: attr(data-label); font-weight:700; font-size:10px; color:var(--muted); display:block; text-transform:uppercase; letter-spacing:.5px; margin-bottom:2px; }
            .data-table tr { border-bottom: 1px solid var(--border); display: block; padding: 10px 0; }
            .data-table tr:last-child { border: none; }
        }
        .btn-del {
            padding: 6px 10px;
            background: #fee2e2; color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 7px; font-size: 12px; font-weight: 600;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            transition: all .2s;
            line-height: 1;
        }
        .btn-del:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

        .action-cell {
            display: flex; align-items: center; gap: 6px;
        }
    </style>
@endpush

@section('content')

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.registrations.index') }}">
            <div class="filter-row">
                <div class="filter-group" style="flex:2;min-width:200px;">
                    <label>Search</label>
                    <input type="text" name="search" placeholder="Name, mobile, email…" value="{{ request('search') }}">
                </div>
                <div class="filter-group">
                    <label>City</label>
                    <select name="city">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
                <button type="submit" class="btn-filter">🔍 Filter</button>
                <a href="{{ route('admin.registrations.index') }}" class="btn-reset">✕ Reset</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-card">
        <div class="table-header">
            <div>
                <div class="table-title">📋 Registrations</div>
                <div class="table-count">{{ $registrations->total() }} total records</div>
            </div>
            <a href="{{ route('admin.registrations.export', request()->only('city')) }}" class="btn-export">⬇️ Export CSV</a>
        </div>

        @if($registrations->isEmpty())
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>No registrations found matching your filters.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Gender</th>
                        <th>Event Date</th>
                        <th>Banner</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($registrations as $reg)
                        <tr>
                            <td data-label="ID">
                                {{ ($registrations->currentPage() - 1) * $registrations->perPage() + $loop->iteration }}
                            </td>
                            <td data-label="Participant">
                                <div class="avatar-cell">
                                    @if($reg->photo_cropped)
                                        <img src="{{ Storage::disk('s3')->url($reg->photo_cropped) }}"
                                             class="reg-avatar"
                                             alt="">
                                    @else
                                        <div class="reg-avatar-placeholder">{{ strtoupper(substr($reg->full_name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="reg-name">{{ $reg->full_name }}</div>
                                        @if($reg->email)
                                            <div class="reg-email">{{ $reg->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="City"><span class="city-pill">{{ $reg->city }}</span></td>
                            <td data-label="Gender">{{ $reg->gender }}</td>
                            <td data-label="Event Date">{{ \Carbon\Carbon::parse($reg->event_date)->format('d M Y') }}</td>
                            <td data-label="Banner">
                                @if($reg->generated_banner)
                                    <span class="banner-badge">✅ Ready</span>
                                @else
                                    <span class="no-banner-badge">Pending</span>
                                @endif
                            </td>
                            <td data-label="Registered">    {{ $reg->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                            </td>
                            <td data-label="Action">
                                <div class="action-cell">
                                    <a href="{{ route('admin.registrations.show', $reg) }}" class="btn-view">View →</a>
                                    <form method="POST"
                                          action="{{ route('admin.registrations.destroy', $reg) }}"
                                          class="delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                class="btn-del"
                                                data-name="{{ $reg->full_name }}">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing {{ $registrations->firstItem() }}–{{ $registrations->lastItem() }} of {{ $registrations->total() }}
                </div>
                {{ $registrations->links('vendor.pagination.admin') }}
            </div>
        @endif
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-form .btn-del').forEach(btn => {
            btn.addEventListener('click', function () {
                const form = this.closest('form');
                const name = this.dataset.name;
                Swal.fire({
                    title: 'Delete Registration?',
                    text: `"${name}" will be permanently deleted. This cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
