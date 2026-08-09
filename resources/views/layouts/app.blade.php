<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PISFIL SIG v1.0')</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('pisfil-theme');
            const prefersLight = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
            const initialTheme = savedTheme || (prefersLight ? 'light' : 'dark');
            document.documentElement.setAttribute('data-theme', initialTheme);
        })();
    </script>

    <!-- Tipografías -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Iconos y Gráficos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts Adicionales si los hay -->
    @stack('scripts_head')
    
    <!-- TailwindCSS / Vite Original (por si hay estilos o componentes que lo sigan usando) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        /* --- Tema Claro (Moderno / Corporativo) --- */
        [data-theme="light"] {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-2: #f1f5f9;
            --line: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --primary: #2563eb;
            --secondary: #ea580c;
            --accent: #ca8a04;
            --success: #16a34a;
            --danger: #dc2626;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --blueprint-grid: linear-gradient(rgba(37, 99, 235, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(37, 99, 235, 0.03) 1px, transparent 1px);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
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

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface-2); border-radius: 4px; border: 1px solid var(--line); }
        ::-webkit-scrollbar-thumb:hover { background: var(--muted); }

        body {
            font-family: var(--font-body);
            background-color: var(--bg) !important;
            color: var(--text) !important;
            display: flex;
            height: 100vh;
            overflow: hidden;
            transition: var(--transition);
            background-image: var(--blueprint-grid);
            background-size: 40px 40px;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: 280px;
            background: var(--surface);
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            z-index: 30;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .brand {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid var(--line);
            text-decoration: none;
            color: var(--text);
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            object-fit: cover;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 16px;
            color: #fff;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .brand-text strong { display: block; font-family: var(--font-display); font-size: 18px; letter-spacing: 0.5px; }
        .brand-text span { display: block; font-family: var(--font-mono); font-size: 11px; color: var(--muted); margin-top: 3px; }

        .nav { flex: 1; overflow-y: auto; padding-top: 10px; padding-bottom: 20px; }
        .nav-label { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.1em; color: var(--muted); text-transform: uppercase; margin: 24px 24px 10px; }
        .nav ul { list-style: none; padding: 0 16px; }
        .nav li { margin-bottom: 4px; }
        .nav li a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: var(--radius-md); font-size: 14px; color: var(--text); text-decoration: none; font-weight: 500; transition: var(--transition); border-left: 3px solid transparent; }
        .nav li a i { font-size: 16px; width: 24px; text-align: center; color: var(--muted); transition: var(--transition); }
        .nav li a:hover { background: var(--surface-2); transform: translateX(4px); }
        .nav li a.active { background: var(--surface-2); color: var(--primary); border-left-color: var(--primary); }
        .nav li a.active i { color: var(--primary); }
        .submenu { display: none; margin-left: 20px; border-left: 1px solid var(--line); padding-left: 10px; margin-top: 5px; }
        .submenu.show { display: block; }
        .nav-toggle-icon { margin-left: auto !important; width: auto !important; transition: transform 0.3s ease; font-size: 12px !important; }
        .nav-toggle-icon.rotated { transform: rotate(90deg); }

        /* ---------- Main Content ---------- */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; scroll-behavior: smooth; position: relative; }

        /* Topbar */
        .topbar {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--line);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 20;
            transition: var(--transition);
        }
        .topbar-left .eyebrow { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.1em; color: var(--primary); text-transform: uppercase; margin-bottom: 4px; }
        .topbar-left h1 { font-family: var(--font-display); font-size: 24px; font-weight: 700; margin: 0; }
        
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        
        .icon-btn { position: relative; background: var(--surface-2); border: 1px solid var(--line); border-radius: var(--radius-md); width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; color: var(--text); transition: var(--transition); }
        .icon-btn:hover { border-color: var(--primary); color: var(--primary); }
        .badge { position: absolute; top: -6px; right: -6px; background: var(--secondary); color: #fff; font-family: var(--font-mono); font-size: 10px; font-weight: 700; border-radius: 12px; min-width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; padding: 0 4px; border: 2px solid var(--surface); animation: pulse 2s infinite; }
        
        .user-chip { display: flex; align-items: center; gap: 12px; padding-left: 20px; border-left: 1px solid var(--line); position: relative; }
        .avatar { width: 42px; height: 42px; border-radius: 12px; background: var(--surface-2); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; font-family: var(--font-mono); font-size: 14px; font-weight: 600; color: var(--primary); text-transform: uppercase; }
        .user-chip div strong { display: block; font-size: 14px; font-weight: 600; color: var(--text); }
        .user-chip div span { display: block; font-size: 11px; color: var(--muted); font-family: var(--font-mono); margin-top: 2px; text-transform: uppercase; }

        .role-context { margin: 24px 32px 0; padding: 14px 20px; background: rgba(63, 167, 218, 0.08); border: 1px solid rgba(63, 167, 218, 0.25); border-radius: var(--radius-md); font-size: 13px; color: var(--text); display: flex; gap: 12px; align-items: center; }
        [data-theme="light"] .role-context { background: rgba(37, 99, 235, 0.05); border-color: rgba(37, 99, 235, 0.2); }

        .content { padding: 32px; display: flex; flex-direction: column; gap: 40px; }

        .dropdown-menu { display: none; position: absolute; right: 0; top: 100%; margin-top: 10px; background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-md); box-shadow: var(--shadow-md); z-index: 50; overflow: hidden; min-width: 150px; }
        .dropdown-menu.show { display: block; }
        .dropdown-menu button { display: block; width: 100%; text-align: left; padding: 12px 16px; background: transparent; border: none; color: var(--text); font-family: var(--font-body); font-size: 14px; cursor: pointer; transition: background 0.2s; }
        .dropdown-menu button:hover { background: var(--surface-2); color: var(--danger); }

        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .stagger-1 { animation: slideUp 0.5s ease forwards; opacity: 0; }
        .stagger-2 { animation: slideUp 0.5s ease 0.1s forwards; opacity: 0; }
        .stagger-3 { animation: slideUp 0.5s ease 0.2s forwards; opacity: 0; }
        .stagger-4 { animation: slideUp 0.5s ease 0.3s forwards; opacity: 0; }

        /* ---------- Paneles (Estilo Blueprint) ---------- */
        .panel { position: relative; background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 32px 24px 24px; box-shadow: var(--shadow-md); transition: var(--transition); }
        .panel::before, .panel::after { content: ''; position: absolute; width: 16px; height: 16px; pointer-events: none; transition: var(--transition); }
        .panel::before { top: -1px; left: -1px; border-top: 2px solid var(--primary); border-left: 2px solid var(--primary); border-radius: 16px 0 0 0; opacity: 0.6; }
        .panel::after { bottom: -1px; right: -1px; border-bottom: 2px solid var(--primary); border-right: 2px solid var(--primary); border-radius: 0 0 16px 0; opacity: 0.6; }
        .panel:hover::before, .panel:hover::after { opacity: 1; width: 24px; height: 24px; }
        
        .panel-tag { position: absolute; top: -12px; left: 24px; background: var(--surface-2); border: 1px solid var(--primary); border-radius: 6px; padding: 4px 12px; font-family: var(--font-mono); font-size: 11px; font-weight: 600; letter-spacing: 0.08em; color: var(--primary); text-transform: uppercase; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }

        /* --- KPIs --- */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .kpi-card { background: var(--surface); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: 24px; display: flex; flex-direction: column; position: relative; overflow: hidden; transition: var(--transition); box-shadow: var(--shadow-md); }
        .kpi-card:hover { transform: translateY(-5px); border-color: var(--primary); }
        .kpi-label { font-family: var(--font-mono); font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
        .kpi-value { font-family: var(--font-display); font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .kpi-delta { font-size: 12.5px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .kpi-delta.up { color: var(--success); }
        .kpi-delta.warn { color: var(--secondary); }
        .sparkline-box { height: 50px; margin-top: 10px; margin-bottom: -15px; }

        /* --- Gráficos (Grid) --- */
        .charts-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; }
        @media (max-width: 1100px) { .charts-grid { grid-template-columns: 1fr; } }
        .chart-panel h2 { font-family: var(--font-display); font-size: 18px; margin-bottom: 20px; }

        /* --- Tablas --- */
        .table-panel .panel-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px; }
        .table-panel h2 { font-family: var(--font-display); font-size: 18px; }
        .hint { font-family: var(--font-mono); font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; background: var(--surface-2); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--line); }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th { text-align: left; font-family: var(--font-mono); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); padding: 16px; border-bottom: 1px solid var(--line); background: rgba(0, 0, 0, 0.1); }
        td { padding: 16px; border-bottom: 1px solid var(--line); font-size: 13.5px; transition: var(--transition); }
        tr:hover td { background: var(--surface-2); }
        .mono { font-family: var(--font-mono); font-size: 13px; font-weight: 500; color: var(--primary); }
        
        .pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-family: var(--font-mono); font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; border: 1px solid transparent; }
        .pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .pill.ok { background: rgba(79, 174, 122, 0.1); color: var(--success); border-color: rgba(79, 174, 122, 0.2); }
        .pill.ok::before { background: var(--success); }
        .pill.warn { background: rgba(226, 114, 46, 0.1); color: var(--secondary); border-color: rgba(226, 114, 46, 0.2); }
        .pill.warn::before { background: var(--secondary); }
        .pill.danger { background: rgba(217, 83, 79, 0.1); color: var(--danger); border-color: rgba(217, 83, 79, 0.2); }
        .pill.danger::before { background: var(--danger); }
        .pill.pending { background: rgba(201, 162, 39, 0.1); color: var(--accent); border-color: rgba(201, 162, 39, 0.2); }
        .pill.pending::before { background: var(--accent); }

        /* --- Contabilidad Ledger --- */
        .ledger-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 20px; }
        .ledger-card { background: var(--surface-2); border: 1px solid var(--line); border-radius: var(--radius-md); padding: 20px; display: flex; flex-direction: column; gap: 8px; cursor: pointer; transition: var(--transition); }
        .ledger-card:hover { border-color: var(--primary); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
        .ledger-card strong { font-family: var(--font-display); font-size: 16px; color: var(--primary); }
        .ledger-card span { font-size: 13px; color: var(--muted); }

        /* Ajuste para los elementos Tailwind que esten dentro del contenido principal para que no hereden fondo blanco */
        .content .bg-white { background-color: var(--surface) !important; color: var(--text) !important; border-color: var(--line) !important; }
        .content .text-gray-800 { color: var(--text) !important; }
        .content .text-gray-600 { color: var(--muted) !important; }
        .content .border-gray-200 { border-color: var(--line) !important; }
        .content table thead { background-color: var(--surface-2) !important; color: var(--muted) !important; }
        .content table th, .content table td { border-color: var(--line) !important; }
        .content tr:hover { background-color: var(--surface-2) !important; }
        .content input, .content select, .content textarea { background-color: var(--surface-2) !important; color: var(--text) !important; border-color: var(--line) !important; }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <img src="{{ asset('images/Logo_sistema.png') }}" class="brand-logo" alt="Logo PISFIL EMSAC">
            <div class="brand-text">
                <strong>PISFIL SIG</strong>
                <span>v1.0 · EMSAC</span>
            </div>
        </a>

        @auth
        <nav class="nav">
            <ul>
                @foreach($global_menus as $menu)
                    @php
                        $canViewMenu = !$menu->permiso_id || (auth()->user() && $menu->permiso && auth()->user()->hasPermission($menu->permiso->codigo));
                        $visibleSubmenus = $menu->submenus->filter(function ($submenu) {
                            return !$submenu->permiso_id || (auth()->user() && $submenu->permiso && auth()->user()->hasPermission($submenu->permiso->codigo));
                        });
                        $hasSubmenus = $visibleSubmenus->count() > 0;
                        $isMenuExpanded = false;
                        if ($hasSubmenus) {
                            foreach($visibleSubmenus as $sub) {
                                if ($sub->url && request()->is(ltrim($sub->url, '/').'*')) {
                                    $isMenuExpanded = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    @if($canViewMenu || $hasSubmenus)
                        <li>
                            <a href="{{ $hasSubmenus ? '#' : ($menu->url ? url($menu->url) : '#') }}" class="{{ (!$hasSubmenus && $menu->url && request()->is(ltrim($menu->url, '/').'*')) ? 'active' : '' }} {{ $hasSubmenus ? 'toggle-submenu' : '' }}">
                                <i class="{{ $menu->icono }}"></i> {{ $menu->nombre }}
                                @if($hasSubmenus)
                                    <i class="fas fa-chevron-right nav-toggle-icon {{ $isMenuExpanded ? 'rotated' : '' }}"></i>
                                @endif
                            </a>
                            @if($hasSubmenus)
                                <ul class="submenu {{ $isMenuExpanded ? 'show' : '' }}">
                                    @foreach($visibleSubmenus as $submenu)
                                        <li>
                                            <a href="{{ $submenu->url ? url($submenu->url) : '#' }}" class="{{ $submenu->url && request()->is(ltrim($submenu->url, '/').'*') ? 'active' : '' }}" style="padding: 8px 12px; font-size: 13px;">
                                                <i class="{{ $submenu->icono ?? 'fas fa-angle-right' }}"></i> {{ $submenu->nombre }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
        @endauth
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <p class="eyebrow"><i class="fas fa-terminal"></i> Terminal Activa</p>
                <h1>@yield('header_title', 'Panel de Control')</h1>
            </div>

            <div class="topbar-right">
                <!-- Theme Toggle -->
                <button class="icon-btn" id="themeToggle" title="Cambiar Tema">
                    <i class="fas fa-sun"></i>
                </button>

                <!-- Notifications -->
                <button class="icon-btn" title="Alertas">
                    <i class="far fa-bell"></i>
                    <span class="badge">0</span>
                </button>

                <!-- Perfil -->
                @auth
                <div class="user-chip cursor-pointer" id="userMenuToggle">
                    <div class="avatar">{{ substr(Auth::user()->name, 0, 2) }}</div>
                    <div>
                        <strong>{{ Auth::user()->name }}</strong>
                        <span>{{ Auth::user()->rol->nombre ?? 'Usuario' }}</span>
                    </div>
                    
                    <!-- Dropdown Menú -->
                    <div class="dropdown-menu" id="userDropdown">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"><i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión</button>
                        </form>
                    </div>
                </div>
                @else
                <div class="user-chip">
                    <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none; font-weight: bold;">Iniciar Sesión</a>
                </div>
                @endauth
            </div>
        </header>

        <!-- Role Context Banner -->
        @auth
        <div class="role-context stagger-1">
            <i class="fas fa-info-circle" style="color: var(--primary); font-size: 16px;"></i>
            <span>{{ Auth::user()->rol->descripcion ?? 'Acceso al sistema.' }}</span>
        </div>
        @endauth

        <div class="content stagger-2">
            @yield('content')
        </div>
    </main>

    <script>
        // --- Lógica del Modo Oscuro/Claro (Theme Toggle) ---
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        
        if (themeToggle) {
            const themeIcon = themeToggle.querySelector('i');
            const setThemeIcon = () => {
                themeIcon.className = html.getAttribute('data-theme') === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            };
            setThemeIcon();

            themeToggle.addEventListener('click', () => {
                const isDark = html.getAttribute('data-theme') === 'dark';
                const nextTheme = isDark ? 'light' : 'dark';
                html.setAttribute('data-theme', nextTheme);
                localStorage.setItem('pisfil-theme', nextTheme);
                setThemeIcon();
                
                // Disparar evento para que los gráficos se actualicen si existen
                document.dispatchEvent(new Event('themeChanged'));
            });
        }

        // --- Lógica de Menú de Usuario ---
        const userMenuToggle = document.getElementById('userMenuToggle');
        const userDropdown = document.getElementById('userDropdown');
        
        if (userMenuToggle && userDropdown) {
            userMenuToggle.addEventListener('click', (e) => {
                userDropdown.classList.toggle('show');
                e.stopPropagation();
            });

            document.addEventListener('click', (e) => {
                if (!userMenuToggle.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        }

        // --- Lógica de Menú Acordeón ---
        const toggleSubmenus = document.querySelectorAll('.toggle-submenu');
        toggleSubmenus.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const submenu = toggle.nextElementSibling;
                const icon = toggle.querySelector('.nav-toggle-icon');
                
                if (submenu) {
                    submenu.classList.toggle('show');
                    if (icon) {
                        icon.classList.toggle('rotated');
                    }
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
