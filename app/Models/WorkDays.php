<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkDays extends Model
{
    use HasFactory;
    protected $table = 'workdays';
    protected $fillable = [
        'officer_id',
        'day_of_week',
    ];
    public function officer(){
        return $this->belongsTo(Officer::class,'officer_id','officer_id');
    }
}
?>