<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Baloo+2:wght@700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --saffron:  #FF6B00;
            --deep:     #1A1040;
            --gold:     #F5C242;
            --cream:    #FFF8EE;
            --white:    #FFFFFF;
            --muted:    #7B6E8A;
            --border:   #E8DFF5;
            --success:  #22C55E;
            --error:    #EF4444;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--cream);
            min-height: 100vh;
            color: var(--deep);
        }

        header {
            background: linear-gradient(135deg, var(--deep) 0%, #2D1B6E 100%);
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 4px 20px rgba(26,16,64,0.3);
        }
        .header-icon {
            width: 46px; height: 46px;
            background: var(--saffron);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        header h1 {
            font-family: 'Baloo 2', cursive;
            color: var(--white);
            font-size: 22px;
            line-height: 1.1;
        }
        header h1 span { color: var(--gold); }

        main { max-width: 860px; margin: 0 auto; padding: 36px 20px 60px; }


        .alert-error {
            background: #FEF2F2; border: 1px solid #FECACA;
            color: #DC2626; border-radius: 10px;
            padding: 12px 16px; margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<header>
    <div class="header-icon">🗺️</div>
</header>

<main>
    @if($errors->any())
    <div class="alert-error">
        <ul style="list-style:none;">
            @foreach($errors->all() as $e)
                <li>⚠ {{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @yield('content')
</main>

</body>
</html>
