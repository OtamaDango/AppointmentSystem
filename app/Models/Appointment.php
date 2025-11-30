<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'officer_id',
        'visitor_id',
        'name',
        'status',
        'date',
        'StartTime',
        'EndTime',
        'AddedOn',
    ];
    public function officer(){
        return $this->belongsTo(Officer::class,'officer_id','officer_id');
    }
    public function visitor(){
        return $this->belongsTo(Visitor::class,'visitor_id','officer_id');
    }
    public function activities(){
        return $this->hasOne(Activity::class,'appointment_id','appointment_id');
    }
}
?>