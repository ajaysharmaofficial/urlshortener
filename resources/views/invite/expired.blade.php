<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invitation Expired</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
            min-height: 100vh;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center" style="min-height:100vh;">
        <div class="text-center">
            <h2 class="text-danger mb-3"><i class="bi bi-x-circle"></i> Invitation Expired</h2>

            <p class="text-muted mb-4">
                {{ $message ?? 'The invitation link is invalid or has already been used.' }}
            </p>

            <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>
        </div>
    </div>

</body>

</html>