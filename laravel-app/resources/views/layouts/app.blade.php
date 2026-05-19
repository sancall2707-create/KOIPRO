<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Kopi Tiang Alam')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            dark:   '#0a0a0a',
                            medium: '#1a1a1a',
                            light:  '#f5f0e8',
                            accent: '#c9a84c',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-dark:    #0a0a0a;
            --bg-medium:  #1a1a1a;
            --bg-light:   #f5f0e8;
            --accent:     #c9a84c;
            --accent-muted: #d4b96a;
            --text-dark:  #1a1a1a;
            --text-mid:   #4a4a4a;
            --text-light: #f5f0e8;
            --border:     #2a2a2a;
            --success:    #4caf50;
            --danger:     #e53935;
            --gold-light: #e8d48b;
            --gold-dark:  #8b7a3c;
        }
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <script src="{{ asset('js/cart.js') }}" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body style="background: var(--bg-dark);">
    @yield('content')
</body>
</html>
