<?php

namespace App\Http\Controllers;
use App\Models\Officer;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    public function index(){
        return Officer::all();
    }
    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'post_id' => 'required|exists:posts,id',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'workdays' => 'required|array|min:1',
            'workdays.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $officer = Officer::create([
            'name' => $validated['name'],
            'post_id' => $validated['post_id'],
            'work_start_time' => $validated['work_start_time'],
            'work_end_time' => $validated['work_end_time'],
            'status' => 'Active',
        ]);

        foreach($validated['workdays']as $day){
            $officer->workDays()->create([
                'day_of_week' => $day,
            ]);
        }

        return response()->json([
            'message' => 'Officer created successfully',
            'officer' => $officer], 201);
    }
    public function update(Request $request,$id){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'post_id' => 'required|exists:posts,id',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'workdays' => 'required|array|min:1',
            'workdays.*' => 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun',
        ]);

        $officer = Officer::findOrFail($id);
        $officer->name = $validated['name'];
        $officer->post_id = $validated['post_id'];
        $officer->work_start_time = $validated['work_start_time'];
        $officer->work_end_time = $validated['work_end_time'];
        $officer->save();

        $officer->workDays()->delete();
        foreach($validated['workdays']as $day){
            $officer->workDays()->create([
                'day_of_week' => $day,
            ]);
        }
        //deactivate activities that fall outside new schedule
        $officer->activities()
            ->where('date','>=',now())
            ->where(function($query) use ($validated){
                $query->whereTime('start_time','<',$validated['work_start_time'])
                      ->orWhereTime('end_time','>',$validated['work_end_time']);
            })
            ->update(['status' => 'Deactivated']);

        return response()->json([
            'message' => 'Officer updated successfully',
            'officer' => $officer], 200);
    }
    public function activate($id){
        $officer = Officer::findOrFail($id);
        // check if post is active
        if($officer->post->status !== 'Active'){
            return response()->json(['error' => 'Cannot activate officer with inactive post'], 400);
        }
        $officer->status = 'Active';
        $officer->save();
        //reactivate future activities if visitor is active
        $officer->activities()
            ->where('status','Deactivated')
            ->where('date','>=', now())
            ->whereHas('appointment.visitor', function($query){
                $query->where('status','Active');
            })
            ->update(['status' => 'Active']);

        return response()->json([
            'message' => 'Officer activated successfully',
            'officer' => $officer],200);
    }
    public function deactivate($id){
        $officer = Officer::findOrFail($id);
        $officer->status = 'Inactive';
        $officer->save();
        //deactivate future activities
        $officer->activities()
            ->where('status','Active')
            ->where('date','>=',now())
            ->update(['status' => 'Deactivated']);
        
        return response()->json([
            'message' => 'Officer deactivated successfully',
            'officer' => $officer],200);
    }
}
?>