<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $primaryKey = 'post_id';
    protected $fillable = [
        'name',
        'status',
    ];
    public function officers(){
        return $this->hasMany(Officer::class,'post_id','post_id');
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