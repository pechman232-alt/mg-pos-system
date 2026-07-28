<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ការកក់ជោគជ័យ | MG Service</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { 
            --mg-blue: #002663; 
            --mg-red: #e31e24; 
            --text-dark: #1e293b;
            --text-light: #64748b;
            --font-kh: "Noto Sans Khmer", sans-serif; 
        }

        body { 
            font-family: var(--font-kh) !important; 
            background-color: #f0f2f5; 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px; 
        }
        
        .ticket-container { width: 100%; max-width: 420px; }
        
        .ticket-card { 
            background-color: #ffffff !important; 
            border-radius: 40px; 
            box-shadow: 0 25px 50px rgba(0,0,0,0.1); 
            overflow: hidden; 
            position: relative;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .ticket-header { 
            background: var(--mg-blue); 
            color: white; 
            padding: 45px 20px 35px; 
            text-align: center; 
            position: relative; 
        }
        
        .success-check { 
            background: white; 
            color: #10b981; 
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 15px; 
            font-size: 28px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .ticket-body { padding: 35px 40px; background-color: #ffffff; }
        
        .id-badge { 
            text-align: center; 
            font-weight: 700; 
            color: var(--text-light); 
            font-size: 0.75rem; 
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 25px;
            display: block;
        }

        .info-row { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding-bottom: 12px; 
            border-bottom: 1px dashed #e2e8f0;
            margin-bottom: 15px;
        }

        .label { color: var(--text-light); font-size: 0.85rem; }
        .value { color: var(--text-dark); font-weight: 700; font-size: 1rem; text-align: right; }
        .value.highlight-blue { color: #2563eb; }
        .value.highlight-red { color: var(--mg-red); }

        .note-box { 
            background: #fff1f2; 
            color: var(--mg-red); 
            padding: 12px; 
            border-radius: 15px; 
            font-size: 0.8rem; 
            text-align: center; 
            margin-top: 10px;
            font-weight: 700;
        }

        .btn-group-vertical { width: 100%; margin-top: 25px; display: flex; flex-direction: column; gap: 12px; }
        
        .btn-custom { 
            border: none; 
            padding: 16px; 
            border-radius: 20px; 
            font-weight: 700; 
            font-size: 1rem; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            transition: 0.3s;
            text-decoration: none;
            width: 100%;
        }

        .btn-save { background: #10b981; color: white !important; }
        .btn-tg { background: #0088cc; color: white !important; }
        .btn-home { background: #64748b; color: white !important; }
        
        .btn-custom i { margin-right: 10px; font-size: 1.2rem; }
        .btn-custom:hover { transform: translateY(-3px); opacity: 0.9; }

        .footer-copyright { text-align: center; color: var(--text-light); font-size: 0.75rem; padding: 25px 0; border-top: 1px solid #f1f5f9; background: #fafafa; }

        .ticket-header::after, .ticket-header::before { 
            content: ''; position: absolute; bottom: -15px; width: 30px; height: 30px; background: #f0f2f5; border-radius: 50%; 
        }
        .ticket-header::before { left: -15px; }
        .ticket-header::after { right: -15px; }
    </style>
</head>
<body>

<div class="ticket-container">
    <div id="captureArea" class="ticket-card animate__animated animate__zoomIn">
        <div class="ticket-header">
            <div class="success-check"><i class="fas fa-check"></i></div>
            <h3 class="fw-bold mb-1">ការកក់ជោគជ័យ!</h3>
            <p class="small opacity-75 mb-0">សូមរក្សាសំបុត្រនេះសម្រាប់បង្ហាញពេលមកដល់</p>
        </div>

        <div class="ticket-body">
            <span class="id-badge">Booking ID: MG-{{ strtoupper(substr(md5($booking->id), 0, 8)) }}</span>
            
            @php
                $rawLoc = $booking->location;
                $branchKhmer = $rawLoc; 

                if (stripos($rawLoc, 'Hq') !== false || stripos($rawLoc, 'head') !== false || $rawLoc == '13' || $rawLoc == '4') {
                    $branchKhmer = 'សាខាការិយាល័យកណ្តាល (HQ)';
                } elseif (stripos($rawLoc, '50m') !== false || $rawLoc == '11' || $rawLoc == '8') {
                    $branchKhmer = 'សាខាផ្លូវលេខ ៥០ម៉ែត្រ (50M)';
                } elseif (stripos($rawLoc, 'Sr') !== false || stripos($rawLoc, 'siem') !== false || $rawLoc == '12' || $rawLoc == '5') {
                    $branchKhmer = 'សាខាសៀមរាប (SR)';
                } elseif (stripos($rawLoc, 'Btb') !== false || stripos($rawLoc, 'battam') !== false || $rawLoc == '10' || $rawLoc == '6') {
                    $branchKhmer = 'សាខាបាត់ដំបង (BTB)';
                }
            @endphp

            <div class="info-group">
                <div class="info-row">
                    <span class="label">អតិថិជន</span>
                    <span class="value">{{ $booking->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">ស្លាកលេខ</span>
                    <span class="value highlight-blue">{{ $booking->plate }}</span>
                </div>
                <div class="info-row">
                    <span class="label">ម៉ូឌែល</span>
                    <span class="value">{{ $booking->model }}</span>
                </div>
                <div class="info-row">
                    <span class="label">គីឡូម៉ែត្រ</span>
                    <span class="value">{{ $booking->mileage ? number_format($booking->mileage) . ' KM' : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">សេវាកម្ម</span>
                    <span class="value">{{ $booking->service_type }}</span>
                </div>
                <div class="info-row">
                    <span class="label">ទីតាំងសាខា</span>
                    <span class="value" style="font-size: 0.85rem; color: var(--mg-blue);">{{ $branchKhmer }}</span>
                </div>
                <div class="info-row">
                    <span class="label">ថ្ងៃណាត់ជួប</span>
                    <span class="value">{{ date('d M Y', strtotime($booking->booking_date)) }}</span>
                </div>
                <div class="info-row" style="border:none; margin-bottom: 0;">
                    <span class="label">ម៉ោងណាត់</span>
                    <span class="value highlight-red">{{ date('h:i A', strtotime($booking->booking_time)) }}</span>
                </div>
            </div>

            <div class="note-box">
                <i class="fas fa-info-circle me-1"></i> សូមមកដល់មុន ១៥ នាទីនៃម៉ោងណាត់
            </div>
        </div>

        <div class="footer-copyright">
            MG Workshop Service Center © 2026
        </div>
    </div>

    <div class="btn-group-vertical">
        <button onclick="saveAsPhoto()" class="btn-custom btn-save shadow-sm">
            <i class="fas fa-file-download"></i> រក្សាទុកក្នុងទូរស័ព្ទ (Save Image)
        </button>
        <button onclick="shareToTelegram()" class="btn-custom btn-tg shadow-sm">
            <i class="fab fa-telegram-plane"></i> Share to Telegram
        </button>
        <a href="{{ route('services.public_create') }}" class="btn-custom btn-home shadow-sm">
            <i class="fas fa-check-circle"></i> រួចរាល់ (Done)
        </a>
    </div>
</div>

<script>
  async function saveAsPhoto() {
    const area = document.getElementById('captureArea');
    
    Swal.fire({
        title: 'កំពុងរៀបចំរូបភាព...',
        text: 'សូមរង់ចាំមួយភ្លែត ដើម្បីឱ្យពណ៌ដិតច្បាស់',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    setTimeout(async () => {
        try {
            window.scrollTo(0, 0);

            const canvas = await html2canvas(area, { 
                scale: 3,                
                backgroundColor: "#ffffff", 
                useCORS: true,           
                logging: false,
                removeContainer: true,
                onclone: (clonedDoc) => {
                    const clonedElement = clonedDoc.getElementById('captureArea');
                    clonedElement.style.animation = "none";
                    clonedElement.style.transition = "none";
                    clonedElement.style.opacity = "1";
                    clonedElement.style.visibility = "visible";
                    clonedElement.style.transform = "none";
                    
                    const allTexts = clonedElement.querySelectorAll('.value, .label, h3, p');
                    allTexts.forEach(el => {
                        el.style.opacity = "1";
                    });
                }
            });
            
            const link = document.createElement('a');
            link.download = 'MG-Booking-{{ $booking->plate }}.png';
            link.href = canvas.toDataURL('image/png', 1.0);
            link.click();
            
            Swal.close(); 
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'មានបញ្ហាក្នុងការរក្សារូបភាព!', 'error');
        }
    }, 500); 
}

    async function shareToTelegram() {
        const area = document.getElementById('captureArea');
        try {
            const canvas = await html2canvas(area, { scale: 3, backgroundColor: "#ffffff" });
            const dataUrl = canvas.toDataURL('image/png');
            const blob = await (await fetch(dataUrl)).blob();
            const file = new File([blob], 'MG-Booking.png', { type: 'image/png' });

            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                await navigator.share({
                    files: [file],
                    title: 'សំបុត្រកក់ MG Service',
                });
            } else {
                saveAsPhoto();
            }
        } catch (err) {
            console.error(err);
        }
    }
</script>

</body>
</html>