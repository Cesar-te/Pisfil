<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Pisfil EMSAC</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-800 font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/20 bg-white/95 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr]">
                <div class="flex flex-col justify-center bg-gradient-to-br from-indigo-50 via-white to-slate-50 p-8 sm:p-10 lg:p-12">
                    <div class="mb-8">
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-600">Pisfil EMSAC</p>
                        <h1 class="mt-3 text-3xl font-bold text-slate-900 sm:text-4xl">Gestiona tu operación con estilo</h1>
                        <p class="mt-3 max-w-md text-sm leading-6 text-slate-600 sm:text-base">
                            Accede a tu panel de control y mantén tus procesos organizados, ágiles y seguros.
                        </p>
                    </div>

                    <div class="flex items-center justify-center rounded-[1.75rem] border border-dashed border-indigo-200 bg-gradient-to-br from-indigo-100/80 to-white p-8 shadow-inner">
                        <div class="text-center">
                            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-indigo-600/10 text-3xl font-semibold text-indigo-700 shadow-sm">
                                Logo
                            </div>
                            <p class="mt-4 text-sm font-medium text-slate-500">Espacio para el logo de la empresa</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center bg-white p-8 sm:p-10 lg:p-12">
                    <div class="w-full max-w-md">
                        <div class="mb-8 text-center lg:text-left">
                            <h2 class="text-2xl font-bold text-slate-900">Iniciar sesión</h2>
                            <p class="mt-2 text-sm text-slate-500">Ingresa tus credenciales para continuar</p>
                        </div>

                        @if (session('status'))
                            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
                                <input id="email" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Contraseña</label>
                                <input id="password" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200" type="password" name="password" required autocomplete="current-password" />
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end pt-2">
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-slate-800 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition duration-200 hover:translate-y-[-1px] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Iniciar Sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
