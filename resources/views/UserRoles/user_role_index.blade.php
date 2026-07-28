@extends('layouts.homePages')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- 🌟 ហៅ DataTables និង jQuery មកប្រើដើម្បីកុំឱ្យ Error 🌟 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
    /* --- GLOBAL BRANDING & OPTIMIZATION --- */
    :root {
        --brand-indigo: #4f46e5;
        --brand-purple: #7c3aed;
        --brand-pink: #ec4899;
        --brand-red: #ef4444;
        --bg-body: #f8fafc;
        --surface-white: #ffffff;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #cbd5e1;
        --font-main: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important;
    }

    body, .container-xxl {
        font-family: var(--font-main);
        background-color: var(--bg-body);
        font-size: 0.85rem;
        color: var(--text-main);
    }

    .container-xxl {
        max-width: 100% !important;
        width: 100% !important;
        padding: 20px 30px !important;
    }

    /* --- ANIMATIONS --- */
    @keyframes fadeUp {
        0% { opacity: 0; transform: translate3d(0, 20px, 0); }
        100% { opacity: 1; transform: translate3d(0, 0, 0); }
    }
    .anim-fade-up {
        animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        will-change: opacity, transform;
    }

    /* --- MAIN TABLE CARD --- */
    .card-main {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.04);
        background: var(--surface-white);
        overflow: hidden;
    }

    /* Professional Gradient Header */
    .card-header-gradient {
        background: linear-gradient(135deg, var(--brand-indigo) 0%, var(--brand-purple) 100%) !important;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .header-title { color: #fff; font-weight: 800; font-size: 1.15rem; margin: 0; letter-spacing: -0.3px; display: flex; align-items: center; }
    .kh-sub { font-family: 'Noto Sans Khmer', sans-serif; font-size: 0.8rem; font-weight: 400; opacity: 0.85; margin-left: 10px; }

    /* Search Bar */
    .header-search-wrapper { position: relative; flex: 1; max-width: 350px; min-width: 200px; }
    .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.7); }
    .header-search-input {
        width: 100%; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px; padding: 10px 15px 10px 40px; color: #fff; transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }
    .header-search-input:focus {
        outline: none; background: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 0 4px rgba(255,255,255,0.1);
    }
    .header-search-input::placeholder { color: rgba(255,255,255,0.6); }

    /* Button Add */
    .btn-add-role {
        background: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 9px 20px; border-radius: 8px; font-weight: 700; text-decoration: none; transition: all 0.3s ease;
        display: inline-flex; align-items: center; gap: 8px; text-transform: uppercase; font-size: 0.75rem;
    }
    .btn-add-role:hover { background: #fff; color: var(--brand-indigo); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

    /* --- TABLE STYLING WITH BORDERS --- */
    .table-responsive { padding: 15px; }
    .table { border-collapse: collapse; width: 100%; border: 1px solid var(--border-color); }
    
    .table thead th { 
        background: #f8fafc; color: var(--text-muted); font-weight: 800; 
        text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.8px;
        padding: 16px 20px; border: 1px solid var(--border-color); border-bottom: 2px solid #94a3b8;
    }
    
    .table tbody td { 
        padding: 15px 20px; vertical-align: middle; 
        border: 1px solid var(--border-color); transition: background 0.2s ease; font-size: 0.85rem;
    }
    .table tbody tr { transition: transform 0.2s ease, background 0.2s ease; }
    .table tbody tr:hover td { background-color: #f1f5f9; }
    
    /* Badges & Actions */
    .badge-role { 
        background: #f8fafc; color: var(--brand-indigo); border: 1px solid var(--border-color); 
        padding: 6px 14px; border-radius: 8px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; 
        display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .btn-action {
        width: 34px; height: 34px; border-radius: 8px; display: inline-flex;
        align-items: center; justify-content: center; background: #fff;
        color: var(--text-muted); border: 1px solid var(--border-color); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover { border-color: var(--brand-indigo); color: var(--brand-indigo); background: #f5f3ff; transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .btn-delete:hover { border-color: var(--brand-red); color: var(--brand-red); background: #fef2f2; }

    /* DataTables Pagination & Info */
    .dataTables_info { padding: 15px 0 !important; font-weight: 600; color: var(--text-muted); font-size: 0.8rem; }
    .dataTables_paginate { padding: 15px 0 !important; }
    .paginate_button { border: 1px solid var(--border-color) !important; border-radius: 8px !important; font-weight: 600 !important; margin: 0 2px !important; transition: 0.3s !important; background: #fff !important; color: var(--text-muted) !important; }
    .paginate_button.current { background: var(--brand-indigo) !important; color: white !important; border-color: var(--brand-indigo) !important; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3) !important; }
    .paginate_button:hover:not(.current) { background: #f1f5f9 !important; color: var(--brand-indigo) !important; border-color: var(--border-color) !important; }
</style>

<div class="container-xxl">
    
    <!-- 🌟 MAIN TABLE CARD (With Animation) 🌟 -->
    <div class="card-main anim-fade-up">
        
        <div class="card-header-gradient">
            <h5 class="header-title">
                <i class="fas fa-shield-halved me-2"></i>ROLES & PERMISSIONS <span class="kh-sub">គ្រប់គ្រងសិទ្ធិ</span>
            </h5>
            
            <!-- Custom Live Search -->
            <div class="header-search-wrapper d-none d-md-block">
                <i class='fas fa-search search-icon'></i>
                <input type="text" id="cleanSearch" class="header-search-input" placeholder="Search roles...">
            </div>

            <a href="{{ url('/userRoles/create') }}" class="btn-add-role">
                <i class="fas fa-plus-circle"></i> Create New Role
            </a>
        </div>

        <div class="card-body p-0">
            @if(count($userRoles) > 0)
            <div class="table-responsive">
                <table id="rolesTable" class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">#</th>
                            <th>Role Designation</th>
                            <th>Registration Date</th>
                            <th>Last Modification</th>
                            <th class="text-center" style="width: 120px;">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userRoles as $i => $role)
                        <tr>
                            <td class="text-center text-muted fw-bold" style="background-color: #f8fafc;">{{ $i+1 }}</td>
                            <td><span class="badge-role">{{ $role->name }}</span></td>
                            <td class="fw-bold text-dark">{{ $role->created_at->format('M d, Y') }}</td>
                            <td class="text-muted fw-medium">{{ $role->updated_at->diffForHumans() }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ url('/userRoles/edit', encrypt($role->id)) }}" class="btn-action shadow-sm" title="Edit Role">
                                        <i class="fas fa-pen-to-square" style="font-size: 0.8rem;"></i>
                                    </a>
                                    <button type="button" class="btn-action btn-delete shadow-sm" title="Delete Role" 
                                            onclick="deleteRole('{{ encrypt($role->id) }}', '{{ addslashes($role->name) }}')">
                                        <i class="fas fa-trash-can" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-folder-open display-1 text-muted" style="opacity: 0.2;"></i>
                <p class="mt-4 fw-bold text-secondary">No roles defined yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Form សម្រាប់លុបទិន្នន័យ -->
<form id="delete-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    $(document).ready(function() {
        // 🌟 Initialize DataTables 🌟
        const table = $('#rolesTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center flex-wrap"ip>',
            "pageLength": 10,
            "ordering": false,
            "language": {
                "info": "Showing _START_ to _END_ of _TOTAL_ roles",
                "paginate": { 
                    "previous": "<i class='fas fa-angle-left'></i>", 
                    "next": "<i class='fas fa-angle-right'></i>" 
                }
            },
            "drawCallback": function(settings) {
                $('#rolesTable tbody').hide().fadeIn(300);
            }
        });

        // Custom Live Search Event
        $('#cleanSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

   // មុខងារលុប
   function deleteRole(encryptedId, roleName) {
        if (Swal.isVisible()) return;

        Swal.fire({
            title: 'លុបតួនាទី?',
            html: `អ្នកកំពុងលុបតួនាទី: <b style="color:#ef4444">${roleName}</b> <br> <span class="text-muted small">ទិន្នន័យនេះមិនអាចយកមកវិញបានទេ។</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash me-1"></i> បាទ/ចាស, លុបវា!',
            cancelButtonText: 'បោះបង់',
            fontFamily: 'Plus Jakarta Sans, Noto Sans Khmer',
            buttonsStyling: false,
            reverseButtons: true,
            // 🌟 ជួសជុល Responsive Buttons ត្រង់នេះ 🌟
            customClass: {
                popup: 'rounded-4 shadow-sm border-0',
                actions: 'd-flex flex-wrap justify-content-center gap-3 w-100 mt-4', // ប្រើ gap-3 ដើម្បីរក្សាគម្លាតប៊ូតុងស្មើគ្នា
                confirmButton: 'btn btn-danger fw-bold rounded-3 px-4 py-2 border-0 shadow-sm m-0 flex-grow-1 flex-md-grow-0', // បន្ថែម flex-grow ឱ្យពេញទំហំលើទូរស័ព្ទ
                cancelButton: 'btn btn-secondary fw-bold rounded-3 px-4 py-2 border-0 shadow-sm m-0 flex-grow-1 flex-md-grow-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'កំពុងដំណើរការ...',
                    text: 'សូមរង់ចាំបន្តិច។',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    customClass: { popup: 'rounded-4 shadow-sm border-0' }
                });

                const form = document.getElementById('delete-form');
                form.action = "{{ url('/userRoles/delete') }}/" + encryptedId;
                form.submit();
            }
        });
    }
</script>
@endsection