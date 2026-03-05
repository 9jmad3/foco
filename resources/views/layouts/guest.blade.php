<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="min-h-screen flex flex-col items-center bg-white sm:bg-gray-100">

            {{ $slot }}
        </div>

        @livewireScripts

    <!-- Mascota con bocadillo -->
    @if (request()->routeIs('login') || request()->routeIs('register'))

    <div id="demo-mascot" class="demo-mascot">

        <img src="{{ asset('images/bannerinformativo.png') }}" alt="Mascota aviso">

        <div class="demo-bubble">

            <button class="demo-close" onclick="document.getElementById('demo-mascot').style.display='none'">
                ✕
            </button>

            ⚠️ Esta es una versión de pruebas de la aplicación. No introduzcas datos sensibles ni contraseñas que utilices en otros servicios. Puedes usar un correo inventado ya que no se enviarán correos reales desde esta plataforma.

        </div>

    </div>
    @endif

   <style>
    .demo-mascot{
        position: fixed;
        bottom: 0;
        left: 20px;   /* antes 0 → lo separa un poco del borde */
        width: 140px;
        z-index: 50;
        animation: mascotRise 0.8s ease-out;
    }

    .demo-bubble{
        position:absolute;
        bottom:110px;
        left:110px;

        background:#FEF3C7;
        color:#92400E;

        padding:16px 22px;
        border-radius:12px;
        font-size:14px;

        width:220px;   /* en vez de max-width */
        line-height:1.5;

        box-shadow:0 10px 20px rgba(0,0,0,0.12);
        border:1px solid #FCD34D;
    }

    .demo-close{
    position:absolute;
    top:8px;
    right:10px;

    background:none;
    border:none;

    font-size:18px;
    cursor:pointer;
    color:#92400E;
}

.demo-close:hover{
    opacity:0.7;
}

    </style>

    </body>
</html>
