<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Admin — Kopi Tiang Alam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>:root { --accent: hsl(35,90%,50%); --bg-dark: hsl(24,35%,18%); }</style>
</head>
<body style="background: linear-gradient(135deg, hsl(24,35%,12%) 0%, hsl(24,35%,22%) 100%); min-height: 100vh;">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex p-4 rounded-2xl mb-4" style="background: var(--accent);">
                <svg class="w-8 h-8" fill="none" stroke="hsl(24,10%,10%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold" style="color: hsl(40,33%,98%);">Kopi Tiang Alam</h1>
            <p class="text-sm mt-1" style="color: hsl(35,30%,60%);">Admin Panel</p>
        </div>

        <!-- Form -->
        <div class="rounded-2xl p-6" style="background: hsl(24,35%,22%); border: 1px solid hsl(24,30%,28%);">
            <h2 class="font-bold text-lg mb-5" style="color: hsl(40,33%,98%);">Masuk</h2>

            @if($errors->any())
                <div class="rounded-xl p-3 mb-4 text-sm" style="background: hsl(0,84%,15%); color: hsl(0,84%,80%);">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1.5" style="color: hsl(35,30%,65%);">Username</label>
                    <input name="username" type="text" value="{{ old('username') }}" placeholder="admin"
                           class="w-full border rounded-xl h-11 px-3 text-sm outline-none"
                           style="background: hsl(24,35%,15%); border-color: hsl(24,30%,30%); color: hsl(40,33%,92%);"
                           required autofocus />
                </div>
                <div>
                    <label class="block text-sm mb-1.5" style="color: hsl(35,30%,65%);">Password</label>
                    <input name="password" type="password" placeholder="••••••••"
                           class="w-full border rounded-xl h-11 px-3 text-sm outline-none"
                           style="background: hsl(24,35%,15%); border-color: hsl(24,30%,30%); color: hsl(40,33%,92%);"
                           required />
                </div>
                <button type="submit"
                        class="w-full h-11 rounded-xl font-semibold text-sm mt-2"
                        style="background: var(--accent); color: hsl(24,10%,10%);">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
