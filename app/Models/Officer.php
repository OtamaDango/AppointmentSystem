<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    protected $fillable = [
        'name',
        'post_id',
        'status',
        'WorkStartTime',
        'WorkEndTime',
    ];
    public function post(){
        return $this->belongsTo(Post::class);
    }
    public function appointments(){
        return $this->hasMany(Appointment::class);
    }
    public function activities(){
        return $this->hasMany(Activity::class);
    }
    public function workDays(){
        return $this->hasMany(WorkDays::class);
    }

    public function activate(){
        if($this->post->status == 'Inactive') 
            return false;
        $this->status = 'Active';
        $this->save();

        foreach($this->activities()-> where('status', 'Inactive')->get() as $activity){
            if($activity->appointment && $activity->appointment->status == 'Active'){
                $activity->status = 'Active';
                $activity->save();
            }
        }
    }
    public function deactivate(){
        $this->status = 'Inactive';
        $this->save();

        foreach($this->appointments()-> where('status', 'Active')->get() as $activity){
            $activity->status = 'Inactive';
            $activity->save();
        }
    }
}
?>