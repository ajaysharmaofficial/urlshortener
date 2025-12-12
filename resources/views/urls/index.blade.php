@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-link-45deg"></i> URLs</h3>
            @can('urls.create')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUrl">
                    <i class="bi bi-plus-circle"></i> Create URL
                </button>
            @endcan
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            @if (auth()->user()->hasRole('SuperAdmin'))
                <div class="card mb-3 p-3">
                    <form method="GET" action="">
                        <div class="row g-2">

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Filter by Company</label>
                                <select name="company_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Companies</option>

                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Short URL</th>
                            <th>Original URL</th>
                            <th>Hits</th>
                            <th>Created By</th>
                            <th width="160">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($urls as $url)
                            <tr>
                                <td>{{ $url->id }}</td>

                                <td>
                                    <a target="_blank" href="/urlshortener/public/u/{{ $url->short_code }}">
                                        {{ $url->short_code }}
                                    </a>
                                </td>

                                <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $url->original_url }}
                                </td>

                                <td>{{ $url->hits }}</td>

                                <td>{{ $url->creator->name ?? 'Unknown' }}</td>

                                <td>
                                    <button class="btn btn-sm btn-outline-secondary btn-open-view"
                                        data-short="{{ e($url->short_code) }}" data-original="{{ e($url->original_url) }}"
                                        data-hits="{{ $url->hits }}"
                                        data-user="{{ e($url->creator->name ?? 'Unknown') }}"
                                        data-created="{{ $url->created_at->format('d M Y, h:i A') }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @can('urls.update')
                                        <button class="btn btn-sm btn-outline-primary ms-1 btn-open-edit"
                                            data-id="{{ $url->id }}" data-original="{{ e($url->original_url) }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endcan
                                    @can('urls.delete')
                                        <button class="btn btn-sm btn-outline-danger ms-1 btn-delete-url"
                                            data-id="{{ $url->id }}" data-short="{{ e($url->short_code) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No URLs found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $urls->links() }}
        </div>
    </div>



    <div class="modal fade" id="modalAddUrl" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="{{ route('urls.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Create Short URL</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Original URL</label>
                            <input type="url" name="original_url" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-success" type="submit">
                            <i class="bi bi-check-circle"></i> Create
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modalViewUrl" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-eye"></i> URL Details</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <table class="table table-bordered">
                        <tr>
                            <th>Short</th>
                            <td id="v_short"></td>
                        </tr>
                        <tr>
                            <th>Original</th>
                            <td id="v_original"></td>
                        </tr>
                        <tr>
                            <th>Hits</th>
                            <td id="v_hits"></td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td id="v_user"></td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td id="v_created"></td>
                        </tr>
                    </table>

                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modalEditUrl" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="#" method="POST" id="formEditUrl">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit URL</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Original URL</label>
                            <input type="url" name="original_url" id="edit_original" class="form-control" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <form id="deleteUrlForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endsection




@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const updateTemplate = "{{ route('urls.update', ':id') }}";
            const destroyTemplate = "{{ route('urls.destroy', ':id') }}";

            /* VIEW */
            document.querySelectorAll('.btn-open-view').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.getElementById('v_short').textContent = this.dataset.short;
                    document.getElementById('v_original').textContent = this.dataset.original;
                    document.getElementById('v_hits').textContent = this.dataset.hits;
                    document.getElementById('v_user').textContent = this.dataset.user;
                    document.getElementById('v_created').textContent = this.dataset.created;

                    new bootstrap.Modal(document.getElementById('modalViewUrl')).show();
                });
            });
            document.querySelectorAll('.btn-open-edit').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    const original = this.dataset.original;

                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_original').value = original;

                    const form = document.getElementById('formEditUrl');
                    form.action = updateTemplate.replace(':id', id);

                    new bootstrap.Modal(document.getElementById('modalEditUrl')).show();
                });
            });

            document.querySelectorAll('.btn-delete-url').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    const shortUrl = this.dataset.short;

                    Swal.fire({
                        title: "Are you sure?",
                        text: `Delete short URL "${shortUrl}"?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        confirmButtonText: "Yes, Delete",
                        cancelButtonText: "Cancel"
                    }).then(result => {

                        if (result.isConfirmed) {
                            const form = document.getElementById('deleteUrlForm');
                            form.action = destroyTemplate.replace(':id', id);
                            form.submit();
                        }
                    });

                });
            });

        });
    </script>
@endpush
