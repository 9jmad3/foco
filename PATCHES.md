# FOCO Pack v2 (MVP Medio) - Cómo integrarlo en un Laravel 13 recién creado

## Requisitos
- Laravel 13
- Jetstream + Livewire instalado
- Tailwind (incluido con Jetstream)
- Auth activo (login/register)
- DB configurada

## Pasos
1) Copia las carpetas del ZIP sobre tu proyecto (manteniendo rutas):
   - app/
   - database/
   - resources/
   - routes/
   - public/

2) Ejecuta migraciones:
   php artisan migrate

3) Asegura que tu layout incluye el manifest y registra el service worker.
   En `resources/views/layouts/app.blade.php` añade dentro de `<head>`:

   <link rel="manifest" href="/manifest.webmanifest">
   <meta name="theme-color" content="#234F3F">

   Y antes de cerrar `</body>` añade:

   <script>
     if ('serviceWorker' in navigator) {
       window.addEventListener('load', () => {
         navigator.serviceWorker.register('/sw.js').catch(() => {});
       });
     }
   </script>

4) Compila frontend (para generar /build):
   npm install
   npm run build

5) Rutas:
   /hoy        (principal, auto-rellena desde plantilla default)
   /plantillas (crear/editar/reordenar y marcar default)
   /ajustes    (límite diario + modo estricto + plantilla default)
   /resumen    (resumen semanal)

## Onboarding recomendado (1 minuto)
1) Entra en /plantillas
2) Crea una plantilla (ej: “Día normal”)
3) Añade 3–5 bloques
4) Pulsa “Marcar default”
5) Vuelve a /hoy → se auto-rellena

## Nota sobre iconos PWA
Coloca icon-192.png e icon-512.png en public/icons (PNG, cuadrados).


## Onboarding automático (nuevo en v3)
- Este pack incluye un UserObserver que, al registrarse un usuario, crea automáticamente:
  - tipos base (Trabajo/Gym/Baile/Proyecto/Personal)
  - plantilla "Día normal" (default) con 3 bloques
  - user_settings con límite 3 (estricto)

Opcional: si ya tienes usuarios creados y quieres aplicar el onboarding:
  php artisan foco:onboard-existing
