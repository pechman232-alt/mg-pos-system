<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SaleReportTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * 🌟 កំណត់ឈ្មោះក្បាលជួរ (Headers) ទាំង ៤១ ឱ្យត្រូវតាមឯកសារដើម ១០០%
     */
    public function headings(): array
    {
        return [
            'Branch',
            'Member Card',
            'Brand Name',
            "Customer's Name",
            'Phone Number',
            'Gender',
            'Date of Birth',
            'Age',
            'Source Customer',
            'Vehicle Model',
            'Color',
            'Model Year',
            'VIN Number',
            'Engine Number',
            'Plate Number',
            'Invoice No.',
            'Invoice Date',
            'Delivery Date',
            'Follow-Up',
            'Follow-Up Commends',
            'Remark admin',
            'Warranty',
            'Battery Warranty For LeapMotor',
            'Sales Person',
            'Address',
            'Contract No.',
            'Contract Date',
            'VPC & VDR',
            'Offer License Plate',
            'Offer UV Film',
            'Offer Floor Mat',
            'Side Step',
            'Auto Tailgate',
            'Rear Spoiler',
            'Warranty Letter',
            'Certificate Engine Oil',
            'Pro.Offer - Oil',
            'Pro.Offer Oil - Date',
            'Vehicle Battery Inspection Record',
            'Remark - SA',
            'Price'
        ];
    }

    /**
     * 🌟 ទិន្នន័យគំរូ ១ជួរ (រៀបចំត្រូវតាមលេខរៀង Column ទាំង ៤១ ខាងលើ)
     */
    public function array(): array
    {
        return [
            [
                '60M',                                 // 1. Branch
                '11004976',                            // 2. Member Card
                'MG',                                  // 3. Brand Name
                'Mr. Kea Thoeun',                      // 4. Customer's Name
                '088 353 5884',                        // 5. Phone Number
                'Male',                                // 6. Gender
                '1968-09-01',                          // 7. Date of Birth
                '57',                                  // 8. Age
                'Tik Tok',                             // 9. Source Customer
                'MG ZS 1.5 COM 4AT 2WD',               // 10. Vehicle Model
                'White',                               // 11. Color
                '2025',                                // 12. Model Year
                'LSJW74U6XSZ269972',                   // 13. VIN Number
                '15S4CCMGS6160219',                    // 14. Engine Number
                '2AB-1234',                            // 15. Plate Number
                'C25V120244',                          // 16. Invoice No.
                '2025-09-20',                          // 17. Invoice Date
                '2025-09-20',                          // 18. Delivery Date
                'A',                                   // 19. Follow-Up
                'Customer pick up but not speak',      // 20. Follow-Up Commends
                'No respond',                          // 21. Remark admin
                '5 Years/150,000 KM',                  // 22. Warranty
                'N/A',                                 // 23. Battery Warranty For LeapMotor
                'Mr. Ngin Putpunlear',                 // 24. Sales Person
                'Prey Veng',                           // 25. Address
                '25120223',                            // 26. Contract No.
                '2025-09-02',                          // 27. Contract Date
                'Both',                                // 28. VPC & VDR
                'Yes',                                 // 29. Offer License Plate
                'No',                                  // 30. Offer UV Film
                'Yes',                                 // 31. Offer Floor Mat
                'No',                                  // 32. Side Step
                'No',                                  // 33. Auto Tailgate
                'No',                                  // 34. Rear Spoiler
                'Yes',                                 // 35. Warranty Letter
                'NONE',                                // 36. Certificate Engine Oil
                'N/A',                                 // 37. Pro.Offer - Oil
                '',                                    // 38. Pro.Offer Oil - Date
                '',                                    // 39. Vehicle Battery Inspection Record
                '',                                    // 40. Remark - SA
                '25000'                                // 41. Price
            ]
        ];
    }

    /**
     * 🌟 Design ពណ៌ក្បាលជួរអោយលេចធ្លោជាលក្ខណៈស្តង់ដារ
     */
    public function styles(Worksheet $sheet)
    {
        // ដាក់ពណ៌ Background លេខកូដពណ៌ MG (Dark Blue) និងអក្សរពណ៌ស
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF002663']]
            ],
        ];
    }
}