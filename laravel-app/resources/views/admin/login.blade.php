<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login Admin — Kopi Tiang Alam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>:root { --accent: #c9a84c; --bg-dark: #0a0a0a; }</style>
</head>
<body style="background: linear-gradient(135deg, #000000 0%, #0a0a0a 50%, #1a1a1a 100%); min-height: 100vh;">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="Kopi Tiang Alam" class="w-24 h-24 mx-auto rounded-full shadow-2xl mb-4" style="border: 3px solid #c9a84c;" />
            <h1 class="text-2xl font-bold" style="color: #c9a84c;">Kopi Tiang Alam</h1>
            <p class="text-sm mt-1" style="color: #8b7a3c;">Admin Panel</p>
        </div>

        <!-- Form -->
        <div class="rounded-2xl p-6" style="background: #1a1a1a; border: 1px solid #2a2a2a;">
            <h2 class="font-bold text-lg mb-5" style="color: #f5f0e8;">Masuk</h2>

            @if($errors->any())
                <div class="rounded-xl p-3 mb-4 text-sm" style="background: rgba(229,57,53,0.15); color: #e53935;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1.5" style="color: #d4b96a;">Username</label>
                    <input name="username" type="text" value="{{ old('username') }}" placeholder="admin"
                           class="w-full border rounded-xl h-11 px-3 text-sm outline-none"
                           style="background: #0a0a0a; border-color: #2a2a2a; color: #f5f0e8;"
                           required autofocus />
                </div>
                <div>
                    <label class="block text-sm mb-1.5" style="color: #d4b96a;">Password</label>
                    <input name="password" type="password" placeholder="••••••••"
                           class="w-full border rounded-xl h-11 px-3 text-sm outline-none"
                           style="background: #0a0a0a; border-color: #2a2a2a; color: #f5f0e8;"
                           required />
                </div>
                <button type="submit"
                        class="w-full h-11 rounded-xl font-semibold text-sm mt-2"
                        style="background: #c9a84c; color: #0a0a0a;">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
