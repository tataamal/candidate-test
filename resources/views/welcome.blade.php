@extends('layouts.guest')

@section('content')
    <div class="h-screen bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img
            id="background"
            class="absolute h-full w-full object-cover"
            src="https://app.clttoolbox.com.au/images/login-bg.jpg"
            alt="CLT Toolbox background"
        />
        <div class="absolute inset-0 bg-black/40 animate-blur-in"></div>

        <div class="relative h-full flex flex-col">
            <header class="py-4 px-10">
                @if (Route::has('login'))
                    <nav class="flex justify-end animate-fade-in">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="rounded-md px-3 py-2 ring-1 ring-transparent transition hover:text-gray-100 focus:outline-none focus-visible:ring-[#FF2D20] text-gray-200"
                            >
                                Dashboard
                            </a>
                        @endauth
                    </nav>
                @endif
            </header>

            <div class="flex-1 flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <div class="grid grid-cols-2 items-center gap-4 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            <img
                                src="https://app.clttoolbox.com.au/images/logos/logo_color_white.png"
                                alt="CLT Toolbox"
                                class="animate-fade-in"
                            />
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <div class="flex flex-col items-center gap-4 mt-6 animate-fade-in z-10 w-full max-w-sm mx-auto">
                            @guest
                                <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                                    <a
                                        href="{{ route('login') }}"
                                        class="text-center text-lg font-semibold rounded-md px-8 py-3 bg-[#FF2D20] text-white transition hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF2D20] focus-visible:ring-offset-2 shadow-lg w-full sm:w-auto"
                                    >
                                        Log in
                                    </a>
                                    @if (Route::has('register'))
                                        <a
                                            href="{{ route('register') }}"
                                            class="text-center text-lg font-semibold rounded-md px-8 py-3 bg-white text-gray-900 transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 shadow-lg w-full sm:w-auto"
                                        >
                                            Register
                                        </a>
                                    @endif
                                </div>
                            @endguest
                        </div>
                    @endif

                    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    </footer>
                </div>
            </div>
        </div>
    </div>
@endsection
