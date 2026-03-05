<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>

            <div class="mt-6 border-t pt-4">
                <div class="flex justify-center">
                    <p class="text-sm text-gray-600 text-center">
                        ¿No tienes cuenta?
                        <a href="{{ route('register') }}"
                        class="ml-1 font-medium text-emerald-600 hover:text-emerald-800 underline">
                            Regístrate
                        </a>
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="mx-4 text-xs text-gray-400 uppercase tracking-wider">
                    Información
                </span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('foco.how') }}"
                class="text-sm text-gray-500 hover:text-emerald-600 transition">
                    ¿Qué es FOCO y cómo funciona?
                </a>
            </div>

        </form>
    </x-authentication-card>
</x-guest-layout>
