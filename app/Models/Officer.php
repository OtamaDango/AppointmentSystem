<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
class Officer extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'post_id',
        'status',
        'WorkStartTime',
        'WorkEndTime',
    ];
    protected $hidden = [
        'password',
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