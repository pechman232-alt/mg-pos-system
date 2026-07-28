@extends('layouts.homePages')
@section('content')

<!-- បញ្ចូល Library សម្រាប់ Convert HTML ទៅជា PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Khmer:wght@400;700&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --dark-text: #0f172a;
        --label-text: #64748b;
        --border-color: #e2e8f0;
        --bg-label: #f8fafc;
        --brand-red: #CE181E;
        --brand-blue: #1d4ed8;
    }

    body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif !important; }

    /* 🌟 Fit Screen & Pop-up Style Document 🌟 */
    .document-wrapper {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08); /* ធ្វើឱ្យអណ្តែតដូច Pop-up */
        padding: 30px 40px;
        margin: 0 auto;
        position: relative;
    }

    /* Page Header */
    .page-header { margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 2px solid var(--border-color); padding-bottom: 15px;}
    .page-title { font-size: 20px; font-weight: 800; color: var(--dark-text); text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 10px; }
    .page-subtitle { font-size: 12px; font-weight: 700; color: var(--brand-red); margin-top: 5px; }
    
    .avatar-wrapper { width: 60px; height: 60px; border-radius: 10px; border: 2px solid var(--border-color); object-fit: cover; }

    /* Section Cards */
    .report-section { background: #fff; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 20px; overflow: hidden; }
    
    .section-header { 
        padding: 12px 20px; 
        font-size: 12px; 
        font-weight: 800; 
        color: var(--dark-text); 
        background: #f8fafc; 
        border-bottom: 1px solid var(--border-color); 
        text-transform: uppercase; 
        display: flex; align-items: center; gap: 10px; 
    }

    /* Grid Table System */
    .table-grid { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .table-grid td { border-bottom: 1px solid var(--border-color); border-right: 1px solid var(--border-color); vertical-align: middle; }
    .table-grid tr:last-child td { border-bottom: none; }
    .table-grid td:last-child { border-right: none; }

    .td-label { 
        width: 20%; 
        padding: 12px 20px; 
        font-size: 11px; 
        font-weight: 700; 
        color: var(--label-text); 
        text-transform: uppercase; 
        text-align: right; 
        background: var(--bg-label); 
    }
    .td-value { 
        width: 30%; 
        padding: 12px 20px; 
        font-size: 14px; 
        font-weight: 700; 
        color: var(--dark-text); 
        background: #fff; 
    }

    .pw-font { font-family: 'JetBrains Mono', monospace; color: var(--brand-red); }
    .text-red { color: var(--brand-red); }
    .text-green { color: #10b981; }

    /* Action Buttons Area (No Print) */
    .action-bar {
        background: #fff;
        padding: 15px 25px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid var(--border-color);
    }

    /* Print Configuration */
    @media print {
        @page { size: A4; margin: 10mm; }
        body { background: #fff; }
        .no-print { display: none !important; }
        .document-wrapper { box-shadow: none; padding: 0; border: none; }
        .report-section { break-inside: avoid; }
        .td-label { background: #f9f9f9 !important; -webkit-print-color-adjust: exact; }
    }
</style>

<!-- 🌟 ប្រើ container-fluid ឱ្យពេញអេក្រង់ (Fit Screen) 🌟 -->
<div class="container-fluid ">
    
    <!-- Action Bar ខាងលើសម្រាប់ចុច (មិនជាប់ក្នុង PDF/Print) -->
    <div class="action-bar no-print">
        <div>
            <h5 class="m-0 fw-bold text-dark"><i class="fas fa-eye me-2 text-primary"></i> Document Viewer</h5>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('employees.index') }}" class="btn btn-light fw-bold border" style="border-radius: 8px;">
                <i class="fas fa-list me-1"></i> DIRECTORY
            </a>
            <a href="{{ route('employees.edit', encrypt($employees->id)) }}" class="btn btn-outline-primary fw-bold" style="border-radius: 8px;">
                <i class="fas fa-edit me-1"></i> EDIT
            </a>
            <!-- ប៊ូតុង Print ធម្មតា -->
            <button onclick="window.print()" class="btn btn-dark fw-bold shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-print me-1"></i> PRINT A4
            </button>
            <!-- 🌟 ប៊ូតុង Save ជា PDF 🌟 -->
            <button onclick="saveAsPDF()" class="btn fw-bold shadow-sm text-white" style="background-color: var(--brand-red); border-radius: 8px;">
                <i class="fas fa-file-pdf me-1"></i> SAVE PDF
            </button>
        </div>
    </div>

    <!-- 🌟 Pop-up Style Wrapper 🌟 (ផ្ទៃដែលនឹងត្រូវ Save ជា PDF) -->
    <div class="document-wrapper" id="pdf-content">
        
        <!-- Header របស់ឯកសារ -->
        <div class="page-header">
            <div>
                <h1 class="page-title"><i class="fas fa-file-invoice text-danger"></i> PERSONNEL RECORD ARCHIVE</h1>
                <div class="page-subtitle">GENERATED: {{ now()->format('d-M-Y, h:i A') }}</div>
            </div>
            <div>
                <img src="{{ $employees->image_employee ? url('/imageEmployee/' . $employees->image_employee) : url('backend/assets/img/avatars/1.png') }}" class="avatar-wrapper">
            </div>
        </div>

        <!-- 1. CORE INFORMATION -->
        <div class="report-section">
            <div class="section-header">
                <i class="fas fa-user-circle" style="color: #6366f1;"></i> CORE INFORMATION
            </div>
            <table class="table-grid">
                <tr>
                    <td class="td-label">FULL NAME (EN)</td>
                    <td class="td-value">{{ $employees->full_name }}</td>
                    <td class="td-label">GENDER</td>
                    <td class="td-value">{{ $employees->gender }}</td>
                </tr>
                <tr>
                    <td class="td-label">NAME (KH)</td>
                    <td class="td-value" style="font-family: 'Noto Sans Khmer';">{{ $employees->first_name_kh }} {{ $employees->last_name_kh }}</td>
                    <td class="td-label">DATE OF BIRTH</td>
                    <td class="td-value">{{ $employees->dob ? \Carbon\Carbon::parse($employees->dob)->format('d-M-Y') : '---' }}</td>
                </tr>
                <tr>
                    <td class="td-label">PHONE NUMBER</td>
                    <td class="td-value text-red"><i class="fas fa-phone-alt me-2"></i>{{ $employees->phone_number ?? '---' }}</td>
                    <td class="td-label">AGE</td>
                    <td class="td-value">{{ $employees->age ?? '---' }} Years</td>
                </tr>
            </table>
        </div>

        <!-- 2. ORGANIZATIONAL DEPLOYMENT -->
        <div class="report-section">
            <div class="section-header">
                <i class="fas fa-sitemap" style="color: #f59e0b;"></i> ORGANIZATIONAL DEPLOYMENT
            </div>
            <table class="table-grid">
                <tr>
                    <td class="td-label">EMPLOYEE ID</td>
                    <td class="td-value fw-bolder">{{ $employees->user_id }}</td>
                    <td class="td-label">COMPANY ENTITY</td>
                    <td class="td-value">{{ $employees->company->company_name ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="td-label">DEPARTMENT</td>
                    <td class="td-value">{{ $employees->department->name ?? '---' }}</td>
                    <td class="td-label">BRANCH ASSIGN</td>
                    <td class="td-value">{{ $employees->branch->branch_name ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="td-label">FUNCTIONAL POSITION</td>
                    <td class="td-value text-primary">{{ $employees->position->name ?? '---' }}</td>
                    <td class="td-label">CONTRACT DATE</td>
                    <td class="td-value">{{ $employees->contract ? \Carbon\Carbon::parse($employees->contract)->format('d-M-Y') : '---' }}</td>
                </tr>
                <tr>
                    <td class="td-label">RECORD STATUS</td>
                    <td class="td-value {{ $employees->status == 1 ? 'text-green' : 'text-red' }}">
                        {{ $employees->status == 1 ? 'ACTIVE NODE' : 'INACTIVE / RESIGNED' }}
                    </td>
                    <td class="td-label">DOMAIN GROUP</td>
                    <td class="td-value">{{ $employees->group_domain ?? '---' }}</td>
                </tr>
            </table>
        </div>

        <!-- 3. SYSTEM ACCESS CONTROL -->
        <div class="report-section">
            <div class="section-header">
                <i class="fas fa-shield-alt" style="color: #ef4444;"></i> SYSTEM ACCESS CONTROL
            </div>
            <table class="table-grid">
                <tr>
                    <td class="td-label">OFFICIAL SYSTEM USER</td>
                    <td class="td-value">{{ $employees->official_userid ?: '---' }}</td>
                    <td class="td-label">OFFICIAL PASSWORD</td>
                    <td class="td-value pw-font">{{ $employees->official_pass ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="td-label">M365 ACCOUNT</td>
                    <td class="td-value">{{ $employees->m_user_id ?: '---' }}</td>
                    <td class="td-label">M365 PASSWORD</td>
                    <td class="td-value pw-font">{{ $employees->m_pass ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="td-label">NAS USERNAME</td>
                    <td class="td-value">{{ $employees->s_user_id ?: '---' }}</td>
                    <td class="td-label">NAS PASSWORD</td>
                    <td class="td-value pw-font">{{ $employees->s_pass ?: 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- 4. FACILITIES & REMARKS -->
        <div class="report-section mb-0">
            <div class="section-header">
                <i class="fas fa-id-badge" style="color: #10b981;"></i> FACILITIES & REMARKS
            </div>
            <table class="table-grid">
                <tr>
                    <td class="td-label">INTERNAL EXTENSION</td>
                    <td class="td-value">{{ $employees->extension_id ?: '---' }}</td>
                    <td class="td-label">DOOR ACCESS CARD</td>
                    <td class="td-value">{{ $employees->access_door ?: '---' }}</td>
                </tr>
                <tr>
                    <td class="td-label">VOIP REGISTER PASS</td>
                    <td class="td-value">{{ $employees->register_pass ?: '---' }}</td>
                    <td class="td-label">HR SYSTEM ID</td>
                    <td class="td-value">{{ $employees->hr_user_id ?: '---' }}</td>
                </tr>
                <tr>
                    <td class="td-label">ADMINISTRATIVE REMARKS</td>
                    <td class="td-value" colspan="3" style="font-weight: 500; width: 80%;">
                        {{ $employees->note ?: 'No additional remarks recorded.' }}
                    </td>
                </tr>
            </table>
        </div>

    </div>
</div>

<script>
    // មុខងារសម្រាប់ទាញយកជា PDF
    function saveAsPDF() {
        const element = document.getElementById('pdf-content'); // យកតែផ្ទាំងឯកសារមក Print
        
        // កំណត់ទម្រង់ PDF
        const opt = {
            margin:       [10, 10, 10, 10], // គម្លាតលើ ក្រោម ឆ្វេង ស្តាំ
            filename:     'Personnel_Record_{{ $employees->user_id }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true }, // scale: 2 ធ្វើឱ្យរូបភាពនិងអក្សរច្បាស់
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // ដំណើរការ Download
        html2pdf().set(opt).from(element).save();
    }
</script>

@endsection