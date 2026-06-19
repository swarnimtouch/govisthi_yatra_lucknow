@extends('layouts.admin')

@section('title', 'Registration #' . $registration->id)
@section('page-title', 'Registration Detail')

@push('styles')
<style>
    .back-link {
        display: inline-flex; align-items: center; gap: 6px;
        color: var(--muted); font-size: 13px; font-weight: 600;
        text-decoration: none; margin-bottom: 20px;
        transition: color .2s;
    }
    .back-link:hover { color: var(--saffron); }

    .detail-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 20px;
        align-items: start;
    }
    @media(max-width:800px) { .detail-grid { grid-template-columns: 1fr; } }

    .card {
        background: var(--white);
        border-radius: 18px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(26,16,64,.06);
        overflow: hidden;
    }
    .card-head {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border);
        font-family: 'Baloo 2', cursive;
        font-size: 16px; font-weight: 700; color: var(--deep);
    }
    .card-body { padding: 24px; }

    /* Photo card */
    .photo-section { text-align: center; }
    .reg-photo-large {
        width: 180px; height: 180px;
        border-radius: 16px; object-fit: cover;
        border: 3px solid var(--saffron);
        margin-bottom: 12px;
        box-shadow: 0 8px 24px rgba(255,107,0,.2);
    }
    .reg-photo-placeholder {
        width: 180px; height: 180px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--saffron), var(--saffron2));
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 64px; font-weight: 800; color: #fff;
        margin-bottom: 12px;
    }
    .reg-name-large {
        font-family: 'Baloo 2', cursive;
        font-size: 20px; font-weight: 800; color: var(--deep);
    }
    .city-pill-large {
        display: inline-block;
        background: rgba(255,107,0,.1); color: var(--saffron);
        font-size: 13px; font-weight: 700;
        padding: 4px 14px; border-radius: 20px;
        margin-top: 8px;
    }

    /* Info rows */
    .info-row {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
    }
    .info-row:last-child { border-bottom: none; }
    .info-icon { font-size: 18px; width: 28px; text-align: center; flex-shrink: 0; margin-top: 1px; }
    .info-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
    .info-value { font-size: 14px; color: var(--deep); font-weight: 500; margin-top: 2px; }

    /* Banner preview */
    .banner-preview {
        width: 100%; 
        border-radius: 12px;
        overflow: hidden; 
        border: 2px solid var(--saffron);
        max-width: 400px; 
        margin: 0 auto;   
    }
    .banner-preview img { 
        width: 100%; 
        height: auto; 
        display: block; 
    }
    .no-banner {
        background: var(--cream); border-radius: 12px;
        padding: 40px; text-align: center; color: var(--muted);
        font-size: 14px;
    }

    /* Actions */
    .action-row {
        display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap;
    }
    .btn-dl {
        padding: 10px 18px;
        background: linear-gradient(135deg, var(--saffron), var(--saffron2));
        color: #fff; border: none; border-radius: 9px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        text-decoration: none; font-family: 'Poppins', sans-serif;
    }
    .btn-delete {
        padding: 10px 18px;
        background: #fee2e2; color: #991b1b;
        border: 1px solid #fecaca; border-radius: 9px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        font-family: 'Poppins', sans-serif; transition: all .2s;
    }
    .btn-delete:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
</style>
@endpush

@section('content')
<a href="{{ route('admin.registrations.index') }}" class="back-link">← Back to All Registrations</a>

<div class="detail-grid">

    <!-- Left: photo & summary -->
    <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="card">
            <div class="card-head">👤 Participant</div>
            <div class="card-body photo-section">
                @if($registration->photo_cropped)
                    <img src="{{ Storage::disk('s3')->url($registration->photo_cropped) }}"
                         class="reg-photo-large"
                         alt="Photo">                @else
                    <div class="reg-photo-placeholder">{{ strtoupper(substr($registration->full_name, 0, 1)) }}</div>
                @endif
                <div class="reg-name-large">{{ $registration->full_name }}</div>
                <div class="city-pill-large">📍 {{ $registration->city }}</div>

            </div>
        </div>

        <div class="card">
            <div class="card-head">📋 Details</div>
            <div class="card-body" style="padding:16px 24px;">

                <div class="info-row">
                    <div class="info-icon">📅</div>
                    <div>
                        <div class="info-label">Event Date</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($registration->event_date)->format('d F Y') }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">🕐</div>
                    <div>
                        <div class="info-label">Registered At</div>
                        <div class="info-value">    {{ $registration->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        @if(strtolower($registration->gender) == 'male')
                            👨
                        @elseif(strtolower($registration->gender) == 'female')
                            👩
                        @else
                            ⚧️
                        @endif
                    </div>

                    <div>
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $registration->gender }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Right: banner -->
    <div class="card">
        <div class="card-head">🖼️ Generated Banner</div>
        <div class="card-body">
            @if($registration->generated_banner)
                <div class="banner-preview">
                    <img src="{{ Storage::disk('s3')->url($registration->generated_banner) }}"
                         alt="Generated Banner">                </div>
                <div class="action-row">
                    <a href="{{ route('admin.registrations.download-banner', $registration->id) }}"
                       class="btn-dl">
                        ⬇️ Download Banner
                    </a>
                </div>
            @else
                <div class="no-banner">
                    <div style="font-size:48px;margin-bottom:10px;">🖼️</div>
                    <p>Banner has not been generated yet for this registration.</p>
                </div>
            @endif

            <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--border);">
                <div style="font-size:13px;font-weight:600;color:var(--muted);margin-bottom:12px;text-transform:uppercase;letter-spacing:.5px;">Danger Zone</div>
                <form method="POST"
                      id="delete-form"
                      action="{{ route('admin.registrations.destroy', $registration) }}">
                    @csrf @method('DELETE')
                    <button type="button" id="delete-btn" class="btn-delete">
                        🗑️ Delete Registration
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('delete-btn').addEventListener('click', function () {
            Swal.fire({
                title: 'Delete Registration?',
                html: `<b>{{ addslashes($registration->full_name) }}</b>'s registration will be<br>permanently deleted. This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });
    </script>
@endpush
