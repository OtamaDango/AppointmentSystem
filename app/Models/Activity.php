<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Activity extends Model
{
    use HasFactory;
    protected $primaryKey = 'activity_id';  
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
        return $this->belongsTo(Officer::class,'officer_id','officer_id');
    }
    public function appointment(){
        return $this->belongsTo(Appointment::class,'appointment_id','appointment_id');
    }
}
?>