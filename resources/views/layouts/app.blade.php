{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'URL Shortener') }}</title>

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        .card-custom {
            border-radius: .6rem;
        }
    </style>
</head>

<body>

    @include('layouts.nav')

    <main class="py-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Delete confirm (safe, minimal) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.confirm-delete').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    if (!confirm(
                            'Are you sure you want to delete this item? This action cannot be undone.'
                            )) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
