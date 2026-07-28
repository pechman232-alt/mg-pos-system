<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;700&display=swap" rel="stylesheet">

<div class="container py-4">
    <div class="d-flex justify-content-center gap-2 mb-4 no-print">
        <button onclick="saveTicketPDF()" class="btn btn-danger rounded-pill px-4 shadow fw-bold">
            <i class="fas fa-file-pdf me-2"></i> Save as PDF
        </button>
        <button onclick="window.print()" class="btn btn-light border rounded-pill px-4 shadow fw-bold">
            <i class="fas fa-print me-2"></i> Print Ticket
        </button>
    </div>

    <div id="printable-ticket" style="background: white; max-width: 500px; margin: auto; border: 1px solid #eee; border-radius: 25px; overflow: hidden; font-family: 'Kantumruy Pro', sans-serif; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        
        <div style="background: #002663; color: white; padding: 35px 20px; text-align: center;">
            <h2 style="margin: 0; font-weight: 800; letter-spacing: 1px; font-size: 24px;">MG WORKSHOP</h2>
            <p style="margin: 5px 0 0; opacity: 0.8; font-size: 13px; text-transform: uppercase;">Job Completion Ticket</p>
        </div>

        <div style="padding: 30px;">
            <div style="text-align: center; margin-bottom: 25px;">
                <div style="border: 3px double #10b981; color: #10b981; display: inline-block; padding: 5px 20px; border-radius: 10px; font-weight: 900; transform: rotate(-3deg); font-size: 18px;">
                    រួចរាល់ / COMPLETED
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="padding: 12px 0; color: #64748b;">ស្លាកលេខ / PLATE</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 800; color: #002663; font-size: 16px;">{{ $booking->plate }}</td>
                </tr>
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="padding: 12px 0; color: #64748b;">ម៉ូឌែល / MODEL</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 700;">{{ $booking->model }}</td>
                </tr>
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="padding: 12px 0; color: #64748b;">អតិថិជន / CUSTOMER</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 700;">{{ $booking->customer_name }}</td>
                </tr>
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="padding: 12px 0; color: #64748b;">សេវាកម្ម / SERVICE</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 700; color: #e31e24;">{{ $booking->service_type }}</td>
                </tr>
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="padding: 12px 0; color: #64748b;">ម៉ោងចាប់ផ្ដើម / START</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 600;">{{ date('h:i A', strtotime($booking->start_time)) }}</td>
                </tr>
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="padding: 12px 0; color: #64748b;">ម៉ោងបញ្ចប់ / END</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: 600;">{{ date('h:i A', strtotime($booking->end_time)) }}</td>
                </tr>
            </table>

            <div style="background: #f0fdf4; margin-top: 25px; padding: 15px; border-radius: 15px; text-align: center; border: 1px solid #bbf7d0;">
                <p style="margin: 0; font-size: 11px; color: #166534; font-weight: 700; text-transform: uppercase;">រយៈពេលចំណាយសរុប (Total Duration)</p>
                @php
                    $start = \Carbon\Carbon::parse($booking->start_time);
                    $end = \Carbon\Carbon::parse($booking->end_time);
                    $diff = $start->diff($end);
                    $duration = ($diff->h > 0 ? $diff->h . ' ម៉ោង ' : '') . $diff->i . ' នាទី';
                @endphp
                <h3 style="margin: 5px 0 0; color: #10b981; font-weight: 800; font-size: 22px;">{{ $duration }}</h3>
            </div>

            <div style="margin-top: 40px; display: flex; justify-content: space-between; text-align: center;">
                <div style="width: 45%;">
                    <div style="border-bottom: 1px solid #e2e8f0; height: 50px;"></div>
                    <p style="margin-top: 10px; font-size: 11px; font-weight: 700; color: #1e293b;">ហត្ថលេខាមេជាង<br><span style="font-size: 9px; opacity: 0.6;">Technician</span></p>
                </div>
                <div style="width: 45%;">
                    <div style="border-bottom: 1px solid #e2e8f0; height: 50px;"></div>
                    <p style="margin-top: 10px; font-size: 11px; font-weight: 700; color: #1e293b;">ហត្ថលេខាអតិថិជន<br><span style="font-size: 9px; opacity: 0.6;">Customer Sign</span></p>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: center; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <p style="margin: 0; font-size: 11px; color: #94a3b8;">សូមអរគុណដែលបានជ្រើសរើសសេវាកម្ម MG Cambodia!</p>
                <p style="margin: 5px 0 0; font-size: 10px; font-weight: bold; color: #475569;">{{ date('d-M-Y H:i A') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    function saveTicketPDF() {
        // ចាប់យក Element តាមរយៈ ID printable-ticket
        const element = document.getElementById('printable-ticket');
        
        if(!element) {
            alert("Error: រកមិនឃើញទម្រង់សំបុត្រសម្រាប់ Save ទេ!");
            return;
        }

        const opt = {
            margin:       0.3,
            filename:     'MG_Ticket_{{ $booking->plate }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        // ដំណើរការ Save
        html2pdf().set(opt).from(element).save();
    }

    // បន្ថែម CSS សម្រាប់ Print តាមម៉ាស៊ីនបោះពុម្ព
    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            #printable-ticket { box-shadow: none !important; border: none !important; margin: 0 auto !important; }
        }
    `;
    document.head.appendChild(style);
</script>