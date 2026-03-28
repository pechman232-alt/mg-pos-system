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
        'service_type',
        'location',
        'booking_time',
        'booking_date',
        'status',
        'arrival_time',    // 💡 ត្រូវតែថែមអាហ្នឹងមួយទៀត ទើបប៊ូតុង Arrived ដើរ
        'start_time',
        'end_time',
        'sa_name',
        'technician_name',
        'est_finish_date',
        'mechanic_remark',
        'total_seconds' 
    ];

    // ៣. បំប្លែងទិន្នន័យអូតូ (Casting)
    protected $casts = [
        'booking_date' => 'date',
    
    ];
    /**
     * Accessor: បង្ហាញឈ្មោះទីតាំងពេញជាភាសាខ្មែរ (Update តាមសាខាថ្មី)
     * របៀបប្រើក្នុង View: {{ $job->location_kh }}
     */
    public function getLocationKhAttribute()
    {
        $locations = [
            'SR'  => 'សាខាសៀមរាប (ខេត្តសៀមរាប)',
            'BTB' => 'សាខាបាត់ដំបង (ខេត្តបាត់ដំបង)',
            'HQ'  => 'សាខាការិយាល័យកណ្តាលភ្នំពេញ (រាជធានីភ្នំពេញ)',
            '50M' => 'សាខាផ្លូវលេខ ៥០ម៉ែត្រ (រាជធានីភ្នំពេញ)',
            'MC'  => 'មជ្ឈមណ្ឌលសេវាកម្មយានដ្ឋានសេវាកម្ម',
        ];

        return $locations[$this->location] ?? $this->location;
    }

    /**
     * Mutator: បំប្លែងស្លាកលេខឱ្យទៅជាអក្សរធំជានិច្ចមុននឹង Save ចូល Database
     * ឧទាហរណ៍៖ បើវាយ "2ab-1234" វានឹងរក្សាទុកជា "2AB-1234"
     */
    public function setPlateAttribute($value)
    {
        $this->attributes['plate'] = strtoupper($value);
    }

    /**
     * Accessor: បង្ហាញម៉ោងឱ្យមើលទៅស្អាត (AM/PM)
     * របៀបប្រើក្នុង View: {{ $job->formatted_time }}
     */
    public function getFormattedTimeAttribute()
    {
        return $this->booking_time ? Carbon::parse($this->booking_time)->format('h:i A') : 'N/A';
    }

    /**
     * Accessor: បង្ហាញថ្ងៃខែឱ្យមើលទៅស្អាត (Day-Month-Year)
     * របៀបប្រើក្នុង View: {{ $job->formatted_date }}
     */
    public function getFormattedDateAttribute()
    {
        return $this->booking_date ? Carbon::parse($this->booking_date)->format('d-M-Y') : 'N/A';
    }

    /**
     * Accessor: គណនារយៈពេលធ្វើការសរុប (នាទី)
     * របៀបប្រើក្នុង View: {{ $job->work_duration }} mn
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