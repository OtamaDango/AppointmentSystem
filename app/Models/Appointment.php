<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Appointment extends Model
{
    use HasFactory;
    protected $primaryKey = 'appointment_id';
    public $incrementing = true;
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
        return $this->belongsTo(Visitor::class,'visitor_id','visitor_id');
    }
    public function activities(){
        return $this->hasOne(Activity::class,'appointment_id','appointment_id');
    }
}
?>