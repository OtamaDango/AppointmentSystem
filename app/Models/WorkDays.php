<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkDays extends Model
{
    protected $table = 'workdays';
    protected $fillable = [
        'officer_id',
        'day_of_week',
    ];
    public function officer(){
        return $this->belongsTo(Officer::class);
    }
}
?>