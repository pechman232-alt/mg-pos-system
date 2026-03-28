@extends('layouts.homePages')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ================= VARIABLES & BRANDING ================= */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --font-main: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
    }

    body { 
        font-family: var(--font-main); 
        background-color: var(--bg-body); 
        color: #1e293b;
        -webkit-font-smoothing: antialiased;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-card { animation: fadeInUp 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 1.5rem 2rem !important;
    }

    .card { 
        border: none; 
        border-radius: 20px; 
        box-shadow: 0 15px 35px rgba(0,0,0,0.05); 
        background: #ffffff; 
        overflow: hidden;
    }

    .navbar-custom {
        background: linear-gradient(90deg, var(--brand-indigo) 0%, var(--brand-purple) 33%, var(--brand-pink) 66%, var(--brand-red) 100%);
        padding: 1.25rem 1.8rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title {
        color: #ffffff !important;
        font-weight: 800;
        font-size: 1.05rem;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .kh-sub { font-family: 'Noto Sans Khmer', sans-serif; font-size: 0.75rem; font-weight: 400; opacity: 0.8; margin-left: 8px; }

    .btn-add-role { 
        background: rgba(255,255,255,0.15); 
        color: white; 
        border: 1px solid rgba(255,255,255,0.3); 
        padding: 8px 20px; 
        border-radius: 10px; 
        font-weight: 700; 
        transition: 0.3s;
        text-transform: uppercase;
        font-size: 0.7rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-add-role:hover { background: #fff; color: var(--brand-indigo); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

    .table thead th { 
        font-weight: 800; 
        text-transform: uppercase; 
        font-size: 0.65rem; 
        color: #64748b; 
        background: #f8fafc; 
        padding: 16px 20px;
        letter-spacing: 1px;
    }
    
    .table tbody td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.82rem;
    }

    .badge-role {
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.65rem;
        background: #eef2ff;
        color: var(--brand-indigo);
        text-transform: uppercase;
        display: inline-block;
    }
    
    .btn-action { 
        width: 34px; height: 34px; border-radius: 10px; display: inline-flex; align-items: center; 
        justify-content: center; background: #fff; color: #64748b; transition: 0.2s; border: 1px solid #e2e8f0;
    }
    .btn-action:hover { border-color: var(--brand-indigo); color: var(--brand-indigo); transform: translateY(-2px); }
    .btn-delete:hover { border-color: var(--brand-red); color: var(--brand-red); background: #fff1f2; }

    @media (max-width: 768px) {
        .navbar-custom { flex-direction: column; gap: 15px; }
        #datatable thead { display: none; }
        #datatable tbody tr { display: block; margin-bottom: 1rem; border: 1px solid #edf2f7; border-radius: 16px; padding: 1rem; }
        #datatable tbody td { display: flex; justify-content: space-between; align-items: center; text-align: right; padding: 0.5rem 0; border: none; }
        #datatable tbody td::before { content: attr(data-label); font-weight: 800; text-transform: uppercase; font-size: 0.6rem; color: #94a3b8; }
    }
</style>

<div class="container-xxl">
    <div class="card animate-card">
        <nav class="navbar-custom">
            <h5 class="header-title">
                <i class="fas fa-shield-halved me-2"></i>ROLES & PERMISSIONS <span class="kh-sub">គ្រប់គ្រងសិទ្ធិ</span>
            </h5>
            <a href="{{ url('/userRoles/create') }}" class="btn-add-role">
                <i class="fas fa-plus-circle"></i> Create New Role
            </a>
        </nav>

        <div class="card-body p-0">
            @if(count($userRoles) > 0)
            <div class="table-responsive">
                <table id="datatable" class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">#</th>
                            <th>Role Designation</th>
                            <th>Registration Date</th>
                            <th>Last Modification</th>
                            <th class="text-center">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userRoles as $i => $role)
                        <tr>
                            <td class="text-center text-muted fw-bold" data-label="No">{{ $i+1 }}</td>
                            <td data-label="Role Name"><span class="badge-role">{{ $role->name }}</span></td>
                            <td data-label="Created Date" class="text-muted">{{ $role->created_at->format('M d, Y') }}</td>
                            <td data-label="Updated Date" class="text-muted">{{ $role->updated_at->diffForHumans() }}</td>
                            <td data-label="Actions" class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/userRoles/edit', encrypt($role->id)) }}" class="btn-action" title="Edit"><i class="fas fa-pen-to-square"></i></a>
                                    <button type="button" class="btn-action btn-delete" onclick="deleteRole('{{ encrypt($role->id) }}', '{{ $role->name }}')"><i class="fas fa-trash-can"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open display-1 text-muted opacity-20"></i>
                <p class="mt-4 fw-800 text-secondary">No roles defined.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<form id="delete-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // ១. មុខងារលុប (ប្រើតែមួយនេះគត់ លុបអាផ្សេងចេញឱ្យអស់)
    function deleteRole(encryptedId, roleName) {
        // ការពារកុំឱ្យវា Trigger ស្ទួនពេលកំពុងបង្ហាញ Pop-up
        if (Swal.isVisible()) return;

        Swal.fire({
            title: 'លុបតួនាទី?',
            html: `អ្នកកំពុងលុបតួនាទី: <b>${roleName}</b> <br> <span class="text-danger small">ទិន្នន័យនេះមិនអាចយកមកវិញបានទេ!</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'បាទ/ចាស, លុបវា!',
            cancelButtonText: 'បោះបង់',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // បង្ហាញ Loading ភ្លាមៗដើម្បីការពារការចុចស្ទួន
                Swal.fire({
                    title: 'កំពុងដំណើរការ...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                // កំណត់ Action និង Submit Form
                const form = document.getElementById('delete-form');
                form.action = "{{ url('/userRoles/delete') }}/" + encryptedId;
                form.submit();
            }
        });
    }

    // ២. ការបង្ហាញសារជោគជ័យ (លោតតែម្ដងគត់តាមរយៈ Session)
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'បរាជ័យ!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#4f46e5'
            });
        @endif
        
        // ឆែកមើលសារ info ពីការ Update
        @if(session('info'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: "{{ session('info') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    });
</script>
@endsection