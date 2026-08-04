<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceBooking extends Model
{
    use HasFactory;

    // ១. បញ្ជាក់ឈ្មោះ Table ក្នុង Database
    protected $table = 'service_bookings';

    // ២. បញ្ជាក់ Column ដែលអនុញ្ញាតឱ្យបញ្ចូលទិន្នន័យ (Mass Assignment)
    protected $fillable = [
        'customer_name',
        'contact',
        'model',
        'plate',
        'mileage',         // ✅ ត្រឹមត្រូវ! វាអនុញ្ញាតឱ្យ Save ចូល Database ហើយ
        'service_type',
        'location',
        'branch_id',
        'booking_time',
        'booking_date',
        'status',
        'arrival_date',
        'arrival_time',
        'start_time',
        'end_time',
        'sa_name',
        'remark' ,
        'technician_name',
        'est_finish_date',
        'mechanic_remark',
        'total_seconds',
        'paused_duration',
        'is_walk_in'  , 
        'note'    // 🌟 ថែម Field ថ្មីនេះ ដើម្បីឱ្យវាអនុញ្ញាតការ Save ពី Controller
    ];

    // ៣. បំប្លែងទិន្នន័យអូតូ (Casting)
    protected $casts = [
        'booking_date' => 'date',
        'mileage'      => 'integer', // 🌟 បន្ថែមទីនេះ ដើម្បីប្រាកដថាទិន្នន័យដែលចូលទៅ DB គឺជាទម្រង់លេខ (INT) ជានិច្ច
        'is_walk_in'   => 'integer', // 🌟 ប្រាកដថាវាជាលេខ 0 ឬ 1
        'estimation_history' => 'array',
    ];

    /**
     * Accessor: បង្ហាញឈ្មោះទីតាំងពេញជាភាសាខ្មែរ
     */
    public function getLocationKhAttribute()
    {
        $locations = [
            // ឈ្មោះពេញ (សម្រាប់ទិន្នន័យថ្មី)
            'Service Sr (siem Reap)'                   => 'សាខាសៀមរាប (ខេត្តសៀមរាប)',
            'Service Btb (battambang)'                 => 'សាខាបាត់ដំបង (ខេត្តបាត់ដំបង)',
            'Service Hq (head Office)'                 => 'សាខាការិយាល័យកណ្តាល (រាជធានីភ្នំពេញ)',
            'Service 50m (street 50m - Phnom Penh)'    => 'សាខាផ្លូវលេខ ៥០ម៉ែត្រ (រាជធានីភ្នំពេញ)',
            
            'Mg Service Sr (siem Reap)'                => 'សាខាសៀមរាប (ខេត្តសៀមរាប)',
            'Mg Service Btb (battambang)'              => 'សាខាបាត់ដំបង (ខេត្តបាត់ដំបង)',
            'Mg Service Hq (head Office)'              => 'សាខាការិយាល័យកណ្តាល (រាជធានីភ្នំពេញ)',
            'Mg Service 50m (street 50m - Phnom Penh)' => 'សាខាផ្លូវលេខ ៥០ម៉ែត្រ (រាជធានីភ្នំពេញ)',
            
            // សាខាចាស់ (អក្សរកាត់)
            'SR'  => 'សាខាសៀមរាប (ខេត្តសៀមរាប)',
            'BTB' => 'សាខាបាត់ដំបង (ខេត្តបាត់ដំបង)',
            'HQ'  => 'សាខាការិយាល័យកណ្តាល (រាជធានីភ្នំពេញ)',
            '50M' => 'សាខាផ្លូវលេខ ៥០ម៉ែត្រ (រាជធានីភ្នំពេញ)',
            'MC'  => 'មជ្ឈមណ្ឌលសេវាកម្មយានដ្ឋានសេវាកម្ម',

            // 🌟 ទិន្នន័យចាស់ៗដែលជាប់ជាលេខ ID ក្នុង Database
            '10' => 'សាខាបាត់ដំបង (ខេត្តបាត់ដំបង)',         // លេខ 10 ផ្អែកតាម Database របស់បង
            '11' => 'សាខាសៀមរាប (ខេត្តសៀមរាប)',            // (បើលេខ 11 ជាសាខាផ្សេង សូមបងជួយកែឈ្មោះត្រង់នេះ)
            '12' => 'សាខាការិយាល័យកណ្តាល (រាជធានីភ្នំពេញ)',  // (បើលេខ 12 ជាសាខាផ្សេង សូមបងជួយកែឈ្មោះត្រង់នេះ)
            '13' => 'សាខាផ្លូវលេខ ៥០ម៉ែត្រ (រាជធានីភ្នំពេញ)',  // (បើលេខ 13 ជាសាខាផ្សេង សូមបងជួយកែឈ្មោះត្រង់នេះ)
        ];

        return $locations[$this->location] ?? $this->location;
    }

    /**
     * Mutator: បំប្លែងស្លាកលេខឱ្យទៅជាអក្សរធំជានិច្ចមុននឹង Save ចូល Database
     */
    public function setPlateAttribute($value)
    {
        $this->attributes['plate'] = strtoupper($value);
    }

    /**
     * Accessor: បង្ហាញម៉ោងឱ្យមើលទៅស្អាត (AM/PM)
     */
    public function getFormattedTimeAttribute()
    {
        return $this->booking_time ? Carbon::parse($this->booking_time)->format('h:i A') : 'N/A';
    }

    /**
     * Accessor: បង្ហាញថ្ងៃខែឱ្យមើលទៅស្អាត (Day-Month-Year)
     */
    public function getFormattedDateAttribute()
    {
        return $this->booking_date ? Carbon::parse($this->booking_date)->format('d-M-Y') : 'N/A';
    }

    /**
     * Accessor: គណនារយៈពេលធ្វើការសរុប (នាទី)
     */
    public function getWorkDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);
            return $start->diffInMinutes($end);
        }
        return 0;
    }
}