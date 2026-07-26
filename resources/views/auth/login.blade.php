<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Pisfil EMSAC</title>

    <!-- Tipografías -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Iconos y Gráficos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- Tema Oscuro (Industrial / Blueprint) --- */
        [data-theme="dark"] {
            --bg: #11151a;
            --surface: #1a2028;
            --surface-2: #212a33;
            --line: #2b3540;
            --text: #e9eff3;
            --muted: #8d99a6;
            --primary: #3fa7da;
            --secondary: #e2722e;
            --accent: #c9a227;
            --success: #4fae7a;
            --danger: #d9534f;
            --glass-bg: rgba(26, 32, 40, 0.85);
            --blueprint-grid: linear-gradient(rgba(63, 167, 218, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(63, 167, 218, 0.05) 1px, transparent 1px);
            --shadow-md: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        /* Variables Comunes */
        :root {
            --font-display: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --font-mono: 'IBM Plex Mono', monospace;
            --radius-lg: 16px;
            --radius-md: 10px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background-image: var(--blueprint-grid);
            background-size: 40px 40px;
        }

        /* ---------- Paneles (Estilo Blueprint) ---------- */
        .panel { 
            position: relative; 
            background: var(--surface); 
            border: 1px solid var(--line); 
            border-radius: var(--radius-lg); 
            padding: 40px; 
            box-shadow: var(--shadow-md); 
            transition: var(--transition); 
            width: 100%;
            max-width: 400px;
        }
        .panel::before, .panel::after { content: ''; position: absolute; width: 24px; height: 24px; pointer-events: none; transition: var(--transition); }
        .panel::before { top: -1px; left: -1px; border-top: 2px solid var(--primary); border-left: 2px solid var(--primary); border-radius: 16px 0 0 0; opacity: 0.8; }
        .panel::after { bottom: -1px; right: -1px; border-bottom: 2px solid var(--primary); border-right: 2px solid var(--primary); border-radius: 0 0 16px 0; opacity: 0.8; }
        
        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 40px;
            text-align: center;
        }
        .brand-logo {
            width: 120px;
            height: 120px;
            border-radius: 24px;
            object-fit: cover;
        }
        .brand-text strong { display: block; font-family: var(--font-display); font-size: 22px; letter-spacing: 0.5px; }
        .brand-text span { display: block; font-family: var(--font-mono); font-size: 13px; color: var(--muted); margin-top: 3px; }

        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            background-color: var(--surface-2);
            border: 1px solid var(--line);
            color: var(--text);
            padding: 14px 16px;
            border-radius: var(--radius-md);
            font-family: var(--font-body);
            font-size: 14px;
            outline: none;
            transition: var(--transition);
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(63, 167, 218, 0.2);
        }

        .btn-primary {
            width: 100%;
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 14px;
            border-radius: var(--radius-md);
            font-family: var(--font-display);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .btn-primary:hover {
            background-color: rgba(63, 167, 218, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(63, 167, 218, 0.2);
        }

        .error-msg {
            color: var(--danger);
            font-size: 12px;
            margin-top: 6px;
            font-family: var(--font-body);
        }
        
        .status-msg {
            background-color: rgba(79, 174, 122, 0.1);
            color: var(--success);
            border: 1px solid rgba(79, 174, 122, 0.2);
            padding: 12px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <div class="panel">
        <div class="brand">
            <img src="{{ asset('images/Logo_sistema.png') }}" class="brand-logo" alt="Logo PISFIL EMSAC">
            <div class="brand-text">
                <strong>PISFIL SIG</strong>
            </div>
        </div>

        @if (session('status'))
            <div class="status-msg">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                @error('email')
                    <p class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
                @error('password')
                    <p class="error-msg"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                @enderror
            </div>

            <div style="margin-top: 35px;">
                <button type="submit" class="btn-primary">
                    Iniciar Sesión <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

</body>
</html>
