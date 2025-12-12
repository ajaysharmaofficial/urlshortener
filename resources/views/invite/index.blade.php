@extends('layouts.app')

@section('content')
    @role(['SuperAdmin', 'Admin'])
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold"><i class="bi bi-envelope-plus"></i> Invitations</h3>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddInvite">
                    <i class="bi bi-plus-circle"></i> Send Invite
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
                                <th style="width:60px;">#</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Company</th>
                                <th>Invited By</th>
                                <th style="width:220px;">Token</th>
                                <th>Date</th>
                                <th style="width:130px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($invitations as $invite)
                                <tr>
                                    <td>{{ $invite->id }}</td>

                                    <td>{{ $invite->email }}</td>

                                    <td>
                                        <span class="badge bg-primary">{{ $invite->role }}</span>
                                    </td>

                                    <td>{{ $invite->company->name ?? 'N/A' }}</td>
                                    <td>{{ $invite->inviter->name ?? 'System' }}</td>

                                    <td>
                                        <code>{{ \Illuminate\Support\Str::limit($invite->token, 40) }}</code>
                                    </td>

                                    <td>{{ optional($invite->created_at)->format('d M Y, h:i A') ?? '-' }}</td>

                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary btn-open-view"
                                            data-id="{{ $invite->id }}" data-email="{{ e($invite->email) }}"
                                            data-role="{{ e($invite->role) }}"
                                            data-company="{{ e(optional($invite->company)->name ?? 'N/A') }}"
                                            data-inviter="{{ e(optional($invite->inviter)->name ?? 'System') }}"
                                            data-token="{{ e($invite->token) }}"
                                            data-date="{{ optional($invite->created_at)->format('d M Y, h:i A') ?? '' }}"
                                            title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger ms-1 btn-delete-invite"
                                            data-id="{{ $invite->id }}" data-email="{{ e($invite->email) }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No invitations found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
            <div class="mt-3">
                {{ $invitations->links() }}
            </div>
        </div>

        <div class="modal fade" id="modalAddInvite" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('invite.store') }}" method="POST" id="formAddInvite">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-envelope-plus"></i> Send Invite</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            @if ($errors->any() && old('__form') === 'add_invite')
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Role</label>
                                <select name="role" class="form-select" required id="select_role">
                                    @if (auth()->user()->hasRole('SuperAdmin'))
                                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    @elseif(auth()->user()->hasRole('Admin'))
                                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="Member" {{ old('role') == 'Member' ? 'selected' : '' }}>Member</option>
                                    @endif
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Assign Company</label>
                                <select name="company_id" class="form-select" required>
                                    <option value="">-- Select Company --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}"
                                            {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="__form" value="add_invite">
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-check-circle"></i> Send Invite
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalViewInvite" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-eye"></i> Invitation Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th style="width:140px">Email</th>
                                <td id="v_email"></td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td id="v_role"></td>
                            </tr>
                            <tr>
                                <th>Company</th>
                                <td id="v_company"></td>
                            </tr>
                            <tr>
                                <th>Invited By</th>
                                <td id="v_inviter"></td>
                            </tr>
                            <tr>
                                <th>Token</th>
                                <td><code id="v_token"></code></td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td id="v_date"></td>
                            </tr>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <form id="deleteInviteForm" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endrole
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const destroyRouteTemplate = "{{ route('invite.destroy', ['invite' => ':id']) }}";
            const baseUrl = "{{ url('invite') }}";

            document.querySelectorAll('.btn-open-view').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('v_email').textContent = this.dataset.email || '—';
                    document.getElementById('v_role').textContent = this.dataset.role || '—';
                    document.getElementById('v_company').textContent = this.dataset.company || '—';
                    document.getElementById('v_inviter').textContent = this.dataset.inviter || '—';
                    document.getElementById('v_token').textContent = this.dataset.token || '—';
                    document.getElementById('v_date').textContent = this.dataset.date || '—';

                    new bootstrap.Modal(document.getElementById('modalViewInvite')).show();
                });
            });

            document.querySelectorAll('.btn-delete-invite').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const email = this.dataset.email || '';

                    Swal.fire({
                        title: "Are you sure?",
                        text: `Delete invitation for "${email}"?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Delete",
                        cancelButtonText: "Cancel",
                        confirmButtonColor: "#d33"
                    }).then(result => {
                        if (result.isConfirmed) {
                            const url = destroyRouteTemplate.replace(':id', id);

                            const form = document.getElementById('deleteInviteForm');
                            form.action = url;
                            form.submit();
                        }
                    });
                });
            });
            @if ($errors->any() && old('__form') === 'add_invite')
                new bootstrap.Modal(document.getElementById('modalAddInvite')).show();
            @endif

        });
    </script>
@endpush
