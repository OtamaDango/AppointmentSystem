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
        return $this->belongsTo(Officer::class);
    }
    public function visitor(){
        return $this->belongsTo(Visitor::class);
    }
    public function activities(){
        return $this->hasMany(Activity::class);
    }
}
?>