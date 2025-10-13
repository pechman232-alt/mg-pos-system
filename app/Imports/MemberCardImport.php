<?php

namespace App\Imports;

use App\Models\MemberCard;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MemberCardImport implements ToModel, WithHeadingRow ,WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MemberCard([
            'member_card' => $row['member_card'],
        ]);
    }

    public function rules(): array {
        return [
            'member_card' => 'required|max:255|unique:member_cards,member_card',
        ];
    }
}
