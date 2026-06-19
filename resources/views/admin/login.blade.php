<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Event Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --saffron: #FF6B00; --saffron2: #FF9A3C;
            --deep: #1A1040;    --cream: #FFF8F0;
            --border: #E8E0F0;  --muted: #7C6F8E;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1A1040 0%, #2D1B69 50%, #1a1040 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
            position: relative; overflow: hidden;
        }

        /* Decorative orbs */
        body::before, body::after {
            content: ''; position: absolute; border-radius: 50%;
            filter: blur(80px); opacity: .35; pointer-events: none;
        }
        body::before {
            width: 480px; height: 480px;
            background: var(--saffron);
            top: -120px; right: -80px;
        }
        body::after {
            width: 360px; height: 360px;
            background: #7C3AED;
            bottom: -80px; left: -80px;
        }

        .login-card {
            background: rgba(255,255,255,.97);
            border-radius: 24px;
            padding: 44px 40px;
            width: 100%; max-width: 420px;
            box-shadow: 0 32px 80px rgba(0,0,0,.4);
            position: relative; z-index: 1;
        }

        .login-logo {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 48px; height: 48px; border-radius: 14px;
            background: linear-gradient(135deg, var(--saffron), var(--saffron2));
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            box-shadow: 0 6px 20px rgba(255,107,0,.35);
        }
        .login-heading {
            font-family: 'Baloo 2', cursive;
            font-size: 22px; font-weight: 800; color: var(--deep);
        }
        .login-sub { font-size: 12px; color: var(--muted); }

        .divider-text {
            font-size: 11px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: var(--muted);
            margin-bottom: 24px;
            display: flex; align-items: center; gap: 10px;
        }
        .divider-text::before, .divider-text::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: var(--deep); margin-bottom: 6px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); font-size: 16px;
        }
        .form-group input {
            width: 100%; padding: 12px 14px 12px 42px;
            border: 1.5px solid var(--border);
            border-radius: 10px; font-size: 14px;
            font-family: 'Poppins', sans-serif;
            background: var(--cream); outline: none;
            color: var(--deep);
            transition: border .2s, box-shadow .2s;
        }
        .form-group input:focus {
            border-color: var(--saffron); background: #fff;
            box-shadow: 0 0 0 3px rgba(255,107,0,.12);
        }
        .form-group input.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.1); }
        .invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 5px; }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 22px;
        }
        .remember-row input[type=checkbox] { accent-color: var(--saffron); width: 15px; height: 15px; }
        .remember-row label { font-size: 13px; color: var(--muted); cursor: pointer; }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--saffron), var(--saffron2));
            color: #fff; border: none;
            border-radius: 12px; font-size: 15px; font-weight: 700;
            cursor: pointer; font-family: 'Poppins', sans-serif;
            box-shadow: 0 6px 20px rgba(255,107,0,.38);
            transition: all .25s; letter-spacing: .3px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(255,107,0,.48); }
        .btn-login:active { transform: translateY(0); }

        .login-footer {
            margin-top: 24px; text-align: center;
            font-size: 12px; color: var(--muted);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <div class="logo-icon">🎟️</div>
            <div>
                <div class="login-heading">EventAdmin</div>
                <div class="login-sub">Management Panel</div>
            </div>
        </div>

        <div class="divider-text">Sign in to continue</div>

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrap">
                    <span class="input-icon">✉️</span>
                    <input
                        type="email" id="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@yourevent.com"
                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        required autofocus
                    >
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password" id="password" name="password"
                        placeholder="Enter your password"
                        class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        required
                    >
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Keep me signed in</label>
            </div>

            <button type="submit" class="btn-login">🔑 Sign In to Dashboard</button>
        </form>

        <div class="login-footer">
            Secure Admin Access · Event Registration Manager
        </div>
    </div>
</body>
</html>
