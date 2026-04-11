<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - CLT Manager</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: { extend: { fontFamily: { sans: ['Figtree', 'sans-serif'], serif: ['Merriweather', 'serif'] }, }, }
            }
        </script>
    @endif
    <style>
        .animate-fade-in { animation: fade-in 0.5s ease-out; }
        @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        body { background-color: #f8fafc; }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md animate-fade-in">
        <div class="flex justify-center items-center gap-3 text-[#367b59]">
            <svg class="h-10 w-10" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/>
                <path d="M12 10.5L9 17h6l-3-6.5z"/>
            </svg>
            <span class="text-3xl font-bold font-serif text-gray-900" style="font-family: 'Merriweather', serif;">CLT Manager</span>
        </div>
        <h2 class="mt-8 text-center text-2xl font-bold font-serif text-gray-900" style="font-family: 'Merriweather', serif;">
            Create an account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-[#367b59] hover:text-[#2c6448] transition">
                Log in instead
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md animate-fade-in" style="animation-delay: 0.1s; animation-fill-mode: both;">
        <div class="bg-white py-8 px-4 shadow-sm border border-gray-100 sm:rounded-xl sm:px-10">
            
            <div id="error-message" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-md" role="alert">
                <ul class="list-disc pl-5 text-sm" id="error-list"></ul>
            </div>

            <form class="space-y-5" action="#" method="POST" id="register-form">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Full Name
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" required class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] sm:text-sm transition">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] sm:text-sm transition">
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Role
                    </label>
                    <div class="mt-1">
                        <select id="role" name="role" required class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] sm:text-sm transition bg-white">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="supplier">Supplier</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] sm:text-sm transition">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] sm:text-sm transition">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submit-btn" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#367b59] hover:bg-[#2c6448] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#367b59] transition">
                        Register
                    </button>
                </div>
                
                <div class="text-center mt-6">
                    <a href="{{ url('/') }}" class="text-sm font-medium text-gray-400 hover:text-gray-600 transition">
                        &larr; Back to Home
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('register-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const role = document.getElementById('role').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            
            const errorDiv = document.getElementById('error-message');
            const errorList = document.getElementById('error-list');
            const btn = document.getElementById('submit-btn');

            errorDiv.classList.add('hidden');
            errorList.innerHTML = '';
            
            btn.disabled = true;
            btn.innerHTML = 'Registering...';

            try {
                const response = await fetch('/api/v1/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name, email, role, password, password_confirmation })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    window.location.href = '/dashboard';
                } else {
                    errorDiv.classList.remove('hidden');
                    
                    if (data.errors) {
                        for (const [key, messages] of Object.entries(data.errors)) {
                            messages.forEach(msg => {
                                const li = document.createElement('li');
                                li.textContent = msg;
                                errorList.appendChild(li);
                            });
                        }
                    } else {
                        const li = document.createElement('li');
                        li.textContent = data.message || 'Registration failed.';
                        errorList.appendChild(li);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                errorDiv.classList.remove('hidden');
                
                const li = document.createElement('li');
                li.textContent = 'An unexpected error occurred. Please try again.';
                errorList.appendChild(li);
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Register';
            }
        });
    </script>
</body>
</html>
