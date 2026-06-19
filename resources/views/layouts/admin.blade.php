<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — Event Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --saffron:  #FF6B00;
            --saffron2: #FF9A3C;
            --deep:     #1A1040;
            --deep2:    #2D1B69;
            --cream:    #FFF8F0;
            --white:    #FFFFFF;
            --border:   #E8E0F0;
            --muted:    #7C6F8E;
            --success:  #22c55e;
            --danger:   #ef4444;
            --sidebar-w: 256px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #F4F0FC;
            color: var(--deep);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-w);
            background: var(--deep);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s;
        }
        .sidebar-logo {
            padding: 28px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-logo .logo-mark {
            display: flex; align-items: center; gap: 10px;
        }
        .logo-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--saffron), var(--saffron2));
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .logo-text { font-family: 'Baloo 2', cursive; font-size: 18px; font-weight: 800; color: #fff; }
        .logo-sub  { font-size: 11px; color: rgba(255,255,255,.4); margin-top: 2px; letter-spacing: .5px; }

        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .nav-section-label {
            font-size: 10px; font-weight: 700; letter-spacing: 1.2px;
            color: rgba(255,255,255,.3); text-transform: uppercase;
            padding: 12px 24px 6px;
        }
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 24px;
            color: rgba(255,255,255,.6);
            text-decoration: none;
            font-size: 14px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .2s;
        }
        .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
        .nav-link.active {
            background: rgba(255,107,0,.15);
            color: var(--saffron2);
            border-left-color: var(--saffron);
        }
        .nav-link .icon { font-size: 16px; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .admin-badge {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 12px;
        }
        .admin-avatar {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--saffron), var(--saffron2));
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 700; color: #fff;
        }
        .admin-name  { font-size: 13px; font-weight: 600; color: #fff; }
        .admin-role  { font-size: 11px; color: rgba(255,255,255,.4); }
        .btn-logout {
            width: 100%; padding: 9px;
            background: rgba(239,68,68,.15);
            color: #f87171;
            border: 1px solid rgba(239,68,68,.25);
            border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            transition: all .2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,.25); color: #fff; }

        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title {
            font-family: 'Baloo 2', cursive;
            font-size: 20px; font-weight: 700; color: var(--deep);
        }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-time  { font-size: 12px; color: var(--muted); }

        .page-content { padding: 28px 32px; flex: 1; }

        .flash {
            padding: 12px 18px; border-radius: 10px;
            margin-bottom: 20px; font-size: 13px; font-weight: 600;
            display: flex; align-items: center; gap: 8px;
        }
        .flash-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .flash-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        .hamburger {
            display: none; background: none; border: none;
            font-size: 22px; cursor: pointer; color: var(--deep);
        }
        @media(max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .hamburger { display: block; }
            .page-content { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
        }

        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 99;
        }
        .sidebar-overlay.show { display: block; }
    </style>
    @stack('styles')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">
            <div class="logo-icon">🎟️</div>
            <div>
                <div class="logo-text">EventAdmin</div>
                <div class="logo-sub">Management Panel</div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="icon">📊</span> Dashboard
        </a>

        <div class="nav-section-label">Registrations</div>
        <a href="{{ route('admin.registrations.index') }}" class="nav-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
            <span class="icon">📋</span> All Registrations
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="admin-badge">
            <div class="admin-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="admin-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="admin-role">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">⏻ Sign Out</button>
        </form>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="main">
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="hamburger" onclick="toggleSidebar()">☰</button>
            <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="topbar-right">
            <span class="topbar-time" id="live-time"></span>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div class="flash flash-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">❌ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    function tick() {
        const now = new Date();
        document.getElementById('live-time').textContent =
            now.toLocaleDateString('en-IN', { day:'numeric', month:'short', year:'numeric' }) + ' · ' +
            now.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
    }
    tick(); setInterval(tick, 1000);

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('show');
    }
</script>
@stack('scripts')
</body>
</html>
