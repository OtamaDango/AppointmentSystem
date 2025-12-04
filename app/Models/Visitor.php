<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Visitor extends Model
{
    use HasFactory;
    protected $primaryKey = 'visitor_id';
    protected $fillable = [
        'name',
        'mobileno',
        'email',
        'status',
    ];
    public function appointments(){
        return $this->hasMany(Appointment::class,'visitor_id','visitor_id');
    }
    public function activate(){
        $this->status = 'Active';
        $this->save();

        foreach($this->appointments()-> where('status', 'Inactive')->get() as $appointment){
            if($appointment->officer->status == 'Active'){
                $appointment->status = 'Active';
                $appointment->save();
            }
        }
    }
    public function deactivate(){
        $this->status = 'Inactive';
        $this->save();

        foreach($this->appointments()-> where('status', 'Active')->get() as $appointment){
            $appointment->status = 'Inactive';
            $appointment->save();
        }
    }

}
?>