@foreach ($employees as $index => $item)
@php $encryptedId = encrypt($item->id); @endphp
<tr style="border-bottom: 1px solid #f1f5f9;">
    <td class="ps-4 fw-bold text-muted">{{ ($employees->currentPage() - 1) * $employees->perPage() + $index + 1 }}</td>
    
    <td>
        <div class="d-flex align-items-center">
            <img src="{{ $item->image_employee ? url('/imageEmployee/' . $item->image_employee) : url('backend/assets/img/avatars/1.png') }}" 
                 class="rounded-3 me-3" style="width:50px; height:50px; object-fit:cover; border:1px solid #eee;">
            <div>
                <div class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">{{ $item->first_name_kh }} {{ $item->last_name_kh }}</div>
                <div class="text-muted small" style="font-size: 0.75rem;">{{ $item->full_name }} (ID: {{ $item->user_id }})</div>
            </div>
        </div>
    </td>

    <td>
        <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $item->position->name ?? $item->postionName }}</div>
        <div class="text-indigo small fw-bold">{{ $item->department->name ?? $item->departmentName }}</div>
    </td>

    <td>
        <div class="small text-dark"><i class="fas fa-phone-alt me-2 text-success"></i>{{ $item->phone_number ?? '-' }}</div>
        <div class="small text-muted mt-1"><i class="fas fa-tty me-2 text-primary"></i>Ext: {{ $item->extension_id ?? '-' }}</div>
    </td>

    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-label-primary dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" style="font-size: 10px;">
                ACCESS
            </button>
            <ul class="dropdown-menu shadow border-0 p-2" style="min-width: 200px; font-size: 0.75rem;">
                <li class="mb-1 border-bottom pb-1">
                    <small class="text-muted d-block fw-bold">SYSTEM</small>
                    <code>{{ $item->official_userid }}</code> / <span class="text-danger fw-bold">{{ $item->official_pass }}</span>
                </li>
                <li>
                    <small class="text-muted d-block fw-bold">M365</small>
                    <code>{{ $item->m_user_id }}</code> / <span class="text-danger fw-bold">{{ $item->m_pass }}</span>
                </li>
            </ul>
        </div>
    </td>

    <td class="text-center">
        <span class="badge bg-label-success rounded-pill" style="font-size: 10px;">ACTIVE</span>
    </td>

    <td class="text-end pe-4">
        <div class="d-flex justify-content-end gap-1">
            <a href="{{ route('employees.details', $encryptedId) }}" class="btn btn-icon btn-sm btn-outline-secondary border-0"><i class="fas fa-eye"></i></a>
            <a href="{{ route('employees.edit', $encryptedId) }}" class="btn btn-icon btn-sm btn-outline-primary border-0"><i class="fas fa-pen"></i></a>
            
            <button type="button" onclick="deleteEmployee('{{ $encryptedId }}')" class="btn btn-icon btn-sm btn-outline-danger border-0">
                <i class="fas fa-trash-alt"></i>
            </button>
            
            <form id="delete-form-{{ $encryptedId }}" action="{{ route('employees.destroy', $encryptedId) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </td>
</tr>
@endforeach

<tr>
    <td colspan="7" class="border-0">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted small fw-bold">TOTAL: {{ $employees->total() }} PERSONNEL</div>
            <div id="laravel-ajax-pagination">
                {!! $employees->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </td>
</tr>