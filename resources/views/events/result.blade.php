@extends('layouts.app')

@section('content')
    <style>
        .result-card {
            background: var(--white);
            border-radius: 20px;
            padding: 24px 20px;
            box-shadow: 0 8px 40px rgba(26,16,64,0.08);
            border: 1px solid var(--border);
            text-align: center;
        }
        .success-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #F0FDF4; border: 1.5px solid #BBF7D0;
            color: #15803D; border-radius: 999px;
            padding: 4px 14px; font-size: 12px; font-weight: 600;
            margin-bottom: 10px;
        }
        .result-title {
            font-family: 'Baloo 2', cursive;
            font-size: 20px; font-weight: 800;
            color: var(--deep); margin-bottom: 2px;
        }
        .result-sub { font-size: 13px; color: var(--muted); margin-bottom: 16px; }

        .banner-wrap {
            border-radius: 12px; overflow: hidden;
            border: 2px solid var(--border);
            margin: 0 auto 16px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.10);
            max-width: 350px; 
        }
        .banner-wrap img { 
            width: 100%; 
            height: auto; 
            display: block; 
        }

        .info-row {
            display: flex; gap: 8px; justify-content: center;
            flex-wrap: wrap; margin-bottom: 16px;
        }
        .info-chip {
            background: var(--cream); border: 1px solid var(--border);
            border-radius: 8px; padding: 6px 14px;
            font-size: 13px; color: var(--deep); font-weight: 600;
        }
        .info-chip span { font-size: 11px; color: var(--muted); font-weight: 400; display: block; }

        .action-row { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn-download {
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--saffron), #FF9A3C);
            color: #fff; border: none; border-radius: 12px;
            font-size: 15px; font-weight: 700;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(255,107,0,0.3);
            transition: all .2s;
        }
        .btn-download:hover { transform: translateY(-2px); color: #fff; }
        .btn-new {
            padding: 12px 20px;
            background: var(--cream); color: var(--deep);
            border: 1.5px solid var(--border);
            border-radius: 12px; font-size: 14px;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            text-decoration: none; font-weight: 600;
        }
    </style>

    <div class="result-card">
        <div class="success-badge">✅ Registration Successful</div>
        <div class="result-title">Your Banner is Ready! 🎉</div>
        <div class="result-sub">{{ $reg->full_name }} – {{ $reg->city }} Event</div>

        <div class="banner-wrap">
            @if($reg->banner_url)
                <img src="{{ $reg->banner_url }}" alt="Event Banner">
            @elseif($reg->photo_url)
                <img src="{{ $reg->photo_url }}" alt="Photo">
            @endif
        </div>

        <div class="info-row">
            <div class="info-chip">
                <span>Name</span>{{ $reg->full_name }}
            </div>
            <div class="info-chip">
                <span>City</span>{{ $reg->city }}
            </div>
        </div>

        <div class="action-row">
            @if($reg->generated_banner)
                <a href="{{ Storage::disk('s3')->temporaryUrl(
                    $reg->generated_banner,
                    now()->addMinutes(10),
                    ['ResponseContentDisposition' => 'attachment; filename="event-banner-' . \Illuminate\Support\Str::slug($reg->full_name) . '.jpg"']
                ) }}"
                   class="btn-download">⬇ Download Banner</a>
            @endif
            <a href="{{ route('event.index') }}" class="btn-new">＋ New Registration</a>
        </div>
    </div>

@endsection
