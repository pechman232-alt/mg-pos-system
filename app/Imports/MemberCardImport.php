<?php

namespace App\Imports;

use App\Models\MemberCard;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class MemberCardImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsEmptyRows
{
    public function model(array $row)
    {
        // ១. ទាញយកលេខកាតពី Excel (តាមក្បាលជួរ member_card)
        $cardNumber = isset($row['member_card']) ? trim($row['member_card']) : null;

        // ២. បើជួរនោះទទេស្អាត (Null ឬ គ្មានអក្សរ) មិនបាច់បញ្ចូលទេ
        if ($cardNumber === null || $cardNumber === '') {
            return null;
        }

        // ៣. បង្កើតកាតថ្មីជានិច្ច ទោះបីជាលេខកាតនោះស្ទួនគ្នាក៏ដោយ (អនុញ្ញាត Duplicate)
        // យើងកំណត់ is_sale = 1 (Available) ជាស្រេច ដើម្បីរង់ចាំឱ្យ Sale Report មកកាត់ស្តុកនៅពេលក្រោយ
        return new MemberCard([
            'member_card' => $cardNumber,
            'is_sale'     => 1 
        ]);
    }

    // ==========================================
    // 🌟 ការកំណត់ Batch & Chunk Size ដើម្បីឱ្យ Import ដើរលឿន និងមិនគាំង
    // ==========================================
    public function batchSize(): int
    {
        return 200; // បញ្ចូលទៅ Database ម្តង 200 ជួរ
    }

    public function chunkSize(): int
    {
        return 200; // អានពី Excel ម្តង 200 ជួរ
    }
}