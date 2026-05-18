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
                            dark:   'hsl(24,35%,18%)',
                            medium: 'hsl(24,35%,25%)',
                            light:  'hsl(40,33%,98%)',
                            accent: 'hsl(35,90%,50%)',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-dark:    hsl(24,35%,18%);
            --bg-medium:  hsl(24,35%,25%);
            --bg-light:   hsl(40,33%,98%);
            --accent:     hsl(35,90%,50%);
            --accent-muted: hsl(35,45%,75%);
            --text-dark:  hsl(24,10%,10%);
            --text-mid:   hsl(24,10%,40%);
            --text-light: hsl(40,33%,98%);
            --border:     hsl(35,25%,85%);
            --success:    hsl(145,65%,42%);
            --danger:     hsl(0,84%,60%);
        }
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <script src="{{ asset('js/cart.js') }}" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body style="background: var(--bg-light);">
    @yield('content')
</body>
</html>
