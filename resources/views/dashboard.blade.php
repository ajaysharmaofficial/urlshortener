@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row g-3">

            <div class="col-12">
                <div class="card card-custom p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Welcome, {{ $my_name }}</h4>
                            <small class="text-muted">Role: <strong>{{ $my_role }}</strong></small>
                        </div>

                        <div>
                            @role('SuperAdmin')
                                <a href="{{ route('companies.index') }}" class="btn btn-sm btn-primary">Manage Companies</a>
                            @endrole

                            @role('Admin|Member|SuperAdmin')
                                <a href="{{ route('urls.index') }}" class="btn btn-sm btn-secondary">View URLs</a>
                            @endrole
                        </div>
                    </div>
                </div>
            </div>
            @role('SuperAdmin')
                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>Total Companies</h6>
                        <h3 class="mt-2">{{ number_format($total_companies ?? 0) }}</h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>Total Users</h6>
                        <h3 class="mt-2">{{ number_format($total_users ?? 0) }}</h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>Total Short URLs</h6>
                        <h3 class="mt-2">{{ number_format($total_urls ?? 0) }}</h3>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="card p-3">
                        <h5>Recent Companies</h5>
                        <div class="row">
                            @forelse($recent_companies ?? [] as $c)
                                <div class="col-md-3">
                                    <div class="border rounded p-2 mb-2">
                                        <strong>{{ $c->name }}</strong><br>
                                        <small class="text-muted">{{ $c->domain ?: '-' }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No companies yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endrole

            @role('Admin')
                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>Your Company</h6>
                        <p class="mb-0"><strong>{{ $company->name ?? '—' }}</strong></p>
                        <small class="text-muted">{{ $company->domain ?? '' }}</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>Team Members</h6>
                        <h3 class="mt-2">{{ number_format($members_count ?? 0) }}</h3>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>URLs</h6>
                        <h3 class="mt-2">{{ number_format($urls_own_company_count ?? 0) }}</h3>
                    </div>
                </div>
            @endrole

            @role('Member')
                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>Your Company</h6>
                        <p class="mb-0"><strong>{{ $company->name ?? '—' }}</strong></p>
                        <small class="text-muted">{{ $company->domain ?? '' }}</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-3">
                        <h6>Your Created URLs</h6>
                        <h3 class="mt-2">{{ number_format($my_created_urls_count ?? 0) }}</h3>
                    </div>
                </div>
            @endrole

            <div class="col-12 mt-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent URLs</h5>
                        <small class="text-muted">Showing latest 8</small>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Short</th>
                                    <th>Original</th>
                                    <th>Hits</th>
                                    <th>Creator</th>
                                    <th>Company</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_urls ?? [] as $u)
                                    <tr>
                                        <td>{{ $u->id }}</td>
                                        <td><a href="{{ route('url.redirect', $u->short_code) }}"
                                                target="_blank">{{ $u->short_code }}</a></td>
                                        <td style="max-width: 320px; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $u->original_url }}</td>
                                        <td>{{ $u->hits }}</td>
                                        <td>{{ $u->creator->name ?? '—' }}</td>
                                        <td>{{ $u->company->name ?? '–' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted">No recent URLs available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
