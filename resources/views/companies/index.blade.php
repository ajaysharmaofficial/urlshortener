@extends('layouts.app')

@section('content')
    @role('SuperAdmin')
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0"><i class="bi bi-building"></i> Companies</h3>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCompany">
                    <i class="bi bi-plus-circle"></i> Add Company
                </button>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 80px;">#</th>
                                <th>Name</th>
                                <th>Domain</th>
                                <th style="width:170px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($companies as $company)
                                <tr>
                                    <td class="fw-semibold">{{ $company->id }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->domain ?? '—' }}</td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-open-view"
                                            title="View" data-id="{{ $company->id }}" data-name="{{ e($company->name) }}"
                                            data-domain="{{ e($company->domain) }}"
                                            data-created="{{ $company->created_at->format('d M Y, h:i A') }}"
                                            data-updated="{{ $company->updated_at->format('d M Y, h:i A') }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-primary ms-1 btn-open-edit"
                                            title="Edit" data-id="{{ $company->id }}" data-name="{{ e($company->name) }}"
                                            data-domain="{{ e($company->domain) }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger ms-1 btn-delete-company"
                                            title="Delete" data-id="{{ $company->id }}" data-name="{{ e($company->name) }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No companies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                {{ $companies->links() }}
            </div>
        </div>

        <div class="modal fade" id="modalAddCompany" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('companies.store') }}" method="POST" id="formAddCompany">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-building-add"></i> Add Company</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            @if ($errors->any() && old('__form') === 'add')
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Company Name</label>
                                <input type="text" name="name" class="form-control" required value="{{ old('name') }}"
                                    placeholder="Enter company name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Domain (optional)</label>
                                <input type="text" name="domain" class="form-control" value="{{ old('domain') }}"
                                    placeholder="example.com">
                            </div>

                            <input type="hidden" name="__form" value="add">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-success" type="submit"><i class="bi bi-check-circle"></i> Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditCompany" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="#" method="POST" id="formEditCompany">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Company</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="company_id" id="edit_company_id">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Company Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Domain (optional)</label>
                                <input type="text" name="domain" id="edit_domain" class="form-control">
                            </div>

                            <input type="hidden" name="__form" value="edit">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalViewCompany" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-eye"></i> Company Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 120px;">Name</th>
                                <td id="view_name"></td>
                            </tr>

                            <tr>
                                <th>Domain</th>
                                <td id="view_domain"></td>
                            </tr>

                            <tr>
                                <th>Created</th>
                                <td id="view_created"></td>
                            </tr>

                            <tr>
                                <th>Updated</th>
                                <td id="view_updated"></td>
                            </tr>
                        </table>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <form id="deleteCompanyForm" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endrole
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const baseCompaniesUrl = "{{ url('companies') }}";

            document.querySelectorAll('.btn-open-view').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const name = this.dataset.name ?? '';
                    const domain = this.dataset.domain ?? '';
                    const created = this.dataset.created ?? '';
                    const updated = this.dataset.updated ?? '';

                    document.getElementById('view_name').innerHTML = name || '—';
                    document.getElementById('view_domain').innerHTML = domain || '—';
                    document.getElementById('view_created').textContent = created || '—';
                    document.getElementById('view_updated').textContent = updated || '—';

                    new bootstrap.Modal(document.getElementById('modalViewCompany')).show();
                });
            });

            document.querySelectorAll('.btn-open-edit').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name ?? '';
                    const domain = this.dataset.domain ?? '';

                    document.getElementById('edit_company_id').value = id;
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_domain').value = domain;

                    const form = document.getElementById('formEditCompany');
                    form.action = baseCompaniesUrl + '/' + id;

                    new bootstrap.Modal(document.getElementById('modalEditCompany')).show();
                });
            });

            document.querySelectorAll('.btn-delete-company').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name || '';

                    Swal.fire({
                        title: "Are you sure?",
                        html: "Delete <strong>" + name + "</strong> ?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Delete",
                        cancelButtonText: "Cancel",
                        confirmButtonColor: "#d33",
                        reverseButtons: true
                    }).then(result => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('deleteCompanyForm');
                            form.action = baseCompaniesUrl + '/' + id;
                            form.submit();
                        }
                    });
                });
            });

            @if ($errors->any() && old('__form') === 'add')
                new bootstrap.Modal(document.getElementById('modalAddCompany')).show();
            @elseif ($errors->any() && old('__form') === 'edit' && session('open_edit_id'))
                (function() {
                    const id = "{{ session('open_edit_id') }}";
                    const name = "{{ old('name') }}";
                    const domain = "{{ old('domain') }}";
                    document.getElementById('edit_company_id').value = id;
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_domain').value = domain;
                    document.getElementById('formEditCompany').action = baseCompaniesUrl + '/' + id;
                    new bootstrap.Modal(document.getElementById('modalEditCompany')).show();
                })();
            @endif

        });
    </script>
@endpush
