<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Accept Invitation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            background: #f5f7fb;
        }

        .invite-wrap {
            min-height: calc(100vh - 40px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .invite-card {
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: visible;
        }

        .card-header .bi {
            margin-right: 8px;
            vertical-align: -2px;
        }

        @media (max-width: 480px) {
            .invite-card {
                max-width: 96%;
                padding: 0 8px;
            }
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.15rem rgba(30, 132, 73, 0.12);
        }
    </style>
</head>

<body>
    <div class="invite-wrap">
        <div class="card invite-card shadow-lg border-0">

            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="bi bi-envelope-check"></i> Invitation Confirmation
                </h4>
            </div>

            <div class="card-body p-4">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="text-center mb-4">
                    <p class="text-muted mb-1">You have been invited to join</p>
                    <h4 class="fw-bold mb-2">{{ optional($invite->company)->name ?? 'Company' }}</h4>
                    <span class="badge bg-success fs-6">{{ $invite->role ?? '-' }}</span>
                </div>

                <div class="border rounded bg-light p-3 mb-4">
                    <p class="mb-1"><strong>Email:</strong> {{ $invite->email ?? '-' }}</p>
                    <p class="mb-1"><strong>Invited By:</strong> {{ optional($invite->inviter)->name ?? 'System' }}
                    </p>
                    <p class="mb-0">
                        <strong>Date:</strong> {{ optional($invite->created_at)->format('d M Y, h:i A') ?? '-' }}
                    </p>
                </div>

                <p class="text-muted text-center mb-3">
                    To complete your account setup, please create a password.
                </p>

                <form action="{{ url('invite/accept/' . ($invite->token ?? '')) }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="name">Full Name</label>
                        <input id="name" type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            required placeholder="Your full name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">Password</label>
                        <input id="password" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" required
                            placeholder="Create password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror" required
                            placeholder="Confirm password">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2">
                        <i class="bi bi-check-circle"></i> Create My Account
                    </button>

                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
