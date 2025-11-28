<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'officer_id',
        'appointment_id',
        'start_date',
        'start_time', 
        'end_date', 
        'end_time', 
        'status',
    ];
    public function officer(){
        return $this->belongsTo(Officer::class);
    }
    public function appointment(){
        return $this->belongsTo(Appointment::class);
    }
    protected static function booted()
    {
        static::created(function ($appointment) {
            Activity::create([
                'type' => 'Appointment',
                'officer_id' => $appointment->officer_id,
                'appointment_id' => $appointment->id,
                'start_date' => $appointment->date,
                'start_time' => $appointment->StartTime,
                'end_date' => $appointment->date,
                'end_time' => $appointment->EndTime,
                'status' => $appointment->status,
            ]);
        });

        static::updated(function ($appointment) {
            if($appointment->status === 'Cancelled') {
                foreach($appointment->activities()->where('type','Appointment')->get() as $activity){
                    $activity->status = 'Cancelled';
                    $activity->save();
                }
            }
        });
    }

}
?>