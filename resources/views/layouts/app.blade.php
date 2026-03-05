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

        <link rel="manifest" href="/manifest.webmanifest">
        <meta name="theme-color" content="#234F3F">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <div class="demo-banner">
            ⚠️ Esta es una versión de pruebas de la aplicación. No introduzcas datos sensibles ni contraseñas que utilices en otros servicios.
            </div>
            <style>
                .demo-banner{
    position: sticky;
    top: 64px; /* altura aproximada del menú */

    background:#FEF3C7;
    color:#92400E;

    border-bottom:1px solid #FCD34D;

    padding:10px 20px;
    text-align:center;
    font-size:14px;

    z-index:40;
}
                </style>


            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        <script>
(async () => {
  try {
    // Desregistrar todos los Service Workers
    if ('serviceWorker' in navigator) {
      const regs = await navigator.serviceWorker.getRegistrations();
      for (const reg of regs) {
        await reg.unregister();
      }
    }

    // Borrar Cache Storage (PWA/Workbox/etc)
    if (window.caches) {
      const keys = await caches.keys();
      await Promise.all(keys.map(k => caches.delete(k)));
    }

    // Si quieres, fuerza recarga dura una vez
    if (!sessionStorage.getItem('__sw_purged')) {
      sessionStorage.setItem('__sw_purged', '1');
      location.reload(true);
    }
  } catch (e) {}
})();
</script>

        {{-- <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
        </script> --}}
    </body>
</html>
