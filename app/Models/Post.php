<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];
    public function officers(){
        return $this->hasMany(Officer::class);
    }
    public function activate(){
        $this->status = 'Active';
        $this->save();
    }
    public function deactivate(){
        $this->status = 'Inactive';
        $this->save();
    }
}
?>